<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\Bid;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    // главный экран админа
    public function index(): View
    {
        return view('admin.dashboard', [
            'stats' => [
                'users' => User::count(),
                'live' => Auction::live()->count(),
                'pending' => Auction::where('status', 'pending_moderation')->count(),
                'finished' => Auction::where('status', 'finished')->count(),
                'total' => Auction::count(),
            ],
            'recentBids' => Bid::with(['user', 'auction'])->latest('created_at')->take(8)->get(),
        ]);
    }
}
