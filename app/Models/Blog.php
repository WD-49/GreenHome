<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'summary',
        'content',
        'thumbnail',
        'status',
        'author_id',
        'blog_category_id',
    ];

    /**
     * Danh mục của bài viết
     */
    public function category()
    {
        return $this->belongsTo(BlogCategory::class, 'blog_category_id');
    }

    /**
     * Tác giả bài viết
     */
    public function author()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
