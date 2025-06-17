<?php

namespace App\Models;

use App\Models\Blog as ModelsBlog;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class BlogCategory extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'slug',
    ];

    /**
     * Quan hệ: 1 blog_category có nhiều blog (giả sử có bảng blogs).
     */
    public function blogs()
    {
        return $this->hasMany(Blog::class, 'blog_category_id');
    }




    /**
     * Hook xử lý:
     * - Tự sinh slug nếu chưa có
     * - Xóa mềm các blog khi xóa mềm danh mục blog
     * - Khôi phục các blog khi khôi phục danh mục blog
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($blogCategory) {
            if (empty($blogCategory->slug)) {
                $blogCategory->slug = Str::slug($blogCategory->name);
            }
        });

        static::deleting(function ($blogCategory) {
            if (!$blogCategory->isForceDeleting()) {
                $blogCategory->blogs()->each(function ($blog) {
                    $blog->delete();
                });
            }
        });

        static::restoring(function ($blogCategory) {
            $blogCategory->blogs()->onlyTrashed()->restore();
        });
    }
}
