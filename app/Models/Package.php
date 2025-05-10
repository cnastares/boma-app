<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;


class Package extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $fillable = [
        'name', 'duration', 'features', 'is_default', 'is_category_count_enabled', 'is_enabled'
    ];

    public function promotions()
    {
        return $this->belongsToMany(Promotion::class, 'package_promotions');
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'package_categories');
    }

    public function packageItems()
    {
        return $this->hasMany(PackageItem::class);
    }

    public function packagePromotions()
    {
        return $this->hasMany(PackagePromotion::class);
    }

    public function packageCategories()
    {
        return $this->hasMany(PackageCategory::class);
    }

}
