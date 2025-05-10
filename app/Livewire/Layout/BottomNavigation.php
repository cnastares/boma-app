<?php

namespace App\Livewire\Layout;

use Livewire\Component;

/**
 * BottomNavigation Component.
 *
 * Represents the bottom navigation bar for the application, typically used
 * in mobile or tablet views for easy access to key sections of the application.
 */
class BottomNavigation extends Component
{
    /**
     * Render the bottom navigation view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.layout.bottom-navigation');
    }
}
