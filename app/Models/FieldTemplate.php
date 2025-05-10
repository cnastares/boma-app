<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FieldTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        "name",
        'enable',
    ];

    public function fieldTemplateMappings()
    {
        return $this->hasMany(FieldTemplateMappings::class, 'field_template_id');
    }

    public function categories()
    {
        return $this->hasMany(Category::class, 'field_template_id');
    }

    public function scopeEnabled($query)
    {
        return $query->where('field_templates.enable', true);
    }

    public function scopeIsDefault($query, $value)
    {
        return $query->where('field_templates.default', $value);
    }

    /**
     * Get field that mapped with categories
     *
     * @param [type] $query
     * @param [type] $mainCategorySlug
     * @param [type] $subCategorySlug
     * @return void
     */
    public function scopeWithMainCategoryAndSubCategory($query, $mainCategorySlug, $subCategorySlug)
    {

        if (!$subCategorySlug) {
            $subCategorySlug = Category::whereHas('parent', function ($query) use ($mainCategorySlug) {
                $query->where('slug', $mainCategorySlug);
            })->pluck('slug')->toArray();
        }
        $categoriesSlug = is_array($subCategorySlug) ? $subCategorySlug : [$subCategorySlug];
        array_push($categoriesSlug, $mainCategorySlug);
        return $query->whereHas('categories', function ($query) use ($categoriesSlug) {
            $query->whereIn('slug', $categoriesSlug);
        });
    }
}
