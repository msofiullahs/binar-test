<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Appends;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

#[Fillable(['name', 'email', 'password', 'role', 'active'])]
#[Hidden(['password', 'remember_token'])]
#[Appends(['orders_count', 'can_edit'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

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
            'role' => 'string',
            'active' => 'boolean',
            'orders_count' => 'integer',
            'can_edit' => 'boolean',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    protected function ordersCount(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->orders()->count(),
        );
    }

    protected function canEdit(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                $user = Auth::user();

                if ($user) {
                    switch ($user->role) {
                        case 'administrator':
                            return true;
                        case 'manager':
                            return $this->role == 'user' || $this->id == $user->id;
                        default:
                            return $this->id == $user->id;
                    }
                }

                return false;
            },
        );
    }
}
