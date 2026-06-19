{{-- resources/views/filament/modals/transfer-in-details.blade.php --}}

@php
    $statusConfig = match($record->status) {
        'completed'            => ['label' => 'COMPLETED',   'bg' => '#16a34a', 'ring' => '#22c55e'],
        'pending_bkk_approval' => ['label' => 'PENDING',     'bg' => '#d97706', 'ring' => '#f59e0b'],
        'accepted_bkk'         => ['label' => 'ACCEPTED',    'bg' => '#2563eb', 'ring' => '#3b82f6'],
        'Rejected'             => ['label' => 'REJECTED',    'bg' => '#dc2626', 'ring' => '#ef4444'],
        'cancelled'            => ['label' => 'CANCELLED',   'bg' => '#6b7280', 'ring' => '#9ca3af'],
        default                => ['label' => strtoupper($record->status ?? ''), 'bg' => '#6b7280', 'ring' => '#9ca3af'],
    };
@endphp

<div style="font-family: 'Poppins', sans-serif; color: #e2e8f0;">

    {{-- ── TOP HEADER ──────────────────────────────────────────────────── --}}
    <div style="
        background: linear-gradient(135deg, #0e1f3d 0%, #162d56 100%);
        border-radius: 14px 14px 0 0;
        padding: 20px 22px 18px;
        margin: -16px -16px 0;
        border-bottom: 1px solid rgba(255,255,255,0.07);
        display: flex;
        align-items: center;
        gap: 14px;
    ">
        {{-- Icon circle --}}
        <div style="
            width: 44px; height: 44px;
            background: linear-gradient(135deg, #d4920a, #f5b014);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 0 0 4px rgba(212,146,10,0.2);
        ">
            <svg width="22" height="22" fill="none" stroke="white" stroke-width="2.2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>

        {{-- Title + subtitle --}}
        <div style="flex:1; min-width:0;">
            <div style="font-size:15px; font-weight:700; color:#fff; line-height:1.3;">
                {{ __('message.Transfer-IN') }}
                {{-- <span style="
                    display:inline-block;
                    background:#22c55e;
                    color:#fff;
                    font-size:9px;
                    font-weight:700;
                    letter-spacing:.08em;
                    padding:1px 7px;
                    border-radius:20px;
                    margin-left:6px;
                    vertical-align:middle;
                ">LIVE</span> --}}
            </div>
            <div style="font-size:11px; color:#8aaad8; margin-top:2px;">
                {{ __('message.Transfer-IN') }} — {{ __('message.Bank Details') }}
            </div>
        </div>
    </div>

    {{-- ── BADGE ROW ──────────────────────────────────────────────────── --}}
    <div style="
        background: #0a1628;
        padding: 12px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 1px solid rgba(255,255,255,0.05);
        margin: 0 -16px;
    ">
        <div style="display:flex; gap:10px; align-items:center; flex-wrap:wrap;">
            {{-- Invoice badge --}}
            <span style="
                display:inline-flex; align-items:center; gap:5px;
                background: rgba(255,255,255,0.07);
                border: 1px solid rgba(255,255,255,0.12);
                padding: 4px 12px;
                border-radius: 20px;
                font-size: 11px;
                font-weight: 600;
                color: #d4920a;
                font-family: monospace;
            ">
                {{ $record->invoice_number }}
            </span>

            {{-- Status badge --}}
            <span style="
                display:inline-block;
                background: {{ $statusConfig['bg'] }};
                color: #fff;
                font-size: 10px;
                font-weight: 700;
                letter-spacing: .1em;
                padding: 4px 12px;
                border-radius: 20px;
                box-shadow: 0 0 0 3px {{ $statusConfig['ring'] }}33;
            ">
                {{ $statusConfig['label'] }}
            </span>
        </div>

        <div style="font-size:11px; color:#8aaad8; white-space:nowrap;">
            {{ __('message.Created at') }}:
            <strong style="color:#b8caea;">{{ $record->created_at?->format('d M Y H:i') }}</strong>
        </div>
    </div>

    {{-- ── DETAIL CARDS GRID ───────────────────────────────────────────── --}}
    <div style="
        padding: 18px 6px 6px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    ">

        {{-- Invoice Number --}}
        <div style="
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 14px 16px;
            transition: border-color .2s;
        ">
            <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:#5c87c4; margin:0 0 6px;">
                {{ __('message.Invoice') }}
            </p>
            <p style="font-size:13px; font-weight:700; color:#d4920a; margin:0; font-family:monospace;">
                {{ $record->invoice_number ?? '—' }}
            </p>
        </div>

        {{-- Account Name --}}
        <div style="
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 14px 16px;
        ">
            <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:#5c87c4; margin:0 0 6px;">
                {{ __('message.Account Name') }}
            </p>
            <p style="font-size:13px; font-weight:700; color:#e2e8f0; margin:0;">
                {{ $record->acc_name ?? '—' }}
            </p>
        </div>

        {{-- Bank Name --}}
        <div style="
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 14px 16px;
        ">
            <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:#5c87c4; margin:0 0 6px;">
                {{ __('message.Bank Name') }}
            </p>
            <p style="font-size:13px; font-weight:700; color:#e2e8f0; margin:0;">
                {{ $record->bank_name ?? '—' }}
            </p>
        </div>

        {{-- Account Number --}}
        <div style="
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 14px 16px;
        ">
            <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:#5c87c4; margin:0 0 6px;">
                {{ __('message.Account Number') }}
            </p>
            <p style="font-size:14px; font-weight:700; color:#e2e8f0; margin:0; font-family:monospace; letter-spacing:.08em;">
                {{ $record->acc_number ?? '—' }}
            </p>
        </div>

        {{-- Amount --}}
        <div style="
            background: linear-gradient(135deg, rgba(212,146,10,0.12), rgba(245,176,20,0.06));
            border: 1px solid rgba(212,146,10,0.35);
            border-radius: 12px;
            padding: 14px 16px;
        ">
            <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:#d4920a; margin:0 0 6px;">
                {{ __('message.Amount') }}
            </p>
            <p style="font-size:22px; font-weight:900; color:#f5b014; margin:0; line-height:1.1;">
                ฿ {{ number_format((float) $record->entered_amount, 2) }}
            </p>
        </div>

        {{-- Net Amount --}}
        <div style="
            background: linear-gradient(135deg, rgba(22,163,74,0.12), rgba(34,197,94,0.06));
            border: 1px solid rgba(22,163,74,0.35);
            border-radius: 12px;
            padding: 14px 16px;
        ">
            <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:#16a34a; margin:0 0 6px;">
                {{ __('message.Net Amount') }}
            </p>
            <p style="font-size:22px; font-weight:900; color:#22c55e; margin:0; line-height:1.1;">
                ฿ {{ number_format((float) $record->net_amount, 2) }}
            </p>
        </div>

    </div>

    {{-- ── SECONDARY ROW: Fee + Customer ──────────────────────────────── --}}
    <div style="
        padding: 0 6px 6px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    ">
        {{-- Transfer Fee --}}
        <div style="
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 14px 16px;
        ">
            <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:#5c87c4; margin:0 0 6px;">
                {{ __('message.Transfer Fee') }}
            </p>
            <p style="font-size:14px; font-weight:700; color:#f87171; margin:0;">
                ฿ {{ number_format((float) $record->trf_fee, 2) }}
                @if($record->trf_fee_in_persentage)
                    <span style="font-size:11px; color:#6b7280; margin-left:4px;">
                        ({{ $record->trf_fee_in_persentage }}%)
                    </span>
                @endif
            </p>
        </div>

        {{-- Customer --}}
        <div style="
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            padding: 14px 16px;
        ">
            <p style="font-size:9px; font-weight:600; text-transform:uppercase; letter-spacing:.1em; color:#5c87c4; margin:0 0 6px;">
                {{ __('message.Customer') }}
            </p>
            <p style="font-size:13px; font-weight:700; color:#e2e8f0; margin:0;">
                {{ $record->customer_name ?? '—' }}
            </p>
            @if($record->phone)
                <p style="font-size:11px; color:#6b7280; margin:3px 0 0;">
                    {{ $record->phone }}
                </p>
            @endif
        </div>
    </div>

    {{-- ── FOOTER QUICK LINKS ──────────────────────────────────────────── --}}
    @if($record->invoice_url || $record->transaction_slip)
    <div style="
        display: flex;
        gap: 10px;
        padding: 14px 6px 4px;
        border-top: 1px solid rgba(255,255,255,0.07);
        margin-top: 8px;
    ">
        @if($record->invoice_url)
        <a href="{{ $record->invoice_url }}" target="_blank" style="
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 16px;
            border-radius: 10px;
            background: rgba(212,146,10,0.1);
            border: 1px solid rgba(212,146,10,0.3);
            color: #d4920a;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
            transition: background .2s;
        " onmouseover="this.style.background='rgba(212,146,10,0.2)'"
           onmouseout="this.style.background='rgba(212,146,10,0.1)'">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            {{ __('message.View Invoice') }}
        </a>
        @endif

        @if($record->transaction_slip)
        <a href="{{ asset('storage/' . $record->transaction_slip) }}" target="_blank" style="
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            padding: 10px 16px;
            border-radius: 10px;
            background: rgba(34,197,94,0.1);
            border: 1px solid rgba(34,197,94,0.3);
            color: #22c55e;
            font-size: 12px;
            font-weight: 600;
            text-decoration: none;
        " onmouseover="this.style.background='rgba(34,197,94,0.2)'"
           onmouseout="this.style.background='rgba(34,197,94,0.1)'">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
            </svg>
            {{ __('message.Transaction Slip') }}
        </a>
        @endif
    </div>
    @endif

</div>