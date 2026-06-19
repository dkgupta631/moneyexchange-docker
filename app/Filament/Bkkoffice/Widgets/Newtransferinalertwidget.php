<?php

namespace App\Filament\Bkkoffice\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\Widget;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class NewTransferInAlertWidget extends Widget
{
    protected static string $view = 'filament.bkkoffice.widgets.new-transfer-in-alert';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public bool   $showPopup        = false;
    public bool   $showRejectForm   = false;
    public bool   $showUploadForm   = false;
    public string $rejectCategory   = '';
    public string $rejectReasonText = '';
    public ?array $latestTransfer   = null;
    public int    $lastKnownCount   = 0;
    public int    $pendingCount     = 0;

    public function mount(): void
    {
        $this->lastKnownCount = cache()->get('bkk_transferin_count_' . auth()->id(), 0);
        $this->pendingCount   = $this->getPendingCount();
    }

    public function getPendingCount(): int
    {
        return MoneyTransferInvoice::where('status', 'pending_bkk_approval')
            ->where('transfer_type', 'Transfer-IN')
            ->whereDate('created_at', Carbon::today())
            ->count();
    }

    public function checkNewTransfers(): void
    {
        $currentCount       = $this->getPendingCount();
        $this->pendingCount = $currentCount;

        if ($currentCount > $this->lastKnownCount) {
            $latest = MoneyTransferInvoice::where('status', 'pending_bkk_approval')
                ->where('transfer_type', 'Transfer-IN')
                ->latest()
                ->first();

            if ($latest) {
                $this->latestTransfer = [
                    'id'             => $latest->id,
                    'invoice_number' => $latest->invoice_number,
                    'customer_name'  => $latest->customer_name,
                    'acc_name'       => $latest->acc_name,
                    'acc_number'     => $latest->acc_number,
                    'bank_name'      => $latest->bank_name,
                    'currency'       => $latest->currency,
                    'amount'         => number_format($latest->entered_amount, 2),
                    'trf_fee'        => number_format($latest->trf_fee, 2),
                    'net_amount'     => number_format($latest->net_amount, 2),
                    'created_at'     => $latest->created_at
                                           ->setTimezone('Asia/Bangkok')
                                           ->format('d M Y H:i:s'),
                ];

                $this->showPopup      = true;
                $this->showRejectForm = false;
                $this->showUploadForm = false;
                $this->rejectCategory = '';
                $this->rejectReasonText = '';

                $this->dispatch('new-transferin-arrived', transfer: $this->latestTransfer);

                Notification::make()
                    ->title('🔔 ' . __('message.New Transfer-IN Request!'))
                    ->body(($latest->currency) . ' ' . number_format($latest->entered_amount, 2) . ' — ' . $latest->bank_name)
                    ->success()
                    ->persistent()
                    ->send();
            }

            $this->lastKnownCount = $currentCount;
            cache()->put('bkk_transferin_count_' . auth()->id(), $currentCount, 3600);
        }
    }

    // ✅ Accept → show upload form
    public function openUploadForm(): void
    {
        $this->showRejectForm = false;
        $this->showUploadForm = true;
    }

    // ✅ Submit upload from popup (called from Alpine with file handled separately)
    public function acceptTransfer(int $id): void
    {
        $record = MoneyTransferInvoice::find($id);
        if ($record) {
            $record->update(['status' => 'accepted_bkk']);

            Notification::make()
                ->title('✅ ' . __('message.Transfer-IN Accepted'))
                ->body(__('message.Invoice') . " #{$record->invoice_number} " . __('message.has been accepted. Please upload the slip.'))
                ->success()
                ->send();
        }

        $this->closePopup();
    }

    // ✅ Open reject form
    public function openRejectForm(): void
    {
        $this->showRejectForm   = true;
        $this->showUploadForm   = false;
        $this->rejectCategory   = '';
        $this->rejectReasonText = '';
    }

    public function cancelReject(): void
    {
        $this->showRejectForm   = false;
        $this->rejectCategory   = '';
        $this->rejectReasonText = '';
    }

    public function cancelUpload(): void
    {
        $this->showUploadForm = false;
    }

    // ✅ Reject reason passed from Alpine (no wire:model focus issue)
    public function submitReject(int $id, string $category, string $otherReason): void
    {
        $reason = $category === 'other'
            ? (trim($otherReason) ?: __('message.Other'))
            : $category;

        if (empty(trim($reason))) {
            return;
        }

        $record = MoneyTransferInvoice::find($id);
        if ($record) {
            $record->update([
                'status'        => 'Rejected',
                'reject_reason' => $reason,
            ]);

            Notification::make()
                ->title('❌ ' . __('message.Transfer-IN Rejected'))
                ->body(__('message.Invoice') . " #{$record->invoice_number} — " . $reason)
                ->danger()
                ->send();
        }

        $this->closePopup();
    }

    public function closePopup(): void
    {
        $this->showPopup        = false;
        $this->showRejectForm   = false;
        $this->showUploadForm   = false;
        $this->rejectCategory   = '';
        $this->rejectReasonText = '';
        $this->latestTransfer   = null;
        $this->lastKnownCount   = $this->getPendingCount();
        $this->pendingCount     = $this->lastKnownCount;
        cache()->put('bkk_transferin_count_' . auth()->id(), $this->lastKnownCount, 3600);
        $this->dispatch('transferin-popup-closed');
    }
}