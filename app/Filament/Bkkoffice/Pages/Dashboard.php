<?php

namespace App\Filament\Bkkoffice\Pages;

use App\Filament\Bkkoffice\Widgets\TransferStatsWidget;
use App\Filament\Bkkoffice\Widgets\TransferInStatsWidget;
use App\Filament\Bkkoffice\Widgets\NewTransferAlertWidget;
use App\Filament\Bkkoffice\Widgets\NewTransferInAlertWidget;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?int $navigationSort = 0;

    public function getTitle(): string
    {
        return __('message.Dashboard');
    }

    public static function getNavigationLabel(): string
    {
        return __('message.Dashboard');
    }

    // ✅ Change getWidgets() to getHeaderWidgets()
    public function getWidgets(): array
    {
        return [
            NewTransferInAlertWidget::class,
            NewTransferAlertWidget::class,
            TransferInStatsWidget::class,
            TransferStatsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 1;
    }
}