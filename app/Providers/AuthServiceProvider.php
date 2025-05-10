<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Settings\GeneralSettings;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;
use App\Policies\AdPolicy;
use App\Models\Ad;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Ad::class => AdPolicy::class, // Add this line
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->greeting(__('messages.t_et_verify_email_greeting',['name' => $notifiable->name]))
                ->subject(__('messages.t_et_verify_email_subject'))
                ->line(__('messages.t_et_welcome_line', ['siteName' =>  app(GeneralSettings::class)->site_name]) )
                ->line(__('messages.t_et_verify_email_line'))
                ->action(__('messages.t_et_verify_email_action'), $url);
        });

        ResetPassword::toMailUsing(function (object $notifiable, string $url) {
            $url=route('password.reset',['token'=>$url]);
            return (new MailMessage)
            ->greeting(__('messages.t_et_reset_email_greeting',['name' => $notifiable->name]))
            ->subject(__('messages.t_et_reset_password_email_subject'))
            ->line(__('messages.t_et_reset_password_email_line'))
            ->action(__('messages.t_et_reset_password_email_action'), $url)
            ->line(__('messages.t_et_reset_password_email_expire_line', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]));

        });
    }
}
