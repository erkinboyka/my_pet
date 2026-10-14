<?php

namespace App\Http\Controllers;

use App\Http\Requests\PlaceBidRequest;
use App\Models\Auction;
use App\Models\Bid;
use App\Services\AuctionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class BidController extends Controller
{
    public function __construct(private AuctionService $auctions)
    {
    }

    /**
     * Принять ставку. Клиент присылает только amount — остальное
     * сервер сам читает из лота.
     */
    public function store(PlaceBidRequest $request, Auction $auction): RedirectResponse
    {
        $user = $request->user();

        // Ставки делают только клиенты
        if (! $user->isClient()) {
            return back()->with('error', 'Ставки может делать только участник с ролью client.');
        }

        // На свой лот ставить нельзя
        if ($auction->moderator_id === $user->id || $auction->created_by === $user->id) {
            return back()->with('error', 'На собственный лот ставить нельзя.');
        }

        $amount = (float) $request->input('amount');

        $this->auctions->placeBid($auction, $user->id, $amount);

        return back()->with(
            'success',
            'Ваша ставка принята: '.number_format($amount, 0, ',', ' ').' сомони.'
        );
    }

    /**
     * История своих ставок.
     */
    public function myBids(): View
    {
        $bids = Bid::where('user_id', auth()->id())
            ->with('auction')
            ->latest('created_at')
            ->latest('id')
            ->paginate(10);

        return view('client.my-bids', [
            'bids' => $bids,
        ]);
    }

    /**
     * Выигранные лоты.
     */
    public function wins(): View
    {
        $wins = Auction::where('winner_id', auth()->id())
            ->with('category')
            ->orderByDesc('end_at')
            ->paginate(10);

        return view('client.wins', [
            'wins' => $wins,
        ]);
    }
}
