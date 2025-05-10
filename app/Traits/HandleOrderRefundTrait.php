<?php

namespace App\Traits;

use App\Models\Reservation\RefundTransaction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;


trait HandleOrderRefundTrait
{
    public static function orderNotReceivedForm(): array
    {
        $proofFileUpload = SpatieMediaLibraryFileUpload::make('proof_attachment')
        ->maxSize(maxUploadFileSize())
        ->collection('order_refund_proof')
        ->visibility('public')
        ->image()
        ->required()
        ->imageEditor();

        $summaryFileUpload = SpatieMediaLibraryFileUpload::make('summary_attachment')
        ->maxSize(maxUploadFileSize())
        ->collection('order_summary_proof')
        ->visibility('public')
        ->image()
        ->required()
        ->imageEditor();

        $storageType = config('filesystems.default');

        if ($storageType == 's3') {
            $proofFileUpload->disk($storageType);
            $summaryFileUpload->disk($storageType);
        }

        return [
            $proofFileUpload->label(__('messages.t_order_refund_proof'))->helperText(__('messages.t_order_refund_proof_helper')),
            $summaryFileUpload->label(__('messages.t_order_summary_proof'))->helperText(__('messages.t_order_summary_proof_helper')),
            Textarea::make('description')
                ->label(__('messages.t_description'))
                ->required()
                ->placeholder(__('messages.t_order_refund_description')),
        ];
    }
}
