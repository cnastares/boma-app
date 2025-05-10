<?php

namespace App\Filament\Pages\Settings;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Artisan;
use Config as ConfigManager;
use Illuminate\Support\Facades\Config;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;
use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class ManageFileStorageSettings extends Page
{
    use HasPageShield;

    public ?array $data = [];

    protected static string $view = 'filament.pages.settings.manage-file-storage-settings';

    protected static ?int $navigationSort = 5;

    public static function canAccess(): bool
    {
        return userHasPermission('page_ManageFileStorageSettings');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_file_storage');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_settings');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_file_storage');
    }
    public function mount(): void
    {
        $this->data = [
            'storage_type' => config('filament.default_filesystem_disk'),
            's3_key' => config('filesystems.disks.s3.key'),
            's3_secret' => config('filesystems.disks.s3.secret'),
            's3_region' => config('filesystems.disks.s3.region'),
            's3_bucket' => config('filesystems.disks.s3.bucket'),
        ];
        $this->form->fill($this->data);
    }

    public function setEnvValue($values)
    {
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                setEnvironmentValue($envKey, $envValue);
            }
        }
        return true;
    }

    public function save()
    {
        try {
            $data = $this->form->getState();
            if (array_key_exists('storage_type', $data)) {
                if ($data['storage_type'] === 's3') {
                    if ($this->validateAwsCredentials($data['s3_key'], $data['s3_secret'], $data['s3_region'], $data['s3_bucket'])) {
                        ConfigManager::write('chatify.storage_disk_name', 's3');
                        $env_val['FILAMENT_FILESYSTEM_DISK'] = 's3';
                        $env_val['AWS_ACCESS_KEY_ID'] = $data['s3_key'];
                        $env_val['AWS_SECRET_ACCESS_KEY'] = $data['s3_secret'];
                        $env_val['AWS_DEFAULT_REGION'] = $data['s3_region'];
                        $env_val['AWS_BUCKET'] = $data['s3_bucket'];

                        $this->setEnvValue($env_val);
                    } else {
                        // dd('ok');
                        Notification::make()
                        ->title(__('messages.t_invalid_s3'))
                        ->danger()
                        ->send();
                        return;
                    }

                } elseif ($data['storage_type'] === 'media') {
                    ConfigManager::write('chatify.storage_disk_name', 'chat');
                    $env_val['FILAMENT_FILESYSTEM_DISK'] =  'media';
                    $this->setEnvValue($env_val);
                }
            }
            // Clear cache
            Artisan::call('config:clear');

            Notification::make()
            ->title(__('messages.t_saved'))
            ->success()
                ->send();
        } catch (\Throwable $th) {
            // Error
            Notification::make()
                ->title(__('messages.t_common_error'))
                ->danger()
                ->send();
            //throw $th;
        }
    }

    function validateAwsCredentials($key, $secret, $region, $bucket)
    {
       //dd($key, $secret, $region, $bucket);
        try {
            $s3Client = new S3Client([
                'credentials' => [
                    'key' => $key,
                    'secret' => $secret,
                ],
                'version' => 'latest',
                'region' => $region,
            ]);

            // Try to list objects in the bucket
            $s3Client->listObjects([
                'Bucket' => $bucket,
            ]);

            return true; // Credentials are valid
        } catch (AwsException $e) {
            return false; // Invalid credentials
        }
    }

    public function form(Form $form): Form
    {
        $isDemo = Config::get('app.demo');

        return $form->schema([
            $isDemo ?
                Placeholder::make('storage_type')
                ->content(fn (Get $get) => $get('storage_type'))
                ->hint(__('messages.t_ap_storage_type_demo_hint')) :
                Select::make('storage_type')
                ->label(__('messages.t_ap_storage_type'))
                ->placeholder(__('messages.t_ap_select_storage_type'))
                ->required()
                ->live()
                ->options([
                    'media' => __('messages.t_ap_local'),
                    's3' => __('messages.t_ap_amazon_s3'),
                ])
                ->hint(__('messages.t_ap_select_storage_type_hint')),

            Grid::make()->schema([
                $isDemo ?
                    Placeholder::make('s3_key')
                    ->content('*****')
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint(__('messages.t_ap_s3_key_demo_hint')) :
                    TextInput::make('s3_key')
                    ->label(__('messages.t_ap_amazon_s3_key'))
                    ->placeholder(__('messages.t_ap_enter_amazon_s3_key'))
                    ->required()
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint(__('messages.t_ap_amazon_s3_key_hint')),

                $isDemo ?
                    Placeholder::make('s3_secret')
                    ->content('*****')
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint(__('messages.t_ap_s3_secret_demo_hint')) :
                    TextInput::make('s3_secret')
                    ->label(__('messages.t_ap_amazon_s3_secret'))
                    ->placeholder(__('messages.t_ap_enter_amazon_s3_secret'))
                    ->required()
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint(__('messages.t_ap_amazon_s3_secret_hint')),

                $isDemo ?
                    Placeholder::make('s3_region')
                    ->content('*****')
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint(__('messages.t_ap_s3_region_demo_hint')) :
                    TextInput::make('s3_region')
                    ->label(__('messages.t_ap_amazon_s3_region'))
                    ->placeholder(__('messages.t_ap_enter_amazon_s3_region'))
                    ->required()
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint(__('messages.t_ap_amazon_s3_region_hint')),

                $isDemo ?
                    Placeholder::make('s3_bucket')
                    ->content('*****')
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint(__('messages.t_ap_s3_bucket_demo_hint')) :
                    TextInput::make('s3_bucket')
                    ->label(__('messages.t_ap_amazon_s3_bucket'))
                    ->placeholder(__('messages.t_ap_enter_amazon_s3_bucket'))
                    ->required()
                    ->visible(fn (Get $get): bool => $get('storage_type') == 's3')
                    ->hint(__('messages.t_ap_amazon_s3_bucket_hint')),
            ])
        ])
            ->columns(2)
            ->statePath('data');
    }
}
