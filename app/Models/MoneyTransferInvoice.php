<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class MoneyTransferInvoice extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $table = 'money_transfer_invoices';

    protected $fillable = [
        'invoice_number',
        'customer_name',
        'phone',
        'transfer_type',
        'bank_name',
        'acc_name',
        'acc_number',
        'currency',
        'entered_amount',
        'trf_fee_in_persentage',
        'trf_fee',
        'net_amount',
        'status',
        'reject_reason',
        'invoice_url',
        'invoice_slip',
        'transaction_slip',
        'createdBy',
    ];

    /**
     * Status labels for display
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending_bkk_approval' => __('message.Pending'),
            'accepted_bkk'         => __('message.Accepted'),
            'completed'            => __('message.Completed'),
            'Rejected'             => __('message.Rejected'),
            'cancelled'            => __('message.Cancelled'),
            default                => $this->status,
        };
    }

    /**
     * Scopes
     */
    public function scopeTransferOut($query)
    {
        return $query->where('transfer_type', 'Transfer-OUT');
    }

    public function scopeTransferIn($query)
    {
        return $query->where('transfer_type', 'Transfer-IN');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', now()->toDateString());
    }

    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_bkk_approval');
    }

    public function scopeAccepted($query)
    {
        return $query->where('status', 'accepted_bkk');
    }
}
