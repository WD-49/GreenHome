<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

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
            CartItem::class, // Model cuối cùng bạn muốn truy cập (CartItem)
            Cart::class,     // Model trung gian (Cart)
            'user_id',       // Khóa ngoại trên bảng trung gian (carts.user_id) liên kết với User
            'cart_id',       // Khóa ngoại trên bảng cuối cùng (cart_items.cart_id) liên kết với Cart
            'id',            // Khóa chính của User (users.id)
            'id'             // Khóa chính của bảng trung gian (carts.id)
        );
    }

    protected static function booted()
    {
        static::deleting(function ($user) {
            if (!$user->forceDeleting) {
                $user->profile()->delete();
            }
        });
    }

    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isClient()
    {
        return $this->role === 'client';
    }
}
