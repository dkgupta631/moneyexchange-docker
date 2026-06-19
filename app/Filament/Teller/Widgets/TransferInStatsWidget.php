<?php

namespace App\Filament\Teller\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class TransferInStatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $today = Carbon::today();

        $pending   = MoneyTransferInvoice::where('transfer_type', 'Transfer-IN')->whereDate('created_at', $today)->whereIn('status', ['pending_bkk_approval'])->count();
        $accepted  = MoneyTransferInvoice::where('transfer_type', 'Transfer-IN')->whereDate('created_at', $today)->where('status', 'accepted_bkk')->count();
        $completed = MoneyTransferInvoice::where('transfer_type', 'Transfer-IN')->whereDate('created_at', $today)->where('status', 'completed')->count();
        $rejected  = MoneyTransferInvoice::where('transfer_type', 'Transfer-IN')->whereDate('created_at', $today)->where('status', 'Rejected')->count();

        $totalNet = MoneyTransferInvoice::where('transfer_type', 'Transfer-IN')->whereDate('created_at', $today)->where('status', 'completed')->sum('net_amount');
        $totalFee = MoneyTransferInvoice::where('transfer_type', 'Transfer-IN')->whereDate('created_at', $today)->where('status', 'completed')->sum('trf_fee');

        return [
            Stat::make('⏳ ' . __('message.Pending') . ' ' . __('message.Transfer-IN'), $pending)
                ->description(__('message.Awaiting your action'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning')
                ->chart([1, $pending]),

            Stat::make('✅ ' . __('message.Accepted') . ' ' . __('message.Transfer-IN'), $accepted)
                ->description(__('message.Processing in progress'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),

            Stat::make('✔ ' . __('message.Completed') . ' ' . __('message.Transfer-IN'), $completed)
                ->description('Total: ' . number_format($totalNet, 2) . ' | Fee: ' . number_format($totalFee, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('❌ ' . __('message.Rejected') . ' ' . __('message.Transfer-IN'), $rejected)
                ->description(__('message.Review if needed'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}