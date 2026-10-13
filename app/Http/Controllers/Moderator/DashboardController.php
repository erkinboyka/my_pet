<?php

namespace App\Http\Controllers\Moderator;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Обзор модератора: только его лоты.
     */
    public function index(): View
    {
        $user = auth()->user();

        $myLotIds = $user->managedAuctions()->pluck('id');

        return view('moderator.dashboard', [
            'stats' => [
                'total' => $myLotIds->count(),
                'live' => Auction::live()->where('moderator_id', $user->id)->count(),
                'pending' => Auction::where('moderator_id', $user->id)->where('status', 'pending_moderation')->count(),
                'finished' => Auction::where('moderator_id', $user->id)->where('status', 'finished')->count(),
            ],
            'liveLots' => Auction::live()->where('moderator_id', $user->id)
                ->orderBy('end_at')->take(5)->get(),
            'recentBids' => Bid::with(['user', 'auction'])
                ->whereIn('auction_id', $myLotIds)
                ->latest('created_at')
                ->take(8)
                ->get(),
        ]);
    }
}
