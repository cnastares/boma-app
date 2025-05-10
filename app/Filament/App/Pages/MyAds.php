<?php

namespace App\Filament\App\Pages;

use Filament\Pages\Page;
use Filament\Tables\Table;
use App\Models\Ad;
use App\Models\AdPromotion;
use App\Models\OrderPackage;
use App\Models\UserAdPosting;
use App\Settings\AdSettings;
use Filament\Tables\Actions\BulkAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\SelectColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use App\Settings\PackageSettings;
use App\Settings\SubscriptionSettings;
use App\Settings\UserSettings;
use Artesaos\SEOTools\Traits\SEOTools as SEOToolsTrait;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\HtmlString;

class MyAds extends Page implements HasForms, HasTable
{
    use InteractsWithTable, InteractsWithForms, SEOToolsTrait;

    protected static ?int $navigationSort = 1;
    protected static string $view = 'filament.app.pages.my-ads';

    public static function getNavigationGroup(): ?string
    {
        return __('messages.t_ads_navigation');
    }
    public static function getNavigationLabel(): string
    {
        return __('messages.t_my_ads');
    }

    public function getTitle(): string
    {
        return __('messages.t_my_ads');
    }

    public static function table(Table $table): Table
    {
        $userId = auth()->id();
        $headingDescription = self::getHeadingDescription($userId);

        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyState(view('tables.empty-state', ['message' => __('messages.t_no_ads')]))
            ->emptyStateHeading(__('messages.t_no_ads'))
            ->query(Ad::where('user_id', $userId))
            ->description($headingDescription)
            ->columns(self::getTableColumns())
            ->filters(self::getTableFilters())
            ->actions(self::getTableActions())
            ->bulkActions(self::getBulkActions())
            ->checkIfRecordIsSelectableUsing(fn(Model $record): bool => $record->status->value === 'active');
    }

    protected static function getHeadingDescription($userId): ?string
    {
        $userAdPosting = UserAdPosting::where('user_id', $userId)->first();
        $freeAdLimitUsed = $userAdPosting->free_ad_count ?? 0;
        $freeAdLimit = 0;
        $renewalPeriod = null;

        if (self::isPluginActive('packages') && app(PackageSettings::class)->status) {
            $freeAdLimit = app(PackageSettings::class)->free_ad_limit;
            $renewalPeriod = __('messages.t_' . app(PackageSettings::class)->ad_renewal_period);
        } else if (isSubscriptionEnabled()) {
            $freeAdLimit = getSubscriptionFreeAdLimit();
            $freeAdLimitUsed = auth()->user()->getFreeAdCount();
        }

        $availableAdLimit = max(0, ($freeAdLimit - $freeAdLimitUsed));

        return __('messages.t_free_ads_used') . ": {$freeAdLimitUsed}, " .
            __('messages.t_available_free_ads') . ": {$availableAdLimit}, " .
            __('messages.t_renewal_period') . ": {$renewalPeriod}";
        return null;
    }

    protected static function getTableColumns(): array
    {
        return [
            SpatieMediaLibraryImageColumn::make('ads')
                ->collection('ads')
                ->conversion('thumb')
                ->defaultImageUrl(fn($record)=>getAdPlaceholderImage($record->id))
                ->label(__('messages.t_ad_images'))
                ->size(40)
                ->circular()
                ->overlap(2)
                ->stacked()
                ->limit(3)
                ->extraImgAttributes(fn ($record): array => [
                    'alt' => $record->title . __('messages.t_ad_image'),
                ]),
            TextColumn::make('title')
                ->limit(app(UserSettings::class)->max_character ?? 45)
                ->tooltip(fn(TextColumn $column) => strlen($column->getState()) > $column->getCharacterLimit() ? $column->getState() : null)
                ->label(__('messages.t_ad_title'))
                ->searchable(),
            TextColumn::make('view_count')->label(__('messages.t_views'))->sortable(),
            TextColumn::make('likes_count')->label(__('messages.t_likes'))->sortable(),
            TextColumn::make('source')->label(__('messages.t_source'))->formatStateUsing(fn(string $state) => __("messages.t_{$state}"))->sortable(),
            TextColumn::make('posted_date')->label(__('messages.t_posted_on_date'))->date('d/m/Y'),
            TextColumn::make('status')->label(__('messages.t_status'))->sortable(),
            SelectColumn::make('status')
                ->updateStateUsing(fn($record, $state, $livewire) => $livewire->updateAdStatus($record, $state))
                ->selectablePlaceholder(false)
                ->options(self::getStatusOptions())
                ->label(__('messages.t_change_status_action'))
                ->disableOptionWhen(fn(string $value): bool => in_array($value, ['inactive', 'expired', 'pending']))
                ->disabled(fn($state) => in_array($state->value, ['expired', 'inactive', 'pending'])),
        ];
    }

    protected static function getStatusOptions(): array
    {
        return [
            'draft' => __('messages.t_draft_status'),
            'active' => __('messages.t_active_status'),
            'sold' => __('messages.t_sold_status'),
            'inactive' => __('messages.t_deactivated_status'),
            'expired' => __('messages.t_expired_status'),
            'pending' => __('messages.t_pending_status'),
        ];
    }

    protected static function getTableFilters(): array
    {
        return [
            SelectFilter::make('status')->options(self::getStatusOptions()),
        ];
    }

    protected static function getTableActions(): array
    {
        return [
            Action::make('view')
                ->icon('heroicon-o-eye')
                ->label(__('messages.t_preview_ad'))
                ->url(fn(Ad $record): string => route('ad.overview', $record->slug))
                ->openUrlInNewTab(),
            Action::make('package_sell')
                ->icon('heroicon-o-bolt')
                ->button()
                ->color('success')
                ->action(fn(Ad $record, $livewire) => $livewire->redirectToPackageSelection($record))
                ->visible(fn(Ad $record) => $record->status->value == 'active')
                ->hidden(app('filament')->hasPlugin('subscription') && app(SubscriptionSettings::class)->status)
                ->label(__('messages.t_boost_your_sale')),

            Action::make('subscription_sell')
                ->action(fn(Ad $record, $livewire) => $livewire->redirectToPlanPromotionSelection($record))
                ->visible(fn(Ad $record) => $record->status->value == 'active')
                ->visible(app('filament')->hasPlugin('subscription') && app(SubscriptionSettings::class)->status)
                ->label(__('messages.t_boost_your_sale'))
                ->icon('heroicon-o-bolt')
                ->button()
                ->color('success'),

            Action::make('view_status')
                ->label(__('messages.t_view_status_ad'))
                ->modalSubmitAction(false)
                ->color('info')
                ->modalHeading(fn() => __('messages.t_ad_status'))
                ->modalDescription(fn(Ad $record) => new HtmlString(self::generateStatusDescription($record))),

            ActionGroup::make([
                EditAction::make()
                    ->url(fn(Ad $record): string => route('post-ad', ['id' => $record->id])),
                Action::make('view_modifications')
                    ->icon('heroicon-o-queue-list')
                    ->label(__('messages.t_view_modifications'))
                    ->url(fn(Ad $record): string => route('ad-modifications', [
                        'id' => $record->id
                    ]))
                    ->visible(fn(Ad $record) => $record->status->value != 'pending' && app(AdSettings::class)->admin_approval_required)
                    ->openUrlInNewTab(),
                DeleteAction::make()
                    ->modalHeading(__('messages.t_delete_ad')),

            ])
                ->icon('heroicon-m-ellipsis-horizontal')
                ->tooltip(__('messages.t_actions')),
            Action::make('analytics')
                ->label(__('messages.t_analytics'))
                ->visible(fn($record) => $record->status != 'draft' && getSubscriptionSetting('status'))
                ->url(fn($record) => route('filament.app.pages.ad-traffic-analytics.{id}', ['id' => $record->id]))
        ];
    }

    protected static function getBoostActions(): ActionGroup
    {
        return ActionGroup::make([
            Action::make('subscription_sell')
                ->visible(fn(Ad $record) => $record->status->value === 'active')
                ->action(fn(Ad $record, $livewire) => $livewire->redirectToPlanPromotionSelection($record))
                ->label(__('messages.t_choose_plan_promotion')),
            Action::make('buy_contract')
                ->visible(fn(Ad $record) => $record->status->value === 'active')
                ->action(fn(Ad $record, $livewire) => $livewire->redirectToContractPromotion($record))
                ->label(__('messages.t_buy_contract')),
        ])->visible(self::isPluginActive('subscription'))->icon('heroicon-o-bolt')->color('success');
    }

    protected static function getBulkActions(): array
    {
        return [
            BulkAction::make('sell')
                ->icon('heroicon-o-bolt')
                ->visible(self::isPluginActive('subscription'))
                ->color('success')
                ->action(fn(Collection $records, $livewire) => $livewire->redirectToMultiplePromotionSelection($records))
                ->label(__('messages.t_boost_your_sale')),
        ];
    }

    public static function generateStatusDescription(Ad $record): string
    {
        $activePromotions = AdPromotion::where('ad_id', $record->id)->whereDate('end_date', '>=', now())->get();
        $status = __('messages.t_' . $record->status->value . '_status');
        $description = "<p>" . __('messages.t_ad_current_status', ['status' => $status]) . "</p>";

        if ($activePromotions->isNotEmpty()) {
            $description .= "<p>" . __('messages.t_with_active_promotions') . "</p><ul>";
            foreach ($activePromotions as $promotion) {
                $description .= "<li>" . __('messages.t_promotion_active_until', [
                    'promotionName' => $promotion->promotion->name,
                    'date' => $promotion->end_date->format('d/m/Y')
                ]) . "</li>";
            }
            $description .= "</ul>";
        } else {
            $description .= "<p>" . __('messages.t_no_additional_promotions') . "</p>";
        }

        return $description;
    }

    public function redirectToPlanPromotionSelection(Ad $record)
    {
        if (app('filament')->hasPlugin('subscription') && app(SubscriptionSettings::class)->status) {
            $routeParameters = [
                'promotion_type' => 'apply',
                'id' => $record->id,
                'current' => 'promotion-details'
            ];
            return redirect()->route('choose-promotion', $routeParameters);
        }
    }
    public function redirectToPackageSelection(Ad $record)
    {
        if (app('filament')->hasPlugin('packages') && app(PackageSettings::class)->status) {
            $userOrderPackages = OrderPackage::where('user_id', auth()->id())
                ->whereHas('packageItems', function ($query) {
                    $query->whereDate('expiry_date', '>=', now())
                        ->where('type', 'promotion')
                        ->where('available', '>', 0);
                })
                ->first();

            $actionType = $userOrderPackages ? 'apply' : 'single';

            $routeParameters = [
                'pkg_type' => $actionType,
                'ad_id' => $record->id,
            ];

            return redirect()->route('filament.app.pages.choose-package', $routeParameters);
        } else {
            $routeParameters = [
                'id' => $record->id,
                'current' => 'ad.post-ad.promote-ad',
            ];
            return redirect()->route('post-ad', $routeParameters);
        }
    }

    public function redirectToContractPromotion(Ad $record)
    {

        $routeParameters = [
            'id' => $record->id,
            'current' => 'ad.post-ad.promote-ad',
        ];
        return redirect()->route('post-ad', $routeParameters);
    }

    protected static function isPluginActive(string $plugin): bool
    {
        return app('filament')->hasPlugin($plugin);
    }
    /**
     * Get the available subscription id
     *
     * @return void
     */
    protected function findAvailableSubscription()
    {
        $user = auth()->user();
        $activeSubscriptions = $user->getActiveSubscriptions();
        foreach ($activeSubscriptions as $subscription) {
            $remainAdCount = $subscription->getRemainAdCount();
            $subscriptionsAdLimit = getAdLimit();
            if ($remainAdCount > 0) {
                return $subscription->id;
            }
        }
        return null;
    }

    /**
     * Updates the status of an ad based on the given state.
     * If the state is not 'active', directly updates the status.
     * If the subscription is disabled, sets the ad source to 'package'.
     * If the subscription ad limit is reached, reverts to the old status with a notification.
     * Otherwise, determines the ad source as 'free' or 'subscription' based on ad limits and updates the status.
     *
     * @param mixed $record The ad record to update the status for.
     * @param string $state The new state to set for the ad.
     * @return void
     */
    public function updateAdStatus($record, $state)
    {
        $subscriptionId = null;
        $adSource = null;
        //update status other than the active state
        if ($state != 'active') {
            $this->updateStatus($record, $state, $adSource, $subscriptionId);
            return;
        }
        //Check subscription is disabled then update status and source to "package"
        if (!function_exists('isSubscriptionEnabled') || !isSubscriptionEnabled()) {
            $adSource = 'package';
            $this->updateStatus($record, $state, $adSource, $subscriptionId);
            return;
        }
        // If Subscription AdLimitOver is over return the old status with notification
        if (isSubscriptionAdLimitOver()) {
            $oldState = $record?->status?->value;
            $this->updateStatus($record, $oldState, $adSource, $subscriptionId);

            Notification::make()
                ->title(__('messages.t_ad_limit_reached'))
                ->body(__('messages.t_subscription_limit_reached_description'))
                ->danger()
                ->duration(5000)
                ->send();
        } else {
            //Handle If user ad limit is not over
            $freeAdLimit = getFreeAdLimit();
            $freeAdCount = auth()->user()->getFreeAdCount();
            $adSource = $freeAdLimit > $freeAdCount ? 'free' : 'subscription';
            $subscriptionId = $this->findAvailableSubscription();
            $this->updateStatus($record, $state, $adSource, $subscriptionId);
        }
        return;
    }

    protected function updateStatus($record, $state, $adSource, $subscriptionId)
    {
        $record->update([
            'status' => $state,
            'source' => $adSource,
            'subscription_id' => $subscriptionId,
        ]);
    }

    public function redirectToMultiplePromotionSelection(Collection $records)
    {

        $routeParameters = [
            'promotion_type' => 'multiple',
            'current' => 'promotion-details'
        ];
        Session::put('selected-ads', $records->pluck('slug')->toArray());
        return redirect()->route('choose-promotion', $routeParameters);
    }
}
