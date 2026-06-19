<?php

namespace App\Filament\Teller\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

use Illuminate\Support\HtmlString;

class TransferOutStatsWidget extends BaseWidget
{
    /**
     * Auto-refresh every 8 seconds for real-time updates
     */
    protected static ?string $pollingInterval = '5s';

    protected function getStats(): array
    {

        $pending = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'pending_bkk_approval')
            ->count();

        $accepted = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'accepted_bkk')
            ->count();

        $completed = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->count();

        $completedAmount = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->sum('net_amount');
        $totalFee = MoneyTransferInvoice::where('status', 'completed')
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', Carbon::today())
            ->sum('trf_fee');

        $netFeeDescription = new HtmlString('
            <div style="display:flex; width:100%; margin-top:8px; border-radius:10px; overflow:hidden; border:1px solid rgba(22,163,74,0.18); box-shadow:0 1px 4px rgba(0,0,0,0.07);">
                <div style="flex:1; background:linear-gradient(135deg,#f0fdf4,#dcfce7); padding:9px 14px; display:flex; flex-direction:column; gap:2px;">
                    <span style="font-size:9px; font-weight:700; color:#15803d; text-transform:uppercase; letter-spacing:0.08em;">&#x2197; Total</span>
                    <span style="font-size:15px; font-weight:800; color:#15803d; letter-spacing:-0.3px; line-height:1.2;">&#3647;' . number_format($completedAmount, 2) . '</span>
                </div>
                <div style="width:1px; background:rgba(22,163,74,0.2);"></div>
                <div style="flex:1; background:linear-gradient(135deg,#eff6ff,#dbeafe); padding:9px 14px; display:flex; flex-direction:column; gap:2px; align-items:flex-end;">
                    <span style="font-size:9px; font-weight:700; color:#1d4ed8; text-transform:uppercase; letter-spacing:0.08em;">&#128179; Fee </span>
                    <span style="font-size:15px; font-weight:800; color:#1d4ed8; letter-spacing:-0.3px; line-height:1.2;">&#3647;' . number_format($totalFee, 2) . '</span>
                </div>
            </div>
        ');

        $rejected = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'Rejected')
            ->count();

        return [
            Stat::make(
                '⏳ ' . __('message.Pending') . ' ' . __('message.Transfer-OUT'),
                $pending
            )
                ->description(__('message.Awaiting your action'))
                ->descriptionIcon('heroicon-m-clock')
                ->color($pending > 0 ? 'warning' : 'gray')
                ->chart($this->getRecentCounts('pending_bkk_approval')),

            Stat::make(
                '✅ ' . __('message.Accepted') . ' ' . __('message.Transfer-OUT'),
                $accepted
            )
                ->description(__('message.Processing in progress'))
                ->descriptionIcon('heroicon-m-check-circle')
                ->color($accepted > 0 ? 'info' : 'gray')
                ->chart($this->getRecentCounts('accepted_bkk')),

            Stat::make(
                '✔️ ' . __('message.Completed') . ' ' . __('message.Transfer-OUT'),
                $completed
            )
                ->description( __('message.Total').' ' . number_format($completedAmount, 2). ' | '. __('message.Fee') .' '. number_format($totalFee, 2))
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success')
                ->chart($this->getRecentCounts('completed')),

            Stat::make(
                '✖️ ' . __('message.Rejected') . ' ' . __('message.Transfer-OUT'),
                $rejected
            )
                ->description(__('message.Review if needed'))
                ->descriptionIcon('heroicon-m-x-circle')
                ->color($rejected > 0 ? 'danger' : 'gray')
                ->chart($this->getRecentCounts('Rejected')),
        ];
    }

    /**
     * Generate a mini 7-point chart for last 7 days counts
     */
    private function getRecentCounts(string $status): array
    {
        $counts = [];
        for ($i = 6; $i >= 0; $i--) {
            $counts[] = MoneyTransferInvoice::query()
                ->where('transfer_type', 'Transfer-OUT')
                ->whereDate('created_at', Carbon::today()->subDays($i))
                ->where('status', $status)
                ->count();
        }
        return $counts;
    }
}