<?php

namespace App\Filament\Resources\TransferOutReportResource\Pages;

use App\Filament\Resources\TransferOutReportResource;
use Filament\Resources\Pages\ListRecords;
use Filament\Actions\Action;

class ListTransferOutReports extends ListRecords
{
    protected static string $resource = TransferOutReportResource::class;
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
         return __('message.transfer_out_report');
    }
}