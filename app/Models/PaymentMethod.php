<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PaymentMethod extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'payment_methods';

    protected $fillable = [
        'name',
        'description',
        'status',
    ];

    /**
     * Một phương thức thanh toán có nhiều đơn hàng
     */
}
