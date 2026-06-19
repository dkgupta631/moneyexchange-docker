<?php

namespace App\Filament\Resources\TransferInReportResource\Pages;

use App\Filament\Resources\TransferInReportResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListTransferInReports extends ListRecords
{
    protected static string $resource = TransferInReportResource::class;
    public function getBreadcrumb(): string
    {
        return __('message.List');
    }
    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTitle(): string
    {
        return __('message.transfer_in_report');
    }
}