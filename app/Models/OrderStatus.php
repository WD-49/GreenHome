<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderStatus extends Model
{
    /** @use HasFactory<\Database\Factories\OrderStatusFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = ['name'];

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
