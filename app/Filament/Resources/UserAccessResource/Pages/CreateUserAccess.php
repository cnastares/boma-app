<?php

namespace App\Filament\Resources\UserAccessResource\Pages;

use App\Filament\Resources\UserAccessResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Facades\Hash;

class CreateUserAccess extends CreateRecord
{
    protected static string $resource = UserAccessResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['password'] = Str::random(10);
        return $data;
    }

    protected function afterCreate(): void
    {
        $user = User::where('email', $this->data['email'])->first();
        // $password = Str::random(10);

        // $user->update([
        //     'password' => Hash::make($password),
        //     // 'phone' => $this->data['phone_number'],
        // ]);

        // $user->assignRole('business_owner');
        $user->markEmailAsVerified();

        // Generate a reset password token
        $token = app('auth.password.broker')->createToken($user);

        // Customize the ResetPassword notification
        $notification = new ResetPassword($token);
        $notification->toMailUsing(function ($notifiable, $token) use ($user) {
            $resetUrl = Filament::getResetPasswordUrl($token, $user);
            return (new \Illuminate\Notifications\Messages\MailMessage)
                ->subject('Reset Your Password')
                ->line('Click the button below to reset your password:')
                ->action('Reset Password', $resetUrl)
                ->line('If you did not request a password reset, no further action is required.');
        });

        // Send the notification
        Notification::send($user, $notification);
    }
}
