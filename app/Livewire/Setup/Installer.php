<?php

namespace App\Livewire\Setup;

use App\Http\Controllers\Logger;
use App\Models\User;
use App\Settings\GeneralSettings;
use Livewire\Component;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Wizard;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Facades\File;
use Filament\Forms\Components\CheckboxList;
use App\Helper\DatabaseManager;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Str;
use Closure;
use Config;
use Illuminate\Support\Facades\Process;

/**
 * The Installer class manages the installation process of the application.
 */
class Installer extends Component implements HasForms
{
    use InteractsWithForms;

    public $setupFinished;

    public $logger;

    public ?array $data = [];


    /**
     * Mounts the component and initializes configuration data.
     */
    public function mount(): void
    {
        // Redirect if already installed
        if (file_exists(storage_path('installed'))) {
            $this->redirect('/');
        }

        $this->data = $this->loadConfigurationData();
        $this->form->fill($this->data);

    }

    /**
     * Loads configuration data from the environment.
     *
     * @return array The loaded configuration data.
     */
    private function loadConfigurationData(): array
    {
        return [
            'app_url' => url('/'),
            'purchase_code' => config('envato.purchase_code'),
            'envato_username' => config('envato.username'),
            'site_name' => config('app.name'),
            'db_host' => config('database.connections.mysql.host'),
            'db_name' => config('database.connections.mysql.database'),
            'db_port' => config('database.connections.mysql.port'),
            'db_username' => config('database.connections.mysql.username'),
            'db_password' => config('database.connections.mysql.password'),
            'smtp_host' => config('mail.mailers.smtp.host'),
            'smtp_port' => config('mail.mailers.smtp.port'),
            'smtp_password' => config('mail.mailers.smtp.password'),
            'smtp_user' => config('mail.mailers.smtp.username'),
            'from_email' => config('mail.from.address'),
            'from_name' => config('mail.from.name'),
        ];
    }

     /**
     * Logs a message to a custom logger.
     *
     * @param string $message The message to log.
     * @param array  $context Additional log context.
     */
    private function logMessage($type, $text = '', $timestamp = true)
    {
        $logger = new Logger(storage_path() . '/logs/install.log');
        $logger->log($type, $text, $timestamp);
    }

    public function getPhpSettingsOptions()
    {
        $phpVersion = phpversion();
        $requiredVersion = '8.0.0';
        $status = version_compare($phpVersion, $requiredVersion, '>=') ? 'Ok' : 'Error';


        $options = [
            'php_version_ok' => 'PHP Version Check',
        ];

        $descriptions = [
            'php_version_ok' => new HtmlString("Current PHP Version: $phpVersion, Required Version: ^$requiredVersion, Status: $status")
        ];

        // Update $this->data['php_settings'] based on the PHP version status
        $this->data['php_settings'] = $status === 'Ok' ? ['php_version_ok'] : [];

        return ['options' => $options, 'descriptions' => $descriptions];
    }


    public function getPhpExtensionsOptions()
    {
        $extensions = [
            'mysqli', 'gd', 'curl', 'openssl', 'pdo', 'bcmath', 'ctype',
            'fileinfo', 'mbstring', 'tokenizer', 'xml', 'json'
        ];

        $options = [];
        $descriptions = [];
        $this->data['php_extensions'] = []; // Initialize the array

        foreach ($extensions as $extension) {
            $isEnabled = extension_loaded($extension);
            $extensionName = ucfirst($extension) . ' PHP Extension';

            $options[$extension] = $extensionName;
            $descriptions[$extension] = new HtmlString(
                "Current Setting: " . ($isEnabled ? 'On' : 'Off') .
                ", Required Setting: On, Status: " . ($isEnabled ? 'Ok' : 'Error')
            );

            // If the extension is enabled, add it to the php_extensions array
            if ($isEnabled) {
                $this->data['php_extensions'][] = $extension;
            }
        }

        // Additional check for allow_url_fopen
        $isAllowUrlFopenEnabled = ini_get('allow_url_fopen');
        $options['allow_url_fopen'] = 'Allow_url_fopen';
        $descriptions['allow_url_fopen'] = new HtmlString(
            "Current Setting: " . ($isAllowUrlFopenEnabled ? 'On' : 'Off') .
            ", Required Setting: On, Status: " . ($isAllowUrlFopenEnabled ? 'Ok' : 'Error')
        );

        if ($isAllowUrlFopenEnabled) {
            $this->data['php_extensions'][] = 'allow_url_fopen';
        }

        return ['options' => $options, 'descriptions' => $descriptions];
    }


    public function getWritablePermissionsOptions()
    {
        $paths = [
            'resources' => base_path('resources'),
            'public' => base_path('public'),
            'storage' => base_path('storage'),
            '.env' => base_path('.env'),
            'lang' => base_path('lang'),
            'config' => base_path('config'),
            'public/media' => base_path('public/media')
        ];

        $options = [];
        $descriptions = [];
        $this->data['php_permission'] = [];

        foreach ($paths as $key => $path) {
            $exists = File::exists($path);
            $isWritable = File::isWritable($path);
            $options[$key] = ucfirst($key);
            $description = "Path: $path, Exists: " . ($exists ? 'Yes' : 'No') .
                        ", Writable: " . ($isWritable ? 'Yes' : 'No');
            $descriptions[$key] = new HtmlString($description);
            if ($exists && $isWritable) {
                $this->data['php_permission'][] = $key;
            }
        }

        return ['options' => $options, 'descriptions' => $descriptions];
    }

    public function checkPurchaseCode()
    {
        $fullUrl = url('/');
        $isValidDomain = $this->is_valid_domain_name($fullUrl);
        $data = $this->data;

        $this->logMessage('Purchase Key', 'Starting purchase code validation');
        if ($isValidDomain) {
            $requestData = $this->preparePurchaseValidationData($data, $fullUrl);
            try {
                $response = $this->validatePurchaseCode($requestData);

                if (!$response->successful()) {
                    $this->handleInvalidPurchaseCode();
                } else {
                    $this->updateEnvWithPurchaseDetails($data);
                }
            } catch (\Exception $e) {
                $errorMessage = 'Validation API not reachable';
                $this->logMessage('API Error', $errorMessage);
                $this->jsAlert($errorMessage);
            }
        } else {
            $this->updateEnvWithPurchaseDetails($data);
        }
    }

    private function jsAlert($message) {
        $this->js("alert('$message'); window.location.reload();");
    }

    private function preparePurchaseValidationData($data, $fullUrl) {
        return [
            'app_code' => config('app.app_code'),
            'type' => 0,
            'purchase_code' => $data['purchase_code'],
            'version' => config('app.build_version'),
            'url' => $fullUrl,
            'app_url' => config('app.url'),
        ];
    }

    private function validatePurchaseCode($requestData) {
        return Http::acceptJson()->post('https://supportapi.saasforest.com/api/v1/validate-purchase', $requestData);
    }

    private function handleInvalidPurchaseCode() {
        $errorMessage = "Purchase code invalid. Please contact support.";
        $this->jsAlert($errorMessage);
    }

    private function updateEnvWithPurchaseDetails($data) {
        $envValues = [
            'PURCHASE_CODE' => $data['purchase_code'],
            'ENVATO_USERNAME' => $data['envato_username'],
        ];
        $this->setEnvValue($envValues);
    }


    private function checkDatabaseConnection()
    {
        $this->logMessage('Step-2', 'Configuring database settings');
        $data = $this->data;
        $this->configureDatabase($data);

        try {
            DB::connection()->getPdo();
            $this->logMessage('Step-2', 'Database connection successful');
            return true;
        } catch (\Exception $e) {
            $this->logMessage('Step-2', 'Database connection error: ' . $e->getMessage());
            return false;
        }
    }

    private function configureDatabase($data)
    {
        $settings = config("database.connections.mysql");
        config([
            'database' => [
                'default' => 'mysql',
                'connections' => [
                    'mysql' => array_merge($settings, [
                        'driver' => 'mysql',
                        'host' => $data['db_host'],
                        'port' => '3306',
                        'database' => $data['db_name'],
                        'username' => $data['db_username'],
                        'password' => $data['db_password'] ? $data['db_password'] : '',
                    ]),
                ],
            ],
        ]);

        // Purge the current connection and reconnect with new configuration
        DB::purge('mysql');
        DB::reconnect('mysql');
    }


    public function finishSetup()
    {
        $this->validate();
        $this->jsAlert('Setting up your database. Please wait...');
        $fullUrl = url('/');
        $data = $this->data;
        $this->logMessage('Step-3', 'Final installation process initiated');

        if (!config('app.app_code')) {
            $this->logMessage('Step-3', 'Invalid application code');
            $this->jsAlert('Invalid application code. Installation aborted.');
            return;
        }

        try {
            $this->logMessage('Step-3', 'Saving installation info');
            $this->saveInfoInFile($fullUrl, $data['purchase_code']);

            $this->logMessage('Step-3', 'Proceeding with database setup');
            $this->database();
        } catch (\Exception $e) {
            $this->logMessage('Step-3', 'Installation failed: ' . $e->getMessage());
            $this->jsAlert('Installation failed. Error: ' . $e->getMessage());
        }
    }

    public function saveInfoInFile($url, $purchase_code)
    {
        $this->logMessage('Step-3', 'Saving installation information to file');
        $infoFile = storage_path('info');
        if (file_exists($infoFile)) {
            unlink($infoFile);
        }

        $data = json_encode([
            'd' => base64_encode($this->get_domain_name($url)),
            'i' => date('ymdhis'),
            'p' => base64_encode($purchase_code),
            'u' => date('ymdhis'),
        ]);

        file_put_contents($infoFile, $data);
        $this->logMessage('Step-3', 'Installation information saved');
    }

    public function database()
    {
        $this->logMessage('Step-3', 'Starting database migration and seeding');
        $databaseManager = new DatabaseManager();
        $response = $databaseManager->migrateAndSeed();

        if ($response['status'] !== 'success') {
            $this->logMessage('Step-3', 'Database migration and seeding failed');
            $this->jsAlert('Database setup failed.');
            return;
        }

        $this->logMessage('Step-3', 'Database migration and seeding successful');

        // Conditionally seed demo data if enabled
        if ($this->data['demo_data_enabled']) {
            $this->logMessage('Step-3', 'Starting demo data seeding');
            try {
                Artisan::call('db:seed', ['--class' => 'DemoDataSeeder', '--force' => true]);
                $this->logMessage('Step-3', 'Demo data seeded successfully');
            } catch (\Exception $e) {
                $this->logMessage('Step-3', 'Demo data seeding failed: ' . $e->getMessage());
                $this->jsAlert('Failed to seed demo data. ' . $e->getMessage());
                return;
            }
        }

        $this->createAdminAccount();
        $this->makeSetupFinished();
    }


    protected function createAdminAccount()
    {
        $this->logMessage('Step-3', 'Creating admin account');
        $admin = new User(['is_admin' => true]);
        $admin->fill([
            'name' => $this->data['name'],
            'email' => $this->data['email'],
            'password' => bcrypt($this->data['admin_password'])
        ])->save();
    }

    protected function makeSetupFinished()
    {
        $this->logMessage('Step-3', 'Finalizing installation');
        $env_val['APP_KEY'] = 'base64:' . base64_encode(Str::random(32));
        $this->setEnvValue($env_val);

        $installedLogFile = storage_path('installed');
        if (!file_exists($installedLogFile)) {
            $data = $this->prepareInstalledData();
            file_put_contents($installedLogFile, $data);
        }

        $this->logMessage('Step-3', 'Installation complete');
        $this->generalSettings->setup_finished = true;
        $this->generalSettings->save();

        return redirect('/');
    }

    private function prepareInstalledData()
    {
        $getInfoFile = storage_path('info');
        $data = file_exists($getInfoFile)
            ? file_get_contents($getInfoFile)
            : json_encode([
                'd' => base64_encode($this->get_domain_name(request()->fullUrl())),
                'i' => date('ymdhis'),
                'u' => date('ymdhis'),
            ]);

        if (file_exists($getInfoFile)) {
            unlink($getInfoFile);
        }

        return $data;
    }


    /**
     * Creates the form schema for the installer.
     *
     * @param Form $form The form instance.
     * @return Form The configured form.
     */
    public function form(Form $form): Form
    {
        $permissionData = $this->getWritablePermissionsOptions();
        $extensionData = $this->getPhpExtensionsOptions();
        $settingsData = $this->getPhpSettingsOptions();
        return $form
            ->schema([
                Wizard::make([
                    Wizard\Step::make('Get Started')
                    ->afterValidation(function () {
                        try {
                            $this->logMessage('Step-1', 'Purchase Key validation start');
                            $this->checkPurchaseCode();

                            $this->logMessage('Step-1', 'PHP Version and Extensions check start');
                            // Check PHP Version
                            if (!isset($this->data['php_settings']) || !in_array('php_version_ok', $this->data['php_settings'])) {
                                $this->js("alert('PHP version is not 8.0.0 or higher.'); window.location.reload();");
                                return;
                            }

                          // Check PHP Extensions
                            $requiredExtensions = [
                                'mysqli', 'gd', 'curl', 'openssl', 'pdo', 'bcmath', 'ctype',
                                'fileinfo', 'mbstring', 'tokenizer', 'xml', 'json'
                            ];
                            foreach ($requiredExtensions as $extension) {
                                if (!in_array($extension, $this->data['php_extensions'])) {
                                    $this->js("alert('The $extension extension is not enabled. Please enable it to proceed.'); window.location.reload();");
                                    return;
                                }
                            }

                            $this->logMessage('Step-1', 'Directory Permissions check start');
                            // Check Directory Permissions
                            $requiredDirectories = ['resources', 'public', 'storage', '.env', 'lang', 'public/media', 'config'];
                            foreach ($requiredDirectories as $directory) {
                                if (!in_array($directory, $this->data['php_permission'])) {
                                    $this->js("alert('The $directory directory does not have the required permissions.'); window.location.reload();");
                                    return;
                                }
                            }
                            $this->logMessage('Step-1', 'Media Permissions');
                            Process::path(public_path('media'))->run('chmod -R 777 .');
                            $this->logMessage('Step-1', 'Framework/sessions Permissions');
                            Process::path(storage_path('framework/sessions'))->run('chmod -R 777 .');

                            $this->logMessage('Step-1', 'Initial validation completed');
                        } catch (RequestException $e) {
                            $this->logMessage('Error', 'Exception during initial validation: ' . $e->getMessage());
                            $this->jsAlert('An error occurred during the validation process. Please try again.');
                        }
                    })
                    ->schema([
                        Section::make('Pre-Installation System Check')
                            ->description("This pre-installation check ensures your server environment is ready for setup. It verifies the PHP version, essential PHP extensions, and file/directory permissions, addressing key requirements for a smooth installation.")
                            ->schema([


                                CheckboxList::make('php_settings')
                                ->options($settingsData['options'])
                                ->descriptions($settingsData['descriptions'])
                                ->label('PHP Version Check')
                                ->helperText('This section displays the current PHP version of your server and checks if it meets the minimum required version for optimal application performance.')
                                ->columnspanfull()
                                ->disabled(),

                                CheckboxList::make('php_extensions')
                                ->options($extensionData['options'])
                                ->descriptions($extensionData['descriptions'])
                                ->label('PHP Extensions Status')
                                ->helperText('Lists the critical PHP extensions and their statuses (On/Off). Ensures these extensions are installed and enabled for proper functioning of the application.')
                                ->columnspanfull()
                                ->disabled(),

                                CheckboxList::make('php_permission')
                                ->label('Directory and File Permissions')
                                ->options($permissionData['options'])
                                ->descriptions($permissionData['descriptions'])
                                ->helperText('Verifies the writable permissions for essential directories and files. Ensures that these locations are accessible and modifiable by the application for smooth operations.')
                                ->columnspanfull()
                                ->disabled(),
                            ])
                            ->columns(2),

                            Section::make('Purchase Details')
                            ->description("Enter your purchase and user details.")
                            ->schema([
                                TextInput::make('purchase_code')
                                    ->label('Your purchase code')
                                    ->placeholder('Please enter your purchase code')
                                    ->required(),

                                TextInput::make('envato_username')
                                    ->label('Your CodeCanyon username')
                                    ->placeholder('Please enter your CodeCanyon username')
                                    ->required(),
                            ])
                            ->columns(2),
                    ]),
                    Wizard\Step::make('Configuration')
                    ->afterValidation(function () {
                        try {
                            $this->logMessage('Step-2', 'Database connection check initiated');
                            if (!$this->checkDatabaseConnection()) {
                                $this->logMessage('Step-2', 'Database connection failed');
                                $errorMessage = "Database connection failed. Please ensure correct credentials.";
                                $this->jsAlert($errorMessage);
                                return;
                            } else {
                                $this->logMessage('Step-2', 'Updating environment variables');
                                $this->saveENV();
                                $this->logMessage('Step-2', 'Environment values updated successfully');
                            }
                        } catch (RequestException $e) {
                            $this->logMessage('Step-2', 'Error during database connection check: ' . $e->getMessage());
                            $this->jsAlert('Error during database connection check. Please try again.');
                        }
                    })
                    ->schema([

                        Section::make('General Setup')
                            ->description("Let's start by setting up the general information about your site. ")
                            ->schema([
                                TextInput::make('site_name')
                                    ->label('App Name')
                                    ->afterStateUpdated(function (?string $state, ?string $old) {
                                        Config::write('app.name', $state);
                                    })
                                    ->placeholder('Name of your website')
                                    ->required(),

                                TextInput::make('app_url')
                                    ->label('App URL')
                                    ->placeholder('URL of your website')
                                    ->required(),
                            ])
                            ->columns(2),
                        Section::make('Database Details')
                            ->description('Set your database to make a connection')
                            ->schema([
                                TextInput::make('db_host')
                                    ->label('Host')
                                    ->placeholder('E.g., 127.0.0.1 or localhost')
                                    ->helperText('The server where your database is located. Typically "localhost" for local setups or a specific hostname in shared hosting environments.')
                                    ->required(),

                                TextInput::make('db_port')
                                    ->label('Port')
                                    ->placeholder('E.g., 3306 for MySQL')
                                    ->helperText("The port number your database server is running on. MySQL's default is 3306.")
                                    ->required()
                                    ->disabled(),

                                TextInput::make('db_name')
                                    ->label('Database name')
                                    ->placeholder('E.g., my_database')
                                    ->helperText("The name of your database. Ensure it exists and you've spelled it correctly.")
                                    ->required(),

                                TextInput::make('db_username')
                                    ->label('Database username')
                                    ->placeholder('E.g., root')
                                    ->helperText('The username used to connect to your database.')
                                    ->required(),

                                TextInput::make('db_password')
                                    ->label('Database password')
                                    ->placeholder('Enter database password')
                                    ->password()
                                    ->helperText('The password for the database user. Make sure it is correct.'),

                            ])
                            ->columns(2),

                        Section::make('Mail Settings')
                            ->description("The Mail Settings allow the application to send out crucial emails to the users, such as account verification emails, password reset links, and any notifications or alerts. Without proper mail configuration, the application won’t be able to communicate with the users via email, potentially leading to a poor user experience and loss of engagement.")
                            ->schema([
                                TextInput::make('smtp_host')
                                    ->label('SMTP Host')
                                    ->placeholder('e.g., smtp.gmail.com'),
                                TextInput::make('smtp_user')
                                    ->label('SMTP Username')
                                    ->placeholder('Enter your SMTP username'),
                                TextInput::make('smtp_password')
                                    ->label('SMTP Password')
                                    ->placeholder('Enter your SMTP password')
                                    ->password()
                                    ->autocomplete('new-password'),
                                TextInput::make('smtp_port')
                                    ->numeric()
                                    ->label('SMTP Port')
                                    ->placeholder('e.g., 587 or 465')
                                    ->minValue(1)
                                    ->maxValue(65535),
                                TextInput::make('from_email')
                                    ->label('From Email Address')
                                    ->placeholder('e.g., noreply@yourdomain.com')
                                    ->email(),
                                TextInput::make('from_name')
                                    ->label('From Name')
                                    ->placeholder('e.g., Your Company Name'),


                            ])
                            ->columns(2),
                    ]),
                    Wizard\Step::make('Admin Account Creation')
                        ->schema([
                            Section::make('Administrator Credentials')
                                ->description("Secure your application with strong admin credentials. These will be your keys to the admin panel where you can manage and customize your application further.")
                                ->schema([
                                    TextInput::make('name')
                                        ->label('Admin Name')
                                        ->required(),

                                    TextInput::make('email')
                                        ->label('Admin Email')
                                        ->email()
                                        ->required()
                                        ->validationAttribute('Email')
                                        ->live(onBlur:true)
                                        ->rules([
                                            function () {
                                                return function (string $attribute, $value, Closure $fail) {
                                                    $appName = strtolower(app_name());
                                                    if ($value == 'user@' . $appName . '.com' ||
                                                        $value == 'michael@' . $appName . '.com' ||
                                                        $value == 'admin@' . $appName . '.com') {
                                                        $fail('The :attribute already exists.');
                                                    }
                                                };
                                            }
                                        ])
                                        ->hint('Use a valid admin email for password resets.'),

                                    TextInput::make('admin_password')
                                        ->label('Admin Password')
                                        ->password()
                                        ->required()
                                        ->same('password_confirmation'),

                                    TextInput::make('password_confirmation')
                                        ->label('Confirm Admin Password')
                                        ->password()
                                        ->required()
                                        ->same('admin_password'),

                                ])
                                ->columns(2),

                                Toggle::make('demo_data_enabled')
                                ->label('Load Demo Data')
                                ->helperText('Would you like to populate the database with demo content? This can be useful for testing or getting familiar with the platform.')
                        ]),
                ])
                ->persistStepInQueryString()
                ->submitAction(new HtmlString(Blade::render(<<<BLADE
                    <x-filament::button
                            type="submit"
                            size="sm"
                            wire:loading.attr="disabled"
                        >
                        <span wire:loading.remove>
                            Finish Setup
                        </span>
                        <span wire:loading>
                           Installing... Please wait. (If no response after 3 minutes, reload page and try again)
                        </span>
                    </x-filament::button>
                BLADE)))
            ])
            ->statePath('data');
    }


    public function getGeneralSettingsProperty()
    {
        return app(GeneralSettings::class);
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

    public function saveENV()
    {
        $data = $this->data;
        $this->logMessage('ENV', 'Write start from saveENV');

        $env_val['APP_URL'] = $data['app_url'];
        $env_val['DB_HOST'] =  $data['db_host'];
        $env_val['DB_DATABASE'] =  $data['db_name'];
        $env_val['DB_USERNAME'] =  $data['db_username'];
        $env_val['DB_PASSWORD'] =  $data['db_password'];
        $env_val['MAIL_HOST'] =  $data['smtp_host'];
        $env_val['MAIL_PORT'] =  $data['smtp_port'];
        $env_val['MAIL_USERNAME'] =  $data['smtp_user'];
        $env_val['MAIL_PASSWORD'] =  $data['smtp_password'];
        $env_val['MAIL_FROM_ADDRESS'] =  $data['from_email'];
        $env_val['MAIL_FROM_NAME'] =  $data['from_name'];

        $this->setEnvValue($env_val);

    }

    public function setEnvValue($values)
    {
        $this->logMessage('ENV', 'Write start from setEnvValue');
        if (count($values) > 0) {
            foreach ($values as $envKey => $envValue) {
                $this->setEnvironmentValue($envKey, $envValue);
            }
        }

        $this->logMessage('ENV', 'Write setEnvValue successfully');
        return true;
    }

    public function setEnvironmentValue($envKey, $envValue)
    {
        try {
            $this->logMessage('ENV Write start', $envKey . '=>' . $envValue);
            $envFile = app()->environmentFilePath();
            $str = file_get_contents($envFile);
            $str .= "\n"; // In case the searched variable is in the last line without \n
            $keyPosition = strpos($str, "{$envKey}=");
            if ($keyPosition) {
                if(PHP_OS_FAMILY === 'Windows') {
                    $endOfLinePosition = strpos($str, "\n", $keyPosition);
                } else {
                    $endOfLinePosition = strpos($str, PHP_EOL, $keyPosition);
                }
                $oldLine = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);
                $envValue = str_replace(chr(92), "\\\\", $envValue);
                $envValue = str_replace('"', '\"', $envValue);
                $newLine = "{$envKey}=\"{$envValue}\"";
                if ($oldLine != $newLine) {
                    $str = str_replace($oldLine, $newLine, $str);
                    $str = substr($str, 0, -1);
                    $fp = fopen($envFile, 'w');
                    fwrite($fp, $str);
                    fclose($fp);
                }
            }

            $this->logMessage('ENV Write END', $envKey . '=>' . $envValue);
            return true;
        } catch (\Exception $e) {
            $this->logMessage('ENV', 'Write setEnvValue failed');
            $this->logMessage('Exception', $e->getMessage());
            return false;
        }
    }

    public function is_valid_domain_name($url)
    {
        try {
            $parseUrl = parse_url(trim($url));
            if (isset($parseUrl['host'])) {
                $host = $parseUrl['host'];
            } else {
                $path = explode('/', $parseUrl['path']);
                $host = $path[0];
            }
            $domain_name = trim($host);

            return (preg_match("/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/i", $domain_name));
        } catch (\Exception $e) {
            return false;
        }
    }

    public function render()
    {
        return view('livewire.setup.installer')->extends('livewire.setup.layout')->section('content');
    }

}
