<?php

namespace App\Livewire\Layout;


use App\Settings\ThemeSettings;
use Livewire\Component;

/**
 * CategoryNavigationItem Component.
 * Handles the display of a single category item in a navigation bar.
 */
class CategoryNavigationItem extends Component
{
    // Represents the Category model instance.
    public $category;
    public $locationSlug;

    public function getThemeSettingsProperty()
    {
        return app(ThemeSettings::class);
    }
    /**
     * Renders a placeholder for the category navigation item during lazy loading.
     *
     * @param array $params Additional parameters to pass to the view.
     * @return \Illuminate\Contracts\View\View The placeholder view.
     */
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.category-nav-skeleton', $params);
    }

    /**
     * Renders the category navigation item view.
     * Checks if a category is set before rendering it.
     *
     * @return \Illuminate\Contracts\View\View The category navigation item view.
     */
    public function render()
    {
        if (!$this->category) {
            return $this->placeholder();
        }
        $view = 'livewire.layout.category-navigation-item';

        if($this->themeSettings->selected_theme == 'modern'){
            $view = 'themes.modern.livewire.layout.category-navigation-item';
        }
        return view($view);
    }
}
