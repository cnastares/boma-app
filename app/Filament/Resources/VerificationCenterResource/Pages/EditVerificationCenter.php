<?php

namespace App\Filament\Resources\VerificationCenterResource\Pages;

use App\Filament\Resources\VerificationCenterResource;
use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditVerificationCenter extends EditRecord
{
    protected static string $resource = VerificationCenterResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {

        if (isset($data['status'])) {
            if ($data['status'] == 'declined') {
                $data['declined_at'] = now();
            } elseif ($data['status'] == 'verified') {
                $data['verified_at'] = now();
            }
        }

        return $data;
    }

    public function afterSave()
    {
        if (isset($this->data['status']) && in_array($this->data['status'],['declined','verified']) && isset($this->data['user_id'])) {
            $recipient = User::find($this->data['user_id']);
            if ($recipient) {
                    if ($this->data['status'] == 'declined') {
                        $notificationTitle=__('messages.t_verification_rejected_notification_title');
                        $notificationBody=__('messages.t_reason').$this->data['comments'];
                    } elseif ($this->data['status'] == 'verified') {
                        $notificationTitle=__('messages.t_verification_verified_notification_title') ;
                        $notificationBody=__('messages.t_verification_verified_notification_body') ;
                    }
                    Notification::make()
                    ->title($notificationTitle)
                    ->body($notificationBody)
                    ->sendToDatabase($recipient);
                }
            }
    }
}
