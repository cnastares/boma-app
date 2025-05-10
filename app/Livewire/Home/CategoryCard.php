<?php

namespace App\Livewire\Home;

use App\Enums\AdStatus;
use App\Models\Category;
use App\Settings\ThemeSettings;
use Livewire\Component;

class CategoryCard extends Component
{
    public $category;
    public $sellersCount = 0;
    public $listingsCount = 0;
    public $salesCount = 0;

    /**
     * Mount the component and initialize category details.
     *
     * @param Category $category The category to display.
     */
    public function mount(Category $category)
    {
        $this->category = $category;

        // Aggregate the stats for the given category.
        $this->aggregateCategoryStats();
    }

    public function getThemeSettingsProperty()
    {
        return app(ThemeSettings::class);
    }
    /**
     * Aggregate statistics for the given category.
     */
    protected function aggregateCategoryStats()
    {
        $uniqueSellers = collect();
        $totalListings = 0;
        $totalSales = 0;

        // Loop through each subcategory to aggregate the stats.
        foreach($this->category->subcategories as $subcategory)
        {
            // Sellers: Fetch unique user IDs of ads for this subcategory.
            $uniqueSellersForSub = $subcategory->ads->groupBy('user_id')->keys();
            $uniqueSellers = $uniqueSellers->concat($uniqueSellersForSub)->unique();

            // Listings are the number of ads.
            $totalListings += $subcategory->ads->where('status','!=','')->where('status',  AdStatus::ACTIVE->value)->count();

            // Sales are the ads with status 'sold'.
            $totalSales += $subcategory->ads->where('status','!=','')->where('status', AdStatus::SOLD->value)->count();
        }

        // Update component properties with aggregated data.
        $this->sellersCount = $uniqueSellers->count();
        $this->listingsCount = $totalListings;
        $this->salesCount = $totalSales;
    }

    /**
     * Render a placeholder for the category card.
     *
     * @param array $params Additional parameters for the placeholder.
     * @return \Illuminate\View\View
     */
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.category-skeleton', $params);
    }

    /**
     * Render the category card view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $view = 'livewire.home.category-card';

        if($this->themeSettings->selected_theme == 'modern'){
            $view = 'themes.modern.livewire.home.category-card';
        }
        return view($view);
    }
}
