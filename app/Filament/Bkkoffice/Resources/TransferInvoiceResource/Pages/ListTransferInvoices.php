<?php

namespace App\Filament\Bkkoffice\Resources\TransferInvoiceResource\Pages;

use App\Filament\Bkkoffice\Resources\TransferInvoiceResource;
use App\Filament\Bkkoffice\Widgets\NewTransferAlertWidget;
use Filament\Resources\Pages\ListRecords;

class ListTransferInvoices extends ListRecords
{
    protected static string $resource = TransferInvoiceResource::class;
    public function getBreadcrumb(): string
    {
        return __('message.List');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            NewTransferAlertWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}