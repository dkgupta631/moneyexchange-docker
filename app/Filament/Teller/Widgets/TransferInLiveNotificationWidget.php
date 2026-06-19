<?php

namespace App\Filament\Teller\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Filament\Notifications\Notification;

class TransferInLiveNotificationWidget extends Widget
{
    protected static string $view = 'filament.teller.widgets.transfer-in-live-notification';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public array $latestCompleted = [];
    public int   $pendingCount    = 0;
    public array $seenCompletedIds = [];

    public function mount(): void
    {
        $this->seenCompletedIds = (array) cache()->get($this->cacheKeyCompleted(), []);
        $this->loadData();
    }

    public function checkNewNotifications(): void
    {
        $this->loadData();

        $newCompleted = array_filter(
            $this->latestCompleted,
            fn($r) => ! in_array($r['id'], $this->seenCompletedIds, true)
        );

        foreach ($newCompleted as $record) {
            $this->dispatch('teller-transferin-notification', record: $record);
        }

        if (! empty($newCompleted)) {
            $newIds = array_column($newCompleted, 'id');
            $this->seenCompletedIds = array_values(
                array_unique(array_merge($this->seenCompletedIds, $newIds))
            );
            cache()->put($this->cacheKeyCompleted(), $this->seenCompletedIds, 3600);
        }
    }

    public function sendFilamentNotification(array $record): void
    {
        $invoiceNumber = $record['invoice_number'] ?? '—';

        Notification::make()
            ->title('✅ ' . __('message.Transfer-IN Completed!'))
            ->body(__('message.Invoice') . " {$invoiceNumber} " . __('message.marked as completed. Slip saved.'))
            ->success()
            ->send();
    }

    private function loadData(): void
    {
        $today = Carbon::today();

        $this->pendingCount = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', $today)
            ->whereIn('status', ['pending_bkk_approval', 'accepted_bkk'])
            ->count();

        $this->latestCompleted = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', $today)
            ->where('status', 'completed')
            ->orderByDesc('updated_at')
            ->limit(20)
            ->get()
            ->map(fn ($r) => [
                'id'             => (string) $r->id . '_c',
                'popup_type'     => 'completed',
                'invoice_number' => (string) ($r->invoice_number ?? ''),
                'customer_name'  => (string) ($r->customer_name  ?? ''),
                'bank_name'      => (string) ($r->bank_name      ?? ''),
                'acc_name'       => (string) ($r->acc_name       ?? ''),
                'acc_number'     => (string) ($r->acc_number     ?? ''),
                'currency'       => (string) ($r->currency       ?? ''),
                'entered_amount' => (float)  ($r->entered_amount ?? 0),
                'net_amount'     => (float)  ($r->net_amount     ?? 0),
                'trf_fee'        => (float)  ($r->trf_fee        ?? 0),
            ])
            ->toArray();
    }

    private function cacheKeyCompleted(): string
    {
        return 'teller_seen_transferin_completed_ids_' . auth()->id();
    }
}