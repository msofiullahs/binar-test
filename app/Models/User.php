<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Laravel\Sanctum\HasApiTokens;

#[Fillable(['name', 'email', 'password', 'role', 'active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'role' => 'string',
        'active' => 'boolean',
        'orders_count' => 'integer',
    ];

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isEditableBy(?self $actor): bool
    {
        if (! $actor) {
            return false;
        }

        return match ($actor->role) {
            'administrator' => true,
            'manager' => $this->role === 'user' || $this->id === $actor->id,
            default => $this->id === $actor->id,
        };
    }
}
