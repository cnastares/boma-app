<?php

namespace App\Livewire\User;

use App\Mail\StoreContact;
use App\Settings\GeneralSettings;
use Livewire\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Livewire\Attributes\Url;
use Mail;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;

/**
 * Contact Component.
 *
 * Represents the contact form where users can submit inquiries or feedback.
 */
class Contact extends Component implements HasForms
{
    use InteractsWithForms, SEOToolsTrait;

    // Retains the referrer URL to redirect back if necessary
    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';

    // Form state to hold the input values
    public $state = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'message' => '',
    ];

     /**
     * Mount lifecycle hook.
     */
    public function mount()
    {
        $this->setSeoData();
    }

    /**
     * Define the form fields and their validation rules.
     *
     * @param Form $form
     * @return Form
     */
    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('name')
                   ->label(__('messages.t_name'))
                    ->required(),
                TextInput::make('email')
                    ->label(__('messages.t_email'))
                    ->required()
                    ->email(),
                TextInput::make('phone')
                    ->label(__('messages.t_phone_label'))
                    ->required()
                    ->tel()
                    ->telRegex('/^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\.\/0-9]*$/'),
                Textarea::make('message')
                ->label(__('messages.t_message'))
                ->required(),
            ])
            ->statePath('state');
    }

    /**
     * Handle the form submission to send the contact message.
     */
    public function sendMessage()
    {
        $this->validate();

        // Check if the store owner's contact email is configured
        if (!$this->general_settings->contact_email) {
            Notification::make()
            ->title(__('messages.t_store_contact_config'))
            ->danger()
            ->send();
            return;
        }

        // Send the contact message email
        Mail::to($this->general_settings->contact_email)
            ->send(new StoreContact(
                $this->state['name'],
                $this->state['email'],
                $this->state['phone'],
                $this->state['message'],
            ));

        // Reset the form state
        $this->reset('state');

        // Notify the user of successful message submission
        Notification::make()
        ->title(__('messages.t_message_sent_success'))
        ->success()
        ->send();
    }

    /**
     * Retrieve the general settings from the application's configuration.
     *
     * @return GeneralSettings
     */
    public function getGeneralSettingsProperty()
    {
        return app(GeneralSettings::class);
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

        $title = __('messages.t_seo_contact_page_title') . " $separator " . $siteName;
        $description = __('messages.t_seo_contact_page_description');
        $ogImage = getSettingMediaUrl('seo.ogimage', 'seo', asset('images/ogimage.jpg'));
        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
        $this->seo()->setCanonical(url()->current());
        $this->seo()->opengraph()->setTitle($title);
        $this->seo()->opengraph()->setDescription($description);
        $this->seo()->opengraph()->setUrl(url()->current());
        $this->seo()->opengraph()->setType('website');
        $this->seo()->opengraph()->addImage($ogImage);
        $this->seo()->twitter()->setImage($ogImage);
        $this->seo()->twitter()->setUrl(url()->current());
        $this->seo()->twitter()->setSite("@" . $seoSettings->twitter_username);
        $this->seo()->twitter()->addValue('card', 'summary_large_image');
        $this->seo()->metatags()->addMeta('fb:page_id', $seoSettings->facebook_page_id, 'property');
        $this->seo()->metatags()->addMeta('fb:app_id', $seoSettings->facebook_app_id, 'property');
        $this->seo()->metatags()->addMeta('robots', 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1', 'name');
        $this->seo()->jsonLd()->setTitle($title);
        $this->seo()->jsonLd()->setDescription($description);
        $this->seo()->jsonLd()->setUrl(url()->current());
        $this->seo()->jsonLd()->setType('WebSite');
    }
    /**
     * Render the contact form view.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.user.contact');
    }
}
