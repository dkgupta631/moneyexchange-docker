<?php

namespace App\Filament\Bkkoffice\Resources\TransferInResource\Pages;

use App\Filament\Bkkoffice\Resources\TransferInResource;
use App\Filament\Bkkoffice\Widgets\NewTransferInAlertWidget;
use Filament\Resources\Pages\ListRecords;

class ListTransferIn extends ListRecords
{
    protected static string $resource = TransferInResource::class;

    public function getBreadcrumb(): string
    {
        return __('message.List');
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            NewTransferInAlertWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}