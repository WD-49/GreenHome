<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserProfile extends Model
{
    /** @use HasFactory<\Database\Factories\UserProfileFactory> */
    use SoftDeletes, HasFactory;


    protected $fillable = [
        'user_id',
        'phone',
        'address',
        'gender',
        'birth_date',
        'user_image',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'gender' => 'string',
    ];

    // Quan hệ với users (1-1)
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
