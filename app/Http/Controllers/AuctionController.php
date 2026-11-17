<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use App\Services\AuctionService;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuctionController extends Controller
{
    private AuctionService $auctionService;

    public function __construct(AuctionService $auctionService)
    {
        $this->auctionService = $auctionService;
    }

    // список лотов
    public function index(Request $request): View
    {
        $query = Auction::query()->whereNotIn('status', ['draft', 'pending_moderation', 'cancelled', 'rejected']);

        // поиск по названию и описанию
        // TODO: нормальный поиск через индекс, like по описанию тормозит
        $search = trim((string) $request->query('q'));
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // фильтр по категории
        $categoryId = $request->integer('category');
        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        // вкладки: всё / активные / скоро / завершённые
        $tab = $request->query('tab', 'live');
        if ($tab === 'live') {
            $query->live();
        } elseif ($tab === 'upcoming') {
            $query->upcoming();
        } elseif ($tab === 'finished') {
            $query->finished();
        }

        // сортировка
        $sort = $request->query('sort', 'end_asc');
        if ($sort === 'end_desc') {
            $query->orderByDesc('end_at');
        } elseif ($sort === 'price_asc') {
            $query->orderBy('starting_price');
        } elseif ($sort === 'price_desc') {
            $query->orderByDesc('starting_price');
        } elseif ($sort === 'new') {
            $query->latest();
        } else {
            $query->orderBy('end_at');
        }

        return view('auctions.index', [
            'lots' => $query->paginate(9)->withQueryString(),
            'categories' => \App\Models\Category::orderBy('name')->get(),
            'tab' => $tab,
            'sort' => $sort,
        ]);
    }

    // страница одного лота
    public function show(Auction $auction): View
    {
        // если время вышло — сразу записываем победителя
        $this->auctionService->finalizeIfExpired($auction);

        $auction->load([
            'category',
            'moderator',
            'winner',
            'bids' => fn ($q) => $q->latest('created_at')->latest('id')->take(50),
            'bids.user',
        ]);

        return view('auctions.show', [
            'auction' => $auction,
        ]);
    }
}
