<?php

namespace App\Models;

use App\Traits\HasAdType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Category extends Model implements Sitemapable, HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes, HasTranslations, HasAdType;

    protected $fillable = [
        'name',
        'parent_id',
        'icon',
        'order',
        'slug',
        'description',
        'field_template_id',
        'disable_location',
        'location_details',
        'default_location',
        'disable_condition',
        'disable_price_type',
        'customize_price_type',
        'price_types',
        'has_price_suffix',
        'suffix_field_options',
        'enable_offer',
        'enable_online_shopping',
        'ad_type_id',
        'enable_age_verify',
        'enable_identity_verify',
        'age_value',
        'enable_manual_approval'
    ];

    public $translatable = [
        'name',
        'description',
    ];

    protected $casts = [
        'location_details' => 'array',
        'price_types' => 'array',
        'suffix_field_options' => 'array'
    ];

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function subcategories()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function toSitemapTag(): Url|string|array
    {
        // Main category URL
        $urls[] = Url::create(url('categories', [$this->slug]));

        // If this category has subcategories, add them to the URLs list
        foreach ($this->subcategories as $subcategory) {
            $urls[] = Url::create(url('categories', [$this->slug, $subcategory->slug]));
        }

        return $urls;
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public static function getPopularCategories()
    {
        return self::withCount('ads')
            ->orderBy('ads_count', 'desc')
            ->take(5)
            ->get();
    }

    public function getIconAttribute(): ?string
    {
        return $this->getFirstMediaUrl('category_icons');
    }

    public function fields()
    {
        return $this->belongsToMany(Field::class, 'category_fields');
    }

    public function fieldTemplate()
    {
        return $this->belongsTo(FieldTemplate::class, 'field_template_id');
    }

    // public function fieldTemplates()
    // {
    //     return $this->morphMany(ModelHasFieldTemplate::class, 'model');
    // }

    public function fieldTemplates()
    {
        return $this->morphToMany(FieldTemplate::class, 'model', 'model_has_field_templates');
    }

    public static function getWithFieldTemplates($id = null): Builder
    {
        $query = self::with('fieldTemplates');

        if ($id) {
            $query->where('id', $id);
        }

        return $query;
    }
    public function isSubcategory(): bool
    {
        return !is_null($this->parent_id) && $this->parent->parent_id == null;
    }

    public function isMainCategory(): bool
    {
        return is_null($this->parent_id);
    }

    public function isChildCategory(): bool
    {
        return $this->parent_id && $this->parent->parent_id;
    }
}
