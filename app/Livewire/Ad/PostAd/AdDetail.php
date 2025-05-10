<?php

namespace App\Livewire\Ad\PostAd;

use App\Foundation\AdBase\Traits\AdMethods;
use App\Foundation\AdBase\Traits\AdProperties;
use App\Foundation\AdBase\Traits\HasAdForm;
use App\Foundation\AdBase\Traits\HasContactFields;
use App\Foundation\AdBase\Traits\LocationFunctions;
use Livewire\Component;
use App\Models\Ad;
use App\Models\AdType;
use App\Models\Category;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Cookie;



class AdDetail extends Component implements HasForms
{
    use InteractsWithForms, AdProperties, HasAdForm, AdMethods, HasContactFields, LocationFunctions;

    public function mount($id = null)
    {
        $this->id = $id;

        if ($this->id) {
            $this->loadAdDetails($this->id);
            $this->checkRequiredFieldsFilled();

        } else {

            //If ad type count is 1 then assign first ad type otherwise don't assign
            if (!$this->canDisplayAdTypeSelect()) {
                $this->ad_type_id = AdType::first()?->id ?? null;
            }
            $this->dispatch('required-fields-filled', ['isFilled' => false]);

        }
        $this->loadCategories($this->ad_type_id);
    }

    /**
     * Get the form fields for detail section based on the business
     * @return Form
     */
    public function detailForm(): Form
    {

        $fields = [];
        if ($this->hasEnableOnlineShopping()) {
            $fields = $this->getEcommerceFormFields();
        } else {
            $fields = $this->getClassifiedFormFields();
        }

        return $this->makeForm()
            ->schema(
                $fields
            );
    }

    protected function loadCategories($adTypeId)
    {
        $this->categories = Category::where('ad_type_id', $adTypeId)
            ->with('subcategories.subcategories')
            ->whereNull('parent_id')
            ->get();

    }

    protected function loadAdDetails($id)
    {
        $this->ad = $this->model::with(['adType', 'category'])->find($id);

        if ($this->ad) {
            $this->fillAdDetails();
        }
    }

    public function adTypeSelect(): Form
    {
        return $this->makeForm()
            ->schema([
                Select::make('ad_type_id')
                    ->label(__('messages.t_ad_type'))
                    ->options(AdType::pluck('name', 'id'))
                    ->live(onBlur: true)
                    ->visible(fn($livewire) => $livewire->canDisplayAdTypeSelect()) //If ad type count is 1 , then no need to show ad type select and
                    ->required(),
            ]);
    }

    public function titleInput(): Form
    {
        return $this->makeForm()
            ->schema([
                TextInput::make('title')
                    ->label(__('messages.t_title'))
                    ->live(onBlur: true)
                    ->placeholder(__('messages.t_what_are_you_selling'))
                    ->minLength(10)
                    ->maxLength(70)
                    ->required(),
            ]);
    }

    protected function fillAdDetails()
    {

        $this->enableECommerce = $this->ad->adType?->marketplace == ONLINE_SHOP_MARKETPLACE ?? false;
        $isClassifiedMarketplace = $this->ad->adType?->marketplace == CLASSIFIED_MARKETPLACE ?? false;

        $priceTypeIds = $this->validateAdTypePresence('price_types');

        //Fill price_type_id based on ad type
        if ($isClassifiedMarketplace) {
            // Ensure price_type_id is only assigned if it exists in priceTypeIds
            $priceTypeId = $this->validateAdTypePresence('customize_price_type') && !in_array($this->ad->price_type_id, $priceTypeIds)
                ? null
                : $this->ad->price_type_id;
        } else {
            $priceTypeId = $this->enableECommerce
                ? 1
                : $this->ad->price_type_id;
        }

        $adDetails = [
            'title' => $this->ad->title,
            'ad_type_id' => $this->ad->ad_type_id,
            'description' => $this->ad->description,
            'condition_id' => $this->ad->condition_id,
            'price_suffix' => $this->ad->price_suffix,
            'price_type_id' => $priceTypeId,
            'offer_price' => $this->ad->offer_price,
            'price' => $this->ad->price,
            'tags' => $this->ad->tags ?? [],
            'for_sale_by' => $this->ad->for_sale_by,
            'display_phone' => $this->ad->display_phone,
            'phone_number' => $this->ad->phone_number,
            'display_whatsapp' => $this->ad->display_whatsapp,
            'whatsapp_number' => $this->ad?->whatsapp_number,
            'parent_category' => $this->ad->main_category_id ?? $this->ad->category?->parent_id,
            'sub_category_id' => $this->ad->category_id,
            'child_category_id' => $this->ad->child_category_id,
            'disable_condition' => $this->ad->adType?->disable_condition,
            'disable_price_type' => $this->ad->adType?->disable_price_type,
            'description_tiptap' => $this->ad->description_tiptap ?? [],
        ];

        if ($this->enableECommerce) {
            $adDetails = array_merge($adDetails, [
                'sku' => $this->ad->sku,
                'return_policy_id' => $this->ad->return_policy_id,
                // 'enable_cash_on_delivery' => $this->ad->enable_cash_on_delivery,
            ]);
        }
        $this->fill($adDetails);

        if ($this->ad->ad_type_id) {
            $this->setAdTypeDetails();
            $this->loadAdTypeRelatedData($this->ad, $this->ad->ad_type_id);
        }

        if ($this->parent_category) {
            $this->showMainCategories = false;
        }

    }

    protected function setAdTypeDetails()
    {
        if ($this->ad->type_id) {
            $this->disable_condition = $this->ad->adType?->disable_condition;
            $this->disable_price_type = $this->ad->adType?->disable_price_type;

        } else {
            $this->disable_condition = false;
            $this->disable_price_type = false;
        }
    }


    /**
     * Selects the main category for an advertisement.
     *
     * Verifies the age requirement for the given category ID.
     *
     * @param int $categoryId The ID of the category to be set as the main category.
     * @return void
     */
    public function selectMainCategory($categoryId)
    {

        $verifyResult = '';

        if (!Cookie::has("age_verified_{$categoryId}")) {

            $verifyResult = $this->verifyAge($categoryId, 'main_category_id');
            if (!$verifyResult) {
                return;
            }

        } else {
            $this->verifyIdentity($categoryId, 'main_category_id');
        }
        $this->dispatch('ad-updated');

    }


    /**
     * Verifies if age verification is required for the given category.
     *
     * If the category requires age verification and it has not been verified yet,
     * dispatches an event to show the age verification popup.
     *
     * @param int $categoryId The ID of the category to check.
     */
    public function verifyAge($categoryId, $fieldName)
    {

        $category = Category::find($categoryId);

        // Check if category requires age verification and if not already verified
        if ($category && $category->enable_age_verify) {
            $this->ageValue = $category->age_value;
            $this->dispatch('show-age-verification-popup', categoryId: $categoryId, fieldNames: $fieldName);
            return false;
        } else {
            $this->verifyIdentity($categoryId, $fieldName);
        }
    }


    /**
     * Verifies the user's identity based on the category's requirements.
     *
     * If the category requires identity verification and the user is not verified,
     * a popup for identity verification is dispatched. Otherwise, updates the ad's
     * main category and resets related category fields.
     *
     * @param int $categoryId The ID of the category to verify.
     * @return void
     */
    public function verifyIdentity($categoryId, $fieldName)
    {

        $category = Category::find($categoryId);

        Cookie::queue("age_verified_{$categoryId}", true, 43200);
        // Check if category requires age verification and if not already verified
        if ($category && $category->enable_identity_verify) {

            $user = auth()->user();
            if (!$user->verified) {
                $this->dispatch('show-identity-verification-popup', categoryId: $categoryId, fieldNames: $fieldName);
                $this->sub_category_id = null;
                $this->child_category_id = null;
                $this->showMainCategories = false;
                $this->showChildCategory = false;
                return;
            }

        }

        if (!empty($categoryId)) {
            $this->ad->update([$fieldName => $categoryId]);
        } elseif (in_array($fieldName, ['category_id', 'child_category_id']) && empty($categoryId)) {
            $updateData = [$fieldName => null];

            if ($fieldName === 'category_id') {
                $updateData['child_category_id'] = null;
            }

            $this->ad->update($updateData);
        }


        if ($fieldName == 'main_category_id') {
            $this->parent_category = $categoryId;
            $this->sub_category_id = null;
            $this->child_category_id = null;
            $this->showMainCategories = false;
        } elseif ($fieldName == 'category_id') {
            $this->sub_category_id = $categoryId;
            $this->child_category_id = null;
            $this->showMainCategories = false;
            $this->showChildCategory = true;
        }

    }

    /**
     * Redirects the user to the home route.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function goHome()
    {
        return redirect()->route('home');
    }

    /**
     * Redirects the user to the verification page.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function verifyCenter()
    {
        return redirect()->route('filament.app.pages.verification');
    }

    #[On('check-category')]

    /**
     * Checks if the given category ID corresponds to a subcategory or child category.
     *
     * If the category is a subcategory, it resets the sub_category_id property to null.
     * If the category is a child category, it resets the child_category_id property to null.
     *
     * @param int $categoryId The ID of the category to check.
     * @return void
     */
    public function checkCategory($categoryId)
    {
        $checkSubCat = Category::find($categoryId)->isSubcategory();

        if ($checkSubCat) {
            $this->sub_category_id = null;
        }

        $checkChildCat = Category::find($categoryId)->isChildCategory();

        if ($checkChildCat) {
            $this->child_category_id = null;
        }

    }
    protected function updateAdSlug(Ad $ad, $title)
    {
        $ad->slug = Str::slug(Str::limit($title, 138)) . '-' . substr($ad->id, 0, 8);
        $ad->save();
    }

    public function getRequiredFieldsProperty()
    {
        return collect($this->getRules())
            ->filter(fn($rule) => is_array($rule) ? in_array('required', $rule) : $rule === 'required')
            ->keys()
            ->toArray();
    }

    #[On('next-clicked')]
    public function next()
    {
        $this->validate();

        if (!$this->parent_category) {
            $this->addError('parent_category', __('messages.t_select_main_category'));
            return;
        }
        $this->dispatch('validate-dynamic-fields');
    }

    /**
     * Get all form definitions.
     *
     * @return array
     */
    protected function getForms(): array
    {
        return [
            'titleInput',
            'detailForm',
        ];
    }

    /**
     * Handle updates to component properties.
     *
     * @param string $name
     * @param mixed $value
     */
    public function updated($name, $value)
    {
        $this->checkRequiredFieldsFilled();
        $this->validateOnly($name);

        $userId = auth()->id();
        if (!$this->id) {
            if (!$userId) {
                abort(403, 'Unauthorized action.');
            }

            // If it's a new ad, create it
            if (!$this->id && $name == 'title') {
                $this->createNewAd($name, $value, $userId);
            }

        } else {
            // Fetch the existing ad
            $ad = Ad::find($this->id);
            if (!$ad || $ad->user_id !== $userId) {
                abort(403, 'Unauthorized action.');
            }

            // Handle different property updates
            $this->handlePropertyUpdate($ad, $name, $value);
        }
        if ($name === 'ad_type_id') {
            $this->loadCategories($this->ad_type_id);
            if ($this->id) {
                $this->updateAdTypeRelatedData($this->ad, $value);
            }
        }
    }


    /**
     * Creates a new ad and assigns an ad type if only one exists.
     */
    public function createNewAd($name, $value, $userId)
    {
        $ad = Ad::create([
            $name => $value,
            'user_id' => $userId,
            'ad_type_id' => $this->ad_type_id
        ]);

        $this->id = $ad->id;
        $this->updateAdSlug($ad, $value);
        $this->loadAdDetails($this->id);

        //Dispatch the "ad-created" event to parent component
        $this->dispatch('ad-created', id: $ad->id);
    }

    /**
     * Handles different types of property updates on an existing ad.
     */
    public function handlePropertyUpdate(Ad $ad, $name, $value)
    {
        if (str_starts_with($name, 'tags.')) {
            $this->updateTags($ad, $name, $value);
        } elseif ($name === 'title' && !$this->requiresAdminApproval($ad)) {
            $ad->update(['title' => $value]);
            $this->updateAdSlug($ad->fresh(), $value);
        }elseif ($name === 'sub_category_id') {
            $this->checkCookie($value, 'category_id');
        }elseif ($name === 'child_category_id') {
            $this->checkCookie($value, $name);
        }elseif (Str::startsWith($name, 'description') && !$this->requiresAdminApproval($ad)) {
            $ad->update(['description_tiptap' => $value ? $value : []]);
        }
        else {
            $ad->update([$name => $value ? $value : null]);
        }
        if (is_null($ad->sub_category_id)) {
            $ad->update(['sub_category_id' => $value ? $value : null]);
        }
        if ($name === 'ad_type_id') {
            $this->updateAdTypeRelatedData($this->ad, $value);
        }
        if($name == 'category_id' || $name == 'sub_category_id' || $name == 'child_category_id')
        $this->dispatch('ad-updated');

    }

    /**
     * Checks if a cookie indicating age verification exists for the given value.
     *
     * If the cookie does not exist, it triggers age verification.
     * Otherwise, it proceeds to identity verification.
     *
     * @param mixed $value The value used to check the cookie and perform verification.
     * @return void
     */
    public function checkCookie($value, $fieldName)
    {
        if (!Cookie::has("age_verified_{$value}")) {
            $this->verifyAge($value, $fieldName);
        } else {
            $this->verifyIdentity($value, $fieldName);
        }
    }

    /**
     * Updates the "tags" property if an array element is updated.
     */
    public function updateTags($ad, $name, $value)
    {
        $index = explode('.', $name)[1];
        $tags = $this->tags;
        $tags[$index] = $value;
        $this->tags = $tags;
        $ad->update(['tags' => json_encode($tags)]);
    }

    /**
     * Updates Ad type-related properties.
     */
    public function updateAdTypeRelatedData(Ad $ad, $value)
    {
        $this->disable_condition = $ad->adType?->disable_condition;
        $this->disable_price_type = $ad->adType?->disable_price_type;
        $this->sub_category_id = null;
        $this->child_category_id = null;
        $this->showMainCategories = true;
        $this->updateLocation($ad, $value);
        $this->enableECommerce = $ad->adType?->marketplace == ONLINE_SHOP_MARKETPLACE ?? false;
    }

    /**
     * Load Ad type-related properties.
     */
    public function loadAdTypeRelatedData(Ad $ad, $value)
    {
        $this->disable_condition = $ad->adType?->disable_condition;
        $this->disable_price_type = $ad->adType?->disable_price_type;
        $this->showMainCategories = true;
        $this->updateLocation($ad, $value);
        $this->enableECommerce = $ad->adType?->marketplace == ONLINE_SHOP_MARKETPLACE ?? false;
    }
    /**
     * Updates sub-category and category linkage.
     */
    public function updateSubCategory($value)
    {
        $this->ad->category_id = (int) $this->sub_category_id;
        $this->ad->save();
        $this->child_category_id = null;
    }

    public function checkRequiredFieldsFilled()
    {
        $isFilled = false;
        foreach ($this->requiredFields as $field) {
            if($field == "description_tiptap"){
                $isFilled = isset($this->description_tiptap) && count($this->description_tiptap);
                break;
            }
            if (trim($this->$field) !== '' && (!empty(trim($this->$field))) && (!is_null(trim($this->$field)))) {
                $isFilled = true;
            } else {
                $isFilled = false;
                break;
            }
        }
        if (isset($this->display_phone) && $this->display_phone && (!empty($this->phone_number))) {
            $isFilled = $isFilled ? true : false;
        }
        if (isset($this->display_whatsapp) && $this->display_whatsapp && (!empty($this->whatsapp_number))) {
            $isFilled = $isFilled ? true : false;
        }
        $this->dispatch('required-fields-filled', isFilled: $isFilled);
    }

    public function save()
    {
        $this->validate();

        $adData = $this->only([
            'title',
            'category_id',
            'description',
            'price',
            'price_type_id',
            'condition_id',
            'display_phone',
            'phone_number',
            'for_sale_by',
            'tags',
            'price_suffix',
            'offer_price',
            'whatsapp_number',
            'display_whatsapp',
        ]);

        if ($this->id) {
            $ad = Ad::find($this->id);
            $ad->update($adData);
        }

        $this->updateAdSlug($ad, $this->title);

        $this->dispatch('ad-saved', id: $ad->id);
        return $this->redirect('/ads');
    }

    public function render()
    {
        return view('livewire.ad.post-ad.ad-detail');
    }
}
