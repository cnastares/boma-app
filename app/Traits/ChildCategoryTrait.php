<?php

namespace App\Traits;

use App\Models\Category;

trait ChildCategoryTrait
{
    /**
     * Load child category based on subcategory
     * @return void
     */
    public function loadChildCategories()
    {
        $subCategory = Category::where('slug', $this->subCategorySlug)->first();

        if ($subCategory) {
            $this->childCategories = $subCategory->subcategories()->get();
        }
    }

    /**
     * Generate child category url based on main category, subcategory, location and child category
     * @param mixed $childCategoryId
     * @return string|\Illuminate\Contracts\Routing\UrlGenerator
     */
    public function getChildCategoryUrl($childCategoryId)
    {
        $childCategory = Category::where('id', $childCategoryId)->with('parent.parent')->first();
        $subCategory = $childCategory->parent;
        $mainCategory = $childCategory->parent->parent;

        return url(generate_category_url(
            $mainCategory->adType,
            $mainCategory,
            $subCategory,
            $this->locationSlug,
            $childCategory
        ));
    }

    /**
     * Generate Subcategory url based on main category, subcategory, location and child category
     * @return string|\Illuminate\Contracts\Routing\UrlGenerator
     */
    public function getSubCategoryUrl()
    {
        $subCategory = Category::where('slug', $this->subCategorySlug)->first();
        $mainCategory = $subCategory->parent;
        
        return url(generate_category_url(
            $mainCategory->adType,
            $mainCategory,
            $subCategory,
            $this->locationSlug,
        ));
    }
}
