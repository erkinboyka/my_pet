<?php

namespace App\Policies;

use App\Models\Auction;
use App\Models\User;

class AuctionPolicy
{
    /**
     * Модератор работает только со своими лотами, админ — со всеми.
     */
    public function view(User $user, Auction $auction): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $user->isModerator() && $auction->moderator_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isModerator();
    }

    /**
     * Редактирование: модератор — только свои лоты и не завершённые.
     */
    public function update(User $user, Auction $auction): bool
    {
        if ($user->isAdmin()) {
            return $auction->status !== 'finished';
        }

        if (! $user->isModerator() || $auction->moderator_id !== $user->id) {
            return false;
        }

        return ! in_array($auction->status, ['active', 'finished', 'cancelled']);
    }

    /**
     * Отмена лота.
     */
    public function cancel(User $user, Auction $auction): bool
    {
        if ($user->isAdmin()) {
            return $auction->status !== 'finished';
        }

        if (! $user->isModerator() || $auction->moderator_id !== $user->id) {
            return false;
        }

        return in_array($auction->status, ['draft', 'pending_moderation', 'scheduled']);
    }

    /**
     * Одобрение и отклонение лотов — только админ.
     */
    public function moderate(User $user, Auction $auction): bool
    {
        return $user->isAdmin() && $auction->status === 'pending_moderation';
    }
}
