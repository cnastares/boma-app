<?php

namespace App\Livewire\Layout;

use App\Models\Category;
use App\Models\FooterSection;
use App\Models\Page;
use Livewire\Component;

/**
 * Sidebar Component.
 *
 * Represents the sidebar functionality for mobile view. Displays popular categories and selected pages.
 */
class Sidebar extends Component
{
    public $footerSections;
    public $popularCategories;

    public function mount()
    {
        $this->footerSections = FooterSection::with(['footerItems' => function($query) {
            $query->orderBy('order');
        }])->orderBy('order')->get();

        $this->popularCategories = Category::getPopularCategories();
    }

    /**
     * Render the sidebar view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.layout.sidebar');
    }
}
