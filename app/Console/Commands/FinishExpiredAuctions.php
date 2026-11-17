<?php

namespace App\Console\Commands;

use App\Models\Auction;
use App\Services\AuctionService;
use Illuminate\Console\Command;

class FinishExpiredAuctions extends Command
{
    protected $signature = 'auctions:finish';

    protected $description = 'Обновляет статусы лотов по времени: открывает начавшиеся и завершает завершённые';

    public function handle(AuctionService $service): int
    {
        // лоты у которых время открытия уже пришло
        $opened = Auction::where('status', 'scheduled')
            ->where('start_at', '<=', now())
            ->where('end_at', '>', now())
            ->update(['status' => 'active']);

        // а у этих вышло время — записываем победителя
        $expired = Auction::whereIn('status', ['scheduled', 'active'])
            ->where('end_at', '<=', now())
            ->pluck('id');

        foreach ($expired as $id) {
            $service->finalizeIfExpired(Auction::findOrFail($id));
        }

        $this->info("Открыто лотов: {$opened}, завершено: {$expired->count()}.");

        return self::SUCCESS;
    }
}
