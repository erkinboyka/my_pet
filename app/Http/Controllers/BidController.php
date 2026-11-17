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
    private AuctionService $auctions;

    public function __construct(AuctionService $auctions)
    {
        $this->auctions = $auctions;
    }

    // принять ставку, юзер шлёт только amount, остальное само из лота
    public function store(PlaceBidRequest $request, Auction $auction): RedirectResponse
    {
        $user = $request->user();

        // ставить могут только клиенты
        if (! $user->isClient()) {
            return back()->with('error', __('bid.err.role'));
        }

        // на свой лот ставить нельзя
        if ($auction->moderator_id === $user->id || $auction->created_by === $user->id) {
            return back()->with('error', __('bid.err.onOwn'));
        }

        $amount = (float) $request->input('amount');

        $this->auctions->placeBid($auction, $user->id, $amount);

        return back()->with(
            'success',
            __('bid.accepted', ['amount' => format_price($amount)])
        );
    }

    // история ставок юзера
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

    // выигранные лоты
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
