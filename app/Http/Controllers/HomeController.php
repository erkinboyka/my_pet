<?php

namespace App\Http\Controllers;

use App\Models\Auction;
use Illuminate\View\View;

class HomeController extends Controller
{
    /**
     * Главная страница — лендинг.
     */
    public function index(): View
    {
        return view('home', [
            'activeLots' => Auction::live()->latest('end_at')->take(6)->get(),
            'upcomingLots' => Auction::upcoming()->orderBy('start_at')->take(3)->get(),
            'finishedLots' => Auction::where('status', 'finished')->latest('end_at')->take(3)->get(),
        ]);
    }
}
