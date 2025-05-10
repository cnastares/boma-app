<?php

namespace App\Livewire\Partials;

use App\Models\Language;
use App\Settings\GeneralSettings;
use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class LanguageSwitcher extends Component
{
    public $default_title;
    public $default_lang_code;
    public $default_country;
    public $icon;

    public function mount()
    {
        $selected_language = session()->has('locale') ? session()->get('locale') : app(GeneralSettings::class)->default_language;
        $language = Language::where('lang_code', $selected_language)->first();

        $this->default_title = $language ? $language->title : "English";
        $this->default_lang_code = $language ? $language->lang_code : "en";
        $this->default_country = $language ? $language->country : "us";
        $this->icon = $language && $language->icon ? Storage::url($language->icon) : null;
    }

    public function updateLocale($lang_code)
    {
        $language = Language::where('lang_code', $lang_code)->where('is_visible', true)->first();

        if (!$language) {
                Notification::make()
                ->success()
                ->title('Selected language not found!')
                ->send();
            session()->flash('error', 'Selected language not found!');
            return;
        }

        session(['locale' => $language->lang_code]);
        cookie()->queue(cookie()->forever('locale', $language->lang_code));

        $this->js('location.reload();');
    }

    public function render()
    {
        return view('livewire.partials.language-switcher');
    }

}
