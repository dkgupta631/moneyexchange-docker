<?php

namespace App\Filament\Teller\Resources\TransferInResource\Pages;

use App\Filament\Teller\Resources\TransferInResource;
use App\Filament\Teller\Widgets\TransferInLiveNotificationWidget;
use Filament\Resources\Pages\ListRecords;

class ListTransferIns extends ListRecords
{
    protected static string $resource = TransferInResource::class;

    public function getBreadcrumb(): string
    {
        return __('message.List');
    }
    public function getTitle(): string
    {
        return __('message.Transfer-IN Requests');
    }

    protected function getHeaderWidgets(): array
    {
        return [
            TransferInLiveNotificationWidget::class,
        ];
    }

    protected function getHeaderActions(): array
    {
        return [];
    }
}