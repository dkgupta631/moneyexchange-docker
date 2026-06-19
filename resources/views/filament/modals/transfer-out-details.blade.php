{{-- resources/views/filament/modals/transfer-out-details.blade.php --}}
@php
    $statusConfig = match($record->status) {
        'completed'            => ['label' => 'COMPLETED',    'bg' => '#16a34a', 'glow' => '#22c55e33'],
        'pending_bkk_approval' => ['label' => 'PENDING BKK',  'bg' => '#d97706', 'glow' => '#f59e0b33'],
        'accepted_bkk'         => ['label' => 'ACCEPTED BKK', 'bg' => '#2563eb', 'glow' => '#3b82f633'],
        'Rejected'             => ['label' => 'REJECTED',     'bg' => '#dc2626', 'glow' => '#ef444433'],
        'cancelled'            => ['label' => 'CANCELLED',    'bg' => '#6b7280', 'glow' => '#9ca3af33'],
        default                => ['label' => strtoupper($record->status ?? ''), 'bg' => '#6b7280', 'glow' => '#9ca3af33'],
    };
@endphp

<div style="font-family:'Poppins',sans-serif;background:#080f1e;border-radius:16px;overflow:hidden;margin:-20px;color:#e2e8f0;">

    {{-- HEADER --}}
    <div style="background:linear-gradient(135deg,#0e1f3d 0%,#162d56 60%,#1e3a6e 100%);padding:20px 24px 18px;display:flex;align-items:center;gap:14px;border-bottom:1px solid rgba(255,255,255,0.06);">
        <div style="width:46px;height:46px;flex-shrink:0;background:linear-gradient(135deg,#1e3a6e 0%,#3a68ae 100%);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 0 0 5px rgba(58,104,174,0.20),0 4px 14px rgba(58,104,174,0.35);">
            <svg width="22" height="22" fill="none" stroke="#fff" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M5 10l7-7m0 0l7 7m-7-7v18"/>
            </svg>
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:15px;font-weight:700;color:#fff;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                {{ __('message.Transfer-OUT') }}
                {{-- <span style="background:#3a68ae;color:#fff;font-size:9px;font-weight:700;letter-spacing:.09em;padding:2px 8px;border-radius:20px;">LIVE</span> --}}
            </div>
            <div style="font-size:11px;color:#8aaad8;margin-top:3px;">{{ __('message.Transfer-OUT') }} — {{ __('message.Bank Details') }}</div>
        </div>
    </div>

    {{-- BADGE ROW --}}
    <div style="background:#050c1a;padding:11px 24px;display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:8px;border-bottom:1px solid rgba(255,255,255,0.04);">
        <div style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;">
            <span style="display:inline-flex;align-items:center;background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);padding:4px 13px;border-radius:20px;font-size:11px;font-weight:600;color:#d4920a;font-family:'Courier New',monospace;">
                {{ $record->invoice_number }}
            </span>
            <span style="display:inline-block;background:{{ $statusConfig['bg'] }};color:#fff;font-size:10px;font-weight:700;letter-spacing:.1em;padding:4px 13px;border-radius:20px;box-shadow:0 0 0 3px {{ $statusConfig['glow'] }};">
                {{ $statusConfig['label'] }}
            </span>
        </div>
        <div style="font-size:11px;color:#5c87c4;white-space:nowrap;">
             {{ __('message.Created at') }}: <strong style="color:#8aaad8;">{{ $record->created_at?->format('H:i A') }}</strong>
        </div>
    </div>

    {{-- MAIN CARDS GRID --}}
    <div style="padding:18px 20px 8px;display:grid;grid-template-columns:1fr 1fr;gap:10px;">

        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:14px 16px;">
            <p style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#3a68ae;margin:0 0 7px;">{{ __('message.Invoice Number') }}</p>
            <p style="font-size:13px;font-weight:700;color:#d4920a;margin:0;font-family:monospace;">{{ $record->invoice_number ?? '—' }}</p>
        </div>

        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:14px 16px;">
            <p style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#3a68ae;margin:0 0 7px;">{{ __('message.Account Name') }}</p>
            <p style="font-size:13px;font-weight:700;color:#e2e8f0;margin:0;">{{ $record->acc_name ?? '—' }}</p>
        </div>

        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:14px 16px;">
            <p style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#3a68ae;margin:0 0 7px;">{{ __('message.Bank Name') }}</p>
            <p style="font-size:13px;font-weight:700;color:#e2e8f0;margin:0;">{{ $record->bank_name ?? '—' }}</p>
        </div>

        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:14px 16px;">
            <p style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#3a68ae;margin:0 0 7px;">{{ __('message.Account Number') }}</p>
            <p style="font-size:14px;font-weight:700;color:#e2e8f0;margin:0;font-family:monospace;letter-spacing:.07em;">{{ $record->acc_number ?? '—' }}</p>
        </div>

        {{-- Amount gold --}}
        <div style="background:linear-gradient(135deg,rgba(212,146,10,0.13) 0%,rgba(245,176,20,0.05) 100%);border:1px solid rgba(212,146,10,0.40);border-radius:12px;padding:16px 16px 14px;">
            <p style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#d4920a;margin:0 0 8px;">{{ __('message.Amount') }}</p>
            <p style="font-size:24px;font-weight:900;color:#f5b014;margin:0;line-height:1.1;letter-spacing:-.02em;">
                ฿ {{ number_format((float) $record->entered_amount, 2) }}
            </p>
        </div>

        {{-- Net Amount green --}}
        <div style="background:linear-gradient(135deg,rgba(22,163,74,0.13) 0%,rgba(34,197,94,0.05) 100%);border:1px solid rgba(22,163,74,0.40);border-radius:12px;padding:16px 16px 14px;">
            <p style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#16a34a;margin:0 0 8px;">{{ __('message.Net Amount') }}</p>
            <p style="font-size:24px;font-weight:900;color:#22c55e;margin:0;line-height:1.1;letter-spacing:-.02em;">
                ฿ {{ number_format((float) $record->net_amount, 2) }}
            </p>
        </div>

    </div>

    {{-- SECONDARY ROW --}}
    <div style="padding:0 20px 18px;display:grid;grid-template-columns:1fr 1fr;gap:10px;">

        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:14px 16px;">
            <p style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#3a68ae;margin:0 0 7px;">{{ __('message.Transfer Fee') }}</p>
            <p style="font-size:15px;font-weight:700;color:#f87171;margin:0;">
                ฿ {{ number_format((float) $record->trf_fee, 2) }}
                @if($record->trf_fee_in_persentage)
                    <span style="font-size:11px;color:#6b7280;font-weight:500;margin-left:4px;">({{ $record->trf_fee_in_persentage }}%)</span>
                @endif
            </p>
        </div>

        <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:14px 16px;">
            <p style="font-size:9px;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:#3a68ae;margin:0 0 7px;">{{ __('message.Customer') }}</p>
            <p style="font-size:13px;font-weight:700;color:#e2e8f0;margin:0;">{{ $record->customer_name ?? '—' }}</p>
            @if($record->phone)
                <p style="font-size:11px;color:#6b7280;margin:4px 0 0;">{{ $record->phone }}</p>
            @endif
        </div>

    </div>

    {{-- FOOTER LINKS --}}
    @if($record->invoice_url || $record->transaction_slip)
    <div style="display:flex;gap:10px;padding:14px 20px 20px;border-top:1px solid rgba(255,255,255,0.05);">
        @if($record->invoice_url)
        <a href="{{ $record->invoice_url }}" target="_blank"
           style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:11px 16px;border-radius:10px;background:rgba(212,146,10,0.10);border:1px solid rgba(212,146,10,0.30);color:#d4920a;font-size:12px;font-weight:600;text-decoration:none;"
           onmouseover="this.style.background='rgba(212,146,10,0.2)'" onmouseout="this.style.background='rgba(212,146,10,0.10)'">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            {{ __('message.View Invoice') }}
        </a>
        @endif
        @if($record->transaction_slip)
        <a href="{{ asset('storage/' . $record->transaction_slip) }}" target="_blank"
           style="flex:1;display:flex;align-items:center;justify-content:center;gap:7px;padding:11px 16px;border-radius:10px;background:rgba(34,197,94,0.10);border:1px solid rgba(34,197,94,0.30);color:#22c55e;font-size:12px;font-weight:600;text-decoration:none;"
           onmouseover="this.style.background='rgba(34,197,94,0.2)'" onmouseout="this.style.background='rgba(34,197,94,0.10)'">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 24 24">
                <path d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            {{ __('message.Transaction Slip') }}
        </a>
        @endif
    </div>
    @endif

</div>