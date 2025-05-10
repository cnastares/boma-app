<?php

namespace App\Livewire\Layout;

use App\Models\Category;
use App\Models\FooterSection;
use App\Models\Page;
use Livewire\Component;

/**
 * Footer Component.
 *
 * Represents the footer section of the application. It displays popular categories,
 * essential pages like 'About Us', 'Careers', etc., and other relevant links.
 */
class Footer extends Component
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
     * Render the footer view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.layout.footer');
    }
}
