<?php

namespace App\Livewire\Layout;

use App\Settings\HomeSettings;
use App\Settings\ThemeSettings;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Filament\Notifications\Livewire\DatabaseNotifications;

class Header extends Component
{
    public $isSearch = false;
    public $context = '';
    public $isMobileHidden;
    public $sidbarOpen = false;

    #[Reactive]
    public $locationSlug;

    public function getHomeSettingsProperty()
    {
        return app(HomeSettings::class);
    }
    public function getThemeSettingsProperty()
    {
        return app(ThemeSettings::class);
    }
    public function showNotifications(){
        DatabaseNotifications::trigger('filament.notifications.database-notifications-trigger');
    }
    /**
     * Returns the header placeholder view.
     *
     * @return \Illuminate\View\View
     */
    public function placeholder()
    {
        return view('livewire.placeholders.header-skeleton');
    }

    /**
     * Render the header view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        $view = 'livewire.layout.header';

        if($this->themeSettings->selected_theme == 'modern'){
            $view = 'themes.modern.livewire.layout.header';
        }
        return view($view);
    }
}
