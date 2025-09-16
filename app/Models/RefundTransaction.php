<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RefundTransaction extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'refund_transactions';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'refund_reason',
        'refund_image',
        'refund_status',
        'refund_account_name',
        'refund_account_bank',
        'refund_account_number',
        'refund_account_qr',
        'refund_cost',
        'refund_proof_image',
        'refund_date',
        'admin_note',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'refund_cost' => 'decimal:2',
        'refund_date' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the order associated with the refund transaction.
     */
    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }
}
