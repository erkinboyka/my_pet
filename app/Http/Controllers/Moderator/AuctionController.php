<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class AuctionController extends Controller
{
    /**
     * Только свои лоты — проверка по moderator_id, а не по роли.
     */
    public function index(Request $request): View
    {
        $query = Auction::where('moderator_id', auth()->id());

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('moderator.auctions.index', [
            'lots' => $query->orderByDesc('created_at')->paginate(15)->withQueryString(),
            'statuses' => Auction::STATUSES,
        ]);
    }

    /**
     * Форма создания своего лота.
     */
    public function create(): View
    {
        $this->authorize('create', Auction::class);

        return view('moderator.auctions.form', [
            'auction' => new Auction(),
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Сохранить лот. Опубликованный лот уходит админу на модерацию.
     */
    public function store(Request $request): RedirectResponse
    {
        $this->authorize('create', Auction::class);

        $data = $this->validateData($request);

        $auction = new Auction($data);
        $auction->moderator_id = auth()->id();
        $auction->created_by = auth()->id();
        $auction->status = $request->boolean('publish') ? 'pending_moderation' : 'draft';
        $auction->save();

        return redirect()->route('moderator.auctions.index')
            ->with(
                'success',
                $auction->status === 'pending_moderation'
                    ? __('mod.flash.createdPending', ['title' => $auction->title])
                    : __('mod.flash.createdDraft', ['title' => $auction->title])
            );
    }

    /**
     * Страница своего лота (с полной историей ставок).
     */
    public function show(Auction $auction): View
    {
        $this->authorize('view', $auction);

        $auction->load([
            'category',
            'winner',
            'bids' => fn ($q) => $q->latest('created_at')->latest('id')->take(100),
            'bids.user',
        ]);

        return view('moderator.auctions.show', [
            'auction' => $auction,
        ]);
    }

    /**
     * Форма редактирования.
     */
    public function edit(Auction $auction): View
    {
        $this->authorize('update', $auction);

        return view('moderator.auctions.form', [
            'auction' => $auction,
            'categories' => Category::orderBy('name')->get(),
        ]);
    }

    /**
     * Обновить лот.
     */
    public function update(Request $request, Auction $auction): RedirectResponse
    {
        $this->authorize('update', $auction);

        $data = $this->validateData($request);
        $auction->fill($data);
        $auction->status = $this->resolveStatus($auction, $request);
        $auction->save();

        return redirect()->route('moderator.auctions.index')
            ->with('success', __('mod.flash.updated', ['title' => $auction->title]));
    }

    /**
     * Отменить свой лот.
     */
    public function cancel(Auction $auction): RedirectResponse
    {
        $this->authorize('cancel', $auction);

        $auction->update(['status' => 'cancelled']);

        return back()->with('success', __('mod.flash.cancelled', ['title' => $auction->title]));
    }

    /**
     * @return array<string, mixed>
     */
    private function validateData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:200'],
            'description' => ['required', 'string', 'max:5000'],
            'category_id' => ['nullable', 'exists:categories,id'],
            'starting_price' => ['required', 'numeric', 'min:1', 'max:99999999'],
            'minimum_bid_step' => ['required', 'numeric', 'min:1', 'max:99999999'],
            'start_at' => ['required', 'date'],
            'end_at' => ['required', 'date', 'after:start_at'],
        ], [
            'end_at.after' => __('validation.endAfterStart'),
        ]);
    }

    /**
     * Модератор не может сам себя опубликовать — лот уходит админу.
     */
    private function resolveStatus(Auction $auction, Request $request): string
    {
        $current = $auction->status;

        if (in_array($current, ['finished', 'cancelled', 'rejected'])) {
            return $current;
        }

        if (! $request->boolean('publish')) {
            return $current === 'active' ? 'active' : 'draft';
        }

        // Уже одобренный лот просто пересчитываем по времени
        if (in_array($current, ['scheduled', 'active'])) {
            return Carbon::parse($auction->start_at)->isFuture() ? 'scheduled' : 'active';
        }

        // Чёрновик или повторная правка — снова на модерацию
        return 'pending_moderation';
    }
}
