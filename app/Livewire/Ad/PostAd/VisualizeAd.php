<?php

namespace App\Livewire\Ad\PostAd;

use App\Forms\Components\ImageProperties;
use App\Models\Ad;
use App\Settings\AdSettings;
use Livewire\Attributes\On;
use Livewire\Attributes\Reactive;
use Livewire\Component;
use Filament\Forms\Form;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Support\HtmlString;
use Closure;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;

class VisualizeAd extends Component implements HasForms
{
    use InteractsWithForms;

    // Properties related to Ad and its images
    #[Reactive]
    public $id;
    public Ad $record;
    #[Reactive] public $isLastStep;

    public ?array $data = [];
    public bool $isDisabled = true;

    /**
     * Mount the component and fetch the images associated with the ad.
     */
    public function mount()
    {
        $this->record = Ad::find($this->id);
        $adSettings = app(AdSettings::class);

        $imageLimit = $adSettings->image_limit ?? 0;

        // Check if image_properties is null
        // Adjust the logic to work with image_limit instead of actual image count
        $image_properties = [];

        // Determine the loop limit based on the smaller of images count or image limit
        $loopLimit = count($this->record->images());
        for ($index = 0; $index < $loopLimit; $index++) {
            $imgIndex = $index + 1;
            if (!isset($this->record->image_properties["{$imgIndex}"])) {
                $image_properties["{$imgIndex}"] = $this->record->title;
            } else {
                $image_properties["{$imgIndex}"] = $this->record->image_properties["{$imgIndex}"];
            }
        }
        // Update the record's image_properties with default data if there are any images to consider
        if ($loopLimit > 0) {
            $this->record->image_properties = $image_properties;
            $this->record->save();
        }
        $this->form->fill($this->record->attributesToArray());
        $this->checkRequiredFieldsFilled();
    }

    public function form(Form $form): Form
    {
        $adSettings = app(AdSettings::class);

        $imageLimit = $adSettings->image_limit ?? 0;
        $adSettings = app(AdSettings::class);
        return $form
            ->schema([
                SpatieMediaLibraryFileUpload::make('ad_images')
                    ->maxSize(maxUploadFileSize())
                    ->label(__('messages.t_upload_photos'))
                    ->validationAttribute(__('messages.t_upload_photos'))
                    ->collection('ads')
                    ->multiple()
                    ->storeFiles(false)
                    ->live()
                    ->image()
                    ->extraAlpineAttributes(['shouldAppendFiles' => false])
                    ->extraAttributes(['class' => 'disable-ad-upload'])
                    ->deleteUploadedFileUsing(function () use ($adSettings) {
                        if ($adSettings->allow_image_alt_tags) {
                            array_pop($this->data['image_properties']);
                        }
                    })
                    ->visible( (app(AdSettings::class)->admin_approval_required ? true : false) && ($this->record && $this->record->status->value != 'draft')),
                SpatieMediaLibraryFileUpload::make('ads')
                    ->maxSize(maxUploadFileSize())
                    ->label(__('messages.t_upload_photos'))
                    ->validationMessages([
                        'uploaded' => 'The ' . __('messages.t_upload_photos') . ' failed to upload1.',
                    ])

                    ->validationAttribute(__('messages.t_upload_photos'))
                    ->live()
                    ->image()
                    ->multiple()
                    ->live()
                    ->deleteUploadedFileUsing(function () use ($adSettings) {
                        if ($adSettings->allow_image_alt_tags) {
                            array_pop($this->data['image_properties']);
                        }
                    })
                    ->afterStateUpdated(function (Set $set, Get $get) use ($adSettings) {
                        if ($adSettings->allow_image_alt_tags) {
                            $imageProperties = $get('image_properties');
                            $imageProperties[is_null($imageProperties) ? 1 : count($imageProperties) + 1] = $this->record->title;
                            $this->data['image_properties'] = $imageProperties;
                        }
                    })
                    ->label(function () use ($adSettings) {
                        $saveButton = '
                        <div class="flex flex-col sm:flex-row  gap-x-2 justify-between items-end sm:items-center mb-1">
                            <span class="text-gray-950  dark:text-white font-medium">' . __("messages.t_upload_hint") . '</span>
                        </div>';

                        return new HtmlString($saveButton);
                    })
                    ->hint(function(){
                        $saveButton='<div><button wire:click="uploadPhotos" class=" cursor-pointer whitespace-nowrap px-3 py-2 bg-primary-600 classic:border classic:border-black  text-black rounded-xl mb-1 font-semibold"';
                        $saveButton .= $this->isDisabled ? 'style="opacity:0.7;cursor: default;" ' : ' type="button"';
                        $saveButton .= $this->isDisabled ? 'disabled' : '';
                        $saveButton .= '>' . __('messages.t_save_order') . '</button></div>';
                        return new HtmlString($saveButton);
                    })
                    ->markAsRequired(false) // Removes the asterisk
                    //if admin approval required for uploading images then store images in pending collections
                    ->collection( (app(AdSettings::class)->admin_approval_required ? true : false) && ($this->record && $this->record->status->value != 'draft') ? 'pending' : 'ads')
                    ->required(function (Get $get,$livewire) use ($adSettings) {
                        if ($adSettings->can_post_without_image) {
                            return false;
                        }
                        if ($adSettings->admin_approval_required) {
                            return count($get('ad_images')) == 0 ? true : false;
                        }
                        return true;
                    })
                    ->disabled(fn($livewire)=>$livewire->getImageLimit() <= 0)
                    ->maxFiles(function (Get $get) use ($adSettings) {

                        $imageLimit = getSubscriptionSetting('status') && getActiveSubscriptionPlan() ? getActiveSubscriptionPlan()->images_limit : $adSettings->image_limit;
                        if (app(AdSettings::class)->admin_approval_required) {
                            return $imageLimit - count($get('ad_images'));
                        }
                        return $imageLimit;
                    })
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
                    ->helperText(function () use ($adSettings) {
                        $imageLimit = getSubscriptionSetting('status') && getActiveSubscriptionPlan() ? getActiveSubscriptionPlan()->images_limit : $adSettings->image_limit;
                        $content =__('messages.t_add_photos_to_ad', ['image_limit' => $imageLimit]);
                        if($adSettings->can_post_without_image){
                            $content.="<div class='dark:text-gray-400'>
                            <p class='mt-2'>". __('messages.t_skip_upload_step')."</p>
                            <span x-on:click='showModal = true' class='text-primary-600 cursor-pointer font-semibold hover:underline'>".
                                 __('messages.t_view_placeholder')
                            ."</span>
                            </div>";
                        }
                        return new HtmlString($content);
                    })
                    ->appendFiles(),
                ImageProperties::make('image_properties')
                    ->visible(fn(): bool => $adSettings->allow_image_alt_tags)
                    ->helperText(__('messages.t_provide_descriptive_alt_text')),
                TextInput::make('video_link')
                    ->visible(function () {
                        if (getSubscriptionSetting('status') && getActiveSubscriptionPlan()) {
                            return getActiveSubscriptionPlan()->video_posting;
                        }
                        return true;
                    })
                    ->regex('/^(?:https?:\/\/)?(?:m\.|www\.)?(?:youtu\.be\/|youtube\.com\/(?:embed\/|v\/|watch\?v=|watch\?.+&v=))((\w|-){11})(?:\S+)?$/')
                    ->label(__('messages.t_youtube_video_link'))
                    ->url()
                    ->live(onBlur: true)
                    ->suffixIcon('heroicon-m-video-camera')
                    ->placeholder(__('messages.t_example_youtube_link'))
                    ->hint(__('messages.t_add_youtube_video_hint'))


            ])
            ->statePath('data')
            ->model($this->record);
    }

    /**
     * Proceed to the next step after verifying there's at least one image.
     */
    #[On('next-clicked')]
    public function next()
    {
        $this->validate();
        $this->uploadPhotos(false);

        if ($this->isLastStep) {
            if ($this->record && $this->record->status->value != 'draft') {
                $this->dispatch('preview-ad');
            } else {
                $this->dispatch('publish-clicked');
            }
        } else {
            $this->dispatch('next-step');
        }
    }

    public function uploadPhotos($showNotification = true): void
    {
        try {
            $oldMedia = $this->record->media;
            $data = $this->form->getState();
            $data['image_properties'] = $this->data['image_properties'];
            $this->record->update($data);
            //if admin approval required for uploading images
            if (app(AdSettings::class)->admin_approval_required && ($this->record && $this->record->status->value != 'draft')) {
                $latestMedia = $this->record->media;
                $diffMedias = $latestMedia->diff($oldMedia);

                foreach ($diffMedias as $media) {
                    $media->update(['collection_name' => 'ads']);
                    Artisan::call('media-library:regenerate --ids=' . $media->id);
                }
            }

            // Send a success notification only if $showNotification is true
            if ($showNotification) {
                Notification::make()
                    ->title(__('messages.t_common_success'))
                    ->success()
                    ->send();
            }
        } catch (\Exception $e) {
            Notification::make()
                ->title($e->getMessage())
                ->danger()
                ->send();
        }
    }

    public function updated($name): void
    {
        if ($name == 'data.video_link' || str_starts_with($name, 'data.image_properties')) {
            $this->uploadPhotos();
        }
        $this->checkRequiredFieldsFilled();
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
        $isFilled = false;

        if (!count($this->requiredFields)) {
            $isFilled = true;
        }
        foreach ($this->requiredFields as $field) {
            $fieldDetail = explode('.', $field);
            if (count($this->data[$fieldDetail[1]])) {
                $isFilled = true;
            } else {
                $isFilled = false;
                break;
            }
        }
        $this->isDisabled = !$isFilled;
        $this->dispatch('required-fields-filled', isFilled: $isFilled);
    }

    public function getImageLimit(){
        $adSettings=app(AdSettings::class);
        return getSubscriptionSetting('status') && getActiveSubscriptionPlan() ? getActiveSubscriptionPlan()->images_limit : $adSettings->image_limit;
    }
    public function removeImageProperty($key)
    {
        if (isset($this->data['image_properties'][$key])) {
            unset($this->data['image_properties'][$key]);
        }
        $this->record->image_properties = $this->data['image_properties'];
        $this->record->save();
    }
    /**
     * Render the component view.
     */
    public function render()
    {
        return view('livewire.ad.post-ad.visualize-ad');
    }
}
