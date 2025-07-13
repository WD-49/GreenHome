<?php

namespace App\Models;

use App\Models\WishList;
use App\Models\UserProfile;
use App\Models\Comment;
use App\Models\Order;
use App\Models\Cart;
use App\Models\CartItem;
use App\Notifications\VerifyEmail as VerifyEmailNotification;

use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;
use Illuminate\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @method bool hasVerifiedEmail()
 * @method void notify(\Illuminate\Notifications\Notification $notification)
 * @method \Illuminate\Notifications\DatabaseNotificationCollection notifications()
 */
class User extends Authenticatable implements MustVerifyEmailContract
{

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use SoftDeletes, HasFactory, Notifiable, MustVerifyEmail;

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

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function orders(): HasMany
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

    /**
     * Gửi thông báo xác minh email tuỳ chỉnh.
     */
    public function sendEmailVerificationNotification()
    {

        $this->notify(new VerifyEmailNotification);
    }
}
