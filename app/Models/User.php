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
    use HasFactory, Notifiable;

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

    // имя для публичных страниц, типа "Иван К."
    public function shortName(): string
    {
        $parts = preg_split('/\s+/', trim($this->name));

        $result = $this->name;

        if (count($parts) >= 2) {
            $result = $parts[0] . ' ' . mb_strtoupper(mb_substr($parts[1], 0, 1)) . '.';
        }

        return $result;
    }

    // лоты которыми юзер рулит как модератор
    public function managedAuctions()
    {
        return $this->hasMany(Auction::class, 'moderator_id');
    }

    // лоты которые он сам создал
    public function createdAuctions()
    {
        return $this->hasMany(Auction::class, 'created_by');
    }

    // ставки
    public function bids()
    {
        return $this->hasMany(Bid::class);
    }
}
