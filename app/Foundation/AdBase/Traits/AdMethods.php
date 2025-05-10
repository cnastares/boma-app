<?php

namespace App\Foundation\AdBase\Traits;

use App\Settings\PointVaultSettings;
use App\Models\Ad;
use App\Models\AdCondition;
use App\Models\AdType;
use App\Models\PriceType;
use App\Settings\AdSettings;
use App\Settings\GeneralSettings;
use App\Settings\OfflinePaymentSettings;
use Closure;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ToggleButtons;
use Filament\Forms\Get;
use FilamentTiptapEditor\Enums\TiptapOutput;
use FilamentTiptapEditor\TiptapEditor;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;
use Filament\Forms\Set;
use App\Settings\PhoneSettings;
use Filament\Forms\Components\Actions\Action;

trait AdMethods
{
    abstract public function adTypeSelect();
    abstract public function titleInput();
    abstract public function detailForm();
    abstract public function updated($name, $value);
    abstract public function save();
    abstract public function checkRequiredFieldsFilled();

    public function getCategoryId()
    {
        return Hidden::make('category_id')
            ->live();
        // TODO: Handle reset logic when category changes
        // ->afterStateUpdated(function(Set $set){
        //     $set('condition_id', null);
        //     $set('price_type_id', null);
        //     $set('price', null);
        // });
    }

    public function getTipTapDescription()
    {
        return TiptapEditor::make('description_tiptap')
        ->profile('simple')
        ->label(__('messages.t_description'))
        ->disableFloatingMenus()
        ->disableBubbleMenus()
        ->hintAction(
            Action::make('saveChanges')
                ->label(__('messages.t_save_changes'))
                // ->icon('heroicon-m-clipboard')
                ->action(function ($state) {
                    $this->validateOnly('description_tiptap');
                    if($state){
                        $this->ad->update(['description_tiptap' => $state]);
                    }
                })
        )
        ->helperText(__('messages.t_save_changes_reminder'))
        ->output(TiptapOutput::Html)
        ->required();
    }

    public function getPriceTypeSelect()
    {
        return Select::make('price_type_id')
            ->hidden($this->hasEnableOnlineShopping() || $this->validateAdTypePresence('disable_price_type'))
            ->selectablePlaceholder(false)
            ->label(__('messages.t_price_type'))
            ->live()
            ->required()
            ->native(false)
            ->options(function () {
                $priceTypeIds = $this->validateAdTypePresence('price_types');
                return PriceType::when($this->validateAdTypePresence('customize_price_type') && count($priceTypeIds) > 0, function ($query) use ($priceTypeIds) {
                    $query->whereIn('id', $priceTypeIds);
                })->pluck('name', 'id');
            })->columnSpanFull();
    }

    public function getPriceSuffixSelect()
    {
        return Select::make('price_suffix')
            ->label(__('messages.t_price_suffix'))
            ->visible(function (Get $get, $livewire) {
                return (!$this->validateAdTypePresence('disable_price_type')) && ($get('price_type_id') == 1 && $this->validateAdTypePresence('has_price_suffix'));
            })
            ->live(onBlur: true)
            ->required()
            ->helperText(__('messages.t_ap_price_suffix_helper'))
            ->options(function (Get $get) {
                $adType = AdType::find($get('ad_type_id'));
                return array_combine($adType->suffix_field_options ?? [], $adType->suffix_field_options ?? []);
            });
    }

    public function getPriceInput()
    {
        return TextInput::make('price')
            ->required()
            ->numeric()
            ->minValue(1)
            ->live(onBlur: true)
            ->when($this->hasEnablePointSystem(), fn(TextInput $field) => $field->maxValue(fn() => getPointSystemSetting('set_max_points_ad')))
            ->columnSpanFull($this->hasEnablePointSystem())
            ->hidden(function (Get $get) {
                if ($this->hasEnableOnlineShopping())
                    return false;

                if ($this->validateAdTypePresence('disable_price_type') || $get('price_type_id') != 1) {
                    return true;
                }
            })
            ->markAsRequired($this->hasEnablePointSystem() ? false : !$this->validateAdTypePresence('enable_price'))
            ->label(function () {
                if ($this->hasEnablePointSystem()) {
                    return __('messages.t_points');
                } else {
                    return __('messages.t_price');
                }
            })
            ->validationAttribute(function () {
                if ($this->hasEnablePointSystem()) {
                    return __('messages.t_points');
                } else {
                    return __('messages.t_price');
                }
            })
            ->placeholder(function () {
                if ($this->hasEnablePointSystem()) {
                    return __('messages.t_enter_the_points');
                } else {
                    return __('messages.t_price_your_ad');
                }
            })
            ->helperText(function () {
                if ($this->hasEnablePointSystem()) {
                    return __('messages.t_points_helpertext');
                } else {
                    return __('messages.t_set_fair_price');
                }
            })
            ->prefix(function () {
                if ($this->hasEnablePointSystem()) {
                    return getPointSystemSetting('short_name');
                } else {
                    return config('app.currency_symbol');
                }
            })
            ->hiddenLabel(function (Get $get) {
                return !($this->hasEnableOnlineShopping() || $this->hasEnablePointSystem());
            });
    }

    public function getOfferPriceInput()
    {
        return TextInput::make('offer_price')
            ->label(function () {
                if ($this->hasEnablePointSystem()) {
                    return __('messages.t_offer_points');
                } elseif ($this->hasEnableOnlineShopping()) {
                    return __('messages.t_offer_price');
                } else {
                    return __('messages.t_offer_price');
                }
            })
            ->placeholder(function () {
                if ($this->hasEnablePointSystem()) {
                    return __('messages.t_enter_offer_points');
                }else {
                    return __('messages.t_enter_offer_price');
                }
            })
            ->helperText(function () {
                if ($this->hasEnablePointSystem()) {
                    return __('messages.t_offer_points_helpertext');
                } elseif ($this->hasEnableOnlineShopping()) {
                    return __('messages.t_set_fair_price');
                } else {
                    return '';
                }
            })
            ->prefix(function () {
                if ($this->hasEnablePointSystem()) {
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
                if ($this->hasEnableOnlineShopping() && $this->validateAdTypePresence('enable_offer_price')){
                    return false;
                }
                if ($this->hasEnablePointSystem() || !$this->validateAdTypePresence('enable_offer_price') || $this->validateAdTypePresence('disable_price_type') || ($get('price_type_id') && $get('price_type_id') != 1)) {
                    return true;
                }
            });
    }

    public function createSkuInput()
    {
        return TextInput::make('sku')
            ->rules([
                fn(Get $get): Closure => function (string $attribute, $value, Closure $fail) use ($get) {
                    $ad = Ad::where('sku', $value)->exists();
                    if ($ad && $this->ad->sku != $value) {
                        $fail("The {$attribute} already exists.");
                    }
                },
            ])
            ->maxLength(20)
            ->label(__('messages.t_sku'))
            ->helperText(__('messages.t_sku_helper_text'))
            ->required()
            ->validationAttribute(__('messages.t_sku'))
            ->minLength(5)
            ->placeholder(__('messages.t_sku'))
            ->visible($this->hasEnableOnlineShopping() && $this->validateAdTypePresence('marketplace_settings')['enable_sku'])
            ->live(onBlur: true);
    }

    public function createReturnPolicySelect()
    {
        return Select::make('return_policy_id')
            ->label(__('messages.t_return_policy'))
            ->helperText(__('messages.t_return_policy_helper_text'))
            ->required()
            ->live(onBlur: true)
            ->visible($this->hasEnableOnlineShopping())
            ->options(\Adfox\ECommerce\Models\ReturnPolicy::where('user_id', auth()->id())->pluck('policy_name', 'id'));
    }

    public function createCashOnDeliveryToggle()
    {
        return ToggleButtons::make('enable_cash_on_delivery')
            ->label(__('messages.t_enable_cash_on_delivery'))
            ->helperText(__('messages.t_enable_cash_on_delivery_helper_text'))
            ->default(1)
            ->grouped()
            ->boolean()
            ->visible(function() {
                return app('filament')->hasPlugin('offline-payment') && app(OfflinePaymentSettings::class)->status && (!$this->validateAdTypePresence('marketplace_settings')['disable_cash_on_delivery']);
            })
            ->validationAttribute(__('messages.t_enable_cash_on_delivery'));
    }

    public function hasEnableOnlineShopping()
    {
        return ($this->validateAdTypePresence('marketplace') == ONLINE_SHOP_MARKETPLACE) && is_ecommerce_active();
    }

    private function validateAdTypePresence($field)
    {
        if ($this->ad?->adType) {
            return $this->ad->adType?->{$field};
        }

        return false;
    }

    private function canHiddenPriceField($get)
    {
        if ($this->hasEnableOnlineShopping()) {
            return false;
        }

        if ($this->validateAdTypePresence('disable_price_type') || $get('price_type_id') != 1) {
            return true;
        }
    }

    public function hasEnablePointSystem()
    {
        return ($this->validateAdTypePresence('marketplace') == POINT_SYSTEM_MARKETPLACE) && isEnablePointSystem();
    }

    /**
     * Display the ad type select field if there are multiple ad types.
     * @return bool
     */
    public function canDisplayAdTypeSelect(): bool
    {
        return AdType::get()->count() > 1;
    }

    /**
     * Check if admin approval is required for this ad.
     *
     * @param Ad $ad
     * @return bool
     */
    protected function requiresAdminApproval($ad)
    {
        return app(AdSettings::class)->admin_approval_required && $ad->status && $ad->status->value !== 'draft';
    }
}
