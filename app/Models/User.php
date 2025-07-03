<?php

namespace App\Models;

use App\Models\WishList;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use SoftDeletes, HasFactory, Notifiable;

    protected $dates = ['deleted_at'];

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    public function profile()
    {
        return $this->hasOne(UserProfile::class);
    }

    public function comments()
    {
        return $this->hasMany(Comment::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function cartItems()
    {
        return $this->hasManyThrough(
            CartItem::class,
            Cart::class,
            'user_id',
            'cart_id',
            'id',
            'id'
        );
    }

    /**
     * Get the wishlists of the user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(WishList::class, 'user_id');
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            if (!$user->forceDeleting) {
                $user->profile()->delete();
            }
        });
    }

    public function sendPasswordResetNotification($token)
    {
        // Ghi đè để không gửi email mặc định của Laravel
    }
}
