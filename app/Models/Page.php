<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class Page extends Model  implements Sitemapable
{
    use HasFactory;

    protected $fillable = [
        'title',
        'content',
        'seo_title',
        'seo_description',
        'slug',
        'status'
    ];

    public function toSitemapTag(): Url | string | array
    {
        return url('pages', $this->slug);
    }

    public function pages()
    {
        return $this->hasMany(Page::class);
    }

    public function scopeVisible($query)
    {
        return $query->where('status', 'visible');
    }
    public function isVisible()
    {
        return $this->status == 'visible';
    }
}
