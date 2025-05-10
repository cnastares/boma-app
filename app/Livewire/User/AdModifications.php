<?php

namespace App\Livewire\User;

use App\Models\Ad;
use App\Models\Media;
use App\Settings\AdSettings;
use App\Settings\GeneralSettings;
use App\Settings\SEOSettings;
use Approval\Models\Modification;
use Artesaos\SEOTools\Traits\SEOTools;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Livewire\Attributes\Url;
use Livewire\Component;

class AdModifications extends Component implements HasForms, HasTable
{
    use InteractsWithTable, InteractsWithForms, SEOTools;


    public $id;
    public $ad;
    #[Url(as: 'ref', keep: true)]
    public $referrer = '/';
    #[Url(as: 'admin_view')]
    public $ownerView;
    public function mount()
    {
        abort_unless(app(AdSettings::class)->admin_approval_required, 404);
        $this->initializeAd($this->id);
        $this->setSeoData();
    }

    /**
     * Ensure only owners or authorized individuals can see non-active ads.
     */
    protected function checkAdAccess()
    {
        $isActive = $this->ad->status->value === 'active';
        $isOwner = Auth::id() == $this->ad->user_id || $this->ownerView;
        if (!$isActive && !$isOwner) {
            abort(404, 'Ad not found or inactive');
        }
        $this->ownerView = !$isActive && $isOwner;
    }
    /**
     * Initialize the ad details and handle potential access issues.
     *
     * @param Ad $ad The ad to display.
     */
    protected function initializeAd($id)
    {
        $this->ad = Ad::whereId($id)->first();
        if (!$this->ad) {
            abort(404, 'Ad not found');
        }
        $this->checkAdAccess();
    }
    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyState(view('tables.empty-state', ['message' => __('messages.t_no_modifications')]))
            ->query(Modification::query()->orWhereHasMorph('modifiable', [Ad::class], function ($query) {
                $query->whereId($this->id)->OrWhereJsonContains('modifications->model_id', $this->id);
            })
                ->orWhereHasMorph('modifiable', [Media::class], function ($query) {
                    $query->where('model_id', $this->id);
                }))
            ->columns([
                TextColumn::make('modified')
                    ->label(__('messages.t_modified'))
                    ->limit(35)
                    ->default(function ($record): string {
                        $flattened = \Arr::dot($record->modifications);
                        return $record->modifiable_type == 'App\Models\Ad' ? reset($flattened) : '';
                    }),
                TextColumn::make('field')
                    ->label(__('messages.t_field'))
                    ->default(function ($record): string {
                        if ($record->modifiable_type == 'App\Models\Ad') {
                            return __('messages.t_'.array_key_first($record->modifications)) ?? '';
                        }
                        if ($record->modifiable_type == 'App\Models\Media') {
                            return __('messages.t_image') ?? '';
                        }
                        return '';
                    }),
                TextColumn::make('original')
                    ->label(__('messages.t_original'))
                    ->limit(35)
                    ->default(function ($record): string {
                        $flattened = \Arr::dot($record->modifications);
                        return $record->modifiable_type == 'App\Models\Ad' ? end($flattened) : '';
                    }),
                ImageColumn::make('image')
                    ->label(__('messages.t_image'))
                    ->defaultImageUrl(function ($record) {
                        return $record->modifiable_type == 'App\Models\Media' ? $record->modifiable->getUrl() : '';
                    })
                    ->extraImgAttributes(fn ($record): array => [
                        'alt' => __('message.t_ad_image'),
                    ]),
                IconColumn::make('active')
                    ->label(__('messages.t_status'))
                    ->trueIcon('heroicon-o-clock')
                    ->trueColor('warning')
                    ->boolean(),
                TextColumn::make('reason')
                ->label(__('messages.t_reason'))
                ->default(function($record){
                    $disapproval = $record->disapprovals()->first();
                    return $disapproval && $disapproval->reason ? $disapproval->reason :'';
    })
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // Action::make('reason')
                    // ->label(__('messages.t_view_modifications_reason'))
                    // ->modalSubmitAction(false)
                    // ->color('info')
                    // ->icon('heroicon-o-eye')
                    // ->visible(fn(Modification $record) => !$record->active)
                    // ->action(fn( $record) => dd($record->id)),
                DeleteAction::make()
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),

            ]);
    }
    public function generateStatusDescription(Modification $record)
    {
        $disapproval = $record->disapprovals()->first();
        dump($disapproval);
        $reason = $disapproval && $disapproval->reason ? $disapproval->reason : __('messages.t_empty_modification_reason');
        return "<p class='text-black'>" . $reason . "</p>";
    }
    public function render()
    {
        return view('livewire.ad.ad-modifications');
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

        $title = __('messages.t_seo_ad_modification_page_title') . " $separator " . $siteName;
        $description = $seoSettings->meta_description;

        $this->seo()->setTitle($title);
        $this->seo()->setDescription($description);
    }
}
