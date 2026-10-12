<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'phone', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    public function isClient(): bool
    {
        return $this->role === 'client';
    }

    /**
     * Имя для публичных страниц: "Иван К." вместо полного имени.
     */
    public function shortName(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));

        if (count($parts) < 2) {
            return $this->name;
        }

        return $parts[0] . ' ' . mb_strtoupper(mb_substr($parts[1], 0, 1)) . '.';
    }

    /**
     * Лоты, которыми пользователь управляет как модератор.
     */
    public function managedAuctions()
    {
        return $this->hasMany(Auction::class, 'moderator_id');
    }

    /**
     * Лоты, которые пользователь создал.
     */
    public function createdAuctions()
    {
        return $this->hasMany(Auction::class, 'created_by');
    }

    /**
     * Ставки пользователя.
     */
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
}
