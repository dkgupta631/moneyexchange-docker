<?php

namespace App\Filament\Bkkoffice\Widgets;

use App\Models\MoneyTransferInvoice;
use Filament\Widgets\Widget;
use Filament\Notifications\Notification;
use Illuminate\Support\Carbon;

class NewTransferAlertWidget extends Widget
{
    protected static string $view = 'filament.bkkoffice.widgets.new-transfer-alert';

    protected static bool $isLazy = false;

    protected int | string | array $columnSpan = 'full';

    public bool  $showPopup      = false;
    public bool  $showRejectForm = false;
    public string $rejectReason  = '';
    public ?array $latestTransfer = null;
    public int   $lastKnownCount = 0;
    public int   $pendingCount   = 0;

    public function mount(): void
    {
        $this->lastKnownCount = cache()->get('bkk_transfer_count_' . auth()->id(), 0);
        $this->pendingCount   = $this->getPendingCount();
    }

    public function getPendingCount(): int
    {
        return MoneyTransferInvoice::where('status', 'pending_bkk_approval')
            ->where('transfer_type', 'Transfer-OUT')
            ->whereDate('created_at', Carbon::today())
            ->count();
    }

    public function checkNewTransfers(): void
    {
        $currentCount       = $this->getPendingCount();
        $this->pendingCount = $currentCount;

        if ($currentCount > $this->lastKnownCount) {
            $latest = MoneyTransferInvoice::where('status', 'pending_bkk_approval')
                ->where('transfer_type', 'Transfer-OUT')
                ->latest()
                ->first();

            if ($latest) {
                $this->latestTransfer = [
                    'id'             => $latest->id,
                    'invoice_number' => $latest->invoice_number,
                    'customer_name'  => $latest->customer_name,
                    'acc_name'       => $latest->acc_name,       // ✅ added
                    'acc_number'     => $latest->acc_number,     // ✅ added
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
                $this->rejectReason   = '';

                $this->dispatch('new-transfer-arrived', transfer: $this->latestTransfer);

                Notification::make()
                    ->title('🔔 ' . __('message.New Transfer-OUT Request!'))
                    ->body(__('message.From') . ' — ' . ($latest->currency) . ' ' . number_format($latest->entered_amount, 2))
                    ->warning()
                    ->persistent()
                    ->send();
            }

            $this->lastKnownCount = $currentCount;
            cache()->put('bkk_transfer_count_' . auth()->id(), $currentCount, 3600);
        }
    }

    public function acceptTransfer(int $id): void
    {
        $record = MoneyTransferInvoice::find($id);
        if ($record) {
            $record->update(['status' => 'accepted_bkk']);

            Notification::make()
                ->title('✅ ' . __('message.Transfer Accepted'))
                ->body(__('message.Invoice') . " #{$record->invoice_number} " . __('message.has been accepted.'))
                ->success()
                ->send();
        }

        $this->closePopup();
    }

    // ✅ Step 1 — show reject reason form inside popup
    public function openRejectForm(): void
    {
        $this->showRejectForm = true;
        $this->rejectReason   = '';
    }

    public function cancelReject(): void
    {
        $this->showRejectForm = false;
        $this->rejectReason   = '';
    }

    // ✅ Step 2 — submit reject with reason
    public function submitReject(int $id): void
    {
        $this->validate([
            'rejectReason' => 'required|min:3',
        ], [
            'rejectReason.required' => __('message.Reason for Rejection') . ' ' . __('message.is required'),
            'rejectReason.min'      => __('message.Reason must be at least 3 characters'),
        ]);

        $record = MoneyTransferInvoice::find($id);
        if ($record) {
            $record->update([
                'status'        => 'Rejected',
                'reject_reason' => $this->rejectReason,  // ✅ saved to DB
            ]);

            Notification::make()
                ->title('❌ ' . __('message.Transfer Rejected'))
                ->body(__('message.Invoice') . " #{$record->invoice_number} " . __('message.has been rejected.'))
                ->danger()
                ->send();
        }

        $this->closePopup();
    }

    public function closePopup(): void
    {
        $this->showPopup      = false;
        $this->showRejectForm = false;
        $this->rejectReason   = '';
        $this->latestTransfer = null;
        $this->lastKnownCount = $this->getPendingCount();
        $this->pendingCount   = $this->lastKnownCount;
        cache()->put('bkk_transfer_count_' . auth()->id(), $this->lastKnownCount, 3600);
    }
}