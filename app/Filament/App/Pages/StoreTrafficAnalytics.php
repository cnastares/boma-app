<?php

namespace App\Filament\App\Pages;

use App\Filament\App\Widgets\Store\AgeWiseStoreViewsChart;
use App\Filament\App\Widgets\Store\GenderWiseStoreViewsChart;
use App\Filament\App\Widgets\Store\StoreStatsOverview;
use App\Models\Country;
use App\Models\User;
use App\Models\UserTrafficSource;
use Filament\Pages\Page;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Summarizer;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Grouping\Group;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class StoreTrafficAnalytics extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $slug = 'store-traffic-analytics/{id}';

    protected static string $view = 'filament.app.pages.store-traffic-analytics';

    public $record;
    public $storeTrafficSources;
    public $utmSources;
    public $utmMediums;
    public $trafficSources;
    public $filter = [];
    public $locationData = [];
    public $ageTrafficData;
    public $osData=[];
    public $browserData=[];
    public $deviceData=[];

    public static function getNavigationGroup(): ?string
    {
        return __('messages.t_insights_navigation');
    }
    public function getHeading(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return __('messages.t_store_traffic_analytics');
    }
    public function getTitle(): string | Htmlable    {
        return __('messages.t_store_traffic_analytics');
    }
    public function getQueryProperty()
    {
        $query = UserTrafficSource::query()->where('trackable_id', $this->record->id);
        if (isset($this->filter['utm_source'])) {
            $query->where('utm_source', $this->filter['utm_source']);
        }
        if (isset($this->filter['utm_medium'])) {
            $query->where('utm_medium', $this->filter['utm_medium']);
        }
        if (isset($this->filter['traffic_source'])) {
            $query->where('traffic_source', $this->filter['traffic_source']);
        }
        return $query;
    }

    public function mount($id)
    {

        $record = User::find($id);
        if (!$record) {
            abort(404);
        }
        $this->record = $record;
        $this->fetchstoreTrafficSources();
        $this->getLocationDetails();
        $this->getOsData();
        $this->getBrowserData();
        $this->getDeviceData();
    }

    /**
     * fetch unique records of utm_medium,utm_source,traffic_source
     * @return void
     */
    public function fetchstoreTrafficSources()
    {
        $this->storeTrafficSources = $this->record->userTrafficSources;
        if (count($this->storeTrafficSources)) {
            $this->utmMediums = $this->storeTrafficSources->whereNotNull('utm_medium')->unique('utm_medium')->pluck('utm_medium', 'utm_medium');
            $this->utmSources = $this->storeTrafficSources->whereNotNull('utm_source')->unique('utm_source')->pluck('utm_source', 'utm_source');
            $this->trafficSources = $this->storeTrafficSources->whereNotNull('traffic_source')->unique('traffic_source')->pluck('traffic_source', 'traffic_source');
        }

    }
    public static function shouldRegisterNavigation(): bool
    {
        return false;
    }
    public function getGenderData()
    {
        $genderTrafficData = UserTrafficSource::where('trackable_id', $this->record->id)->join('users', 'user_traffic_sources.user_id', '=', 'users.id')
            ->select(
                DB::raw("CASE
            WHEN users.gender IS NOT NULL
            THEN users.gender
            ELSE 'N/A'
            END AS gender"),
                DB::raw('COUNT(*) as total_views')
            )
            ->groupBy('users.gender')->get();
        return $genderTrafficData;
    }

    public function getLocationDetails()
    {

        $countryTrafficData = UserTrafficSource::where('trackable_id', $this->record->id)->select(
            DB::raw("IFNULL(JSON_UNQUOTE(JSON_EXTRACT(location_data, '$.country')), 'N/A') as country"),
            DB::raw("COUNT(*) as total_views")
        )
            ->groupBy('country')
            ->get();
        $totalViews = $countryTrafficData->sum('total_views');
        $countryTrafficPercentages = $countryTrafficData->map(function ($item, $key) use ($totalViews) {
            $country = Country::where('iso2', $item['country'])->first();
            $data = [];
            $data['country_code'] = $item['country'];

            if ($country) {
                $data['country'] = $country ? $country->name : 'N/A';
            } else {
                $data['country'] = 'N/A';
            }
            $data['total_views'] = $item['total_views'];
            //calculate views percentage base on the country
            $data['percentage'] = $totalViews > 0 ? round(($item->total_views / $totalViews) * 100, 2) : 0;
            return $data;
        });
        // Step 4: Sort by views percentage
        $this->locationData = $countryTrafficPercentages->sortByDesc('total_views')->values();
    }

    protected function getHeaderWidgets(): array
    {
        $widgets = [
            StoreStatsOverview::make([
                'storeTrafficSources' => $this->storeTrafficSources,
                'averageViewTime' => $this->getAverageViewTime(),
            ])
        ];
        // if (getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->demographic_analysis_level == 'advanced') {
        //     $widgets[] = GenderWiseStoreViewsChart::make([
        //         'gender' => $this->getGenderData()->pluck('gender')->toArray(),
        //         'views' => $this->getGenderData()->pluck('total_views')->toArray()
        //     ]);
        // }

        return $widgets;
    }

    /**
     * Get average time of user visiting the store
     * @return mixed
     */
    public function getAverageViewTime()
    {
        $averageViewTime = $this->record->pageVisits->avg('time_spent_in_secs');
        return $averageViewTime;
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->emptyState(view('tables.empty-state', ['message' => __('messages.t_no_traffics')]))
            ->query(UserTrafficSource::query()->where('trackable_id', $this->record->id))
            ->columns([
                TextColumn::make('browser')
                ->label(__('messages.t_browser')),
                TextColumn::make('os')
                ->label(__('messages.t_operating_system')),
                TextColumn::make('device_type')
                ->formatStateUsing(fn (string $state): string => ucfirst($state))
                ->label(__('messages.t_device')),
                TextColumn::make('utm_source')
                    ->label(__('messages.t_utm_source'))
                    ->searchable(getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->utm_parameters_level == 'advanced'),
                TextColumn::make('utm_medium')
                    ->formatStateUsing(fn($state) => $state ? ucfirst(str_replace('_', ' ', $state)) : '')
                    ->label(__('messages.t_utm_medium'))
                    ->searchable(getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->utm_parameters_level == 'advanced'),
                TextColumn::make('utm_campaign')
                    ->label(__('messages.t_utm_campaign'))
                    ->formatStateUsing(fn($state) => $state ? ucfirst(str_replace('_', ' ', $state)) : '')
                    ->searchable(getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->utm_parameters_level == 'advanced'),
                TextColumn::make('traffic_source')
                ->visible(function () {
                    if (getSubscriptionSetting('status')&& getActiveSubscriptionPlan()) {
                        return getActiveSubscriptionPlan()->traffic_source;
                    }
                    return false;
                })
                    ->formatStateUsing(fn($state) => $state ? ucfirst(str_replace('_', ' ', $state)) : '')
                    ->label(__('messages.t_traffic_source'))
                    ->searchable(getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->utm_parameters_level == 'advanced'),
                TextColumn::make('referrer_url')
                    ->label(__('messages.t_referrer_url'))
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),
                TextColumn::make('full_url')
                    ->label(__('messages.t_full_url'))

                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }

                        // Only render the tooltip if the column content exceeds the length limit.
                        return $state;
                    }),

            ])
            ->filters($this->getFilters())
            ->groups($this->getTableGroups())
            ->defaultGroup('traffic_source')
            ->actions([
                // ...
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public function getFilters()
    {
        $filters = [];
        if (getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && getActiveSubscriptionPlan()->utm_parameters_level == 'advanced') {
            $filters = [
                SelectFilter::make('utm_source')
                    ->multiple()
                    ->options($this->utmSources),
                SelectFilter::make('utm_medium')
                    ->multiple()
                    ->options($this->utmMediums),
                SelectFilter::make('traffic_source')
                    ->multiple()
                    ->options($this->trafficSources)
            ];
        }
        return $filters;
    }

    public function getTableGroups()
    {
        $groups = [];
        if (getSubscriptionSetting('status')&& getActiveSubscriptionPlan() && in_array(getActiveSubscriptionPlan()->utm_parameters_level, ['basic', 'advanced'])) {
            $groups = [
                Group::make('utm_source')
                    ->getTitleFromRecordUsing(fn(UserTrafficSource $record): string => $record->utm_source ? ucfirst(str_replace('_', ' ', $record->utm_source)) : ''),
                Group::make('utm_medium')
                    ->getTitleFromRecordUsing(fn(UserTrafficSource $record): string => $record->utm_medium ? ucfirst(str_replace('_', ' ', $record->utm_source)) : ''),
                Group::make('utm_campaign')
                    ->getTitleFromRecordUsing(fn(UserTrafficSource $record): string => $record->utm_campaign ? ucfirst(str_replace('_', ' ', $record->utm_source)) : ''),

                Group::make('traffic_source')
                    ->getTitleFromRecordUsing(fn(UserTrafficSource $record): string => ucfirst(str_replace('_', ' ', $record->traffic_source)))
            ];
        }
        return $groups;
    }
    public function getAgeData()
    {
        $ageTrafficData = UserTrafficSource::where('trackable_id', $this->record->id)
            ->select(
                DB::raw("CASE
                            WHEN users.date_of_birth IS NOT NULL THEN
                                CASE
                                    WHEN FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) < 18 THEN 'Under 18'
                                    WHEN FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) BETWEEN 18 AND 24 THEN '18-24'
                                    WHEN FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) BETWEEN 25 AND 34 THEN '25-34'
                                    WHEN FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) BETWEEN 35 AND 44 THEN '35-44'
                                    WHEN FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) BETWEEN 45 AND 54 THEN '45-54'
                                    WHEN FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) >= 55 THEN '55+'
                                    ELSE 'N/A'
                                END
                            ELSE 'N/A'
                         END AS age_group"),
                DB::raw('COUNT(*) as total_views')
            )
            ->join('users', 'user_traffic_sources.user_id', '=', 'users.id')
            ->where(function ($query) {
                $query->whereRaw('FLOOR(DATEDIFF(CURDATE(), users.date_of_birth) / 365) > 1')
                    ->orWhereNull('users.date_of_birth');
            });

        // Apply date filter if provided
        if ($this->ageFilter) {
            switch ($this->ageFilter) {
                case 'today':
                    $ageTrafficData->whereDate('user_traffic_sources.created_at', '=', now()->toDateString());
                    break;
                case 'week':
                    $ageTrafficData->whereBetween('user_traffic_sources.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'month':
                    $ageTrafficData->whereMonth('user_traffic_sources.created_at', '=', now()->month)
                        ->whereYear('user_traffic_sources.created_at', '=', now()->year);
                    break;
                case 'year':
                    $ageTrafficData->whereYear('user_traffic_sources.created_at', '=', now()->year);
                    break;
            }
        }
        $this->ageTrafficData = $ageTrafficData->groupBy('age_group')
            ->orderByRaw("FIELD(age_group, 'Under 18', '18-24', '25-34', '35-44', '45-54', '55+', 'N/A')")
            ->get();

    }

    public static function canAccess(): bool
    {
        return getSubscriptionSetting('status')&& getActiveSubscriptionPlan();
    }
    public function getOsData()
    {
        $this->osData = UserTrafficSource::where('trackable_id', $this->record->id)
        ->select('os',DB::raw('count(*) as total_views'))->groupBy('os')->orderByDesc('total_views')->get();
    }
    public function getBrowserData()
    {
        $this->browserData = UserTrafficSource::where('trackable_id', $this->record->id)
        ->select('browser',DB::raw('count(*) as total_views'))->groupBy('browser')->orderByDesc('total_views')->get();
    }
    public function getDeviceData()
    {
        $this->deviceData = UserTrafficSource::where('trackable_id', $this->record->id)
        ->select('device_type',DB::raw('count(*) as total_views'))->groupBy('device_type')->orderByDesc('total_views')->get();
    }
}
