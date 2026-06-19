<?php

namespace App\Filament\Bkkoffice\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

use Illuminate\Support\HtmlString;
use Illuminate\Support\Carbon;

class TransferInStatsWidget extends BaseWidget
{
    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    protected function getStats(): array
    {
        $pending   = MoneyTransferInvoice::where('status', 'pending_bkk_approval')->where('transfer_type', 'Transfer-IN')->whereDate('created_at', Carbon::today())->count();
        $accepted  = MoneyTransferInvoice::where('status', 'accepted_bkk')->where('transfer_type', 'Transfer-IN')->whereDate('created_at', Carbon::today())->count();
        $completed = MoneyTransferInvoice::where('status', 'completed')->where('transfer_type', 'Transfer-IN')->whereDate('created_at', Carbon::today())->count();
        $rejected  = MoneyTransferInvoice::where('status', 'Rejected')->where('transfer_type', 'Transfer-IN')->whereDate('created_at', Carbon::today())->count();

        $totalNet = MoneyTransferInvoice::where('status', 'completed')
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today())
            ->sum('net_amount');

        $totalFee = MoneyTransferInvoice::where('status', 'completed')
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today())
            ->sum('trf_fee');

        $netFeeDescription = new HtmlString('
            <div style="display:flex; width:100%; margin-top:8px; border-radius:10px; overflow:hidden; border:1px solid rgba(22,163,74,0.18); box-shadow:0 1px 4px rgba(0,0,0,0.07);">
                <div style="flex:1; background:linear-gradient(135deg,#f0fdf4,#dcfce7); padding:9px 14px; display:flex; flex-direction:column; gap:2px;">
                    <span style="font-size:9px; font-weight:700; color:#15803d; text-transform:uppercase; letter-spacing:0.08em;">&#x2197; Total</span>
                    <span style="font-size:15px; font-weight:800; color:#15803d; letter-spacing:-0.3px; line-height:1.2;">&#3647;' . number_format($totalNet, 2) . '</span>
                </div>
                <div style="width:1px; background:rgba(22,163,74,0.2);"></div>
                <div style="flex:1; background:linear-gradient(135deg,#eff6ff,#dbeafe); padding:9px 14px; display:flex; flex-direction:column; gap:2px; align-items:flex-end;">
                    <span style="font-size:9px; font-weight:700; color:#1d4ed8; text-transform:uppercase; letter-spacing:0.08em;">&#128179; Fee </span>
                    <span style="font-size:15px; font-weight:800; color:#1d4ed8; letter-spacing:-0.3px; line-height:1.2;">&#3647;' . number_format($totalFee, 2) . '</span>
                </div>
            </div>
        ');

        return [
            Stat::make('⏳ ' . __('message.Pending') . ' ' . __('message.Transfer-IN'), $pending)
                ->description(__('message.Awaiting your action'))
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),

            Stat::make('✅ ' . __('message.Accepted') . ' ' . __('message.Transfer-IN'), $accepted)
                ->description(__('message.Processing in progress'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color('primary'),

            Stat::make('✔ ' . __('message.Completed') . ' ' . __('message.Transfer-IN'), $completed)
                ->description($netFeeDescription)
                ->color('success'),

            Stat::make('❌ ' . __('message.Rejected') . ' ' . __('message.Transfer-IN'), $rejected)
                ->description(__('message.Review if needed'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color('danger'),
        ];
    }
}