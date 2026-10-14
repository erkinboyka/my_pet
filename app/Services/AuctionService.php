<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuctionService
{
    /**
     * Принять ставку.
     *
     * Вся проверка идёт внутри транзакции с блокировкой строки лота
     * (lockForUpdate), поэтому две одновременные ставки не могут
     * обработать одну и ту же текущую цену.
     */
    public function placeBid(Auction $auction, int $userId, float $amount): Bid
    {
        return DB::transaction(function () use ($auction, $userId, $amount) {
            /** @var Auction $locked */
            $locked = Auction::whereKey($auction->id)->lockForUpdate()->firstOrFail();

            // Сразу закрываем лот, если время вышло
            $this->finalizeIfExpired($locked);

            if (! $locked->isOpenForBids()) {
                throw ValidationException::withMessages([
                    'amount' => 'Лот закрыт для ставок.',
                ]);
            }

            if (now()->lt($locked->start_at)) {
                throw ValidationException::withMessages([
                    'amount' => 'Лот ещё не открыт. Откроется '.$locked->start_at->format('d.m.Y H:i').'.',
                ]);
            }

            // Последняя ставка принадлежит этому же пользователю — перебивать себя нельзя
            $topBid = $locked->bids()->latest('id')->first();
            if ($topBid && $topBid->user_id === $userId) {
                throw ValidationException::withMessages([
                    'amount' => 'Вы и так держите максимальную ставку.',
                ]);
            }

            $min = $locked->nextMinBid();

            if ($amount < $min) {
                throw ValidationException::withMessages([
                    'amount' => 'Минимальная ставка — '.number_format($min, 0, ',', ' ').' сомони.',
                ]);
            }

            $bid = $locked->bids()->create([
                'user_id' => $userId,
                'amount' => $amount,
            ]);

            $locked->update(['current_price' => $amount]);

            return $bid;
        });
    }

    /**
     * Если время лота вышло — статус finished и победитель (последняя ставка).
     * Вызывается лениво при просмотре лота и по расписанию.
     */
    public function finalizeIfExpired(Auction $auction): void
    {
        if (! in_array($auction->status, ['scheduled', 'active'])) {
            return;
        }

        if (now()->lt($auction->end_at)) {
            return;
        }

        $winnerBid = $auction->bids()->latest('id')->first();

        $auction->update([
            'status' => 'finished',
            'winner_id' => $winnerBid?->user_id,
            'winning_price' => $winnerBid?->amount,
        ]);
    }
}
