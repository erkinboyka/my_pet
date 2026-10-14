<?php

use App\Console\Commands\FinishExpiredAuctions;
use Illuminate\Support\Facades\Schedule;

Schedule::command(FinishExpiredAuctions::class)->everyMinute();
