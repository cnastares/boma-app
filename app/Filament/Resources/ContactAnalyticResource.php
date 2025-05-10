<?php

namespace App\Filament\Resources;

use App\Filament\Clusters\CommunicationSettings;
use App\Filament\Resources\ContactAnalyticResource\Pages;
use App\Filament\Resources\ContactAnalyticResource\RelationManagers;
use App\Models\ContactAnalytic;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ExportAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Exports\ContactAnalyticExporter;
use BezhanSalleh\FilamentShield\Contracts\HasShieldPermissions;

class ContactAnalyticResource extends Resource implements HasShieldPermissions
{
    protected static ?string $model = ContactAnalytic::class;

    protected static ?string $cluster = CommunicationSettings::class;
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';
    protected static ?int $navigationSort = 4;

    public static function getPermissionPrefixes(): array
    {
        return [
            'view_any',
            'view',
            'delete_any'
        ];
    }

    public static function canViewAny(): bool
    {
        return userHasPermission('view_any_contact::analytic');
    }

    public static function canView($record): bool
    {
        return userHasPermission('view_contact::analytic');
    }

    public static function canDeleteAny(): bool
    {
        return userHasPermission('delete_any_contact::analytic');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('ad.title')->label('Ad Title'),
                TextColumn::make('viewer_name')->label('Viewed By'),
                TextColumn::make('viewer_phone')->label('Viewer Phone'),
                TextColumn::make('viewer_email')->label('Viewer Email'),
                TextColumn::make('ad_price')->label('Ad Price')->prefix(config('app.currency_symbol')),
            ])
            ->filters([
                //
            ])
            ->actions([
                // Tables\Actions\EditAction::make(),
                Tables\Actions\ViewAction::make()
                ->label('View Listing')
                ->url(fn($record) => $record->ad_url, true)
            ])
            ->headerActions([
                ExportAction::make()
                    ->exporter(ContactAnalyticExporter::class)
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactAnalytics::route('/'),
            // 'create' => Pages\CreateContactAnalytic::route('/create'),
            'edit' => Pages\EditContactAnalytic::route('/{record}/edit'),
        ];
    }
}
