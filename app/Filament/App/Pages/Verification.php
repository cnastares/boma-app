<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;

use App\Forms\Components\WebCamJs;
use App\Models\VerificationCenter;
use Illuminate\Support\Facades\Auth;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use App\Settings\VerificationSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Form;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Grid;
use Filament\Notifications\Notification;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Section;
use Illuminate\Support\HtmlString;
use Filament\Forms\Get;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Illuminate\Support\Arr;

class Verification extends Page
{

    use SEOToolsTrait;
    use InteractsWithForms;

    protected static string $view = 'filament.app.pages.verification';

    protected static ?int $navigationSort = 2;
    public ?array $data = [];

    public VerificationCenter $record;

    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';
    public $canSubmit;

    public static function getNavigationGroup(): ?string
    {
        return __('messages.t_insights_navigation');
    }

    public static function getNavigationLabel(): string
    {
        return __('messages.t_verification_center');
    }

    public function getTitle(): string
    {
        return __('messages.t_verification_center');
    }
    /**
     * Initializes the component.
     */
    public function mount()
    {
        $this->setSeoData();
        $this->populateVerificationData();
        $this->checkRequiredFieldsFilled();
    }

    /**
     * Populate verification data into the form for the authenticated user.
     */
    protected function populateVerificationData()
    {
        // Ensure a user is authenticated
        if (!Auth::check()) {
            // Optionally handle the case where no user is logged in
            return;
        }

        $userId = Auth::id();

        // Fetch the authenticated user's verification data
        $verification = VerificationCenter::where('user_id', $userId)->first();

        if ($verification) {
            $this->record = $verification;
            $this->form->fill($this->record->attributesToArray());
        } else {
            $this->form->fill();
        }
    }

    /**
     * Handle the form submission and create a verification record.
     */
    public function create()
    {
        $this->validate();

        $userId = Auth::id();

        // Create a new VerificationCenter record with the current user's ID
        $verification = new VerificationCenter();
        $verification->fill($this->form->getState());
        $verification->user_id = $userId;
        $verification->save();

        $this->form->model($verification)->saveRelationships();

        $selfie = $this->data['selfie'] ?? null;

        if ($selfie) {
            $this->addImageToMediaCollection($this->data['selfie'], $verification);
        }

        $this->record = $verification;

        // Send success notification
        Notification::make()
            ->title(__('messages.t_verification_submitted'))
            ->success()
            ->send();

        $this->js('location.reload();');
    }

    #[On('take-selfie')]
    public function setSelfie($dataUri)
    {
        $this->data['selfie'] = $dataUri;
    }
    public function addImageToMediaCollection($dataUri, $model)
    {
        // Extract base64 content from the data URI
        $exploded = explode(',', $dataUri);
        if (count($exploded) != 2) {
            return;
        }
        $base64Image = $exploded[1];
        // Use Spatie's addMediaFromBase64 method to handle the base64 string
        $model->addMediaFromBase64($base64Image)->toMediaCollection('selfie');
    }


    /**
     * Defines the form schema for verification.
     */
    public function form(Form $form): Form
    {
        $isRecordPresent = isset($this->record);

        $isDeclined = $isRecordPresent && $this->record->status == 'declined';
        $isApproved = $isRecordPresent && $this->record->status == 'verified';

        $verificationSettings = app(VerificationSettings::class);
        $documentTypes = $verificationSettings->document_types;
        $radioOptions = collect($documentTypes)
            ->where('enable', true)
            ->mapWithKeys(function ($item) {
                return [$item['type'] => __('messages.t_' . $item['type'])];
            })
            ->toArray();


        return $form
            ->schema([
                Section::make()
                    ->afterStateUpdated(function ($livewire) {
                        $livewire->checkRequiredFieldsFilled();
                    })
                    ->hidden(fn(Get $get): bool => $isRecordPresent && !$isDeclined)
                    ->schema([
                        Radio::make('document_type')
                            ->default('id')
                            ->label(__('messages.t_select_document_type'))
                            ->live()
                            ->required()
                            ->options($radioOptions)
                            ->afterStateUpdated(function (?string $state, ?string $old) {
                                $this->dispatch('document-type-updated');
                            })
                            ->hidden($isRecordPresent && !$isDeclined),
                        Grid::make()
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('front_side')
                                    ->maxSize(maxUploadFileSize())
                                    ->label($isRecordPresent ? __('messages.t_display_document_front_side') : __('messages.t_upload_your_document_front_side'))
                                    ->collection('front_side_verification')
                                    ->visibility('private')
                                    ->image()
                                    ->required()
                                    ->validationAttribute($isRecordPresent ? __('messages.t_display_document_front_side') : __('messages.t_upload_your_document_front_side'))
                                    ->downloadable()
                                    ->deleteUploadedFileUsing(function ($livewire) {
                                        $livewire->checkRequiredFieldsFilled();
                                    }),
                                SpatieMediaLibraryFileUpload::make('back_side')
                                    ->maxSize(maxUploadFileSize())
                                    ->label($isRecordPresent ? __('messages.t_display_document_back_side') : __('messages.t_upload_your_document_back_side'))
                                    ->collection('back_side_verification')
                                    ->visibility('private')
                                    ->image()
                                    ->deleteUploadedFileUsing(function ($livewire) {
                                        $livewire->checkRequiredFieldsFilled();
                                    })
                                    ->required(function (Get $get) use ($documentTypes) {
                                        $getDocumentSetting = Arr::where($documentTypes, function ($value, $key) use ($get) {
                                            return $value['type'] == $get('document_type');
                                        });
                                        $isBackRequired = Arr::first($getDocumentSetting);
                                        return isset($isBackRequired['back_required']) ? $isBackRequired['back_required'] : false;
                                    })
                                    ->validationAttribute($isRecordPresent ? __('messages.t_display_document_back_side') : __('messages.t_upload_your_document_back_side'))
                                    ->downloadable(),
                            ]),
                        WebCamJs::make('selfie')
                            ->hidden(function (Get $get) use ($verificationSettings) {
                                $selectedDocumentType = $get('document_type');
                                if (!$selectedDocumentType) {
                                    return true; // Hide if no document type is selected
                                }

                                // Find the selected document type configuration
                                $selectedDocumentConfig = collect($verificationSettings->document_types)
                                    ->firstWhere('type', $selectedDocumentType);

                                // Determine if a selfie is required for the selected document type
                                $selfieRequired = $selectedDocumentConfig ? $selectedDocumentConfig['selfie_required'] : false;

                                return !$selfieRequired; // Hide if selfie is not required, show otherwise
                            })
                            ->label(__('messages.t_take_a_selfie'))
                            ->helperText(__('messages.t_ensure_clear_visibility'))
                            ->required(),
                    ]),
                Grid::make()
                    ->schema([
                        Placeholder::make('docs_status')
                            ->label(__('messages.t_documentation_status'))
                            ->content(function () {
                                $status = $this->record->status ?? 'not submitted';
                                $dateLabel = '';
                                $statusLabel = '';

                                switch ($status) {
                                    case 'verified':
                                        $date = $this->record->verified_at ? $this->record->verified_at : __('messages.t_no_date_available');
                                        $dateLabel = __('messages.t_verified_on', ['date' => date('d/m/Y', strtotime($date))]);
                                        $statusLabel = __('messages.t_verified');
                                        break;
                                    case 'declined':
                                        $date = $this->record->declined_at ? $this->record->declined_at : __('messages.t_no_date_available');
                                        $dateLabel = __('messages.t_declined_on', ['date' => date('d/m/Y', strtotime($date))]);
                                        $statusLabel = __('messages.t_declined');
                                        break;
                                    default:
                                        $date = $this->record->created_at ? $this->record->created_at : __('messages.t_no_date_available');
                                        $dateLabel = __('messages.t_submitted_on', ['date' => date('d/m/Y', strtotime($date))]);
                                        $statusLabel = __('messages.t_pending');
                                        break;
                                }

                                return new HtmlString(ucfirst($statusLabel) . ' - ' . $dateLabel);
                            }),

                        Placeholder::make('submitted_document_type')
                            ->label(__('messages.t_submitted_document_type'))
                            ->content(function () {
                                $type = $this->record->document_type ?? 'not submitted';

                                switch ($type) {
                                    case 'id':
                                        $typeLabel = __('messages.t_government_issued_id');
                                        break;
                                    case 'driver_license':
                                        $typeLabel = __('messages.t_driver_license');
                                        break;
                                    case 'passport':
                                        $typeLabel = __('messages.t_passport');
                                        break;
                                    default:
                                        $typeLabel = ucfirst($type);
                                        break;
                                }

                                return new HtmlString($typeLabel);
                            }),



                        Grid::make()
                            ->hidden($verificationSettings->hide_attachment && $isApproved)
                            ->schema([
                                SpatieMediaLibraryFileUpload::make('front_side')
                                ->maxSize(maxUploadFileSize())
                                    ->label($isRecordPresent ? __('messages.t_display_document_front_side') : __('messages.t_upload_your_document_front_side'))
                                    ->collection('front_side_verification')
                                    ->visibility('private')
                                    ->hidden(fn(Get $get): bool => empty($get('front_side')))
                                    ->image()
                                    ->disabled($isRecordPresent && !$isDeclined),
                                SpatieMediaLibraryFileUpload::make('back_side')
                                ->maxSize(maxUploadFileSize())
                                    ->label($isRecordPresent ? __('messages.t_display_document_back_side') : __('messages.t_upload_your_document_back_side'))
                                    ->collection('back_side_verification')
                                    ->visibility('private')
                                    ->image()
                                    ->hidden(fn(Get $get): bool => empty($get('back_side')))
                                    ->disabled($isRecordPresent && !$isDeclined),
                                SpatieMediaLibraryFileUpload::make('selfie')
                                ->maxSize(maxUploadFileSize())
                                    ->hidden(function (Get $get) use ($verificationSettings) {
                                        $selectedDocumentType = $get('document_type');
                                        if (!$selectedDocumentType) {
                                            return true; // Hide if no document type is selected
                                        }

                                        // Find the selected document type configuration
                                        $selectedDocumentConfig = collect($verificationSettings->document_types)
                                            ->firstWhere('type', $selectedDocumentType);

                                        // Determine if a selfie is required for the selected document type
                                        $selfieRequired = $selectedDocumentConfig ? $selectedDocumentConfig['selfie_required'] : false;

                                        return !$selfieRequired; // Hide if selfie is not required, show otherwise
                                    })
                                    ->label(__('messages.t_selfie'))
                                    ->collection('selfie')
                                    ->visibility('private')
                                    ->downloadable()
                                    ->image()
                                    ->disabled($isRecordPresent && !$isDeclined)
                            ]),

                        Placeholder::make('comments')
                            ->label(__('messages.t_documentation_comments'))
                            ->content(function () {
                                $comments = $this->record->comments;
                                return new HtmlString($comments ? ucfirst($comments) : '-');
                            })
                            ->visible($isRecordPresent),
                    ])
                    ->columns(1)
                    ->hidden(fn(): bool => !$isRecordPresent || $isDeclined)
            ])
            ->statePath('data')
            ->model($isRecordPresent ? $this->record : VerificationCenter::class);
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

        $title = __('messages.t_seo_verification_page_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }

    public function updateDocs()
    {
        $data = $this->form->getState();

        $this->record->update(array_merge($data, ['status' => 'pending']));

        Notification::make()
            ->title(__('messages.t_documents_pending_review'))
            ->success()
            ->send();

        $this->js('location.reload();');
    }
    /**
     * get required fields of the current component
     * @return array
     */
    public function getRequiredFieldsProperty()
    {
        $requiredFields = [];
        $rules = $this->getRules();
        foreach ($rules as $field => $rule) {
            if (is_array($rule) && in_array('required', $rule)) {
                $requiredFields[] = $field;
            } elseif ($rule == 'required') {
                $requiredFields[] = $field;
            }
        }
        return $requiredFields;
    }
    public function getCurrentAction()
    {
        return isset($this->record) && $this->record->status == 'declined' ? 'updateDocs' : 'create';
    }


    /**
     * Check if all required fields are filled.
     * @return void
     */
    public function checkRequiredFieldsFilled()
    {
        $isFilled = true;
        if (!count($this->requiredFields)) {
            $isFilled = true;
        }
        foreach ($this->requiredFields as $field) {
            $fieldDetail = explode('.', $field);
            if (isset($this->data[$fieldDetail[1]]) && is_array($this->data[$fieldDetail[1]]) && (count($this->data[$fieldDetail[1]]))) {
                $isFilled = true;
            } elseif (isset($this->data[$fieldDetail[1]]) && (!is_array($this->data[$fieldDetail[1]])) && trim($this->data[$fieldDetail[1]]) !== '') {
                $isFilled = true;
            } else {
                $isFilled = false;
                break;
            }
        }
        $this->canSubmit = $isFilled;
    }
}
