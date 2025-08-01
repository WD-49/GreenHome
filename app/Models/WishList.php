<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class WishList extends Model
{
    public $timestamps = false; 
    use HasFactory;


    protected $table = 'wishlists'; // 🛠️ CHÍNH XÁC tên bảng trong migration của bạn

    protected $fillable = ['user_id', 'product_id', 'add_at', 'notify_on_sale', 'priority'];
    protected $casts = [
    'notify_on_sale' => 'boolean',
];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
