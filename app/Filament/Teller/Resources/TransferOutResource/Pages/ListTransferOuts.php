<?php

namespace App\Filament\Teller\Resources\TransferOutResource\Pages;

use App\Filament\Teller\Resources\TransferOutResource;
use App\Models\MoneyTransferInvoice;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions;
use Illuminate\Support\Carbon;

class ListTransferOuts extends ListRecords
{
    protected static string $resource = TransferOutResource::class;

    /**
     * Page title with i18n
     */
    public function getBreadcrumb(): string
    {
        return __('message.List');
    }
    public function getTitle(): string
    {
        return __('message.Transfer-OUT Requests');
    }

    /**
     * Header actions (none for teller)
     */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * Header widgets: show the live-poll notification widget
     */
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Teller\Widgets\TransferOutLiveNotificationWidget::class,
        ];
    }
}