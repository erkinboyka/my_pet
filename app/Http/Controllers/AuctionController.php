<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AuctionController extends Controller
{
    /**
     * Список лотов.
     */
    public function index(Request $request): View
    {
        $query = Auction::query()->whereNotIn('status', ['draft', 'pending_moderation', 'cancelled', 'rejected']);

        // Поиск по названию и описанию
        if ($search = trim((string) $request->query('q'))) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Фильтр по категории
        if ($categoryId = $request->integer('category')) {
            $query->where('category_id', $categoryId);
        }

        // Вкладки: всё / активные / скоро / завершённые
        $tab = $request->query('tab', 'live');
        if ($tab === 'live') {
            $query->live();
        } elseif ($tab === 'upcoming') {
            $query->upcoming();
        } elseif ($tab === 'finished') {
            $query->finished();
        }

        // Сортировка
        $sort = $request->query('sort', 'end_asc');
        match ($sort) {
            'end_desc' => $query->orderByDesc('end_at'),
            'price_asc' => $query->orderBy('starting_price'),
            'price_desc' => $query->orderByDesc('starting_price'),
            'new' => $query->latest(),
            default => $query->orderBy('end_at'),
        };

        return view('auctions.index', [
            'lots' => $query->paginate(9)->withQueryString(),
            'categories' => \App\Models\Category::orderBy('name')->get(),
            'tab' => $tab,
            'sort' => $sort,
        ]);
    }

    /**
     * Страница одного лота.
     */
    public function show(Auction $auction): View
    {
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
