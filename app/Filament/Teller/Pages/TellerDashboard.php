<?php
namespace App\Filament\Teller\Pages;
use Filament\Pages\Dashboard as BaseDashboard;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\AccountWidget;
use Illuminate\Support\Carbon;

class TellerDashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    //   protected static string $view = 'filament.teller.pages.teller-dashboard';
    protected static ?int $navigationSort = 0;

    public function getTitle(): string
    {
        return __('message.Dashboard');
    }

    public static function getNavigationLabel(): string
    {
        return __('message.Dashboard');
    }

    // ── Synced to Alpine via data-* attributes in view ───────────────────────
    public int $pendingCount  = 0;
    public int $acceptedCount = 0;
 
    public function mount(): void
    {
        $this->refreshCounts();
    }
 
    // ─── Called by wire:poll inside the notification component ───────────────
    public function checkNewNotifications(): void
    {
        $this->refreshCounts();
 
        $completed = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today())
            ->where('status', 'completed')
            ->latest('updated_at')
            ->first();
 
        if (! $completed) {
            return;
        }
 
        // Alpine localStorage seen-set prevents duplicate popups
        $this->dispatch('transfer-in-new-notification', record: [
            'id'             => $completed->id,
            'invoice_number' => $completed->invoice_number,
            'customer_name'  => $completed->customer_name,
            'bank_name'      => $completed->bank_name,
            'acc_name'       => $completed->acc_name,
            'acc_number'     => $completed->acc_number,
            'entered_amount' => $completed->entered_amount,
            'net_amount'     => $completed->net_amount,
            'currency'       => $completed->currency ?? '$',
            'status'         => $completed->status,
            'popup_type'     => 'completed',
        ]);
    }
 
    private function refreshCounts(): void
    {
        $base = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today());
 
        $this->pendingCount  = (clone $base)->where('status', 'pending_bkk_approval')->count();
        $this->acceptedCount = (clone $base)->where('status', 'accepted_bkk')->count();
    }
    /**
     * Widgets displayed on the teller dashboard
     */
    public function getWidgets(): array
    {
        return [
           \App\Filament\Teller\Widgets\TransferInLiveNotificationWidget::class,
            \App\Filament\Teller\Widgets\TransferInStatsWidget::class,
            \App\Filament\Teller\Widgets\TransferOutLiveNotificationWidget::class,
            \App\Filament\Teller\Widgets\TransferOutStatsWidget::class,
        ];
    }

    public function getColumns(): int | string | array
    {
        return 4;
    }
}