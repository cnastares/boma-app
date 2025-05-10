<?php

namespace App\Filament\Pages\System;

use App\Http\Controllers\Logger;
use App\Models\Addon;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\File;
use ZipArchive;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Actions\Action;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Process;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Form;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class AddonManager extends Page implements HasTable
{
    use InteractsWithTable, HasPageShield;

    public $uploadProgress = 0;

    // Page configuration
    protected static string $view = 'filament.pages.system.addon-manager';
    protected static ?int $navigationSort = 2;

    public static function canAccess(): bool
    {
        return userHasPermission('page_AddonManager');
    }

    // Properties for file upload and listing addons
    public $addons;
    public $data;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_add_on_manager');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_system_manager');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_add_on_manager');
    }
    public function mount()
    {
        $this->addons = $this->listAddons();

        $this->form->fill([]);
    }


    public static function shouldRegisterNavigation(): bool
    {
        return !config('app.demo');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->query(Addon::query())
            ->columns([
                TextColumn::make('name')
                    ->formatStateUsing(fn(string $state): string => ucwords($state))
                    ->label('Addon Name'),
                TextColumn::make('version')
                    ->label('Version'),
                TextColumn::make('build_version')
                    ->label('Build Version'),
                TextColumn::make('min_main_version')
                    ->label('Minimum Main Version'),
                TextColumn::make('update_available')
                    ->color(fn(string $state): string => match ($state) {
                        'uptodate' => 'success',
                        'available' => 'danger',
                    })
                    ->formatStateUsing(function (string $state): string {
                        return $state == 'available'
                            ? '🔔 New Update Available'
                            : '✅ Up to Date';
                    })
                    ->label('Update Status'),
            ])
            ->actions([
                Action::make('download')
                    ->link()
                    ->url(fn($record): string => $record->download_link)
                    ->openUrlInNewTab()
                    ->color('success')
                    ->icon('heroicon-m-arrow-down-tray')

            ]);
    }


    /**
     * Logs a message to a custom logger.
     *
     * @param string $type The type of the message.
     * @param string $text The message to log.
     * @param bool   $timestamp Whether to include a timestamp.
     */
    private function logMessage($type, $text = '', $timestamp = true)
    {
        $logger = new Logger(storage_path() . '/logs/addon.log');
        $logger->log($type, $text, $timestamp);
    }



    protected function listAddons()
    {
        $addons = [];
        $addonPath = base_path('vendor/adfox');

        if (File::exists($addonPath) && File::isDirectory($addonPath)) {
            $directories = File::directories($addonPath);
            foreach ($directories as $directory) {
                $addons[] = [
                    'name' => basename($directory),
                    'path' => $directory,
                ];
            }
        }

        return $addons;
    }



    /**
     * Define the form used for file uploads and system details.
     */
    public function form(Form $form): Form
    {
        return $form->schema([
            FileUpload::make('upload')
                ->maxSize(maxUploadFileSize())
                ->label('Upload File')
                ->required()
                ->helperText('You can add a new addon or update an existing addon by uploading the zip file downloaded from CodeCanyon. Note: Updating an existing addon will overwrite any changes made within the addon. Please ensure to take a backup of the addon before proceeding.')
        ])
            ->columns(2)
            ->statePath('data');
    }

    public function uploadAddon()
    {
        $this->validate();

        // Check if an update file has been uploaded
        if (isset($this->data['upload']) && !empty($this->data['upload'])) {
            // Before starting the upload, reset the progress to 0
            $this->uploadProgress = 0;

            $this->logMessage('Addon Upload', 'Start uploading addon');

            // Increase script execution time limit
            set_time_limit(1200);
            $path = storage_path('app/source-code.zip');

            // Define storage parameters
            $disk = 'local';
            $destinationPath = 'temp'; // Store in 'storage/app/temp'
            $newFileName = 'source-code.zip';

            // Delete existing source-code.zip file if it exists
            if (file_exists($path)) {
                File::delete($path);
                $this->logMessage('Update Execution', 'Existing update file deleted');
            }

            // Retrieve the uploaded file
            $fileIdentifier = array_key_first($this->data['upload']);
            $tempFile = $this->data['upload'][$fileIdentifier];

            $this->uploadProgress = 45;
            // Store the uploaded file
            $zipPath = Storage::disk($disk)->putFileAs($destinationPath, $tempFile, $newFileName);
            $this->logMessage('Addon Upload', 'Addon ZIP file stored at ' . $zipPath);

            $extractPath = storage_path('app/temp_addon');
            if (File::exists($extractPath)) {
                File::deleteDirectory($extractPath);
            }
            File::makeDirectory($extractPath);

            // Extract the ZIP file
            $zip = new ZipArchive();
            if ($zip->open(storage_path('app/' . $zipPath)) === true) {

                $zip->extractTo($extractPath);
                $zip->close();
                $this->logMessage('Addon Upload', 'Addon ZIP file extracted to ' . $extractPath);

                // Determine the module folder name
                $sourceCodePath = $extractPath . '/source-code/app-modules';
                $moduleFolders = File::directories($sourceCodePath);

                $this->uploadProgress = 60;
                $this->uploadProgress = 60;
                if (!empty($moduleFolders)) {
                    foreach ($moduleFolders as $modulePath) {
                        $addonName = basename($modulePath);
                        $destinationPath = base_path('app-modules/' . $addonName);
                        $vendorDestPath = base_path('vendor/adfox/' . $addonName);

                        $this->uploadProgress += 20 / count($moduleFolders);
                        // Move the addon source code to the specified directories
                        File::copyDirectory($modulePath, $destinationPath);
                        File::copyDirectory($modulePath, $vendorDestPath);
                        $this->logMessage('Addon Upload', "Addon source code for $addonName moved to $destinationPath and $vendorDestPath");
                    }

                    $this->uploadProgress = 100;

                    // Clean up temporary files
                    File::deleteDirectory($extractPath);
                    Storage::delete($zipPath);
                    $this->logMessage('Addon Upload', 'Temporary files cleaned up');
                    $this->form->fill([]);
                    Notification::make()
                        ->title('Addons uploaded successfully')
                        ->success()
                        ->send();
                    $this->logMessage('Addon Upload', 'Addons uploaded successfully');

                    $this->js('setTimeout(function() { location.reload(true); }, 3000);');
                } else {
                    $this->uploadProgress = 0;
                    Notification::make()
                        ->title('Invalid addon structure')
                        ->danger()
                        ->send();
                    $this->logMessage('Addon Upload', 'Invalid or ambiguous addon structure in ZIP file');
                }
            } else {
                $this->uploadProgress = 0;
                Notification::make()
                    ->title('Failed to open ZIP file')
                    ->danger()
                    ->send();
                $this->logMessage('Addon Upload', 'Failed to open ZIP file');
            }

            $this->logMessage('Addon Upload', 'End uploading addon');
        }
    }
}
