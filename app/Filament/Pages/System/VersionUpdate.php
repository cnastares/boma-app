<?php

namespace App\Filament\Pages\System;

use Illuminate\Support\Facades\Http;
use Filament\Pages\Page;
use Filament\Forms\Form;
use Illuminate\Http\Request;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\KeyValue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;
use ZipArchive;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Redirect;
use App\Http\Controllers\Logger;
use App\Settings\GeneralSettings;
use App\Settings\LiveChatSettings;
use App\Settings\MaintenanceSettings;
use App\Traits\ConfigBackupManager;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Livewire\WithFileUploads;
use Config;
use BezhanSalleh\FilamentShield\Traits\HasPageShield;

class VersionUpdate extends Page
{
    // Use this trait for handling file uploads
    use WithFileUploads, HasPageShield, ConfigBackupManager;

    public $uploadProgress = 0;

    // Class properties with their purpose explained
    public $latestBuildVersion;
    public $latestVersion;
    public $data;
    public $appCode;
    public $purchaseCode;
    public $buildVersion;
    public $isLocalhost;
    public $isDemo;

    // Page configuration properties
    protected static string $view = 'filament.pages.system.version-update';
    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('messages.t_ap_version_update');
    }

    public static function getNavigationGroup(): string
    {
        return __('messages.t_ap_system_manager');
    }

    public function getTitle(): string
    {
        return __('messages.t_ap_version_update');
    }
    public static function canAccess(): bool
    {
        return userHasPermission('page_VersionUpdate');
    }
    /**
     * Mount method to initialize data on page load.
     */
    public function mount()
    {
        // Increase the maximum size for file uploads
        ini_set('upload_max_filesize', '512M');

        // Set application configuration data
        $this->appCode = config('app.app_code');
        $this->isDemo = config('app.demo');
        $this->purchaseCode = config('envato.purchase_code');
        $this->buildVersion = config('app.build_version');
        $this->isLocalhost = env('IS_LOCAL', false);

        // Get system details and check for new version
        $this->data = [
            'system_detail' => $this->getSystemDetails(),
        ];
        $this->form->fill($this->data);
        $this->checkVersion();
    }

     /**
     * Logs a message to a custom logger.
     *
     * @param string $message The message to log.
     * @param array  $context Additional log context.
     */
    private function logMessage($type, $text = '', $timestamp = true)
    {
        $logger = new Logger(storage_path() . '/logs/update.log');
        $logger->log($type, $text, $timestamp);
    }


    /**
     * Retrieves and returns system details.
     */
    public function getSystemDetails() {
        $data = [
            'Current Version' => config('app.current_version'),
            'Build Version' => config('app.build_version'),
            'Laravel Version' => app()->version(),
            'PHP Version' => phpversion(),
            'MySQL Version' => DB::select("SELECT VERSION() AS version")[0]->version
        ];
       return $data;
    }


    /**
     * Define the form used for file uploads and system details.
     */
    public function form(Form $form): Form
    {
        return $form->schema([
            KeyValue::make('system_detail')
            ->addable(false)
            ->deletable(false)
            ->disabled()
            ->columnSpanFull(),

        FileUpload::make('upload')
            ->label(__('messages.t_ap_upload_file'))
            ->required()
            ->helperText(__('messages.t_ap_file_upload_helper'))
               ->visible(fn (): bool => $this->buildVersion < $this->latestBuildVersion),
            ])
            ->columns(2)
            ->statePath('data');
    }

    /**
     * Check for the latest version of the software from an external API.
     */
    public function checkVersion()
    {
        // Prepare data for API request
        $requestData = [
            'app_code' => $this->appCode,
            'is_localhost' => $this->isLocalhost
        ];

        // Make the API request
        $response = Http::acceptJson()->post('https://supportapi.saasforest.com/api/v1/version-check', $requestData);

        // Process the response
        if ($response->successful()) {
            $responseData = $response->json();
            $this->latestBuildVersion = $responseData['buildVersion'];
            $this->latestVersion = $responseData['currentVersion'];
        } else {
            // Log error if the version check fails
            $this->logMessage('Failed to Check Version', 'END');
        }
    }

    public function get_domain_name($url)
    {
        $parseUrl = parse_url(trim($url));
        if (isset($parseUrl['host'])) {
            $host = $parseUrl['host'];
        } else {
            $path = explode('/', $parseUrl['path']);
            $host = $path[0];
        }
        return  trim($host);
    }

    /**
     * Processes the update request.
     */
    public function processUpdate()
    {
        Artisan::call('config:clear');

        // Validate the incoming request data
        $requestData = [
            'app_code' => $this->appCode,
            'is_localhost' => $this->isLocalhost,
            'buildVersion' => $this->latestBuildVersion,
            'app_url' => env('APP_URL')
        ];

        // Log the start of the process
        $this->logMessage('Process Update', 'Starting update process');

        // Make the API request
        $response = Http::acceptJson()->post('https://supportapi.saasforest.com/api/v1/process-update', $requestData);

        // Handle the response from the API
        if ($response->successful()) {

            $data = $response->object();
            if ($data->status === 'success') {
                // Run database migrations
                Artisan::call('migrate', ['--force' => true]);

                $commands = $data->commands;

                $buildVersion = $data->buildVersion;

                foreach ($commands as $command) {
                    // Execute each command
                    Artisan::call($command);
                }

                // Update installed log file
                $installedLogFile = storage_path('installed');
                if (file_exists($installedLogFile)) {
                    $data = json_decode(file_get_contents($installedLogFile));
                    if (!is_null($data) && isset($data->d)) {
                        $data->u = date('ymdhis');
                    } else {
                        $data = [
                            'd' => base64_encode($this->get_domain_name(request()->fullUrl())),
                            'i' => date('ymdhis'),
                            'p' => base64_encode($this->purchaseCode),
                            'u' => date('ymdhis'),
                        ];
                    }

                    $this->uploadProgress = 100;
                    file_put_contents($installedLogFile, json_encode($data));
                    Artisan::call('storage:link');


                    $app_name = app(GeneralSettings::class)->site_name;
                    $default_language = app(GeneralSettings::class)->default_language;
                    $pusher_key = app(LiveChatSettings::class)->pusher_key;
                    $pusher_secret = app(LiveChatSettings::class)->pusher_secret;
                    $pusher_app_id = app(LiveChatSettings::class)->pusher_app_id;
                    $pusher_cluster = app(LiveChatSettings::class)->pusher_cluster;

                    $headline = app(MaintenanceSettings::class)->headline;
                    $message = app(MaintenanceSettings::class)->message;
                    $secret = app(MaintenanceSettings::class)->secret;

                    Config::write('app.name', $app_name);
                    Config::write('app.locale', $default_language);

                    Config::write('maintenance.headline', $headline);
                    Config::write('maintenance.message', $message);
                    Config::write('maintenance.secret', $secret);

                    Config::write('chatify.pusher.key', $pusher_key);
                    Config::write('chatify.pusher.options.cluster', $pusher_cluster);
                    Config::write('chatify.pusher.app_id', $pusher_app_id);
                    Config::write('chatify.pusher.secret', $pusher_secret);

                    $this->logMessage('Process Update', 'Installation log updated');
                    $this->buildVersion = $buildVersion;

                    Notification::make()
                    ->title('Update processed successfully')
                    ->success()
                    ->send();
                    $this->uploadProgress = 0;
                }

            } else {
                $this->uploadProgress = 0;
                // Log and handle errors in the response
                $this->logMessage('Process Update', 'API response error: ' . $data->message);
                Notification::make()
                ->title('API response error: ' . $data->message)
                ->danger()
                ->send();
            }
        } else {
            $this->uploadProgress = 0;
            // Log and handle errors in making the API request
            $this->logMessage('Process Update', 'API request failed');
            Notification::make()
            ->title('API response error: Something went wrong')
            ->danger()
            ->send();
        }
    }


    /**
     * Execute the update process for the application.
     */
    public function executeUpdate()
    {
        try {
                set_time_limit(1200);
                // Backup configs first
                if (!$this->backupConfigs()) {
                    throw new Exception('Failed to backup configurations');
                }
                $path = storage_path('app/source-code.zip');
                $demoPath = storage_path('app/updates');

                $response['success'] = false;
                $response['message'] = 'File not exist on storage!';

                $this->logMessage('Update Start', '==========');
                if (file_exists($path)) {
                    $this->logMessage('File Found', 'Success');
                    $zip = new ZipArchive;

                    if (is_dir($demoPath)) {
                        $this->logMessage('Updates directory', 'exist');
                        $this->logMessage('Updates directory', 'deleting');
                        File::deleteDirectory($demoPath);
                        $this->logMessage('Updates directory', 'deleted');
                    }

                    $this->uploadProgress = 40;

                    $this->logMessage('Updates directory', 'creating');
                    File::makeDirectory($demoPath, 0777, true, true);
                    $this->logMessage('Updates directory', 'created');

                    $this->logMessage('Zip', 'opening');
                    $res = $zip->open($path);

                    if ($res === true) {
                        $this->logMessage('Zip', 'Open successfully');
                        try {
                            $this->logMessage('Zip Extracting', 'Start');
                            $res = $zip->extractTo($demoPath);
                            $this->logMessage('Zip Extracting', 'END');

                            $this->uploadProgress = 60;
                            // Process the update note and files
                            $this->processUpdateNote($demoPath);

                            $this->logMessage('Demo extracted path', 'Deleting');
                            File::deleteDirectory($demoPath);

                            $zipPath = storage_path('app/source-code.zip');
                            if (file_exists($zipPath)) {
                                File::delete($zipPath);
                            }
                            $this->logMessage('Demo extracted path', 'Deleted');

                            $this->uploadProgress = 80;
                            $this->processUpdate();
                        } catch (Exception $e) {
                            Log::info($e->getMessage());
                            $this->restoreConfigs(); // Restore configs if anything fails
                            $response['message'] = $e->getMessage();
                            $this->logMessage('Exception', $e->getMessage());
                        }
                        $zip->close();
                    } else {
                        $this->logMessage('Zip', 'Open failed');
                    }
                }

                $this->logMessage('', '===============Update END==============');

               // return $response;
        } catch (Exception $e) {
                $this->restoreConfigs(); // Restore configs on any error
                return [
                    'success' => false,
                    'message' => $e->getMessage()
                ];
        }
    }

    /**
     * Processes the update note and moves the files accordingly.
     *
     * @param string $demoPath The path where the update files are located.
     */
    private function processUpdateNote($demoPath)
    {
        $versionFile = file_get_contents($demoPath . DIRECTORY_SEPARATOR . 'update_note.json');
        $updateNote = json_decode($versionFile);
        $this->logMessage('Get update note', 'END');
        $this->logMessage('Get Build Version from update note', 'START');
        $codeVersion = $updateNote->build_version;
        $this->logMessage('Get Build Version from update note', 'END');
        $this->logMessage('Get Root Path from update note', 'START');
        $codeRootPath = $updateNote->root_path;
        $this->logMessage('Get Root Path from update note', 'END');
        $this->logMessage('Get current version', 'START');
        $allMoveFilePath = (array)($updateNote->code_path);

        foreach ($allMoveFilePath as $filePath => $type) {
            $sourcePath = $demoPath . DIRECTORY_SEPARATOR . $codeRootPath . DIRECTORY_SEPARATOR . $filePath;
            $targetPath = base_path($filePath);

            if ($filePath == 'vendor') {
                $subdirectories = File::directories($sourcePath);

                foreach ($subdirectories as $subdir) {
                    $addonName = basename($subdir);

                    // Skip the adfox folder
                    if ($addonName == 'adfox') {
                        continue;
                    }

                    $addonTargetPath = $targetPath . DIRECTORY_SEPARATOR . $addonName;
                    File::copyDirectory($subdir, $addonTargetPath);
                    $this->logMessage('Copy directory', $subdir . ' to ' . $addonTargetPath);
                }
            }
            elseif ($filePath == 'vendor/adfox' || $filePath == 'app-modules') {
                $subdirectories = File::directories($sourcePath);

                foreach ($subdirectories as $subdir) {
                        $addonName = basename($subdir);
                        $addonTargetPath = $targetPath . DIRECTORY_SEPARATOR . $addonName;
                        $installedFilePath = $addonTargetPath . DIRECTORY_SEPARATOR . 'installed';

                        if (!File::exists($addonTargetPath)) {
                            $this->logMessage('Add new addon directory', $addonName);
                            File::copyDirectory($subdir, $addonTargetPath);
                        } else {
                            // Check if installed file exists
                            if (!File::exists($installedFilePath)) {
                                $this->logMessage('Processing existing addon directory (not installed)', $addonName);
                                File::copyDirectory($subdir, $addonTargetPath);
                            } else {
                                $this->logMessage('Skip installed addon directory', $addonName);
                            }
                        }
                }
            } elseif ($type == 'file') {
                File::copy($sourcePath, $targetPath);
                $this->logMessage('Move file', $sourcePath . ' to ' . $targetPath);
            } else {
                File::copyDirectory($sourcePath, $targetPath);
                $this->logMessage('Copy directory', $sourcePath . ' to ' . $targetPath);
            }
        }

    }

    public function manualUpdate() {
        $zipPath = storage_path('app/source-code.zip');

        if (!File::exists($zipPath)) {
            Notification::make()
            ->title('Update File Not Found')
            ->body('Please place the source-code.zip file in the storage/app directory and try again.')
            ->danger()
            ->send();
            return;
        } else {
            $this->logMessage('Manual Update', 'Process started');
            Notification::make()
            ->title('Update Process Started')
            ->body('The update process has begun. Please do not refresh the page or navigate away until it completes.')
            ->info()
            ->send();
            $this->uploadProgress = 10;
            $this->executeUpdate();
            $this->logMessage('Update Execution', 'Process executed');
        }
    }

    /**
     * Executes the update process when a new version file is uploaded.
     */
    public function versionUpdateExecute()
    {
        $this->validate();
        // Check if an update file has been uploaded
        if (isset($this->data['upload']) && !empty($this->data['upload'])) {

            $this->uploadProgress = 0;
            // Notify the user that the process has started
            Notification::make()
            ->title('Update Process Started')
            ->body('The update process has begun. Please do not refresh the page or navigate away until it completes.')
            ->info()
            ->send();
            // Prepare the request data for purchase validation
            $validationData = [
                'app_code'       => $this->appCode,
                'purchase_code'  => $this->purchaseCode,
                'version'        => $this->latestBuildVersion,
                'url'            => env('APP_URL')
            ];

            // Make an HTTP POST request to the Node.js API for purchase validation
            $response = Http::post('https://supportapi.saasforest.com/api/v1/validate-purchase', $validationData);

            // Check the response status
            if ($response->successful()) {
                // Successful validation logic
                $responseData = $response->json();
                $this->logMessage('Update Execution', 'Validation Successful: ' . $responseData['message']);

                $this->logMessage('Update Execution', 'Started');

                // Increase script execution time limit
                set_time_limit(1200);
                $path = storage_path('app/source-code.zip');

                // Delete existing source-code.zip file if it exists
                if (file_exists($path)) {
                    File::delete($path);
                    $this->logMessage('Update Execution', 'Existing update file deleted');
                }

                // Retrieve the uploaded file
                $fileIdentifier = array_key_first($this->data['upload']);
                $tempFile = $this->data['upload'][$fileIdentifier];

                // Define storage parameters
                $disk = 'local';
                $destinationPath = ''; // Store directly in 'storage/app'
                $newFileName = 'source-code.zip';

                // Store the uploaded file
                Storage::disk($disk)->putFileAs($destinationPath, $tempFile, $newFileName);
                $this->logMessage('Update Execution', 'New update file stored');
                $this->uploadProgress = 20;
                // Execute the update process
                $this->executeUpdate();
                $this->logMessage('Update Execution', 'Process executed');
            } else {
                $this->uploadProgress = 0;
                // Handle validation errors
                $errorMessage = $response->failed() ? $response->json()['error'] : 'Validation Failed';
                $this->logMessage('Update Execution', $errorMessage);

                // Display error notification
                Notification::make()
                    ->title('Validation Error: ' . $errorMessage)
                    ->body('Please check your purchase key or contact support for assistance.')
                    ->danger()
                    ->send();
            }

        } else {
            $this->uploadProgress = 0;
            // Log message for cases where no file is uploaded
            $this->logMessage('Update Execution', 'No update file uploaded');
        }
    }

    public static function shouldRegisterNavigation(): bool
    {
        return !config('app.demo');
    }

    /**
     * Deletes the uploaded version file.
     */
    public function versionFileUpdateDelete()
    {
        $path = storage_path('app/source-code.zip');

        // Delete the source-code.zip file if it exists
        if (file_exists($path)) {
            File::delete($path);
            $this->logMessage('File Deletion', 'Update file deleted');
        } else {
            // Log message if the file does not exist
            $this->logMessage('File Deletion', 'No update file found to delete');
        }
    }

}
