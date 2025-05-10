<?php

namespace App\Foundation\AdBase\Traits;

use App\Models\Ad;
use Livewire\Attributes\Rule;

trait AdProperties
{
    // Define default model
    public $model = Ad::class;

    // Ad-specific details
    public $id;
    public $ad;

    // Category properties
    public $showMainCategories = true;
    public $categories = [];

    #[Rule('required', message: 'messages.t_select_main_category_error')]
    public $parent_category = null;

    // #[Rule('required', message: 'messages.t_select_ad_category_error')]
    public $sub_category_id = null;
    public $child_category_id = null; //child category

    #[Rule('required', message: 'messages.t_select_ad_type_error')]
    public $ad_type_id = null;

    // Ad content
    public $title = '';
    public $description = '';

    // Pricing details
    public $price = null;
    public $price_type_id = null;
    public $offer_price = null;

    // Ad conditions and tags
    public $condition_id = null;
    public array $tags = [];

    // Phone details
    public $display_phone = false;
    public $phone_number = null;

    // Miscellaneous ad properties
    public $type = '';
    public $for_sale_by = '';

    // UI control flags
    public $disable_condition = false;
    public $disable_price_type = false;

    // Price suffix (e.g., per item, per hour)
    public $price_suffix = null;

    public $enableECommerce = false;
    public $sku  = null;
    public $return_policy_id  = null;
    public $enable_cash_on_delivery  = null;

    // Phone details
    public $whatsapp_number = false;
    public $display_whatsapp = null;

    public $adType;

    public $ageValue = null;

    public $showChildCategory = false;

    public array $description_tiptap;
}
