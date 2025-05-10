<?php

namespace App\Livewire\Notification;

use App\Models\NotificationSubscriber;
use App\Settings\GeneralSettings;
use App\Settings\NotificationRegistrationSettings;
use App\Settings\SEOSettings;
use Artesaos\SEOTools\Traits\SEOTools;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Registration extends Component
{
    use SEOTools;
    public $isMobileHidden = false;

    #[Validate('required|min:1')]
    public $name;
    #[Validate('required|email|unique:notification_subscribers,email')]

    public $email;
    public function mount()
    {
        if (!$this->notificationRegistrationSettings->enable) {
            abort(404);
        }
        $this->setSeoData();

    }
    public function register()
    {
        $this->validate();
        $subscriber = NotificationSubscriber::create([
            'name' => trim($this->name),
            'email' => trim($this->email)
        ]);
        if ($subscriber) {
            //Send email to the admin about registration
            if ($this->notificationRegistrationSettings->notification_email) {
                try {
                    $message = (new MailMessage)
                        ->subject(__('messages.t_new_registration_for_updates'))
                        ->greeting(__('messages.t_et_notification_subscriber_email_greeting'))
                        ->line(__('messages.t_et_new_user_has_registered_for_updates'))
                        ->line(__('messages.t_name') . ': ' . $this->name)
                        ->line(__('messages.t_email') . ': ' . $this->email);

                    Mail::html($message->render()->toHtml(), function ($mail) {
                        $mail->to($this->notificationRegistrationSettings->notification_email) // Send to your configured email
                            ->subject(__('messages.t_new_registration_for_updates'));
                    });
                } catch (Exception $e) {
                    Log::info($e->getMessage());
                }
            }

            //send notification to the user
            Notification::make()
                ->title(__('messages.t_success_register_notification'))
                ->duration(60000)
                ->success()
                ->send();
        }
        $this->reset(['name', 'email']);
    }

    public function getNotificationRegistrationSettingsProperty()
    {
        return app(NotificationRegistrationSettings::class);
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

        $title = __('messages.t_seo_notification_registration') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }
    public function render()
    {
        return view('livewire.notification.registration');
    }
}
