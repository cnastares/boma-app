<?php

namespace App\Filament\Clusters\AdSettings;

use App\Filament\Clusters\AdSettings as ClustersAdSettings;
use App\Models\SettingsProperty;
use App\Settings\AdSettings;
use Filament\Forms;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\SettingsPage;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class AdPostingRules extends SettingsPage
{
    use HasPageShield;


    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $settings = AdSettings::class;

    protected static ?string $cluster = ClustersAdSettings::class;

    protected static ?int $navigationSort = 1;


    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_ad_posting_rules');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_ad_posting_rules');
    }
    public static function canAccess(): bool
    {
        return userHasPermission('page_AdPostingRules');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $previousData = app(AdSettings::class);
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
    public function form(Form $form): Form
    {
        return $form
            ->schema([

                TextInput::make('ad_duration')
                    ->numeric()
                    ->minValue(1)
                    ->label(__('messages.t_ap_default_duration_for_ads'))
                    ->placeholder(__('messages.t_ap_enter_duration_in_days'))
                    ->required(),

                TextInput::make('image_limit')
                    ->numeric()
                    ->minValue(1)
                    ->maxValue(10)
                    ->label(__('messages.t_ap_maximum_images_allowed_per_ad'))
                    ->placeholder(__('messages.t_ap_enter_image_limit'))
                    ->required(),

                Checkbox::make('allow_image_alt_tags')
                    ->label(__('messages.t_ap_allow_alt_tags_for_images'))
                    ->helperText(__('messages.t_ap_enable_alt_tags_for_images')),

                Checkbox::make('user_verification_required')
                    ->label(__('messages.t_ap_user_verification_required_to_post_ads'))
                    ->helperText(__('messages.t_ap_enable_verification_for_posting_ads')),

                Checkbox::make('ad_moderation')
                    ->label(__('messages.t_ap_require_admin_approval_for_new_ads')),

                Checkbox::make('admin_approval_required')
                    ->label(__('messages.t_ap_require_admin_approval_for_sensitive_edits'))
                    ->helperText(__('messages.t_ap_admin_approval_for_edits')),
                Checkbox::make('can_post_without_image')
                    ->label(__('messages.t_ap_allow_users_to_post_ads_without_images'))
                    ->helperText(__('messages.t_ap_users_can_post_ads_without_uploading_images')),
                SpatieMediaLibraryFileUpload::make('placeholder_image')
                    ->label(__('messages.t_ap_placeholder_image'))
                    ->maxSize(maxUploadFileSize())
                    ->collection('placeholder_images')
                    ->model(SettingsProperty::getInstance('ad.placeholder_image'))
                    ->visibility('public')
                    ->image()
                    ->default(asset('images/placeholder.jpg'))
                    ->imageEditor(),
            ]);
    }
}
