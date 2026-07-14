<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Page extends Model
{
    protected $fillable = ['slug', 'title', 'content', 'meta_description', 'is_published'];

    protected $casts = ['is_published' => 'boolean'];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (Page $page) {
            if (empty($page->slug)) {
                $page->slug = Str::slug($page->title);
            }
        });
    }

    public static function sanitizeContent(?string $content): ?string
    {
        if ($content === null) {
            return null;
        }

        $allowedTags = 'p,br,strong,b,em,i,u,a[href|target|rel],img[src|alt|width|height],h1,h2,h3,h4,h5,h6,ul,ol,li,table,thead,tbody,tr,th,td,blockquote,pre,code,hr,div,span';

        $content = strip_tags($content, '<' . str_replace(',', '><', $allowedTags) . '>');

        $content = preg_replace('/\bon\w+\s*=/i', 'data-blocked=', $content);

        return $content;
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
}
