<?php

namespace App\Filament\App\Pages;

use App\Models\AutoResponse; // Adjust to match the actual model location
use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ConfigureAutoResponse extends Page
{

    protected static string $view = 'filament.app.pages.configure-auto-response';
    protected static ?int $navigationSort = 10;

    public ?array $data = [];

    // Mount method to load any existing auto-response for the user
    public function mount()
    {
        // Load the auto-response message for the logged-in user
        $this->data['auto_response'] = AutoResponse::where('user_id', auth()->id())->value('message') ?? '';
    }

    public function getTitle(): string|Htmlable
    {
        return __('messages.t_manage_auto_response');
    }


    public static function getNavigationLabel(): string
    {
        return __('messages.t_configure_auto_response');
    }
  
    public static function getNavigationGroup(): ?string
    {
        return __('messages.t_insights_navigation');
    }

    public function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Textarea::make('auto_response')
                ->label(__('messages.t_auto_response'))
                ->helperText(__('messages.t_auto_response_helper_text'))
                ->placeholder(__('messages.t_auto_response_placeholder'))
                ->default($this->data['auto_response'])
                ->required()
        ])->statePath('data');
    }

    // Save or update the auto-response
    public function submit()
    {
        $data = $this->form->getState();

        AutoResponse::updateOrCreate(
            ['user_id' => auth()->id()],
            ['message' => $data['auto_response']]
        );

        Notification::make()
            ->title(__('messages.t_saved'))
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->automated_messages;
    }
}
