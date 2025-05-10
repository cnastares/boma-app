<?php

namespace App\Livewire\User;

use App\Models\City;
use App\Models\Country;
use App\Models\State;
use App\Models\User;
use App\Settings\UserSettings;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Livewire\Component;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use App\Settings\GeneralSettings;
use App\Settings\LocationSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

/**
 * MyProfile Component.
 * Allows users to update and manage their profile information.
 */
class MyProfile extends Component implements HasForms
{
    public User $user;
    use InteractsWithForms;
    use SEOToolsTrait;

    public ?array $data = [];
    public ?array $oldData = [];

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';
    public $isDisabled = true;
    public $imageChanged = false;
    /**
     * Mount lifecycle hook.
     * Fetches the user's data and populates the form.
     */
    public function mount(User $user): void
    {
        $this->populateUserData();
        $this->setSeoData();
    }

    /**
     * Populate user data into the form.
     */
    protected function populateUserData()
    {
        // Fetch the authenticated user's data
        $user = Auth::user();

        $this->user = $user;
        $userData = $this->user->attributesToArray();
        if (!isset($userData['country'])) {
            $userData['country'] = app(LocationSettings::class)->default_country ?? 'US';
        }
        $this->form->fill($userData);
        $this->oldData = $this->user->only(['name', 'email', 'phone_number', 'about_me', 'date_of_birth', 'gender']);
    }

    /**
     * Handle the form submission and update the user's profile.
     */
    public function create()
    {
        $user = Auth::user();
        $data = $this->form->getState();
        $user->fill($data);
        $user->save();

        Notification::make()
            ->title(__('messages.t_profile_updated'))
            ->success()
            ->send();
    }

    /**
     * Defines the form schema for updating the user's profile.
     */
    public function form(Form $form): Form
    {
        $userId = auth()->id();
        $path = "users/user-{$userId}/profile";

        $fileUpload = $this->configureProfileImageUpload($path);

        return $form
            ->schema($this->buildFormSchema($fileUpload))
            ->statePath('data')
            ->model($this->user);
    }

    /**
     * Configures the profile image upload component.
     */
    protected function configureProfileImageUpload(string $path): FileUpload
    {

        $fileUpload = SpatieMediaLibraryFileUpload::make('profile_image')
            ->label(__('messages.t_profile_image'))
            ->maxSize(maxUploadFileSize())
            ->collection('profile_images')
            ->visibility('private')
            ->image()
            ->required()
            ->live(debounce: 500)
            ->imageEditor();

        $storageType = config('filesystems.default');
        if ($storageType == 's3') {
            $fileUpload->disk($storageType);
        }

        return $fileUpload;
    }

    /**
     * Builds the form schema for user profile update.
     */
    protected function buildFormSchema(FileUpload $fileUpload): array
    {
        return [
            Section::make(__('messages.t_personal_information'))
                ->description(__('messages.t_update_name_bio_image'))
                ->schema($this->personalInformationSchema($fileUpload))
                ->columns(2),

            Section::make(__('messages.t_contact_info'))
                ->description(__('messages.t_update_email_phone'))
                ->schema($this->contactInformationSchema())
                ->columns(2),
            Section::make(__('messages.t_banner_info'))
                ->hidden(true) //Enable if this banner feature has been subscribed by user
                ->collapsible()
                ->description(__('messages.t_banner_description'))
                ->schema($this->bannerInformationSchema())
                ->columns(2),
            Section::make(__('messages.t_social_media_info'))
                ->visible(function () {
                    if (getSubscriptionSetting('status') && getActiveSubscriptionPlan()) {
                        return getActiveSubscriptionPlan()->enable_social_media_links;
                    }
                    return false;
                }) //Enable if this social media feature has been subscribed by user
                ->collapsible()
                ->description(__('messages.t_social_media_description'))
                ->schema($this->socialMediaInformationSchema())
                ->columns(2),
            Section::make(__('messages.t_business_hours_info'))
                ->visible(function () {
                    if (getSubscriptionSetting('status') && getActiveSubscriptionPlan()) {
                        return getActiveSubscriptionPlan()->enable_business_hours;
                    }
                    return false;
                }) //Enable if this social media feature has been subscribed by user
                ->collapsible()
                ->description(__('messages.t_business_hours_description'))
                ->schema($this->businessHoursSchema())
                ->columnSpanFull(),

        ];
    }

    /**
     * Personal information schema.
     */
    protected function personalInformationSchema(FileUpload $fileUpload): array
    {
        return [
            TextInput::make('name')
                ->live(debounce: 500)
                ->label(__('messages.t_name'))
                ->required()
                ->maxLength(255),
            Textarea::make('about_me')
                ->visible(function () {
                    if (getSubscriptionSetting('status') && getActiveSubscriptionPlan()) {
                        return getActiveSubscriptionPlan()->enable_user_profile_description;
                    }
                    return true;
                })
                ->live(debounce: 500)
                ->label(__('messages.t_about_me'))
                ->maxLength(80)
                ->placeholder(__('messages.t_tell_us_bit')),
            $fileUpload,
            DatePicker::make('date_of_birth')
                ->label(__('messages.t_date_of_birth'))
                ->live(debounce: 500),
            Select::make('gender')
                ->label(__('messages.t_gender'))
                ->live(debounce: 500)
                ->options([
                    'male' => __('messages.t_male'),
                    'female' => __('messages.t_female'),
                    'others' => __('messages.t_others'),
                ])
        ];
    }

    /**
     * Contact information schema.
     */
    protected function contactInformationSchema(): array
    {
        return [
            TextInput::make('email')
                ->live(debounce: 500)
                ->when(app(UserSettings::class)?->can_edit_registered_email == false, function ($component) {
                    $component->hintIcon('heroicon-o-exclamation-triangle', tooltip: (app(UserSettings::class)?->can_edit_registered_email == false) ? __('messages.t_disabled_registered_email_tooltip') : '');
                })
                ->unique(ignoreRecord: true)
                ->disabled(app(UserSettings::class)?->can_edit_registered_email == false)
                ->label(__('messages.t_email'))
                ->required()
                ->email()
                ->maxLength(255),
            PhoneInput::make('phone_number')
                ->initialCountry(app(GeneralSettings::class)->default_mobile_country ?? 'us')
                ->live(debounce: 500)
                ->unique(User::class, 'phone_number', ignoreRecord: true)
                ->label(__('messages.t_phone_number'))
                ->placeholder(__('messages.t_enter_phone_number')),
            PhoneInput::make('whatsapp_number')
                ->initialCountry(app(GeneralSettings::class)->default_mobile_country ?? 'us')
                ->live(debounce: 500)
                ->unique(User::class, 'whatsapp_number', ignoreRecord: true)
                ->label(__('messages.t_whatsapp_number'))
                ->placeholder(__('messages.t_enter_phone_number')),
            Grid::make([
                'default' => 2,
            ])
                ->visible(function () {
                    if (getSubscriptionSetting('status') && getActiveSubscriptionPlan()) {
                        return getActiveSubscriptionPlan()->enable_location;
                    }
                    return false;
                })
                ->schema([
                    Select::make('country')
                        ->preload()
                        ->searchable()
                        ->label(__('messages.t_country'))
                        ->options(
                            !empty($allowedCountries) ?
                                Country::whereIn('iso2', $allowedCountries)->pluck('name', 'iso2')->toArray() :
                                Country::pluck('name', 'iso2')->toArray()
                        )
                        ->live(debounce: 500)
                        ->afterStateUpdated(function (Set $set) {
                            $set('state', null);
                            $set('city', null);
                        }),

                    Select::make('state')
                        ->preload()
                        ->searchable()
                        ->label(__('messages.t_state'))
                        ->options(function (Get $get) {
                            $countryIso2 = $get('country');
                            $countryId = Country::where('iso2', $countryIso2)->first()?->id;

                            if (!$countryId) {
                                return [];
                            }
                            return State::where('country_id', $countryId)->orderBy('name')->pluck('name', 'id')->toArray();
                        })
                        ->live(debounce: 500)
                        ->afterStateUpdated(function (Set $set) {
                            $set('city', null);
                        }),

                    Select::make('city')
                        ->preload()
                        ->searchable()
                        ->label(__('messages.t_city'))
                        ->options(function (Get $get) {
                            $stateId = $get('state');
                            if (!$stateId) {
                                return [];
                            }
                            return City::where('state_id', $stateId)->orderBy('name')->pluck('name', 'id')->toArray();
                        })
                        ->live(debounce: 500)
                ])
        ];
    }

    /**
     * Banner Information.
     */
    protected function bannerInformationSchema(): array
    {
        return [
            SpatieMediaLibraryFileUpload::make('banner_image')
                ->maxSize(maxUploadFileSize())
                ->label(__('messages.t_user_banner_image'))
                ->collection('user_banner_images')
                ->responsiveImages()
                ->customProperties(function (Get $get) {
                    return [
                        'alternative_text' => $get('alternative_text'),
                        'link' => $get('link'),
                    ];
                })
                ->imageEditor()
                ->imageResizeMode('cover')
                ->imageResizeTargetWidth('1480')
                ->imageResizeTargetHeight('350')
                ->helperText(__('messages.t_user_banner_image_helpertext')),
            TextInput::make('alternative_text')
                ->formatStateUsing(fn(Component $livewire) => $livewire->user->bannerImage?->getCustomProperty('alternative_text'))
                ->live()
                ->label(__('messages.t_alternative_text')),
            TextInput::make('link')
                ->formatStateUsing(fn(Component $livewire) => $livewire->user->bannerImage?->getCustomProperty('link'))
                ->label(__('messages.t_link'))
                ->url(),
        ];
    }

    /**
     * Social Media Information.
     */
    protected function socialMediaInformationSchema(): array
    {
        return [
            TextInput::make('facebook_link')
                ->live(debounce: 500)
                ->label(__('messages.t_facebook_link'))
                ->placeholder(__('messages.t_facebook_placeholder'))
                ->url()
                ->hint(__('messages.t_facebook_hint')),

            TextInput::make('twitter_link')
                ->live(debounce: 500)
                ->label(__('messages.t_twitter_link'))
                ->placeholder(__('messages.t_twitter_placeholder'))
                ->url()
                ->hint(__('messages.t_twitter_hint')),

            TextInput::make('instagram_link')
                ->live(debounce: 500)
                ->label(__('messages.t_instagram_link'))
                ->placeholder(__('messages.t_instagram_placeholder'))
                ->url()
                ->hint(__('messages.t_instagram_hint')),

            TextInput::make('linkedin_link')
                ->live(debounce: 500)
                ->label(__('messages.t_linkedin_link'))
                ->placeholder(__('messages.t_linkedin_placeholder'))
                ->url()
                ->hint(__('messages.t_linkedin_hint')),
        ];
    }
    /**
     * Personal information schema.
     */
    protected function businessHoursSchema(): array
    {
        return [
            KeyValue::make('business_hours')

                ->live(debounce: 500)
                ->keyLabel(__('messages.t_day'))
                ->keyPlaceholder(__('messages.t_monday'))
                ->valuePlaceholder(__('messages.t_business_hours_placeholder'))
                ->valueLabel(__('messages.t_timing'))
                ->addActionLabel(__('messages.t_add_business_hours_label'))
        ];
    }
    /**
     * Set SEO data
     */
    protected function setSeoData()
    {
        $generalSettings = app(GeneralSettings::class);
        $seoSettings = app(SEOSettings::class);


        $separator = $generalSettings->separator ?? '-';
        $siteName = $generalSettings->site_name ?? app_name();

        $title = __('messages.t_seo_my_profile_page_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    public function updatedData($value, $property)
    {

        if (str_contains($property, 'profile')) {
            $this->imageChanged = true;
            $this->isDisabled = false;
        } elseif ($this->form->getState() == $this->oldData) {
            $this->isDisabled = $this->imageChanged ? false : true;
        } else {
            $this->isDisabled = false;
        }
        if (in_array($property, ['alternative_text', 'link'])) {
            $this->updateBannerCustomProperties($this->user->banner_image, $property, $this->data[$property]);
        }
    }

    public function deleteMyAccount()
    {
        $user = auth()->user();
        $user->delete();

        Auth::logout();

        return redirect()->to('/');
    }

    public function updateBannerCustomProperties($media, $property, $value)
    {
        $media->setCustomProperty($property, $value);
        $media->save();
    }
    /**
     * Renders the MyProfile view.
     */
    public function render()
    {
        return view('livewire.user.my-profile');
    }
}
