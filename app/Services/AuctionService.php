<?php

namespace App\Services;

use App\Models\Auction;
use App\Models\Bid;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class AuctionService
{
    // тут всё внутри транзакции, лот лочим через lockForUpdate,
    // иначе две ставки одновременно перетрут друг другу цену
    public function placeBid(Auction $auction, int $userId, float $amount): Bid
    {
        return DB::transaction(function () use ($auction, $userId, $amount) {
            $locked = Auction::whereKey($auction->id)->lockForUpdate()->firstOrFail();

            // если время уже вышло — сразу закрываем лот
            $this->finalizeIfExpired($locked);

            if (! $locked->isOpenForBids()) {
                throw ValidationException::withMessages([
                    'amount' => __('bid.err.closed'),
                ]);
            }

            if (now()->lt($locked->start_at)) {
                throw ValidationException::withMessages([
                    'amount' => __('bid.err.notStarted', ['when' => $locked->start_at->format('d.m.Y H:i')]),
                ]);
            }

            // если лидер ставок это тот же юзер — перебивать себя нельзя
            $topBid = $locked->bids()->latest('id')->first();
            if ($topBid && $topBid->user_id === $userId) {
                throw ValidationException::withMessages([
                    'amount' => __('bid.err.selfLeader'),
                ]);
            }

            $min = $locked->nextMinBid();

            if ($amount < $min) {
                throw ValidationException::withMessages([
                    'amount' => __('bid.err.tooLow', ['min' => format_price($min)]),
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

    // если время лота вышло — ставим finished и победителя (последняя ставка).
    // вызывается и при просмотре лота, и по расписанию
    public function finalizeIfExpired(Auction $auction): void
    {
        if (in_array($auction->status, ['scheduled', 'active'])) {
            if (now()->gte($auction->end_at)) {
                $winnerBid = $auction->bids()->latest('id')->first();

                $winnerId = null;
                $winningPrice = null;

                if ($winnerBid) {
                    $winnerId = $winnerBid->user_id;
                    $winningPrice = $winnerBid->amount;
                }

                $auction->update([
                    'status' => 'finished',
                    'winner_id' => $winnerId,
                    'winning_price' => $winningPrice,
                ]);
            }
        }
    }
}
