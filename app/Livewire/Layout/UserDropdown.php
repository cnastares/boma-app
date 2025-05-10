<?php

namespace App\Livewire\Layout;

use Livewire\Component;

/**
 * UserDropdown Component.
 *
 * Represents a dropdown menu associated with the currently logged-in user.
 * This dropdown typically provides options like viewing the user profile,
 * my ads, my favourites and logging out.
 */
class UserDropdown extends Component
{
    /**
     * Render the user dropdown view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.layout.user-dropdown');
    }
}
