<?php

namespace App\Livewire\Admin\System;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Computed;
use Livewire\Component;
use App\FilamentSpatieLaravelBackupPlugin;
use App\Models\BackupDestinationStatus;

class BackupDestinationStatusList extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public function render(): View
    {
        return view('livewire.admin.system.backup-destination-status-list');
    }

    public function table(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->query(BackupDestinationStatus::query())
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label(__('messages.t_ap_name')),
                Tables\Columns\TextColumn::make('disk')
                    ->label(__('messages.t_ap_disk')),
                Tables\Columns\IconColumn::make('healthy')
                    ->label(__('messages.t_ap_healthy'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('amount')
                    ->label(__('messages.t_ap_amount')),
                Tables\Columns\TextColumn::make('newest')
                    ->label(__('messages.t_ap_newest')),
                Tables\Columns\TextColumn::make('usedStorage')
                    ->label(__('messages.t_ap_used_storage'))
                    ->badge(),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }

    #[Computed]
    public function interval(): string
    {
        return '10s';
    }
}
