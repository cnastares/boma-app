<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class AdType extends Model implements HasMedia
{
    use HasFactory, HasUuids, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name',
        'slug',
        'color',
        'enable_title',
        'enable_description',
        'enable_price',
        'enable_offer_price',
        'disable_price_type',
        'customize_price_type',
        'price_types',
        'has_price_suffix',
        'suffix_field_options',
        'disable_location',
        'default_location',
        'country_id',
        'state_id',
        'city_id',
        'enable_tags',
        'allowed_comment',
        'enable_for_sale_by',
        'enable_sell_item_with_option',
        'disable_condition',
        'enable_filters',
        'filter_options',
        'marketplace',
        'marketplace_options',
        'allowed_upload_image',
        'upload_image_options',
        'allow_youtube_video',
        'is_default',
        'location_details',
    ];

    protected $casts = [
        'enable_title' => 'boolean',
        'enable_description' => 'boolean',
        'enable_price' => 'boolean',
        'enable_offer_price' => 'boolean',
        'disable_price_type' => 'boolean',
        'customize_price_type' => 'boolean',
        'price_types' => 'array',
        'has_price_suffix' => 'boolean',
        'suffix_field_options' => 'array',
        'enable_phone_number' => 'boolean',
        'disable_location' => 'boolean',
        'default_location' => 'boolean',
        'enable_tags' => 'boolean',
        'allowed_comment' => 'boolean',
        'enable_for_sale_by' => 'boolean',
        'enable_sell_item_with_option' => 'boolean',
        'disable_condition' => 'boolean',
        'enable_filters' => 'boolean',
        'filter_options' => 'array',
        'marketplace_options' => 'array',
        'allowed_upload_image' => 'boolean',
        'upload_image_options' => 'array',
        'allow_youtube_video' => 'boolean',
        'is_default' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
        'location_details' => 'array',
    ];

    protected $appends = ['filter_settings', 'marketplace_settings'];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($adType) {
            // Ensure only one default ad type exists
            if ($adType?->is_default && $adType?->isDirty('is_default')) {
                static::where('is_default', true)->update(['is_default' => false]);
            }
        });
    }

    public function getIconAttribute(): ?string
    {
        return $this->getFirstMediaUrl('ad_type_icons');
    }

    public function getFilterSettingsAttribute()
    {
        return $this->filter_options[0];
    }

    public function getMarketplaceSettingsAttribute()
    {
        return $this->marketplace_options[0] ?? [];
    }

    public function getMarketplaceOptionsAttribute($value)
    {
        return $value == null ? [[]] : json_decode($value, true);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    public function scopeMarketplace($query, string $type)
    {
        return $query->where('marketplace', $type);
    }

    public function ads()
    {
        return $this->hasMany(Ad::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }
}
