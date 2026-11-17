<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\User;
use Illuminate\View\View;

class ModeratorController extends Controller
{
    // список модераторов и назначенные им лоты
    public function index(): View
    {
        $moderators = User::where('role', 'moderator')
            ->with(['managedAuctions' => fn ($q) => $q->orderByDesc('created_at')])
            ->withCount('managedAuctions')
            ->orderBy('name')
            ->get();

        return view('admin.moderators.index', [
            'moderators' => $moderators,
            'statuses' => Auction::STATUSES,
        ]);
    }
}
