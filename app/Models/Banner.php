<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = ['name', 'description', 'status', 'img', 'link', 'priority'];
    public function category()
{
    return $this->belongsTo(Category::class);
}

}


