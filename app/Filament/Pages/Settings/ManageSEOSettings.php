<?php

namespace App\Filament\Pages\Settings;

use App\Models\Ad;
use App\Models\Category;
use App\Models\Page;
use App\Settings\SEOSettings;
use App\Models\SettingsProperty;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Illuminate\Support\HtmlString;
use Spatie\Sitemap\Sitemap;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class ManageSEOSettings extends SettingsPage
{
    use HasPageShield;

    protected static ?string $slug = 'manage-seo-settings';

    protected static string $settings = SEOSettings::class;

    protected static ?int $navigationSort = 9;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageSEOSettings');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_seo');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_seo');
    }
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(SEOSettings::class);
        $filtered = [];

        foreach ($data as $key => $item) {
            // Check if the property exists in the GeneralSettings class
            if (property_exists($previousData, $key)) {
                // Get the type of the property
                $propertyType = gettype($previousData->$key);

                // If the item is null and the property type is string, set it to an empty string
                if (is_null($item) && $propertyType === 'string') {
                    $filtered[$key] = '';
                    continue;
                }
            }
            // For other cases, just copy the item as is
            $filtered[$key] = $item;
        }
        return $filtered;
    }

    public function generateSitemap()
    {
        try {
            $ads = Ad::all();
            $categories = Category::all();
            $pages = Page::all();

            Sitemap::create()
                ->add($ads)
                ->add($categories)
                ->add($pages)
                ->writeToFile(public_path('sitemap.xml'));

            // Send a success notification
            Notification::make()
                ->title(__('messages.t_common_success'))
                ->success()
                ->send();
        } catch (\Exception $e) {
            // Send a failure notification
            Notification::make()
                ->title(__('messages.t_common_error'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }


    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('meta_title')
                    ->label(__('messages.t_ap_meta_title'))
                    ->placeholder(__('messages.t_ap_meta_title_placeholder'))
                    ->required()
                    ->helperText(__('messages.t_ap_meta_title_helper')),

                TextInput::make('meta_description')
                    ->label(__('messages.t_ap_meta_description'))
                    ->placeholder(__('messages.t_ap_meta_description_placeholder'))
                    ->required()
                    ->columnSpanFull()
                    ->helperText(__('messages.t_ap_meta_description_helper')),

                SpatieMediaLibraryFileUpload::make('ogimage')
                    ->maxSize(maxUploadFileSize())
                    ->label(__('messages.t_ap_og_image'))
                    ->collection('seo')
                    ->visibility('public')
                    ->image()
                    ->model(SettingsProperty::getInstance('seo.ogimage'))
                    ->helperText(__('messages.t_ap_og_image_helper')),

                TextInput::make('twitter_username')
                    ->label(__('messages.t_ap_twitter_username'))
                    ->placeholder(__('messages.t_ap_twitter_username_placeholder'))
                    ->helperText(__('messages.t_ap_twitter_username_helper')),

                TextInput::make('facebook_page_id')
                    ->label(__('messages.t_ap_facebook_page_id'))
                    ->placeholder(__('messages.t_ap_facebook_page_id_placeholder'))
                    ->helperText(__('messages.t_ap_facebook_page_id_helper')),

                TextInput::make('facebook_app_id')
                    ->label(__('messages.t_ap_facebook_app_id'))
                    ->placeholder(__('messages.t_ap_facebook_app_id_placeholder'))
                    ->helperText(__('messages.t_ap_facebook_app_id_helper')),

                Toggle::make('enable_sitemap')
                    ->label(__('messages.t_ap_enable_sitemap'))
                    ->hint(new HtmlString(__(
                        'messages.t_ap_enable_sitemap_hint',
                        [
                            'view_link' => '<a class="cursor-pointer text-blue-600 hover:underline" href="/sitemap.xml" target="_blank">' . __('messages.t_ap_view_sitemap') . '</a>',
                            'generate_link' => '<span class="cursor-pointer text-blue-600 hover:underline" wire:click="generateSitemap">' . __('messages.t_ap_generate_sitemap') . '</span>',
                        ]
                    )))
                    ->helperText(__('messages.t_ap_enable_sitemap_helper'))
                    ->columnSpanfull(),

            ])
            ->columns(2);
    }
}
