<?php

namespace App\Livewire\Layout;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Field;
use App\Settings\ThemeSettings;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Component;

/**
 * SearchBar Component.
 *
 * Represents the search bar functionality allowing users to search for categories and ads.
 */
class SearchBar extends Component
{
    // The user's search query.
    public $search = '';

    // Categories that match the search query.
    public Collection  $categories;

    // Ads that match the search query.
    public Collection $ads;

    public $locationSlug;

    /**
     * Called when the search property is updated.
     *
     * Fetches the top matching categories and ads based on the search query.
     */
    public function updatedSearch()
    {
        $this->categories = Category::where('name', 'like', '%' . $this->search . '%')->take(5)->get();
        // Fetch ads based on title or tags
        $this->ads = Ad::where('status', 'active')
            ->where(function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('tags', 'like', '%' . $this->search . '%')
                    ->orWhereHas('fieldValues', function ($query) {
                        $query->whereHas('field', function ($query) {
                            $query->searchable();
                        })
                            ->where('value', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('location_name', 'like', '%' . $this->search . '%');
            })
            ->take(5)
            ->get();
    }

    /**
     * Perform the search action.
     *
     * Trims the search query and redirects to the search results page.
     */
    public function performSearch()
    {
        $this->search = trim($this->search);
        if(empty($this->search)) {
            return;
        }

        $searchUrl = url('search') . '?query[sortBy]=date&query[search]=' . urlencode($this->search);
        return redirect()->to($searchUrl);
    }

    public function getThemeSettingsProperty()
    {
        return app(ThemeSettings::class);
    }

    /**
     * Returns the search placeholder view.
     *
     * @param array $params Optional parameters to pass to the view.
     * @return \Illuminate\View\View
     */
    public function placeholder(array $params = [])
    {
        return view('livewire.placeholders.search-skeleton', $params);
    }


    /**
     * Render the search bar view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $view = 'livewire.layout.search-bar';

        if($this->themeSettings->selected_theme == 'modern'){
            $view = 'themes.modern.livewire.layout.search-bar';
        }
        return view($view);
    }
}
