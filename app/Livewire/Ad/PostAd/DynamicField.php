<?php

namespace App\Livewire\Ad\PostAd;

use App\Models\Ad;
use App\Models\AdFieldValue;
use App\Models\Category;
use App\Models\CategoryField;
use App\Models\Field;
use Closure;
use Filament\Forms\Components\{Checkbox, DatePicker, DateTimePicker, TextInput, Radio, Select, TimePicker, Textarea};
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;

#[On('ad-updated')]
class DynamicField extends Component implements HasForms
{
    use InteractsWithForms;

    #[Reactive]
    public $id;
    public ?array $data = [];
    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->initializeForm();
        $this->populateDataFromSavedValues();
        $this->checkRequiredFieldsFilled();
    }

    protected function initializeForm(): void
    {
        $fields = $this->getFieldsForAd() ?? [];
        foreach ($fields as $fieldData) {
            $field = $fieldData->field;
            if($field ){
                if (in_array($field->type->value,[ "tagsinput"]) ) {
                    $this->data[$field->id] = [];
                } else {
                    $this->data[$field->id] = null;
                }
            }
        }
    }
    /**
     * Populate data property from saved values.
     */
    protected function populateDataFromSavedValues(): void
    {

        $savedValues = AdFieldValue::where('ad_id', $this->id)
            ->pluck('value', 'field_id')->toArray();
        foreach ($savedValues as $fieldId => $value) {
            $field = Field::find($fieldId);
            if ($field) {
                if (in_array($field->type->value, ["tagsinput"])) {
                    $this->data[$field->id] = $value ?? [];
                } else {
                    $this->data[$field->id] = $value;
                }
            }
        }
    }

    protected function getFieldTemplateMappings($categoryId)
    {
        $templateFieldRecord = Category::whereId($categoryId)->with('fieldTemplate.fieldTemplateMappings.field')->first();
        $templateFields = $templateFieldRecord?->fieldTemplate?->fieldTemplateMappings?->sortBy('order') ?? collect([]);
        return $templateFields;
    }

    /**
     * Get fields for the Ad.
     */
    protected function getFieldsForAd()
    {
        $ad = Ad::find($this->id);
        if (!$ad)
            return collect([]);

        $subCategoryFields = collect([]);
        $mainCategoryFields = collect([]);
        $categoryFields = collect([]);
        $categoryId = $ad->category_id;
        $mainCategoryId = $ad->main_category_id;
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

    public function form(Form $form): Form
    {
        $fields = $this->getFieldsForAd();
        $components = $this->mapFieldsToComponents($fields);
        return $form->schema($components)->statePath('data');
    }

    /**
     * Map fields to form components.
     */
    protected function mapFieldsToComponents($fieldData)
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
                $fieldType = $field->type->value;
                switch ($fieldType) {
                    case 'text':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = TextInput::make($field->id)->label($field->name)->label($field->name)->required($field->required)->live(debounce: 500)->helperText($field->helpertext)->maxLength($field->max_length)->minLength($field->min_length)->alpha($field->validation_type?->value == 'alpha')->alphaNum($field->validation_type?->value == 'alpha_numeric')->alphaDash($field->validation_type?->value == 'alpha_dash')
                        //TODO: add validation with spaces
                        // ->rules([
                        //     fn (): Closure => function (string $attribute, $value, Closure $fail) use($field) {
                        //         if ($field->validation_type?->value=='alpha_space'&&(!ctype_alpha(str_replace(' ', '', $value)))) {
                        //             $fail(__('messages.t_letter_space_validation',['attribute'=>$field->name]));
                        //         }
                        //     },
                        // ])
                        ;
                        break;
                    case 'select':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = Select::make($field->id)->label($field->name)->options($field->options)->required($field->required)->live(debounce: 500)->helperText($field->helpertext);
                        break;
                    case 'checkbox':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = Checkbox::make($field->id)->label($field->name)->required($field->required)->live(debounce: 500)->helperText($field->helpertext);
                        break;
                    case 'radio':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = Radio::make($field->id)->label($field->name)->options($field->options)->required($field->required)->live(debounce: 500)->helperText($field->helpertext);
                        break;
                    case 'datetime':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = DateTimePicker::make($field->id)->label($field->name)->required($field->required)->live(debounce: 500)->helperText($field->helpertext);
                        break;
                    case 'date':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = DatePicker::make($field->id)->label($field->name)->required($field->required)->live(debounce: 500)->helperText($field->helpertext);
                        break;
                    case 'time':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = TimePicker::make($field->id)->label($field->name)->required($field->required)->live(debounce: 500)->helperText($field->helpertext);
                        break;
                    case 'textarea':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = Textarea::make($field->id)->label($field->name)->required($field->required)->live(debounce: 500)->helperText($field->helpertext);
                        break;
                    case 'tagsinput':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = TagsInput::make($field->id)->label($field->name)->required($field->required)->live(debounce: 500)->helperText($field->helpertext);
                        break;
                    case 'number':
                        ${strlen($groupName) ? 'sectionComponents' : 'components'}[] = TextInput::make($field->id)->numeric()->label($field->name)->required($field->required)->live(debounce: 500)->helperText($field->helpertext)->maxValue($field->max_length)->minValue($field->min_length);
                        break;
                }
            }
            if (count($sectionComponents))
                $components[] = Section::make($groupName)->schema($sectionComponents)->collapsible()->collapsible();
        }
        return $components;
    }

    /**
     * Handle updates to component properties.
     */
    public function updated($name, $value)
    {
        $userId = auth()->id();
        if (!$userId)
            abort(403, 'Unauthorized action.');
        $this->checkRequiredFieldsFilled();
        $this->saveFieldValue($name, $value, $userId);
    }

    /**
     * Save field value to the database.
     */
    protected function saveFieldValue($name, $value, $userId)
    {
        $this->form->getState();
        $fieldName = str_replace('data.', '', $name);
        $fieldId = explode('.', $fieldName);
        $field = Field::find(\Arr::first($fieldId));

        if (!$field)
            return;
        $ad = Ad::find($this->id);
        if (!$ad || $ad->user_id !== $userId)
            abort(403, 'Unauthorized action.');
        AdFieldValue::updateOrCreate(['ad_id' => $this->id, 'field_id' => $field->id], ['value' => $value]);
    }

    #[On('validate-dynamic-fields')]
    public function validateDynamicFields()
    {
        $this->form->getState();
        //redirect to next page after validate dynamic fields form
        $this->dispatch('next-step');
    }
    public function getRequiredFieldsProperty()
    {
        $requiredFields = [];
        $rules = $this->getRules();
        foreach ($rules as $field => $rule) {
            if (is_array($rule) && in_array('required', $rule)) {
                $requiredFields[] = $field;
            } elseif ($rule == 'required') {
                $requiredFields[] = $field;
            }
        }
        return $requiredFields;
    }

    public function checkRequiredFieldsFilled()
    {
        $isFilled = true;
        if(!count($this->requiredFields)){
            $isFilled = true;
        }
        foreach ($this->requiredFields as $field) {
            $fieldDetail = explode('.', $field);
            if (isset($this->data[$fieldDetail[1]]) && is_array($this->data[$fieldDetail[1]]) && (count($this->data[$fieldDetail[1]]))) {
                $isFilled = true;
            } elseif (isset($this->data[$fieldDetail[1]])&&(!is_array($this->data[$fieldDetail[1]])) && trim($this->data[$fieldDetail[1]]) !== '') {
                $isFilled = true;
            } else {
                $isFilled = false;
                break;
            }
        }

        $this->dispatch('dynamic-fields-filled', isFilled: $isFilled);
    }
    /**
     * Render the component view.
     */
    public function render(): View
    {
        return view('livewire.ad.post-ad.dynamic-field');
    }
}
