<?php

namespace App\Filament\Teller\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;
use Filament\Notifications\Notification;

class TransferOutLiveNotificationWidget extends Widget
{
    protected static string $view = 'filament.teller.widgets.transfer-out-live-notification';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    // public array $latestAccepted  = [];
    public array $latestCompleted = [];
    public int   $pendingCount    = 0;

    /**
     * Track seen record IDs instead of counts.
     * Count-based diffing double-fires when multiple records
     * change status in the same poll interval.
     */
    // public array $seenAcceptedIds  = [];
    public array $seenCompletedIds = [];

    public function mount(): void
    {
        // Restore seen IDs from cache so a page refresh doesn't
        // re-fire notifications for records the user already saw.
        // $this->seenAcceptedIds  = (array) cache()->get($this->cacheKeyAccepted(),  []);
        $this->seenCompletedIds = (array) cache()->get($this->cacheKeyCompleted(), []);

        $this->loadData();
    }

    // ── called by wire:poll every 5 s ────────────────────────────────────
    public function checkNewNotifications(): void
    {
        $this->loadData();

        // ── new ACCEPTED records? ─────────────────────────────────────────
        // $newAccepted = array_filter(
        //     $this->latestAccepted,
        //     fn($r) => ! in_array($r['id'], $this->seenAcceptedIds, true)
        // );

        // foreach ($newAccepted as $record) {
        //     // Dispatch browser event so Alpine/JS listeners can react
        //     // (e.g. open a popup modal). The Filament notification is
        //     // sent in the Livewire action called back from the view.
        //     $this->dispatch('teller-new-notification', record: $record);
        // }

        // if (! empty($newAccepted)) {
        //     $newIds = array_column($newAccepted, 'id');
        //     $this->seenAcceptedIds = array_values(
        //         array_unique(array_merge($this->seenAcceptedIds, $newIds))
        //     );
        //     cache()->put($this->cacheKeyAccepted(), $this->seenAcceptedIds, 3600);
        // }

        // ── new COMPLETED records? ────────────────────────────────────────
        $newCompleted = array_filter(
            $this->latestCompleted,
            fn($r) => ! in_array($r['id'], $this->seenCompletedIds, true)
        );

        foreach ($newCompleted as $record) {
            $this->dispatch('teller-new-notification', record: $record);
        }

        if (! empty($newCompleted)) {
            $newIds = array_column($newCompleted, 'id');
            $this->seenCompletedIds = array_values(
                array_unique(array_merge($this->seenCompletedIds, $newIds))
            );
            cache()->put($this->cacheKeyCompleted(), $this->seenCompletedIds, 3600);
        }
    }

    /**
     * Called from the Blade view via Alpine:
     *   @teller-new-notification.window="$wire.sendFilamentNotification($event.detail.record)"
     *
     * Filament's Notification::make()->send() MUST run inside a proper
     * Livewire action — never inside poll callbacks or mount().
     */
    public function sendFilamentNotification(array $record): void
    {
        $invoiceNumber = $record['invoice_number'] ?? '—';

        if (($record['popup_type'] ?? '') === 'completed') {
            Notification::make()
                ->title('✅ ' . __('message.Completed!'))
                ->body(
                    __('message.Invoice') . " {$invoiceNumber} " .
                    __('message.marked as completed. Slip saved.')
                )
                ->success()
                ->send();

            return;
        }

        // Default: accepted
        Notification::make()
            ->title('✅ ' . __('message.Transfer Accepted'))
            ->body(
                __('message.Invoice') . " #{$invoiceNumber} " .
                __('message.has been accepted.')
            )
            ->success()
            ->send();
    }

    private function loadData(): void
    {
        $today = Carbon::today();

        $this->pendingCount = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', $today)
            ->whereIn('status', ['pending_bkk_approval', 'accepted_bkk'])
            ->count();

        // $this->latestAccepted = MoneyTransferInvoice::query()
        //     ->where('transfer_type', 'Transfer-OUT')
        //     ->whereDate('created_at', $today)
        //     ->where('status', 'accepted_bkk')
        //     ->orderByDesc('updated_at')
        //     ->limit(20)
        //     ->get()
        //     ->map(fn ($r) => [
        //         'id'             => (string) $r->id,
        //         'popup_type'     => 'accepted',
        //         'invoice_number' => (string) ($r->invoice_number ?? ''),
        //         'customer_name'  => (string) ($r->customer_name  ?? ''),
        //         'bank_name'      => (string) ($r->bank_name      ?? ''),
        //         'acc_name'       => (string) ($r->acc_name       ?? ''),
        //         'acc_number'     => (string) ($r->acc_number     ?? ''),
        //         'currency'       => (string) ($r->currency       ?? 'THB'),
        //         'entered_amount' => (float)  ($r->entered_amount ?? 0),
        //         'net_amount'     => (float)  ($r->net_amount     ?? 0),
        //     ])
        //     ->toArray();

        $this->latestCompleted = MoneyTransferInvoice::query()
            ->where('transfer_type', 'Transfer-OUT')
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
                'currency'       => (string) ($r->currency       ?? 'THB'),
                'entered_amount' => (float)  ($r->entered_amount ?? 0),
                'net_amount'     => (float)  ($r->net_amount     ?? 0),
            ])
            ->toArray();
    }

    private function cacheKeyAccepted(): string
    {
        return 'teller_seen_accepted_ids_' . auth()->id();
    }

    private function cacheKeyCompleted(): string
    {
        return 'teller_seen_completed_ids_' . auth()->id();
    }
}