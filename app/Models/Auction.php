<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Auction extends Model
{
    // человекочитаемые статусы, для выпадашек и таблиц
    public const STATUSES = [
        'draft' => 'Черновик',
        'pending_moderation' => 'На модерации',
        'scheduled' => 'Запланирован',
        'active' => 'Активен',
        'finished' => 'Завершён',
        'cancelled' => 'Отменён',
        'rejected' => 'Отклонён',
    ];

    protected $fillable = [
        'title',
        'description',
        'category_id',
        'starting_price',
        'minimum_bid_step',
        'current_price',
        'start_at',
        'end_at',
        'status',
        'moderator_id',
        'created_by',
        'winner_id',
        'winning_price',
    ];

    protected function casts(): array
    {
        return [
            'starting_price' => 'decimal:2',
            'minimum_bid_step' => 'decimal:2',
            'current_price' => 'decimal:2',
            'winning_price' => 'decimal:2',
            'start_at' => 'datetime',
            'end_at' => 'datetime',
        ];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function moderator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'moderator_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function winner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'winner_id');
    }

    public function bids(): HasMany
    {
        return $this->hasMany(Bid::class);
    }

    // лоты которые идут прямо сейчас
    public function scopeLive(Builder $query): Builder
    {
        return $query
            ->whereNotIn('status', ['draft', 'pending_moderation', 'cancelled', 'rejected', 'finished'])
            ->where('start_at', '<=', now())
            ->where('end_at', '>', now());
    }

    // ещё не открылись
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query
            ->whereNotIn('status', ['draft', 'pending_moderation', 'cancelled', 'rejected', 'finished'])
            ->where('start_at', '>', now());
    }

    // уже кончились
    public function scopeFinished(Builder $query): Builder
    {
        return $query
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'rejected')
            ->where('end_at', '<=', now());
    }

    // можно ли сейчас ставить, время смотрим по серверу
    public function isOpenForBids(): bool
    {
        $result = false;

        if (! in_array($this->status, ['draft', 'pending_moderation', 'cancelled', 'rejected', 'finished'])) {
            $now = now();

            if ($now->gte($this->start_at) && $now->lt($this->end_at)) {
                $result = true;
            }
        }

        return $result;
    }

    // минимальная следующая ставка
    public function nextMinBid(): float
    {
        if ($this->current_price === null) {
            return (float) $this->starting_price;
        }

        return (float) $this->current_price + (float) $this->minimum_bid_step;
    }

    // цена для показа, пока ставок нет — стартовая
    public function currentPrice(): float
    {
        if ($this->current_price !== null) {
            return (float) $this->current_price;
        }

        return (float) $this->starting_price;
    }

    // статус с учётом времени, это то что видит юзер на странице
    public function displayStatus(): string
    {
        $status = $this->status;

        if (in_array($this->status, ['scheduled', 'active'])) {
            $now = now();

            if ($now->lt($this->start_at)) {
                $status = 'scheduled';
            } elseif ($now->gte($this->end_at)) {
                $status = 'finished';
            } else {
                $status = 'active';
            }
        }

        return $status;
    }
}
