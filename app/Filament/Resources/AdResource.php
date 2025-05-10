<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\AdManagement;
use App\Filament\Resources\AdResource\Pages;
use App\Filament\Resources\AdResource\RelationManagers;
use App\Forms\Components\ImageProperties;
use App\Foundation\AdBase\Traits\HasAdForm;
use App\Foundation\AdBase\Traits\HasAdminContactFields;
use App\Models\Ad;
use App\Models\AdCondition;
use App\Models\AdType;
use App\Models\Category;
use App\Models\CategoryField;
use App\Models\City;
use App\Models\Country;
use App\Models\PriceType;
use App\Models\State;
use App\Models\User;
use App\Settings\AdSettings;
use App\Settings\GeneralSettings;
use App\Settings\PhoneSettings;
use Closure;
use Filament\Tables\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Illuminate\Support\HtmlString;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Forms\Components\{Checkbox, DatePicker, DateTimePicker, Fieldset, Hidden, Radio, Section, TimePicker, Textarea};
use FilamentTiptapEditor\TiptapEditor;
use FilamentTiptapEditor\Enums\TiptapOutput;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class AdResource extends Resource implements HasShieldPermissions
{
    use HasAdForm, HasAdminContactFields;
    protected static ?string $model = Ad::class;

    protected static ?string $cluster = AdManagement::class;

    protected static ?string $navigationIcon = 'heroicon-o-list-bullet';

    public static function getPermissionPrefixes(): array
    {
        return [
            'create',
            'update',
            'view_any',
            'delete_any',
            'delete',
        ];
    }

    public static function canCreate(): bool
    {
        return userHasPermission('create_ad');
    }

    public static function canEdit($record): bool
    {
        return userHasPermission('update_ad');
    }

    public static function canDelete($record): bool
    {
        return userHasPermission('delete_ad');
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_ad');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_ad');
    }

    public static function getModelLabel(): string
    {
        return __('messages.t_ap_ad');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_ad_all');
    }

    protected static function getFieldTemplateMappings($categoryId)
    {
        $templateFieldRecord = Category::whereId($categoryId)->with('fieldTemplate.fieldTemplateMappings.field')->first();
        $templateFields = $templateFieldRecord?->fieldTemplate?->fieldTemplateMappings?->sortBy('order') ?? collect([]);
        return $templateFields;
    }

    protected static function getFieldsForAd($categoryId, $mainCategoryId)
    {
        if (!$categoryId && !$mainCategoryId)
            return collect([]);

        $subCategoryFields = collect([]);
        $mainCategoryFields = collect([]);
        $categoryFields = collect([]);

        if ($categoryId) {
            //Sub category Fields
            $categoryFields = CategoryField::where('category_id', $categoryId)
                ->with('field')
                ->get();
            $subCategoryFields = static::getFieldTemplateMappings($categoryId);
        }
        if ($mainCategoryId) {
            //get main Category
            $mainCategory = Category::whereId($mainCategoryId)->first();
            //Sub category Fields
            $mainCategoryFields = $mainCategory ? static::getFieldTemplateMappings($mainCategory->id) : collect([]);
        }
        //return subcategory if not exist then return main category if not exits the normal dynamic fields
        return count($subCategoryFields) ? $subCategoryFields : (count($mainCategoryFields) ? $mainCategoryFields : $categoryFields);
    }

    /**
     * Map fields to form components.
     */
    protected static function mapFieldsToComponents($fieldData)
    {
        $components = [];
        $fieldData = is_null($fieldData) ? collect([]) : $fieldData;
        $fieldGroup = collect([]);
        $finalField = $fieldData->map(function ($field) use (&$components, &$fieldGroup) {
            if ($field && $field->field) {
                // Get the name of the fieldGroup
                $groupName = $field?->field?->fieldGroup?->name ?? '';
                // Push the field to the corresponding group in $fieldGroup
                if (!$fieldGroup->has($groupName)) {
                    $fieldGroup[$groupName] = collect([]);
                }
                $fieldGroup[$groupName]->push($field->field);
            }
        });
        foreach ($fieldGroup as $groupName => $fields) {
            $sectionComponents = [];
            foreach ($fields->sortBy('order') as $field) {
                // Check if the field relationship is not null
                if (!$field) {
                    // Skip this iteration if the field is null
                    continue;
                }
                $dynamicFieldId = 'dynamic_' . $field->id;
                $fieldType = $field->type->value;
                switch ($fieldType) {
                    case 'text':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = TextInput::make($dynamicFieldId)->label($field->name)->label($field->name)->required($field->required)->live(onBlur: true)->helperText($field->helpertext);
                        break;
                    case 'select':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = Select::make($dynamicFieldId)->label($field->name)->options($field->options)->required($field->required)->live(onBlur: true)->helperText($field->helpertext);
                        break;
                    case 'checkbox':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = Checkbox::make($dynamicFieldId)->label($field->name)->required($field->required)->live(onBlur: true)->helperText($field->helpertext);
                        break;

                    case 'radio':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = Radio::make($dynamicFieldId)->label($field->name)->options($field->options)->required($field->required)->live(onBlur: true)->helperText($field->helpertext);
                        break;
                    case 'datetime':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = DateTimePicker::make($dynamicFieldId)->label($field->name)->required($field->required)->live(onBlur: true)->helperText($field->helpertext);
                        break;
                    case 'date':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = DatePicker::make($dynamicFieldId)->label($field->name)->required($field->required)->live(onBlur: true)->helperText($field->helpertext);
                        break;
                    case 'time':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = TimePicker::make($dynamicFieldId)->label($field->name)->required($field->required)->live(onBlur: true)->helperText($field->helpertext);
                        break;
                    case 'textarea':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = Textarea::make($dynamicFieldId)->label($field->name)->required($field->required)->live(onBlur: true)->helperText($field->helpertext);
                        break;
                    case 'tagsinput':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = TagsInput::make($dynamicFieldId)->label($field->name)->required($field->required)->live(onBlur: true)->helperText($field->helpertext);
                        break;
                    case 'number':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = TextInput::make($dynamicFieldId)->numeric()->label($field->name)->required($field->required)->live(onBlur: true)->helperText($field->helpertext);
                        break;
                }
            }
            if (count($sectionComponents))
                $components[] = Section::make($groupName)->schema($sectionComponents)->collapsible()->collapsible();
        }
        return $components;
    }

    private static function validateAdTypePresence($field, $get)
    {
        if ($get && $get('ad_type_id')) {
            $adType = AdType::find($get('ad_type_id'));
            return $adType?->{$field};
        }

        return false;
    }

    public static function hasEnableOnlineShopping($get = null)
    {
        return (self::validateAdTypePresence('marketplace', $get) == ONLINE_SHOP_MARKETPLACE) && is_ecommerce_active();
    }
    public static function form(Form $form): Form
    {
        $adSettings = app(AdSettings::class);

        // Choose fields based on settings
        $fields = self::getClassifiedFormFields();

            $form
            ->schema([
                ...self::adTypeSelect(),
                TextInput::make('title')
                    ->label(__('messages.t_ap_title'))
                    ->live(onBlur: true)
                    ->placeholder(__('messages.t_ap_what_are_you_selling'))
                    ->minLength(10)
                    ->required(),

                ...self::getCategoriesFields(),
                ...$fields,

                SpatieMediaLibraryFileUpload::make('ads')
                    ->maxSize(maxUploadFileSize())
                    ->label(__('messages.t_ap_upload_photos'))
                    ->multiple()
                    ->collection('ads')
                    ->required(function (Get $get) use ($adSettings) {
                        if ($adSettings->can_post_without_image) {
                            return false;
                        }
                        return true;
                    })
                    ->maxFiles($adSettings->image_limit)
                    ->rules([
                        function () {
                            return function (string $attribute, $value, Closure $fail) {
                                $originalName = $value->getClientOriginalName();
                                $maxLength = 191;
                                if (!mb_detect_encoding($originalName)) {
                                    $fail("The file name is too long. Maximum length allowed is {$maxLength} characters.");
                                    Notification::make()
                                        ->title("The file name is too long. Maximum length allowed is {$maxLength} characters.")
                                        ->danger()
                                        ->send();
                                }
                            };
                        },
                    ])
                    ->openable()
                    ->imageEditor()
                    ->imageResizeMode('cover')
                    ->reorderable()
                    ->helperText(__('messages.t_ap_add_photos_to_ad', ['image_limit' => $adSettings->image_limit]))
                    ->appendFiles()
                    ->afterStateUpdated(function (Set $set, Get $get) use ($adSettings) {
                        if ($adSettings->allow_image_alt_tags) {
                            $imageProperties = $get('image_properties');
                            $imageProperties[is_null($imageProperties) ? 1 : count($imageProperties) + 1] = $get('title');
                            $set('image_properties', $imageProperties);
                        }
                    }),

                // KeyValue::make('image_properties')
                //     ->keyLabel(__('messages.t_ap_image_order'))
                //     ->visible(fn(): bool => $adSettings->allow_image_alt_tags)
                //     ->keyPlaceholder(__('messages.t_ap_enter_image_id'))
                //     ->valueLabel(__('messages.t_ap_alt_text'))
                //     ->valuePlaceholder(__('messages.t_ap_enter_alt_text'))
                //     ->addable(false)
                //     ->live(onBlur: true)
                //     ->deletable(false)
                //     ->editableKeys(false)
                //     ->helperText(__('messages.t_ap_provide_descriptive_alt_text'))
                //     ->editableValues(true),
                ImageProperties::make('image_properties')
                    ->visible(fn(): bool => $adSettings->allow_image_alt_tags)
                    ->helperText(__('messages.t_provide_descriptive_alt_text')),
                TextInput::make('video_link')
                    ->label(__('messages.t_ap_youtube_video_link'))
                    ->url()
                    ->live(onBlur: true)
                    ->suffixIcon('heroicon-m-video-camera')
                    ->placeholder(__('messages.t_ap_example_youtube_link'))
                    ->hint(__('messages.t_ap_add_youtube_video_hint')),
                Section::make(__('messages.t_ap_location'))
                    ->hidden(fn($get) => self::validateAdTypePresence('disable_location', $get))
                    ->schema([
                        Select::make('country_id')
                            ->label(__('messages.t_ap_country'))
                            ->options(Country::pluck('name', 'id')->toArray())
                            ->live()
                            ->afterStateUpdated(fn(callable $set) => $set('state_id', null))
                            ->default(function ($set, $get) {
                                if (!self::canDisplayAdTypeSelect()) {
                                    $adType = AdType::find($get('ad_type_id'));
                                    if ($adType) {
                                        if ($adType->default_location) {
                                            return $adType->country_id;
                                        }
                                    }
                                }
                            })
                            ->required(),

                        Select::make('state_id')
                            ->default(function ($set, $get) {
                                if (!self::canDisplayAdTypeSelect()) {
                                    $adType = AdType::find($get('ad_type_id'));
                                    if ($adType) {
                                        if ($adType->default_location) {
                                            return $adType->state_id;
                                        }
                                    }
                                }
                            })
                            ->label(__('messages.t_ap_state'))
                            ->options(function (Get $get) {
                                $countryId = $get('country_id');
                                if (!$countryId) {
                                    return [];
                                }
                                return State::where('country_id', $countryId)->pluck('name', 'id')->toArray();
                            })
                            ->live()
                            ->hidden(fn(Get $get): bool => !$get('country_id') && (!self::validateAdTypePresence('default_location', $get)))
                            ->afterStateUpdated(fn(callable $set) => $set('city_id', null))
                            ->required(),

                        Select::make('city_id')
                            ->default(function ($set, $get) {
                                if (!self::canDisplayAdTypeSelect()) {
                                    $adType = AdType::find($get('ad_type_id'));
                                    if ($adType) {
                                        if ($adType->default_location) {
                                            return $adType->city_id;
                                        }
                                    }
                                }
                            })
                            ->label(__('messages.t_ap_city'))
                            ->options(function (Get $get) {
                                $stateId = $get('state_id');
                                if (!$stateId) {
                                    return [];
                                }
                                return City::where('state_id', $stateId)->pluck('name', 'id')->toArray();
                            })
                            ->hidden(fn(Get $get): bool => !$get('state_id') && (!self::validateAdTypePresence('default_location', $get)))
                            ->required()
                    ]),
                // Add a field to hold the dynamic components
                // Dynamically generate fields based on the selected category
                Forms\Components\Fieldset::make(__('messages.t_ap_dynamic_fields'))
                    ->schema(function (Get $get): array {
                        // Retrieve the selected category_id and main_category_id
                        $categoryId = $get('category_id');
                        $mainCategoryId = $get('main_category_id');

                        // Fetch fields based on the selected category
                        $categoryFields = self::getFieldsForAd($categoryId, $mainCategoryId);

                        // Map the fields to components
                        return self::mapFieldsToComponents($categoryFields);
                    })
                    ->hidden(function (Get $get): bool {
                        // Retrieve the selected category_id and main_category_id
                        $categoryId = $get('category_id');
                        $mainCategoryId = $get('main_category_id');

                        // Fetch fields based on the selected category
                        $categoryFields = self::getFieldsForAd($categoryId, $mainCategoryId);

                        // Hide the fieldset if there are no fields
                        return $categoryFields->isEmpty();
                    }),
            ])
            ->columns(1);
        return $form;
    }

    protected static function getClassifiedFormFields()
    {
        return [
            self::getForSaleByToggle(),
            self::getTipTapDescription(),
            self::getConditionToggle(),
            self::getClassifiedPriceFields(),
            self::getEcommercePriceFields(),
            self::createSkuInput(),
            ...(self::checkContactSectionFields() ?? []),
            self::getTagsInput(),

        ];
    }
    public static function getEcommercePriceFields(){
        return Fieldset::make()->schema([
            self::getPriceTypeSelect(),
            self::getPriceInput(),
            self::getOfferPriceInput(),
            self::getPriceSuffixSelect(),
        ])
        ->visible(fn($get)=> self::hasEnableOnlineShopping($get));
    }
    /**
     * Get the for sale by toggle
     * @return ToggleButtons
     */
    public static function getForSaleByToggle()
    {
        return ToggleButtons::make('for_sale_by')
            ->label(__('messages.t_for_sale_by'))
            ->live()
            ->required()
            ->grouped()
            ->visible(fn($get) => self::validateAdTypePresence('enable_for_sale_by', $get))
            ->options([
                'owner' => __('messages.t_owner_for_sale'),
                'business' => __('messages.t_business_for_sale'),
            ]);
    }


    public static function getTipTapDescription()
    {
        return TiptapEditor::make('description_tiptap')
            ->profile('simple')
            ->label(__('messages.t_description'))
            ->disableFloatingMenus()
            ->disableBubbleMenus()
            // ->reactive()
            ->output(TiptapOutput::Json)
            ->required();
    }

    /**
     * Get condition toggle
     * @return ToggleButtons
     */
    public static function getConditionToggle()
    {
        return ToggleButtons::make('condition_id')
            ->hidden(fn($get) => self::validateAdTypePresence('disable_condition', $get))
            ->label(__('messages.t_condition'))
            ->live()
            ->options(AdCondition::all()->pluck('name', 'id'))
            ->inline();
    }

    public static function adTypeSelect()
    {
        $defaultType = null;
        if (!self::canDisplayAdTypeSelect()) {
            $defaultType = AdType::first()?->id;
        }
        return [
            Select::make('ad_type_id')
                ->label(__('messages.t_ad_type'))
                ->options(AdType::pluck('name', 'id'))
                ->live()
                ->visible(fn() => static::canDisplayAdTypeSelect()) //If ad type count is 1 , then no need to show ad type select and
                ->required()
                ->default($defaultType)
                ->afterStateUpdated(function ($get, $set) {
                    $adType = AdType::find($get('ad_type_id'));
                    if ($adType) {
                        if ($adType->default_location) {
                            $set('country_id', $adType->country_id);
                            $set('state_id', $adType->state_id);
                            $set('city_id', $adType->city_id);
                        } else {
                            $set('country_id', null);
                            $set('state_id', null);
                            $set('city_id', null);
                        }
                    }
                    $set('main_category_id', null);
                }),
            Hidden::make('ad_type_id')
                ->hidden(fn() => static::canDisplayAdTypeSelect()) //If ad type count is 1 , then no need to show ad type select and
                ->default($defaultType),
        ];
    }

    public static function createSkuInput()
    {
        return TextInput::make('sku')
            ->unique(ignoreRecord: true)
            ->maxLength(20)
            ->label(__('messages.t_sku'))
            ->helperText(__('messages.t_sku_helper_text'))
            ->required()
            ->validationAttribute(__('messages.t_sku'))
            ->minLength(5)
            ->placeholder(__('messages.t_sku'))
            ->visible(fn($get) => self::hasEnableOnlineShopping($get) && self::validateAdTypePresence('marketplace_settings', $get)['enable_sku'])
            ->live(onBlur: true);
    }

    public static function getCategoriesFields()
    {
        return [
            Select::make('main_category_id')
                ->label(__('messages.t_ap_main_category'))
                ->options(function (Get $get) {
                    return Category::whereNull('parent_id')->where('ad_type_id', $get('ad_type_id'))->pluck('name', 'id');
                })
                ->live()
                ->required()
                ->dehydrated()
                ->placeholder(__('messages.t_ap_select_main_category')),

            Select::make('category_id')
                ->label(__('messages.t_ap_sub_category'))
                ->options(function (Get $get) {
                    $mainCategoryId = $get('main_category_id');
                    return Category::where('parent_id', $mainCategoryId)->pluck('name', 'id');
                })
                ->live()
                ->hidden(fn(Get $get, Set $set, $livewire): bool => !$get('main_category_id'))
                ->placeholder(__('messages.t_ap_select_sub_category')),
            Select::make('child_category_id')
                ->label(__('messages.t_child_category'))
                ->options(function (Get $get) {
                    $subcategory = Category::with('subcategories')->find($get('category_id'));
                    if ($subcategory && $subcategory->subcategories->isNotEmpty()) {
                        return $subcategory->subcategories?->pluck('name', 'id');
                    }
                    return [];
                })
                ->live()
                ->hidden(fn(Get $get): bool => !$get('category_id'))
                ->placeholder(__('messages.t_select_child_category')),

        ];
    }

    /**
     * Get the form fields for classified price section
     * @return Fieldset|mixed>
     */
    protected static function getClassifiedPriceFields()
    {
        return Fieldset::make()->schema([
            self::getPriceTypeSelect(),
            self::getPriceInput(),
            self::getOfferPriceInput(),
            self::getPriceSuffixSelect(),
        ])->hidden(function ($get) {
            return self::hasEnableOnlineShopping($get) || self::validateAdTypePresence('disable_price_type', $get);
        });
    }
    public static function getPriceTypeSelect()
    {
        return Select::make('price_type_id')
            ->hidden(fn($get) => self::hasEnableOnlineShopping($get) || self::validateAdTypePresence('disable_price_type', $get))
            ->selectablePlaceholder(false)
            ->label(__('messages.t_price_type'))
            ->live()
            ->required()
            ->native(false)
            ->afterStateHydrated(function ($state, $get,$component) {
                $priceTypeIds = self::validateAdTypePresence('price_types', $get);
                // Ensure price_type_id is only assigned if it exists in priceTypeIds
                $priceTypeId =
                    (self::validateAdTypePresence('customize_price_type', $get) && (!in_array($state, $priceTypeIds)) ? null : $state);
                $component->state($priceTypeId);
            })
            ->options(function ($get) {
                $priceTypeIds = self::validateAdTypePresence('price_types', $get);

                return PriceType::when(self::validateAdTypePresence('customize_price_type', $get) && count($priceTypeIds) > 0, function ($query) use ($priceTypeIds) {
                    $query->whereIn('id', $priceTypeIds);
                })->pluck('name', 'id');
            })->columnSpanFull();
    }

    public static function getPriceSuffixSelect()
    {
        return Select::make('price_suffix')
            ->label(__('messages.t_price_suffix'))
            ->visible(function (Get $get, $livewire) {
                return (!self::validateAdTypePresence('disable_price_type', $get)) && ($get('price_type_id') == 1 && self::validateAdTypePresence('has_price_suffix', $get));
            })
            ->live(onBlur: true)
            ->required()
            ->helperText(__('messages.t_ap_price_suffix_helper'))
            ->options(function (Get $get) {
                $adType = AdType::find($get('ad_type_id'));
                return array_combine($adType->suffix_field_options ?? [], $adType->suffix_field_options ?? []);
            });
    }

    public static function getPriceInput()
    {
        return TextInput::make('price')
            ->required()
            ->numeric()
            ->minValue(1)
            ->live(onBlur: true)
            ->maxValue(fn($get) => self::hasEnablePointSystem($get) ? getPointSystemSetting('set_max_points_ad') : null)
            ->columnSpanFull(self::hasEnablePointSystem(null))
            ->hidden(function (Get $get) {
                if (self::hasEnableOnlineShopping($get))
                    return false;

                if (self::validateAdTypePresence('disable_price_type', $get) || $get('price_type_id') != 1) {
                    return true;
                }
            })
            ->markAsRequired(fn($get) => self::hasEnablePointSystem($get) ? false : !self::validateAdTypePresence('enable_price', $get))
            ->label(function ($get) {
                if (self::hasEnablePointSystem($get)) {
                    return __('messages.t_points');
                } else {
                    return __('messages.t_price');
                }
            })
            ->validationAttribute(function ($get) {
                if (self::hasEnablePointSystem($get)) {
                    return __('messages.t_points');
                } else {
                    return __('messages.t_price');
                }
            })
            ->placeholder(function ($get) {
                if (self::hasEnablePointSystem($get)) {
                    return __('messages.t_enter_the_points');
                } else {
                    return __('messages.t_price_your_ad');
                }
            })
            ->helperText(function ($get) {
                if (self::hasEnablePointSystem()) {
                    return __('messages.t_points_helpertext');
                } else {
                    return __('messages.t_set_fair_price');
                }
            })
            ->prefix(function ($get) {
                if (self::hasEnablePointSystem($get)) {
                    return getPointSystemSetting('short_name');
                } else {
                    return config('app.currency_symbol');
                }
            })
            ->hiddenLabel(function (Get $get) {
                return !(self::hasEnableOnlineShopping($get) || self::hasEnablePointSystem($get));
            });
    }

    public static function getOfferPriceInput()
    {
        return TextInput::make('offer_price')
            ->label(function ($get) {
                if (self::hasEnablePointSystem($get)) {
                    return __('messages.t_offer_points');
                } elseif (self::hasEnableOnlineShopping($get)) {
                    return __('messages.t_offer_price');
                } else {
                    return __('messages.t_offer_price');
                }
            })
            ->placeholder(function ($get) {
                if (self::hasEnablePointSystem($get)) {
                    return __('messages.t_enter_offer_points');
                } else {
                    return __('messages.t_enter_offer_price');
                }
            })
            ->helperText(function ($get) {
                if (self::hasEnablePointSystem($get)) {
                    return __('messages.t_offer_points_helpertext');
                } elseif (self::hasEnableOnlineShopping($get)) {
                    return __('messages.t_set_fair_price');
                } else {
                    return '';
                }
            })
            ->prefix(function ($get) {
                if (self::hasEnablePointSystem($get)) {
                    return getPointSystemSetting('short_name');
                } else {
                    return config('app.currency_symbol');
                }
            })
            ->validationAttribute(__('messages.t_offer_price'))
            ->numeric()
            ->minValue(1)
            ->lt('price')
            ->live(onBlur: true)
            ->columnSpanFull()
            ->hidden(function (Get $get) {
                if (self::hasEnableOnlineShopping($get) && self::validateAdTypePresence('enable_offer_price', $get)) {
                    return false;
                }
                if (self::hasEnablePointSystem($get) || !self::validateAdTypePresence('enable_offer_price', $get) || self::validateAdTypePresence('disable_price_type', $get) || ($get('price_type_id') && $get('price_type_id') != 1)) {
                    return true;
                }
            });
    }

    public static function hasEnablePointSystem($get = null)
    {
        return (self::validateAdTypePresence('marketplace', $get) == POINT_SYSTEM_MARKETPLACE) && isEnablePointSystem();
    }
    /**
     * Display the ad type select field if there are multiple ad types.
     * @return bool
     */
    public static function canDisplayAdTypeSelect(): bool
    {
        return AdType::get()->count() > 1;
    }

    /**
     * Checks if the contact section fields should be included based on phone settings.
     *
     * Retrieves the contact section fields if either phone or WhatsApp is enabled
     * in the phone settings. Returns an empty array if neither is enabled.
     *
     * @return array The contact section fields or an empty array.
     */
    protected static function checkContactSectionFields()
    {
        $contactSettings = app(PhoneSettings::class);

        if ($contactSettings->enable_phone || $contactSettings->enable_whatsapp) {
            return [self::getContactSectionFields()];
        }
        return [];
    }
    /**
     * Get the form fields for contact section
     * @return Fieldset|mixed>
     */
    protected static function getContactSectionFields()
    {
        return Fieldset::make('Contact Information')->schema([
            self::getDisplayPhoneToggle(),
            self::getPhoneNumberInput(),
            self::getSameNumberToggle(),
            self::getWhatsappNumberInput(),
        ]);
    }

    /**
     * get tags input field
     */
    public static function getTagsInput()
    {
        return TagsInput::make('tags')
            ->label(__('messages.t_tags'))
            ->helperText(__('messages.t_set_tags'))
            ->visible(fn($get) => self::validateAdTypePresence('enable_tags', $get))
            ->live(onBlur: true);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->modifyQueryUsing(fn(Builder $query) => $query->latest())
            ->columns([
                    SpatieMediaLibraryImageColumn::make('ads')
                        ->collection('ads')
                        ->conversion('thumb')
                        ->defaultImageUrl(fn($record) => getAdPlaceholderImage($record->id))
                        ->label(__('messages.t_ap_ad_images'))
                        ->size(40)
                        ->circular()
                        ->overlap(2)
                        ->stacked()
                        ->limit(3),

                    TextColumn::make('title')
                        ->searchable()
                        ->label(__('messages.t_ap_title')),

                    TextColumn::make('view_count')
                        ->label(__('messages.t_ap_views'))
                        ->sortable(),

                    TextColumn::make('likes_count')
                        ->label(__('messages.t_ap_likes')),

                    TextColumn::make('user.name')
                        ->label(__('messages.t_ap_posted_by'))
                        ->sortable(),

                    TextColumn::make('price')
                        ->label(__('messages.t_ap_price')),

                    TextColumn::make('location_name')
                        ->label(__('messages.t_ap_location')),

                    TextColumn::make('posted_date')
                        ->label(__('messages.t_ap_posted_on'))
                        ->date(),
                    TextColumn::make('mainCategory.name')
                        ->label(__('messages.t_ap_main_category'))
                        ->sortable()
                        ->default('---'),
                    TextColumn::make('category.name')
                        ->label(__('messages.t_ap_sub_category'))
                        ->sortable()
                        ->default('---'),
                    SelectColumn::make('status')
                        ->options([
                                'draft' => __('messages.t_ap_draft'),
                                'active' => __('messages.t_ap_active'),
                                'inactive' => __('messages.t_ap_inactive'),
                                'sold' => __('messages.t_ap_sold'),
                            ])
                        ->label(__('messages.t_ap_change_status'))
                        ->selectablePlaceholder(false),

                ])
            ->defaultSort('posted_date', 'desc')
            ->filters([
                    Tables\Filters\TrashedFilter::make(),
                ])
            ->actions([
                    Tables\Actions\EditAction::make(),
                    Action::make('view')
                        ->icon('heroicon-o-eye')
                        ->label(__('messages.t_ap_view_details'))
                        ->url(fn(Ad $record): string => route('ad.overview', $record->slug))
                        ->openUrlInNewTab(),
                    Tables\Actions\DeleteAction::make(),
                    Tables\Actions\RestoreAction::make(),
                    Tables\Actions\ForceDeleteAction::make(),
                ])
            ->bulkActions([
                    BulkActionGroup::make([
                        Tables\Actions\DeleteBulkAction::make(),
                        Tables\Actions\RestoreBulkAction::make(),
                        Tables\Actions\ForceDeleteBulkAction::make(),
                    ]),
                ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListAds::route('/'),
            'create' => Pages\CreateAd::route('/create'),
            'edit' => Pages\EditAd::route('/{record}/edit')
        ];
    }
}
