<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdManagement;
use App\Filament\Resources\AdTypeResource\Pages;
use App\Models\AdType;
use App\Models\City;
use App\Models\Country;
use App\Models\PriceType;
use App\Models\State;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Closure;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class AdTypeResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = AdType::class;
    protected static ?string $cluster = AdManagement::class;
    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getPermissionPrefixes(): array
    {
        return [
            'create',
            'update',
            'view_any',
            'delete',
        ];
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_ad::type');
    }
    public static function canEdit($record): bool
    {
        return userHasPermission('update_ad::type');
    }
    public static function canDelete($record): bool
    {
        return !$record->is_default && userHasPermission('delete_ad::type');
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_ad::type');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                self::getBasicInformation(),
                self::getMarketplaceConfiguration(),
                self::getAdConfiguration(),
                self::getAdditionalOptions(),
                self::getFilterConfiguration(),
                // self::getImageConfiguration(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('name')->searchable(),
                // TextColumn::make('marketplace')->searchable(),
                // ToggleColumn::make('is_default')
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->hidden(fn($record) => $record->is_default),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getBasicInformation()
    {
        //TODO: Enable when icon field is required
        // $fileUpload = SpatieMediaLibraryFileUpload::make('icon')
        //     ->maxSize(maxUploadFileSize())
        //     ->collection('ad_type_icons')
        //     ->visibility('public')
        //     ->image()
        //     ->required()
        //     ->imageEditor()
        //     ->avatar();

        // $storageType = config('filesystems.default');

        // if ($storageType == 's3') {
        //     $fileUpload->disk($storageType);
        // }

        return Section::make(__('messages.t_ad_type_basic_information'))
            ->schema([
                //TODO: Enable when icon field is required
                // $fileUpload->helperText(__('messages.t_ad_type_icon_helper_text')),

                TextInput::make('name')
                    ->label(__('messages.t_ad_type_name_label'))
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn(callable $get, callable $set) => $set('slug', Str::slug($get('name'))))
                    ->helperText(__('messages.t_ad_type_name_helper_text'))
                    ->columnSpan(2),

                TextInput::make('slug')
                    ->unique(AdType::class, 'slug', ignoreRecord: true)
                    ->afterStateUpdated(fn($state) => Str::slug($state))
                    ->required()
                    ->hintAction(
                        Action::make('generateSlug')
                            ->label(__('messages.t_ad_type_generate_slug'))
                            ->icon('heroicon-m-bolt')
                            ->action(function (Set $set, Get $get) {
                                if ($get('name')) {
                                    $set('slug', Str::slug($get('name')));
                                } else {
                                    $set('slug', Str::slug(Str::random(10)));
                                }
                            })
                    )
                    ->helperText(__('messages.t_ad_type_slug_helper_text'))
                    ->columnSpan(2),
                //TODO: Enable when color field is required
                // ColorPicker::make('color')
                //     ->label(__('messages.t_ad_type_color_label'))
                //     ->nullable()
                //     ->helperText(__('messages.t_ad_type_color_helper_text')),
            ])->columns(3);
    }

    public static function getAdConfiguration()
    {
        return Section::make(__('messages.t_ad_type_ad_configuration'))
            ->columns(3)
            ->schema([
                Fieldset::make(__('messages.t_ad_type_default_fields'))
                    ->schema([
                        Toggle::make('enable_title')
                            ->label(__('messages.t_ad_type_enable_title'))
                            ->default(true)
                            ->disabled()
                            ->helperText(__('messages.t_ad_type_enable_title_helper')),

                        Toggle::make('enable_description')
                            ->label(__('messages.t_ad_type_enable_description'))
                            ->default(true)
                            ->disabled()
                            ->helperText(__('messages.t_ad_type_enable_description_helper')),
                    ])->columns(2),
                self::getClassifiedPriceSection(),
                // self::getPointSystemPriceFields(),
                // Fieldset::make(__('messages.t_ad_type_price_settings'))
                //     ->schema([
                //         Toggle::make('enable_price')
                //             ->label(__('messages.t_ad_type_enable_price'))
                //             ->default(true)
                //             ->visible(fn(callable $get) => $get('marketplace') != CLASSIFIED_MARKETPLACE)
                //             ->helperText(__('messages.t_ad_type_enable_price_helper')),

                //         Toggle::make('enable_offer_price')
                //             ->label(__('messages.t_ad_type_enable_offer_price'))
                //             ->default(true)
                //             // ->visible(fn(callable $get) => $get('marketplace') != CLASSIFIED_MARKETPLACE)
                //             ->helperText(__('messages.t_ad_type_enable_offer_price_helper')),

                //         Toggle::make('disable_price_type')
                //             ->helperText(__('messages.t_ad_type_disable_price_type_helper'))
                //             ->live(onBlur: true)
                //             ->afterStateUpdated(function (Set $set, $state) {
                //                 $set('customize_price_type', false);
                //                 $set('enable_price', $state);
                //             })->visible(fn(callable $get) => !in_array($get('marketplace'), [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE])),

                //         Toggle::make('customize_price_type')
                //             ->helperText(__('messages.t_ad_type_customize_price_type_helper'))
                //             ->afterStateUpdated(function (Set $set) {
                //                 $set('disable_price_type', false);
                //             })->visible(fn(callable $get) => !in_array($get('marketplace'), [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
                //             ->live(onBlur: true),

                //         Select::make('price_types')
                //             ->multiple()
                //             ->helperText(__('messages.t_ad_type_price_types_helper'))
                //             ->required(fn(Get $get) => $get('customize_price_type'))
                //             ->hidden(fn(Get $get) => !$get('customize_price_type'))
                //             ->options(PriceType::all()->pluck('name', 'id'))
                //             ->visible(fn(callable $get) => !in_array($get('marketplace'), [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE])),

                //         TagsInput::make('field_options')
                //             ->label(__('messages.t_ad_type_field_options'))
                //             ->live(onBlur: true)
                //             ->visible(fn($get) => $get('field_type') == 'select')
                //             ->helperText(__('messages.t_ad_type_field_options_helper')),

                //         Toggle::make('has_price_suffix')
                //             ->hidden(fn(Get $get) => $get('disable_price_type'))
                //             ->helperText(__('messages.t_ad_type_has_price_suffix_helper'))
                //             ->live(onBlur: true),

                //         TagsInput::make('suffix_field_options')
                //             ->placeholder(__('messages.t_ad_type_placeholder_suffix_options'))
                //             ->hidden(fn(Get $get) => $get('disable_price_type'))
                //             ->helperText(__('messages.t_ad_type_suffix_field_options_helper'))
                //             ->required(fn(Get $get) => $get('has_price_suffix')),
                // ])->columns(3)->visible(fn(callable $get) => $get('marketplace') != POINT_SYSTEM_MARKETPLACE),

                Fieldset::make(__('messages.t_ad_type_location_settings'))
                    ->schema([
                        Toggle::make('disable_location')
                            ->label(__('messages.t_ad_type_disable_location'))
                            ->helperText(__('messages.t_ad_type_disable_location_helper'))
                            ->reactive()
                            ->afterStateUpdated(fn($state, Set $set) => $state ? $set('default_location', false) : null),

                        Toggle::make('default_location')
                            ->label(__('messages.t_ad_type_default_location'))
                            ->live(onBlur: true)
                            ->default(false)
                            ->helperText(__('messages.t_ad_type_default_location_helper')),

                        Section::make(__('messages.t_ad_type_location_details'))
                            ->collapsible()
                            ->visible(fn(Get $get) => $get('default_location'))
                            ->schema([
                                Select::make('country_id')
                                    ->label(__('messages.t_ad_type_country'))
                                    ->options(Country::orderBy('name')->pluck('name', 'id')->toArray())
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn(callable $set) => $set('state_id', null))
                                    ->required(),

                                Select::make('state_id')
                                    ->label(__('messages.t_ad_type_state'))
                                    ->options(function (Get $get) {
                                        $countryId = $get('country_id');
                                        if (!$countryId) {
                                            return [];
                                        }
                                        return State::where('country_id', $countryId)->orderBy('name')->pluck('name', 'id')->toArray();
                                    })
                                    ->live(onBlur: true)
                                    ->hidden(fn(Get $get): bool => !$get('country_id'))
                                    ->afterStateUpdated(fn(callable $set) => $set('city_id', null))
                                    ->required(),

                                Select::make('city_id')
                                    ->label(__('messages.t_ad_type_city'))
                                    ->options(function (Get $get) {
                                        $stateId = $get('state_id');
                                        if (!$stateId) {
                                            return [];
                                        }
                                        return City::where('state_id', $stateId)->orderBy('name')->pluck('name', 'id')->toArray();
                                    })
                                    ->hidden(fn(Get $get): bool => !$get('state_id'))
                                    ->required(),
                            ])
                            ->columnSpanFull(),
                    ])->columns(3),
            ]);
    }

    public static function getAdditionalOptions()
    {
        return Section::make(__('messages.t_ad_type_additional_options'))
            ->columns(3)
            ->schema([
                Toggle::make('enable_tags')
                    ->label(__('messages.t_ad_type_enable_tags'))
                    ->default(false)
                    ->helperText(__('messages.t_ad_type_enable_tags_helper')),

                // Toggle::make('allowed_comment')
                //     ->label(__('messages.t_ad_type_allow_comments'))
                //     ->default(false)
                //     ->helperText(__('messages.t_ad_type_allow_comments_helper')),

                Toggle::make('enable_for_sale_by')
                    ->label(__('messages.t_ad_type_enable_for_sale_by'))
                    ->default(false)
                    ->helperText(__('messages.t_ad_type_enable_for_sale_by_helper')),

                Toggle::make('disable_condition')
                    ->label(__('messages.t_ad_type_disable_condition'))
                    ->default(false)
                    ->helperText(__('messages.t_ad_type_disable_condition_helper')),
            ]);
    }

    public static function getFilterConfiguration()
    {
        return Section::make(__('messages.t_ad_type_filter_configuration'))
            ->columns(3)
            ->schema([
                Toggle::make('enable_filters')
                    ->label(__('messages.t_ad_type_enable_filters'))
                    ->rules([
                        fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                            $filterOptions = $get('filter_options') ?? [];

                            // Check if any filter is enabled across all filter options
                            $hasEnabledFilters = collect($filterOptions)->contains(function ($filter) {
                                return collect([
                                    $filter['enable_categories'] ?? false,
                                    $filter['enable_sort_by'] ?? false,
                                    $filter['enable_date_range'] ?? false,
                                    $filter['enable_price_range'] ?? false,
                                ])->contains(true);
                            });

                            // If `enable_filters` is ON but no filters are enabled, show an error
                            if ($value && !$hasEnabledFilters) {
                                $fail(__('messages.t_ad_type_at_least_one_filter_required'));
                            }
                        },
                    ]),

                Repeater::make('filter_options')
                    ->label('')
                    ->schema([
                        Toggle::make('enable_categories')
                            ->label(__('messages.t_ad_type_enable_categories'))
                            ->helperText(__('messages.t_ad_type_enable_categories_helper')),

                        Toggle::make('enable_sort_by')
                            ->label(__('messages.t_ad_type_enable_sort_by'))
                            ->helperText(__('messages.t_ad_type_enable_sort_by_helper')),

                        Toggle::make('enable_date_range')
                            ->label(__('messages.t_ad_type_enable_date_range_filter'))
                            ->live(onBlur: true)
                            ->helperText(__('messages.t_ad_type_enable_date_range_filter_helper')),

                        Toggle::make('enable_price_range')
                            ->label(__('messages.t_ad_type_enable_price_range_filter'))
                            ->live(onBlur: true)
                            ->helperText(__('messages.t_ad_type_enable_price_range_filter_helper')),

                        Toggle::make('enable_price_range_toggle')
                            ->label(__('messages.t_ad_type_enable_price_range_toggle_filter'))
                            ->visible(fn(callable $get) => $get('enable_price_range'))
                            ->helperText(__('messages.t_ad_type_enable_price_range_toggle_filter_helper')),
                    ])
                    ->columns(2)
                    ->deletable(false)
                    ->addable(false)
                    ->columnSpan(2)
                    ->reorderable(false),
            ]);
    }

    public static function getMarketplaceConfiguration()
    {
        return Section::make(__('messages.t_ad_type_marketplace_configuration'))
            ->columns(2)
            ->schema([
                Select::make('marketplace')
                    ->options(self::getMarketPlaceOptionData())
                    ->afterStateUpdated(function (callable $get, callable $set) {
                        self::resetMarketPlaceRelatedFields($set);
                    })
                    ->required()
                    ->default('classified')
                    ->live()
                    ->hintAction(
                        Action::make('Adjust the global configuration settings')
                            ->label(__('messages.t_ad_type_adjust_global_configuration_action'))
                            ->icon('heroicon-m-link')
                            ->hidden(function ($state) {
                                $options = self::getMarketPlaceOptionData();
                                return (!$state) || $state == 'classified' || (!array_key_exists($state, $options));
                            })
                            ->url(function ($state) {
                                if (ONLINE_SHOP_MARKETPLACE == $state) {
                                    return route('filament.admin.ecommerce.pages.e-commerce-configuration');
                                } elseif (POINT_SYSTEM_MARKETPLACE == $state) {
                                    return '/admin/point-setting/point-vault-configuration';
                                }
                            })
                            ->openUrlInNewTab()
                    ),

                Repeater::make('marketplace_options')
                    ->label('')
                    ->schema(function (callable $get) {
                        return self::getMarketplaceOptions($get);
                    })
                    ->columns(2)
                    ->deletable(false)
                    ->addable(false)
                    ->columnSpan(2)
                    ->visible(fn(callable $get) => in_array($get('marketplace'), [ONLINE_SHOP_MARKETPLACE]))
                    ->reorderable(false),
            ]);
    }

    public static function getMarketplaceOptions($get)
    {
        if ($get('marketplace') == ONLINE_SHOP_MARKETPLACE) {
            return self::getOnlineShopOptions();
        } elseif ($get('marketplace') == POINT_SYSTEM_MARKETPLACE) {
            return self::getOnlineShopOptions();
        } else {
            return self::getClassifiedOptions();
        }
    }

    public static function getClassifiedOptions()
    {
        return [
            Toggle::make('enable_price_type')
                ->label(__('messages.t_ad_type_enable_price_type'))
                ->helperText(__('messages.t_ad_type_enable_price_type_helper'))
                ->default(false),

            Toggle::make('enable_price_suffix')
                ->label(__('messages.t_ad_type_enable_price_suffix'))
                ->helperText(__('messages.t_ad_type_enable_price_suffix_helper'))
                ->default(false),

            Toggle::make('enable_condition')
                ->label(__('messages.t_ad_type_enable_condition'))
                ->helperText(__('messages.t_ad_type_enable_condition_helper'))
                ->default(false),
        ];
    }

    public static function getOnlineShopOptions()
    {
        return [
            Toggle::make('enable_sku')
                ->label(__('messages.t_ad_type_enable_sku'))
                ->helperText(__('messages.t_ad_type_enable_sku_helper')),

            // Toggle::make('disable_cash_on_delivery')
            //     ->label(__('messages.t_ad_type_disable_cash_on_delivery'))
            //     ->helperText(__('messages.t_ad_type_disable_cash_on_delivery_helper')),
        ];
    }

    public static function getVehicleRentalOptions()
    {
        return [
            Toggle::make('enable_make')
                ->label(__('messages.t_enable_make_label'))
                ->helperText(__('messages.t_enable_make_helper')),

            Toggle::make('enable_model')
                ->label(__('messages.t_enable_model_label'))
                ->helperText(__('messages.t_enable_model_helper')),

            Toggle::make('enable_availability_window')
                ->label(__('messages.t_enable_availability_window_label'))
                ->helperText(__('messages.t_enable_availability_window_helper')),

            Toggle::make('enable_trip_length')
                ->label(__('messages.t_enable_trip_length_label'))
                ->helperText(__('messages.t_enable_trip_length_helper')),

            Toggle::make('enable_transmission')
                ->label(__('messages.t_enable_transmission_label'))
                ->helperText(__('messages.t_enable_transmission_helper')),

            Toggle::make('enable_fuel_type')
                ->label(__('messages.t_enable_fuel_type_label'))
                ->helperText(__('messages.t_enable_fuel_type_helper')),

            Toggle::make('enable_mileage')
                ->label(__('messages.t_enable_mileage_label'))
                ->helperText(__('messages.t_enable_mileage_helper')),

            Toggle::make('enable_start_date')
                ->label(__('messages.t_enable_start_date_label'))
                ->helperText(__('messages.t_enable_start_date_helper')),

            Toggle::make('enable_end_date')
                ->label(__('messages.t_enable_end_date_label'))
                ->helperText(__('messages.t_enable_end_date_helper')),
        ];
    }


    public static function getImageConfiguration()
    {
        return Section::make(__('messages.form.sections.image_configuration'))
            ->columns(4)
            ->schema([
                Toggle::make('allowed_upload_image')
                    ->default(false)
                    ->label(__('messages.form.labels.enable_image'))
                    ->live(onBlur: true)
                    ->helperText(__('messages.form.helpers.enable_image')),

                Repeater::make('upload_image_options')
                    ->label('')
                    ->schema([
                        Fieldset::make('Basic Settings')
                            ->schema([
                                Toggle::make('multiple')
                                    ->label(__('messages.t_multiple_label'))
                                    ->default(false)
                                    ->live(onBlur: true)
                                    ->helperText(__('messages.t_multiple_helper')),

                                TextInput::make('upload_directory')
                                    ->label(__('messages.t_upload_directory_label'))
                                    ->default('uploads')
                                    ->required()
                                    ->helperText(__('messages.t_upload_directory_helper')),

                                Toggle::make('required')
                                    ->label(__('messages.t_required_label'))
                                    ->default(false)
                                    ->helperText(__('messages.t_required_helper')),

                                TextInput::make('min_size')
                                    ->label(__('messages.t_min_size_label'))
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->helperText(__('messages.t_min_size_helper')),

                                TextInput::make('max_size')
                                    ->label(__('messages.t_max_size_label'))
                                    ->numeric()
                                    ->required()
                                    ->default(5120)
                                    ->helperText(__('messages.t_max_size_helper')),

                                TextInput::make('min_files')
                                    ->label(__('messages.t_min_files_label'))
                                    ->numeric()
                                    ->required()
                                    ->default(1)
                                    ->visible(fn(Get $get) => $get('multiple'))
                                    ->helperText(__('messages.t_min_files_helper')),

                                TextInput::make('max_files')
                                    ->label(__('messages.t_max_files_label'))
                                    ->numeric()
                                    ->required()
                                    ->default(10)
                                    ->visible(fn(Get $get) => $get('multiple'))
                                    ->helperText(__('messages.t_max_files_helper')),
                            ])
                            ->columns(3),

                        Fieldset::make('File Type Settings')
                            ->schema([
                                Toggle::make('is_image')
                                    ->label(__('messages.t_is_image_label'))
                                    ->default(false)
                                    ->live(onBlur: true)
                                    ->helperText(__('messages.t_is_image_helper')),

                                Select::make('accepted_file_types')
                                    ->label(__('messages.t_accepted_file_types_label'))
                                    ->multiple()
                                    ->options([
                                        'image/jpeg' => 'JPEG',
                                        'image/png' => 'PNG',
                                        'image/gif' => 'GIF',
                                        'image/bmp' => 'BMP',
                                        'image/svg+xml' => 'SVG',
                                        'application/pdf' => 'PDF',
                                        'application/msword' => 'DOC',
                                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'DOCX',
                                        'text/plain' => 'TXT',
                                    ])
                                    ->hidden(fn(Get $get) => $get('is_image'))
                                    ->helperText(__('messages.t_accepted_file_types_helper')),
                            ])
                            ->columns(2),

                        Fieldset::make('Image Settings')
                            ->schema([
                                Toggle::make('image_editor')
                                    ->label(__('messages.t_image_editor_label'))
                                    ->default(false)
                                    ->live(onBlur: true)
                                    ->visible(fn(Get $get) => $get('is_image'))
                                    ->helperText(__('messages.t_image_editor_helper')),

                                Toggle::make('image_crop_aspect_ratio')
                                    ->label(__('messages.t_image_crop_aspect_ratio_label'))
                                    ->default(false)
                                    ->live(onBlur: true)
                                    ->visible(fn(Get $get) => $get('is_image') && $get('image_editor'))
                                    ->helperText(__('messages.t_image_crop_aspect_ratio_helper')),

                                TextInput::make('image_crop_aspect_ratio_width')
                                    ->label(__('messages.t_image_crop_aspect_ratio_width_label'))
                                    ->numeric()
                                    ->visible(fn(Get $get) => $get('is_image') && $get('image_editor') && $get('image_crop_aspect_ratio'))
                                    ->helperText(__('messages.t_image_crop_aspect_ratio_width_helper')),

                                TextInput::make('image_crop_aspect_ratio_height')
                                    ->label(__('messages.t_image_crop_aspect_ratio_height_label'))
                                    ->numeric()
                                    ->visible(fn(Get $get) => $get('is_image') && $get('image_editor') && $get('image_crop_aspect_ratio'))
                                    ->helperText(__('messages.t_image_crop_aspect_ratio_height_helper')),

                                Select::make('image_resize_mode_option')
                                    ->label(__('messages.t_image_resize_mode_option_label'))
                                    ->options([
                                        'cover' => __('messages.t_resize_cover'),
                                        'contain' => __('messages.t_resize_contain'),
                                        'stretch' => __('messages.t_resize_stretch'),
                                    ])
                                    ->default('cover')
                                    ->visible(fn(Get $get) => $get('is_image'))
                                    ->helperText(__('messages.t_image_resize_mode_option_helper')),

                                Toggle::make('should_maintain_proportion')
                                    ->label(__('messages.t_should_maintain_proportion_label'))
                                    ->default(true)
                                    ->visible(fn(Get $get) => $get('is_image'))
                                    ->helperText(__('messages.t_should_maintain_proportion_helper')),
                            ])
                            ->columns(3)
                            ->visible(fn(Get $get) => $get('is_image')),

                        Fieldset::make('Display Options')
                            ->schema([
                                Toggle::make('downloadable')
                                    ->label(__('messages.t_downloadable_label'))
                                    ->default(true)
                                    ->helperText(__('messages.t_downloadable_helper')),

                                Toggle::make('open_in_new_tab')
                                    ->label(__('messages.t_open_in_new_tab_label'))
                                    ->default(true)
                                    ->helperText(__('messages.t_open_in_new_tab_helper')),

                                Toggle::make('reorderable')
                                    ->label(__('messages.t_reorderable_label'))
                                    ->default(false)
                                    ->visible(fn(Get $get) => $get('multiple'))
                                    ->helperText(__('messages.t_reorderable_helper')),

                                Toggle::make('readable_size')
                                    ->label(__('messages.t_readable_size_label'))
                                    ->default(true)
                                    ->helperText(__('messages.t_readable_size_helper')),

                                Toggle::make('show_dimensions')
                                    ->label(__('messages.t_show_dimensions_label'))
                                    ->default(true)
                                    ->visible(fn(Get $get) => $get('is_image'))
                                    ->helperText(__('messages.t_show_dimensions_helper')),
                            ])
                            ->columns(3),

                        Fieldset::make('Storage Settings')
                            ->schema([
                                TextInput::make('disk_name')
                                    ->label(__('messages.t_disk_name_label'))
                                    ->default('public')
                                    ->helperText(__('messages.t_disk_name_helper')),
                            ])
                            ->columns(2),

                        Fieldset::make('Messages')
                            ->schema([
                                TextInput::make('upload_error_message')
                                    ->label(__('messages.t_upload_error_message_label'))
                                    ->default('Failed to upload file.')
                                    ->helperText(__('messages.t_upload_error_message_helper')),

                                TextInput::make('remove_error_message')
                                    ->label(__('messages.t_remove_error_message_label'))
                                    ->default('Failed to remove file.')
                                    ->helperText(__('messages.t_remove_error_message_helper')),

                                TextInput::make('upload_progress_message')
                                    ->label(__('messages.t_upload_progress_message_label'))
                                    ->default('Uploading file...')
                                    ->helperText(__('messages.t_upload_progress_message_helper')),
                            ])
                            ->columns(3),
                    ])->columns(2)
                    ->deletable(false)
                    ->addable(false)
                    ->columnSpan(3)
                    ->reorderable(false),
            ]);
    }

    public static function getClassifiedPriceSection()
    {

        return Fieldset::make(__('messages.t_ap_price_type_options'))
            ->columns(2)
            ->schema([
                // Toggle::make('enable_price')
                //     ->label(__('messages.t_ad_type_enable_price'))
                //     ->default(true)
                //     ->visible(fn(callable $get) => $get('marketplace') != CLASSIFIED_MARKETPLACE)
                //     ->helperText(__('messages.t_ad_type_enable_price_helper')),

                Toggle::make('disable_price_type')
                    ->helperText(__('messages.t_ad_type_disable_price_type_helper'))
                    ->live(onBlur: true)
                    ->afterStateUpdated(function (Set $set, $state) {
                        $set('customize_price_type', false);
                        $set('enable_price', $state);
                        $set('enable_offer_price', !$state);
                    })->visible(fn(callable $get) => !in_array($get('marketplace'), [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE])),

                Toggle::make('customize_price_type')
                    ->helperText(__('messages.t_ap_toggle_customize_price_type'))
                    ->afterStateUpdated(function (Set $set) {
                        $set('disable_price_type', false);
                    })
                    ->visible(fn(callable $get) => !in_array($get('marketplace'), [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE]))
                    ->live(),

                Select::make('price_types')
                    ->multiple()
                    ->helperText(__('messages.t_ap_select_price_types'))
                    ->required(fn(Get $get) => $get('customize_price_type'))
                    ->hidden(fn(Get $get) => !$get('customize_price_type'))
                    ->options(PriceType::all()->pluck('name', 'id'))
                    ->visible(fn(callable $get) => !in_array($get('marketplace'), [ONLINE_SHOP_MARKETPLACE, POINT_SYSTEM_MARKETPLACE])),


                TagsInput::make('field_options')
                    ->label(__('messages.t_ap_field_options'))
                    ->live()
                    ->visible(fn($get) => $get('field_type') == 'select')
                    ->helperText(__('messages.t_ap_field_input_type')),

                Toggle::make('has_price_suffix')
                    ->hidden(fn(Get $get) => $get('disable_price_type'))
                    ->helperText(__('messages.t_ap_toggle_price_suffix'))
                    ->live(),

                TagsInput::make('suffix_field_options')
                    ->placeholder(__('messages.t_ap_placeholder_suffix_options'))
                    ->hidden(fn(Get $get) => $get('disable_price_type'))
                    ->helperText(__('messages.t_ap_define_suffix_options'))
                    ->required(fn(Get $get) => $get('has_price_suffix')),


                Toggle::make('enable_offer_price')
                    ->label(__('messages.t_ad_type_enable_offer_price'))
                    ->default(true)
                    // ->visible(fn(callable $get) => $get('marketplace') != CLASSIFIED_MARKETPLACE)
                    ->helperText(__('messages.t_ad_type_enable_offer_price_helper')),
            ])
            ->hidden(function (Get $get) {
                return $get('marketplace') == POINT_SYSTEM_MARKETPLACE || $get('enable_online_shopping');
            });
    }

    public static function getPointSystemPriceFields()
    {
        return Fieldset::make(__('messages.t_ap_price_type_options'))
            ->columns(2)
            ->schema([
                Toggle::make('enable_offer_price')
                    ->label(__('messages.t_ad_type_enable_offer_price'))
                    ->default(true)
                    ->helperText(__('messages.t_ad_type_enable_offer_price_helper')),
            ])
            ->visible(fn(callable $get) => $get('marketplace') == POINT_SYSTEM_MARKETPLACE);
    }

    public static function resetMarketPlaceRelatedFields($set)
    {
        $set('enable_price', true);
        $set('disable_price_type', false);
        $set('customize_price_type', false);
        $set('has_price_suffix', false);
        $set('enable_offer_price', false);
        $set('price_types', []);
        $set('field_options', []);
        $set('suffix_field_options', []);
    }

    public static function getMarketPlaceOptionData()
    {
        return [
            'classified' => __('messages.t_ad_type_classified'),
            ...is_ecommerce_active() ? ['online_shop' => __('messages.t_ad_type_online_shop')] : [],
            ...isEnablePointSystem() ? ['point_system' => __('messages.t_ad_type_point_system')] : []
        ];
    }
    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAdTypes::route('/'),
            'create' => Pages\CreateAdType::route('/create'),
            'edit' => Pages\EditAdType::route('/{record}/edit'),
        ];
    }
}
