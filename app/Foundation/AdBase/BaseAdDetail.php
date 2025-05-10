<?php

namespace App\Foundation\AdBase;

use App\Foundation\AdBase\Traits\AdMethods;
use App\Foundation\AdBase\Traits\AdProperties;
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

abstract class BaseAdDetail extends Component implements HasForms
{
    use InteractsWithForms, AdProperties, AdMethods, LocationFunctions;

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

    protected function loadCategories($adTypeId)
    {
        if($adTypeId) {
            $this->categories = Category::when($this->ad, function ($query) use($adTypeId) {
                $query->where('ad_type_id', $adTypeId);
            })
                ->with('subcategories.subcategories')
                ->whereNull('parent_id')
                ->get();

        }else{
            $this->categories = Category::with('subcategories.subcategories')
                ->whereNull('parent_id')
                ->get();
        }

    }

    protected function loadAdDetails($id)
    {
        $this->ad = $this->model::with(['adType','category'])->find($id);

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

    /**
     * Display the ad type select field if there are multiple ad types.
     * @return bool
     */
    public function canDisplayAdTypeSelect(): bool
    {
        return AdType::get()->count() > 1;
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

        $adDetails = [
            'title' => $this->ad->title,
            'ad_type_id' => $this->ad->ad_type_id,
            'description' => $this->ad->description,
            'condition_id' => $this->ad->condition_id,
            'price_suffix' => $this->ad->price_suffix,
            'price_type_id' => $this->ad->price_type_id,
            'offer_price' => $this->ad->offer_price,
            'price' => $this->ad->price,
            'tags' => $this->ad->tags ?? [],
            'for_sale_by' => $this->ad->for_sale_by,
            'display_phone' => $this->ad->display_phone,
            'phone_number' => $this->ad->phone_number,
            'display_whatsapp' => $this->ad->display_whatsapp,
            'whatsapp_number' => $this->ad?->whatsapp_number,
            'main_category_id' => $this->ad->main_category_id,
            'parent_category' => $this->ad->main_category_id,
            'sub_category_id' => $this->ad->category_id,
            'category_id' => $this->ad->category_id,
            'child_category_id' => $this->ad->child_category_id,
            'description_tiptap' => $this->ad->description_tiptap ?? [],
            'disable_condition' => $this->ad->adType?->disable_condition,
            'disable_price_type' => $this->ad->adType?->disable_price_type
        ];

        if ($this->enableECommerce) {
            $adDetails = array_merge($adDetails, [
                'sku' => $this->ad->sku,
                'return_policy_id' => $this->ad->return_policy_id,
                'enable_cash_on_delivery' => $this->ad->enable_cash_on_delivery,
            ]);
        }

        $this->fill($adDetails);

        if ($this->ad->ad_type_id) {
            $this->setAdTypeDetails();
        }

        if ($this->ad->main_category_id) {
            $this->showMainCategories = false;
        }

    }

    protected function setAdTypeDetails()
    {
        if ($this->ad->type_id) {
            $this->disable_condition = $this->ad->adType?->disable_condition;
            $this->disable_price_type = $this->ad->adType?->disable_price_type;

        }else{
            $this->disable_condition = false;
            $this->disable_price_type = false;
        }
    }

    public function selectMainCategory($categoryId)
    {
        $this->ad->main_category_id = $categoryId;
        $this->ad->save();

        $this->parent_category = $categoryId;
        $this->main_category_id = $this->ad->main_category_id;
        $this->category_id = null;
        $this->showMainCategories = false;
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

    public function getCurrentCategory()
    {
        return $this->id ? Category::whereHas('ads', fn($query) => $query->whereId($this->id))->first() : null;
    }

    protected function getForms(): array
    {
        return [
            'titleInput',
            'detailForm',
        ];
    }

    public function updated($name, $value)
    {
        $this->checkRequiredFieldsFilled();
        $userId = auth()->id();

        // Ensure user is authenticated
        if (!$userId) {
            abort(403, 'Unauthorized action.');
        }

        $this->validateOnly($name);

        if (!$this->id) {
            $this->createNewAd($name, $value, $userId);
        } else {
            $this->updateExistingAd($name, $value, $userId);
        }

        if($name == 'ad_type_id'){
            // $this->main_category_id = null;
            // $this->parent_category = null;
            // $this->sub_category_id = null;
            // $this->category_id = null;
            // // $this->showMainCategories = true;
        }

        $this->enableECommerce = $this->ad->adType?->marketplace == ONLINE_SHOP_MARKETPLACE ?? false;

        $this->dispatch('ad-created', ['id' => $this->id]);
    }

    protected function createNewAd($name, $value, $userId)
    {
        $ad = $this->model::create([
            $name => $value,
            'user_id' => $userId
        ]);

        $this->id = $ad->id;
        $this->updateAdSlug($ad, $value);
        $this->loadAdDetails($this->id);
    }

    protected function updateExistingAd($name, $value, $userId)
    {
        $ad = $this->model::find($this->id);

        // Ensure ad exists and belongs to the user
        if (!$ad || $ad->user_id != $userId) {
            abort(403, 'Unauthorized action.');
        }

        if (str_starts_with($name, 'tags.')) {
            $this->updateTags($name, $value, $ad);
        } else {
            $this->updateAdFields($name, $value, $ad);
        }
    }

    protected function updateTags($name, $value, $ad)
    {
        $index = explode('.', $name)[1];
        $tags = $this->tags;
        $tags[$index] = $value;
        $this->tags = $tags;

        // Update tags in the database
        $ad->update(['tags' => json_encode($tags)]);
    }

    protected function updateAdFields($name, $value, $ad)
    {
        $ad->update([$name => $value]);

        if ($name === 'title') {
            // Update slug when the title is updated
            $this->updateAdSlug($ad->fresh(), $value);
        }

        if($name == 'main_category_id'){
            $this->sub_category_id = null;
            $this->category_id = null;
            $this->child_category_id = null;
        }
        if ($name === 'ad_type_id') {
            $this->disable_condition = $ad->adType?->disable_condition;
            $this->disable_price_type = $ad->adType?->disable_price_type;
            $this->showMainCategories = true;
            // Update location if the category has a default location
            $this->updateLocation($ad, $value);
        }
    }

    public function checkRequiredFieldsFilled()
    {
        $isFilled = false;
        foreach ($this->requiredFields as $field) {
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
        // dump($isFilled);
        $this->dispatch('required-fields-filled', isFilled: $isFilled);
    }

    public function render()
    {
        return view('livewire.ad.post-ad.ad-detail');
    }
}
