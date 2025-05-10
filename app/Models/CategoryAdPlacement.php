<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class CategoryAdPlacement extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $fillable = [
        'value',
        'category_id',
        'subcategory_id',
        'is_active',
        'priority',
        'ad_type',
        'images'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'images'=>'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'subcategory_id');
    }

    public function scopeActive($query){
        return $query->where('is_active', true);
    }
    /**
     * Get ad placement based on category and subcategory by priority
     * @param mixed $query
     * @param mixed $categorySlug
     * @param mixed $subcategorySlug
     * @return $query
     */
    public function scopeGetRelevantAdPlacements($query, $categorySlug, $subcategorySlug = null){

        return $query->active()->where(function($query) use ($categorySlug, $subcategorySlug){
            // filter if priority is subcategory and subcategory is not null
            if($subcategorySlug){
                return $query->where('priority','sub')
                ->whereHas('subcategory', function ($query) use ($categorySlug,$subcategorySlug) {
                    $query->where('slug', $subcategorySlug);
                });
            }

            // filter if priority is main category and main category is not null
            if($categorySlug){
                return $query->where('priority','main')->whereHas('category', function ($query) use ($categorySlug) {
                    $query->where('slug', $categorySlug);
                });
            }
        });
    }
}
