<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AuctionController extends Controller
{
    /**
     * Все лоты сайта.
     */
    public function index(Request $request): View
    {
        $query = Auction::query()->with(['category', 'moderator']);

        if ($search = trim((string) $request->query('q'))) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        return view('admin.auctions.index', [
            'lots' => $query->orderByDesc('created_at')->paginate(20)->withQueryString(),
            'statuses' => Auction::STATUSES,
        ]);
    }

    /**
     * Форма создания лота.
     */
    public function create(): View
    {
        return view('admin.auctions.form', [
            'auction' => new Auction(),
            'categories' => Category::orderBy('name')->get(),
            'moderators' => User::where('role', 'moderator')->orderBy('name')->get(),
        ]);
    }

    /**
     * Сохранить новый лот.
     */
    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $auction = new Auction($data);
        $auction->created_by = auth()->id();
        $auction->status = $this->resolveStatus($request, $data['start_at']);
        $auction->save();

        return redirect()->route('admin.auctions.index')
            ->with('success', __('admin.flash.lotCreated', ['title' => $auction->title]));
    }

    /**
     * Форма редактирования лота.
     */
    public function edit(Auction $auction): View
    {
        return view('admin.auctions.form', [
            'auction' => $auction,
            'categories' => Category::orderBy('name')->get(),
            'moderators' => User::where('role', 'moderator')->orderBy('name')->get(),
        ]);
    }

    /**
     * Обновить лот.
     */
    public function update(Request $request, Auction $auction): RedirectResponse
    {
        if ($auction->status === 'finished') {
            return back()->with('error', __('admin.flash.finishedNoEdit'));
        }

        $data = $this->validateData($request);
        $auction->fill($data);
        $auction->status = $this->resolveStatus($request, $data['start_at'], $auction->status);
        $auction->save();

        return redirect()->route('admin.auctions.index')
            ->with('success', __('admin.flash.lotUpdated', ['title' => $auction->title]));
    }

    /**
     * Одобрить лот модератора.
     */
    public function approve(Auction $auction): RedirectResponse
    {
        $this->authorize('moderate', $auction);

        $auction->update([
            'status' => $auction->start_at->isFuture() ? 'scheduled' : 'active',
        ]);

        return back()->with('success', __('admin.flash.approved', ['title' => $auction->title]));
    }

    /**
     * Отклонить лот модератора.
     */
    public function reject(Auction $auction): RedirectResponse
    {
        $this->authorize('moderate', $auction);

        $auction->update(['status' => 'rejected']);

        return back()->with('success', __('admin.flash.rejected', ['title' => $auction->title]));
    }

    /**
     * Отменить лот.
     */
    public function cancel(Auction $auction): RedirectResponse
    {
        if ($auction->status === 'finished') {
            return back()->with('error', __('admin.flash.finishedNoCancel'));
        }

        $auction->update(['status' => 'cancelled']);

        return back()->with('success', __('admin.flash.lotCancelled', ['title' => $auction->title]));
    }

    /**
     * Общая валидация формы лота.
     *
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
            'moderator_id' => ['nullable', Rule::exists('users', 'id')->where('role', 'moderator')],
        ], [
            'end_at.after' => __('validation.endAfterStart'),
        ]);
    }

    /**
     * Статус после публикации: запланирован или активен — решает время.
     */
    private function resolveStatus(Request $request, string $startAt, ?string $current = null): string
    {
        if ($current !== null && in_array($current, ['finished', 'cancelled', 'rejected'])) {
            return $current;
        }

        if (! $request->boolean('publish')) {
            return 'draft';
        }

        return now()->greaterThan(\Illuminate\Support\Carbon::parse($startAt)) ? 'active' : 'scheduled';
    }
}
