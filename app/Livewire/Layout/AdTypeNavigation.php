<?php

namespace App\Livewire\Layout;

use App\Models\AdType;
use App\Models\Category;
use App\Settings\ThemeSettings;
use Livewire\Component;

/**
 * Category Navigation Component.
 *
 * Represents the category navigation functionality. Displays main categories along with their subcategories.
 */
class AdTypeNavigation extends Component
{
    // Main categories to display in the navigation.
    public $adTypes;
    public $categories;

    public $selectedAdType;

    public $locationSlug;

    // Contextual information, if any.
    public $context = '';

    /**
     * Mount the component.
     *
     * Fetches the main categories along with their subcategories for the navigation.
     */
    public function mount()
    {
        $this->locationSlug = request()->route('location');
        $this->adTypes = AdType::with('categories')->get()->sortBy('order');
        $defaultAdType = AdType::where('is_default', 1)->first();
        $this->selectedAdType = $defaultAdType ? $defaultAdType : AdType::first();

        $this->loadCategories();
    }

    public function selectAdType($typeId)
    {
        $this->selectedAdType = AdType::find($typeId);
        $this->loadCategories();
    }

    public function loadCategories()
    {
        $this->categories = Category::with('subcategories')->where('ad_type_id', $this->selectedAdType?->id)->whereNull('parent_id')->get()->sortBy('order');
    }

    public function getThemeSettingsProperty()
    {
        return app(ThemeSettings::class);
    }

    /**
     * Render the category navigation view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $view = 'livewire.layout.ad-type-navigation';

        if ($this->themeSettings->selected_theme == 'modern') {
            $view = 'themes.modern.livewire.layout.ad-type-navigation';
        }

        return view($view);
    }
}
