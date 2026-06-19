<?php

namespace App\Filament\Teller\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Notifications\Notification;
use Filament\Widgets\Widget;
use Illuminate\Support\Carbon;

/**
 * GlobalTransferInNotificationWidget
 *
 * This widget is injected into EVERY teller page via RenderHook (BODY_START).
 * It runs the same polling + popup + voice logic as TransferInLiveNotificationWidget
 * but is mounted globally so it works on any page — dashboard, transfer-out, profile, etc.
 *
 * Place view at:
 *   resources/views/filament/teller/widgets/global-transfer-in-notification.blade.php
 */
class GlobalTransferInNotificationWidget extends Widget
{
    protected static string $view = 'filament.teller.widgets.global-transfer-in-notification';

    protected static bool $isLazy = false;

    // Prevent it showing in normal widget discovery slots
    protected static bool $isDiscovered = false;

    protected int|string|array $columnSpan = 'full';

    public int   $pendingCount      = 0;
    public array $latestCompleted   = [];
    public array $seenCompletedIds  = [];

    public function mount(): void
    {
        $this->seenCompletedIds = (array) cache()->get($this->cacheKey(), []);
        $this->loadData();
    }

    // ── Called by wire:poll.5000ms in the blade view ─────────────────────────
    public function checkNewNotifications(): void
    {
        $this->loadData();

        $newCompleted = array_filter(
            $this->latestCompleted,
            fn ($r) => ! in_array($r['id'], $this->seenCompletedIds, true)
        );

        foreach ($newCompleted as $record) {
            $this->dispatch('teller-transferin-notification', record: $record);
        }

        if (! empty($newCompleted)) {
            $newIds = array_column($newCompleted, 'id');
            $this->seenCompletedIds = array_values(
                array_unique(array_merge($this->seenCompletedIds, $newIds))
            );
            cache()->put($this->cacheKey(), $this->seenCompletedIds, now()->addHours(12));
        }
    }

    // ── Called from Alpine via @this.call() ──────────────────────────────────
    public function sendFilamentNotification(array $record): void
    {
        Notification::make()
            ->title('✅ ' . __('message.Transfer-IN Completed!'))
            ->body(__('message.Invoice') . ' ' . ($record['invoice_number'] ?? '—') . ' ' . __('message.marked as completed. Slip saved.'))
            ->success()
            ->send();
    }

    // ── Data loader ──────────────────────────────────────────────────────────
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

    private function cacheKey(): string
    {
        return 'teller_global_seen_tin_completed_' . auth()->id();
    }
}