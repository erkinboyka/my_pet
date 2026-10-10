<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\View\View;

class AuctionController extends Controller
{
    /**
     * Список лотов.
     */
    public function index(): View
    {
        return view('auctions.index', [
            'lots' => Auction::live()
                ->orderBy('end_at')
                ->get(),
        ]);
    }
}
