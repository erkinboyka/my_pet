<?php

namespace App\Policies;

use App\Models\Auction;
use App\Models\User;

class AuctionPolicy
{
    // модератор работает только со своими лотами, админ со всеми
    public function view(User $user, Auction $auction): bool
    {
        $result = false;

        if ($user->isAdmin()) {
            $result = true;
        } elseif ($user->isModerator() && $auction->moderator_id === $user->id) {
            $result = true;
        }

        return $result;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }

    // редактирование: модератор — только свои и не завершённые
    public function update(User $user, Auction $auction): bool
    {
        $result = false;

        if ($user->isAdmin()) {
            $result = $auction->status !== 'finished';
        } elseif ($user->isModerator() && $auction->moderator_id === $user->id) {
            $result = ! in_array($auction->status, ['active', 'finished', 'cancelled']);
        }

        return $result;
    }

    // отмена лота
    public function cancel(User $user, Auction $auction): bool
    {
        $result = false;

        if ($user->isAdmin()) {
            $result = $auction->status !== 'finished';
        } elseif ($user->isModerator() && $auction->moderator_id === $user->id) {
            $result = in_array($auction->status, ['draft', 'pending_moderation', 'scheduled']);
        }

        return $result;
    }

    // одобрение и отклонение — только админ
    public function moderate(User $user, Auction $auction): bool
    {
        return $user->isAdmin() && $auction->status === 'pending_moderation';
    }
}
