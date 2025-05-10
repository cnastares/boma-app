<?php
namespace App\Foundation\AdBase\Traits;

use App\Models\AdCondition;
use App\Settings\PhoneSettings;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\ToggleButtons;


trait HasAdForm {
    /**
     * Get the form fields for classified ads
     * @return array<Fieldset|mixed>
     */
    protected function getClassifiedFormFields()
    {
        return [
            $this->getForSaleByToggle(),
            $this->getTipTapDescription(),
            $this->getConditionToggle(),
            $this->getClassifiedPriceFields(),
            ...($this->checkContactSectionFields() ?? []),
            $this->getTagsInput(),

        ];
    }

        /**
     * Get the form fields for classified ads
     * @return array<Fieldset|mixed>
     */
    protected function getEcommerceFormFields()
    {
        return [
            $this->getForSaleByToggle(),
            $this->getTipTapDescription(),
            $this->getConditionToggle(),
            $this->getEcommercePriceFields(),
            $this->createSkuInput(),
            $this->createReturnPolicySelect(),
            // $this->createCashOnDeliveryToggle(),
            ...($this->checkContactSectionFields() ?? []),
            $this->getTagsInput(),

        ];
    }

    /**
     * Checks if the contact section fields should be included based on phone settings.
     *
     * Retrieves the contact section fields if either phone or WhatsApp is enabled
     * in the phone settings. Returns an empty array if neither is enabled.
     *
     * @return array The contact section fields or an empty array.
     */
    protected function checkContactSectionFields()
    {
        $contactSettings =  app(PhoneSettings::class);

        if($contactSettings->enable_phone || $contactSettings->enable_whatsapp)
        {
            return [$this->getContactSectionFields()];
        }
        return [];
    }
    /**
     * Get the form fields for contact section
     * @return Fieldset|mixed>
     */
    protected function getContactSectionFields()
    {
        return Fieldset::make('Contact Information')->schema([
            $this->getDisplayPhoneToggle(),
            $this->getPhoneNumberInput(),
            $this->getSameNumberToggle(),
            $this->getWhatsappNumberInput(),
        ]);
    }

    /**
     * Get the form fields for classified price section
     * @return Fieldset|mixed>
     */
    protected function getClassifiedPriceFields()
    {
        return Fieldset::make()->schema([
            $this->getPriceTypeSelect(),
            $this->getPriceInput(),
            $this->getOfferPriceInput(),
            $this->getPriceSuffixSelect(),
        ])->hidden(function () {
            return $this->validateAdTypePresence('disable_price_type');
        });
    }

    /**
     * Get the for sale by toggle
     * @return ToggleButtons
     */
    public function getForSaleByToggle()
    {
        return ToggleButtons::make('for_sale_by')
            ->label(__('messages.t_for_sale_by'))
            ->live()
            ->grouped()
            ->visible($this->validateAdTypePresence('enable_for_sale_by'))
            ->options([
                'owner' => __('messages.t_owner_for_sale'),
                'business' => __('messages.t_business_for_sale'),
            ]);
    }

    /**
     * Get description field
     * @return MarkdownEditor
     */
    public function getDescription()
    {
        return MarkdownEditor::make('description')
            ->disableToolbarButtons(['attachFiles'])
            ->label(__('messages.t_description'))
            ->live(onBlur: true)
            ->minLength(20)
            ->required();
    }

    /**
     * Get condition toggle
     * @return ToggleButtons
     */
    public function getConditionToggle()
    {
        return ToggleButtons::make('condition_id')
            ->hidden($this->validateAdTypePresence('disable_condition'))
            ->label(__('messages.t_condition'))
            ->live()
            ->options(AdCondition::all()->pluck('name', 'id'))
            ->inline();
    }

    /**
     * get tags input field
     */
    public function getTagsInput()
    {
        return TagsInput::make('tags')
            ->label(__('messages.t_tags'))
            ->helperText(__('messages.t_set_tags'))
            ->visible($this->validateAdTypePresence('enable_tags'))
            ->live(onBlur: true);
    }

    public function getEcommercePriceFields(){
        return Fieldset::make()->schema([
            $this->getPriceTypeSelect(),
            $this->getPriceInput(),
            $this->getOfferPriceInput(),
            $this->getPriceSuffixSelect(),
        ]);
    }
}
