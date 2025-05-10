<?php

namespace App\Filament\App\Pages;

use App\Models\Seo;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Contracts\Support\Htmlable;

class ManageSeo extends Page
{

    protected static string $view = 'filament.app.pages.manage-seo';
    protected static ?int $navigationSort = 11;


    public ?array $data = [];

    public function mount()
    {
        $this->form->fill($this->getSeoRecord()?->toArray() ?? []);
    }

    public function getTitle(): string|Htmlable
    {
        return __('messages.t_manage_user_store_page_seo');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_manage_seo');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('messages.t_insights_navigation');
    }
  
    public function form(\Filament\Forms\Form $form): \Filament\Forms\Form
    {
        return $form->schema([
            TextInput::make('meta_title')
                ->label(__('messages.t_meta_title')) // Translation for Meta Title
                ->placeholder(__('messages.t_enter_meta_title')) // Translation for placeholder
                ->required()
                ->helperText(__('messages.t_meta_title_helper_text')), // Translation for helper text

            TextInput::make('meta_description')
                ->label(__('messages.t_meta_description')) // Translation for Meta Description
                ->placeholder(__('messages.t_enter_meta_description')) // Translation for placeholder
                ->required()
                ->columnSpanFull()
                ->helperText(__('messages.t_meta_description_helper_text')), // Translation for helper text
            TagsInput::make('meta_keywords')
                ->visible(function () {
                    if (getSubscriptionSetting('status') && getActiveSubscriptionPlan()) {
                        return getActiveSubscriptionPlan()->seo_tools_level == 'advanced';
                    }
                    return true;
                })
                ->label(__('messages.t_meta_keywords'))
                ->helperText(__('messages.t_meta_keywords_helper_text')),

            SpatieMediaLibraryFileUpload::make('og_image')
                ->visible(function () {
                    if (getSubscriptionSetting('status') && getActiveSubscriptionPlan()) {
                        return getActiveSubscriptionPlan()->seo_tools_level == 'advanced';
                    }
                    return true;
                })
                ->maxSize(maxUploadFileSize())
                ->label(__('messages.t_og_image')) // Translation for OG Image label
                ->collection('seo')
                ->visibility('public')
                ->image()
                ->helperText(__('messages.t_og_image_helper_text')), // Translation for helper text
        ])
            ->model($this->getSeoRecord() ? $this->getSeoRecord() : Seo::class)
            ->statePath('data');
    }

    public function submit()
    {
        $data = $this->form->getState();
        auth()->user()->seo()->updateOrCreate(
            [
                'seoable_id' => auth()->id()
            ],
            [
                'meta_title' => $data['meta_title'],
                'meta_description' => $data['meta_description'],
                'meta_keywords' => $data['meta_keywords'],
            ]
        );
        Notification::make()
            ->title(__('messages.t_saved'))
            ->success()
            ->send();
    }

    public static function canAccess(): bool
    {
        return getSubscriptionSetting('status') && getActiveSubscriptionPlan() && in_array(getActiveSubscriptionPlan()->seo_tools_level, ['basic', 'advanced']);
    }
    public function getSeoRecord()
    {
        return auth()->user()->seo()->first();
    }
}
