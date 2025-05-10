<?php

namespace App\Filament\Exports;

use App\Models\ContactAnalytic;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ContactAnalyticExporter extends Exporter
{

    protected static ?string $model = ContactAnalytic::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('ad.title')->label('Ad Title'),
            ExportColumn::make('viewer_name')->label('Viewed By'),
            ExportColumn::make('viewer_phone')->label('Viewer Phone'),
            ExportColumn::make('viewer_email')->label('Viewer Email'),
            ExportColumn::make('ad_price')->label('Ad Price'),
            ExportColumn::make('ad_url')->label('Ad URL'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Your contact analytic export has completed and ' . number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows) . ' exported.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount) . ' failed to export.';
        }

        return $body;
    }
}
