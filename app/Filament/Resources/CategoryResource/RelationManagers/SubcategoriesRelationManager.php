<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use App\Models\Category;
use App\Models\City;
use App\Models\Country;
use App\Models\Field;
use App\Models\FieldTemplate;
use App\Models\PriceType;
use App\Models\State;
use Filament\Forms;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Fieldset;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\RelationManagers\Concerns\Translatable;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Set;
use Illuminate\Support\Str;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;


class SubcategoriesRelationManager extends RelationManager implements HasShieldPermissions
{
    use Translatable;
    protected static string $relationship = 'subcategories';

    public static function getPermissionPrefixes(): array
    {
        return [
            'create',
            'update',
            'view_any',
            'delete_any',
            'delete',
            'force_delete',
            'force_delete_any',
            'restore',
            'restore_any',
        ];
    }
    public  function canViewAny(): bool
    {
        return userHasPermission('view_any_category');
    }

    public  function canCreate(): bool
    {
        return userHasPermission('create_category');
    }

    public  function canEdit($record): bool
    {
        return userHasPermission('update_category');
    }

    public  function canDelete($record): bool
    {
        return userHasPermission('delete_category');
    }

    public  function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_category');
    }

    public  function canForceDelete($record): bool
    {
        return userHasPermission('force_delete_category');
    }

    public  function canForceDeleteAny(): bool
    {
        return userHasPermission('force_delete_any_category');
    }

    public  function canRestore($record): bool
    {
        return userHasPermission('restore_category');
    }

    public  function canRestoreAny(): bool
    {
        return userHasPermission('restore_any_category');
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(255)
                    ->label(__('messages.t_ap_name')),

                Forms\Components\TextInput::make('slug')
                    ->required()
                    ->hintAction(
                        Action::make('generateSlug')
                            ->label(__('messages.t_ap_generate_url_slug'))
                            ->icon('heroicon-m-bolt')
                            ->action(function (Set $set, Get $get) {
                                if ($get('name')) {
                                    $set('slug', Str::slug($get('name')));
                                }
                            })
                    )
                    ->label(__('messages.t_ap_category_url'))
                    ->unique(ignoreRecord: true),

                Forms\Components\Textarea::make('description')
                    ->label(__('messages.t_ap_description'))
                    ->maxLength(300)
                    ->required(),

                Select::make('field_template_id')
                    ->options(FieldTemplate::all()->pluck('name', 'id'))
                    ->searchable()
                    ->hidden(function($record, $operation){
                        return (!isFieldTemplatePluginEnabled()) || $this->getOwnerRecord()->isSubCategory();
                    })
                    ->label(__('messages.t_ap_field_template')),

                // Toggle::make('enable_online_shopping')
                //     ->live(onBlur: true)
                //     ->helperText(__('messages.t_ap_toggle_online_shopping'))
                //     ->hidden(fn() => !is_ecommerce_active()),

                // Toggle::make('disable_location')
                //     ->inline(false)
                //     ->helperText(__('messages.t_ap_toggle_disable_location'))
                //     ->reactive()
                //     ->afterStateUpdated(fn($state, Set $set) => $state ? $set('default_location', false) : null),

                // Toggle::make('default_location')
                //     ->inline(false)
                //     ->helperText(__('messages.t_ap_toggle_default_location'))
                //     ->reactive()
                //     ->afterStateUpdated(fn($state, Set $set) => $state ? $set('disable_location', false) : null),

                // Section::make(__('messages.t_ap_location_details'))
                //     ->collapsible()
                //     ->hidden(fn(Get $get) => !$get('default_location'))
                //     ->schema([
                //         Select::make('country_id')
                //             ->label(__('messages.t_ap_country'))
                //             ->options(Country::pluck('name', 'id')->toArray())
                //             ->live()
                //             ->afterStateUpdated(fn(callable $set) => $set('state_id', null))
                //             ->required(),

                //         Select::make('state_id')
                //             ->label(__('messages.t_ap_state'))
                //             ->options(function (Get $get) {
                //                 $countryId = $get('country_id');
                //                 if (!$countryId) {
                //                     return [];
                //                 }
                //                 return State::where('country_id', $countryId)->pluck('name', 'id')->toArray();
                //             })
                //             ->live()
                //             ->hidden(fn(Get $get): bool => !$get('country_id'))
                //             ->afterStateUpdated(fn(callable $set) => $set('city_id', null))
                //             ->required(),

                //         Select::make('city_id')
                //             ->label(__('messages.t_ap_city'))
                //             ->options(function (Get $get) {
                //                 $stateId = $get('state_id');
                //                 if (!$stateId) {
                //                     return [];
                //                 }
                //                 return City::where('state_id', $stateId)->pluck('name', 'id')->toArray();
                //             })
                //             ->hidden(fn(Get $get): bool => !$get('state_id'))
                //             ->required(),
                //     ])
                //     ->columnSpanFull(),

                // Toggle::make('disable_condition')
                //     ->hidden(fn(Get $get) => $get('enable_online_shopping') ?? false),

                // Section::make(__('messages.t_ap_price_type_options'))
                //     ->collapsed()
                //     ->columns(2)
                //     ->schema([
                //         Toggle::make('disable_price_type')
                //             ->helperText(__('messages.t_ap_toggle_disable_price_type'))
                //             ->live()
                //             ->afterStateUpdated(function (Set $set) {
                //                 $set('customize_price_type', false);
                //             }),

                //         Toggle::make('customize_price_type')
                //             ->helperText(__('messages.t_ap_toggle_customize_price_type'))
                //             ->afterStateUpdated(function (Set $set) {
                //                 $set('disable_price_type', false);
                //             })
                //             ->live(),

                //         Select::make('price_types')
                //             ->multiple()
                //             ->helperText(__('messages.t_ap_select_price_types'))
                //             ->required(fn(Get $get) => $get('customize_price_type'))
                //             ->hidden(fn(Get $get) => !$get('customize_price_type'))
                //             ->options(PriceType::all()->pluck('name', 'id')),

                //         TagsInput::make('field_options')
                //             ->label(__('messages.t_ap_field_options'))
                //             ->live()
                //             ->visible(fn($get) => $get('field_type') == 'select')
                //             ->helperText(__('messages.t_ap_field_input_type')),

                //         Toggle::make('has_price_suffix')
                //             ->hidden(fn(Get $get) => $get('disable_price_type'))
                //             ->helperText(__('messages.t_ap_toggle_price_suffix'))
                //             ->live(),

                //         TagsInput::make('suffix_field_options')
                //             ->placeholder(__('messages.t_ap_placeholder_suffix_options'))
                //             ->hidden(fn(Get $get) => $get('disable_price_type'))
                //             ->helperText(__('messages.t_ap_define_suffix_options'))
                //             ->required(fn(Get $get) => $get('has_price_suffix')),

                //         Toggle::make('enable_offer')
                //     ])
                //     ->hidden(fn(Get $get) => $get('enable_online_shopping') ?? false),
                ...getVerificationFields(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->recordUrl(function (Category $record): string {
                    if ($record->isSubcategory()) {
                        return route('filament.admin.resources.categories.edit', ['record' => $record]);
                    }

                    return false;
                },
            )
            ->heading(function () {
                $isMainCategory = $this->getOwnerRecord()->isMainCategory();
                return $isMainCategory ? __('messages.t_ap_subcategories') : __('messages.t_ap_child_categories');
            })
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('messages.t_ap_name')),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        return $this->mutateLocationDetails($data);
                    }),
                Tables\Actions\LocaleSwitcher::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->mutateRecordDataUsing(function (array $data): array {
                        if (isset($data['location_details'])) {
                            if (!isset($data['country_id'])) {
                                $data['country_id'] = $data['location_details']['country_id'] ?? null;
                            }
                            if (!isset($data['state_id'])) {
                                $data['state_id'] = $data['location_details']['state_id'] ?? null;
                            }
                            if (!isset($data['city_id'])) {
                                $data['city_id'] = $data['location_details']['city_id'] ?? null;
                            }
                        }
                        return $data;
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        return $this->mutateLocationDetails($data);
                    }),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateActions([
                Tables\Actions\CreateAction::make()
                    ->mutateFormDataUsing(function (array $data): array {
                        return $this->mutateLocationDetails($data);
                    }),
            ]);
    }

    protected function mutateLocationDetails($data)
    {
        $locationDetails = [];
        foreach ($data as $key => $value) {
            $locationKeys = ['country_id', 'state_id', 'city_id'];
            if (in_array($key, $locationKeys)) {
                $locationDetails[$key] = $value;
            }
        }
        $data['location_details'] = $locationDetails;
        return $data;
    }
}
