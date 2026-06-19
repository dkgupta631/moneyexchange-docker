{{--
    transfer-out-live-notification.blade.php
    ─────────────────────────────────────────
    • wire:poll.5000ms="checkNewNotifications" triggers PHP polling
    • Pending banner: auto-shows when status = pending_bkk_approval OR accepted_bkk
    • Popup: auto-shows when status = completed (purple theme)
    • pendingCount is passed directly via Livewire entangle — no data-count trick
--}}

<div
    x-data="tellerNotify()"
    x-init="boot()"
    x-on:teller-new-notification.window="handleNewRecord($event.detail.record)"
    wire:poll.5000ms="checkNewNotifications"
>

{{-- ══════════════════════════════════════════════════════════
     PENDING BANNER — auto-shows via Livewire entangle
     Shows when pendingCount > 0 (pending_bkk_approval OR accepted_bkk)
══════════════════════════════════════════════════════════ --}}
<div x-show="pending > 0" x-cloak style="margin-bottom:24px;">
    <div style="
        position: relative;
        overflow: hidden;
        border-radius: 16px;
        padding: 18px 22px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        background: linear-gradient(135deg, #1c1000 0%, #2d1a00 40%, #1c1000 100%);
        border: 1px solid rgba(251,191,36,0.30);
        border-left: 4px solid #f59e0b;
        box-shadow: 0 0 0 1px rgba(251,191,36,0.06),
                    0 8px 40px rgba(0,0,0,0.60),
                    inset 0 1px 0 rgba(255,255,255,0.04);
    ">
        {{-- glow blobs --}}
        <div style="position:absolute;inset:0;pointer-events:none;
            background:radial-gradient(ellipse 50% 100% at 0% 50%, rgba(245,158,11,0.10) 0%, transparent 65%),
                       radial-gradient(ellipse 30% 80% at 100% 50%, rgba(251,191,36,0.05) 0%, transparent 60%);"></div>

        {{-- LEFT: icon + text --}}
        <div style="display:flex;align-items:center;gap:16px;position:relative;flex:1;min-width:0;">

            {{-- animated icon --}}
            <div style="position:relative;flex-shrink:0;">
                <div style="position:absolute;inset:-6px;border-radius:50%;
                    background:rgba(245,158,11,0.18);
                    animation:tlrPing 1.8s cubic-bezier(0,0,.2,1) infinite;"></div>
                <div style="position:absolute;inset:-12px;border-radius:50%;
                    background:rgba(245,158,11,0.07);
                    animation:tlrPing 1.8s cubic-bezier(0,0,.2,1) infinite;animation-delay:.3s;"></div>
                <div style="position:relative;width:46px;height:46px;border-radius:50%;
                    background:linear-gradient(135deg,#f59e0b,#d97706);
                    display:flex;align-items:center;justify-content:center;
                    box-shadow:0 0 24px rgba(245,158,11,0.55);">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            {{-- text --}}
            <div style="min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                    <span style="font-size:15px;font-weight:800;color:#fde68a;letter-spacing:.01em;">
                        <span x-text="pending"></span>&nbsp; {{ __('message.Transfer-OUT Requests') }}
                    </span>
                    <span style="background:rgba(245,158,11,0.20);border:1px solid rgba(245,158,11,0.45);
                        color:#fbbf24;font-size:10px;font-weight:800;padding:2px 10px;
                        border-radius:6px;text-transform:uppercase;letter-spacing:.08em;">
                        {{ __('message.Pending') }}
                    </span>
                </div>
                <p style="font-size:12px;color:rgba(253,230,138,0.45);margin:0;letter-spacing:.01em;">
                    {{ __('message.Transfer Requests') }}
                </p>
            </div>
        </div>

        {{-- RIGHT: live indicator --}}
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;
            background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.06);
            border-radius:10px;padding:7px 12px;">
            <div style="width:7px;height:7px;border-radius:50%;background:#22c55e;
                box-shadow:0 0 10px rgba(34,197,94,0.9);
                animation:tlrDotBlink 1.2s ease-in-out infinite;"></div>
            <span style="font-size:10px;color:rgba(253,230,138,0.45);letter-spacing:.05em;
                font-family:monospace;white-space:nowrap;">LIVE · ↻ 5s</span>
        </div>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════════
     LIVEWIRE → ALPINE sync
     entangle pendingCount directly so Alpine always has latest value
══════════════════════════════════════════════════════════ --}}
<div
    style="display:none;"
    x-ref="lwSync"
    data-pending="{{ $pendingCount }}"
    wire:key="pending-sync-{{ $pendingCount }}"
    x-effect="pending = parseInt($refs.lwSync.dataset.pending) || 0"
></div>

{{-- ══════════════════════════════════════════════════════════
     POPUP — purple professional, completed only
══════════════════════════════════════════════════════════ --}}
<template x-if="showPopup && currentRecord">
    <div
        style="position:fixed;inset:0;z-index:99999;
               display:flex;align-items:center;justify-content:center;padding:20px;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="dismiss()"
    >
        {{-- backdrop --}}
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.84);
            backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);z-index:-1;"></div>

        {{-- card --}}
        <div
            style="width:100%;max-width:560px;
                   border-radius:24px;overflow:hidden;
                   background:#0f172a;
                   border:1px solid rgba(255,255,255,0.09);
                   box-shadow:0 40px 100px rgba(0,0,0,0.85),
                              0 0 0 1px rgba(167,139,250,0.12),
                              0 0 80px -10px rgba(139,92,246,0.35);"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.stop
        >
            {{-- shimmer top bar --}}
            <div style="height:4px;
                background:linear-gradient(90deg,#6d28d9,#8b5cf6,#c4b5fd,#8b5cf6,#6d28d9);
                background-size:200%;animation:tlrShimmer 2s linear infinite;"></div>

            {{-- ── HEADER ───────────────────────────────────────────── --}}
            <div style="padding:22px 26px 18px;
                border-bottom:1px solid rgba(255,255,255,0.06);
                display:flex;align-items:center;gap:14px;
                background:radial-gradient(ellipse 80% 120% at 0 0,rgba(139,92,246,0.09) 0%,transparent 60%);">

                <div style="width:50px;height:50px;border-radius:14px;flex-shrink:0;
                    background:linear-gradient(135deg,#6d28d9,#8b5cf6);
                    border:1px solid rgba(167,139,250,0.3);
                    display:flex;align-items:center;justify-content:center;
                    box-shadow:0 8px 28px rgba(139,92,246,0.45);
                    animation:tlrIconPulse 2s ease-in-out infinite;">
                    <svg width="24" height="24" fill="none" stroke="white"
                        viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:3px;">
                        <span style="font-size:19px;font-weight:800;color:#f1f5f9;letter-spacing:-0.02em;">
                            Transfer-OUT {{ __('message.Completed') }} ✓
                        </span>
                        <span style="font-size:10px;font-weight:700;color:#a78bfa;
                            background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.30);
                            padding:2px 8px;border-radius:20px;text-transform:uppercase;
                            letter-spacing:0.06em;animation:tlrBlink 1.5s ease-in-out infinite;">LIVE</span>
                    </div>
                    <p style="font-size:12px;color:#64748b;margin:0;">
                        {{ __('message.Transfer-OUT') }} {{ __('message.Completed') }} — {{ __('message.Bank Details') }}
                    </p>
                </div>

                <button @click="dismiss()"
                    style="width:32px;height:32px;border-radius:9px;
                        background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);
                        display:flex;align-items:center;justify-content:center;
                        cursor:pointer;color:#64748b;flex-shrink:0;transition:all 0.15s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.12)';this.style.color='#94a3b8';"
                    onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.color='#64748b';">
                    <svg width="14" height="14" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- ── INVOICE + STATUS ROW ─────────────────────────────── --}}
            <div style="padding:14px 26px 10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <div style="background:rgba(139,92,246,0.12);border:1px solid rgba(139,92,246,0.35);
                    color:#a78bfa;border-radius:8px;padding:4px 13px;
                    font-size:12px;font-weight:700;letter-spacing:.04em;
                    font-family:'JetBrains Mono',monospace;">
                    #<span x-text="currentRecord.invoice_number"></span>
                </div>
                <div style="background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.40);
                    color:#c4b5fd;border-radius:999px;padding:3px 12px;
                    font-size:10px;font-weight:700;letter-spacing:.06em;text-transform:uppercase;">
                    {{ __('message.Completed') }}
                </div>
                <span style="font-size:11px;color:#334155;margin-left:auto;">
                    {{ __('message.Received at') }}:&nbsp;<span x-text="nowTime()"></span>
                </span>
            </div>

            {{-- ── DETAILS GRID ──────────────────────────────────────── --}}
            <div style="padding:2px 26px 16px;display:grid;grid-template-columns:1fr 1fr;gap:10px;">

                <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);
                    border-radius:12px;padding:13px 15px;">
                    <p style="font-size:10px;font-weight:600;color:#475569;
                        text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">
                        {{ __('message.Invoice Number') }}
                    </p>
                    <p style="font-size:13px;font-weight:800;color:#a78bfa;margin:0;
                        font-family:'JetBrains Mono',monospace;letter-spacing:.04em;"
                        x-text="currentRecord.invoice_number"></p>
                </div>

                <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);
                    border-radius:12px;padding:13px 15px;">
                    <p style="font-size:10px;font-weight:600;color:#475569;
                        text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">
                        {{ __('message.Account Name') }}
                    </p>
                    <p style="font-size:14px;font-weight:800;color:#e2e8f0;margin:0;"
                        x-text="currentRecord.acc_name || '—'"></p>
                </div>

                <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);
                    border-radius:12px;padding:13px 15px;">
                    <p style="font-size:10px;font-weight:600;color:#475569;
                        text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">
                        {{ __('message.Bank Name') }}
                    </p>
                    <p style="font-size:14px;font-weight:800;color:#e2e8f0;margin:0;"
                        x-text="currentRecord.bank_name || '—'"></p>
                </div>

                <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);
                    border-radius:12px;padding:13px 15px;">
                    <p style="font-size:10px;font-weight:600;color:#475569;
                        text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">
                        {{ __('message.Account Number') }}
                    </p>
                    <p style="font-size:15px;font-weight:800;color:#e2e8f0;margin:0;
                        font-family:'JetBrains Mono',monospace;"
                        x-text="currentRecord.acc_number || '—'"></p>
                </div>

                <div style="background:rgba(139,92,246,0.08);border:1px solid rgba(139,92,246,0.25);
                    border-radius:12px;padding:15px;">
                    <p style="font-size:10px;font-weight:600;color:#7c3aed;
                        text-transform:uppercase;letter-spacing:0.08em;margin:0 0 6px;">
                        {{ __('message.Amount') }}
                    </p>
                    <p style="font-size:26px;font-weight:900;color:#c4b5fd;margin:0;
                        letter-spacing:-0.03em;line-height:1;"
                        x-text="(currentRecord.currency ?? '') + ' ' + fmt(currentRecord.entered_amount)"></p>
                </div>

                <div style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);
                    border-radius:12px;padding:15px;">
                    <p style="font-size:10px;font-weight:600;color:#16a34a;
                        text-transform:uppercase;letter-spacing:0.08em;margin:0 0 6px;">
                        {{ __('message.Net Amount') }}
                    </p>
                    <p style="font-size:26px;font-weight:900;color:#4ade80;margin:0;
                        letter-spacing:-0.03em;line-height:1;"
                        x-text="(currentRecord.currency ?? '') + ' ' + fmt(currentRecord.net_amount)"></p>
                </div>

            </div>

            {{-- <p style="font-size:11px;color:#334155;text-align:center;margin:0 0 16px;"
                x-text="'{{ __('message.Received at') }}: ' + nowTime()"></p> --}}

            <div style="height:1px;background:rgba(255,255,255,0.06);margin:0 26px 16px;"></div>

        </div>{{-- /card --}}
    </div>{{-- /overlay --}}
</template>

</div>{{-- /x-data --}}

@push('scripts')
<style>
@keyframes tlrPing      { 75%,100%{transform:scale(2.2);opacity:0} }
@keyframes tlrDotBlink  { 0%,100%{opacity:1;box-shadow:0 0 10px rgba(34,197,94,0.9)} 50%{opacity:.3;box-shadow:0 0 4px rgba(34,197,94,0.3)} }
@keyframes tlrBlink     { 0%,100%{opacity:1} 50%{opacity:.3} }
@keyframes tlrShimmer   { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }
@keyframes tlrIconPulse { 0%,100%{box-shadow:0 8px 28px rgba(139,92,246,0.45)} 50%{box-shadow:0 8px 40px rgba(139,92,246,0.75)} }
[x-cloak] { display:none !important; }
</style>
<script>
function tellerNotify() {
    return {
        // ── state ─────────────────────────────────────────────────────────
        pending:       {{ (int) $pendingCount }},   // ← seeded from PHP on load
        showPopup:     false,
        currentRecord: null,
        queue:         [],
        SEEN:          'tlr_seen_v5',

        // ── localStorage seen-set ─────────────────────────────────────────
        getSeenSet() {
            try { return new Set(JSON.parse(localStorage.getItem(this.SEEN) || '[]')); }
            catch { return new Set(); }
        },
        saveSeenSet(s) {
            let a = [...s];
            if (a.length > 800) a = a.slice(-800);
            try { localStorage.setItem(this.SEEN, JSON.stringify(a)); } catch {}
        },
        isSeen(id)  { return this.getSeenSet().has(String(id)); },
        markSeen(id){ const s = this.getSeenSet(); s.add(String(id)); this.saveSeenSet(s); },

        // ── MAIN ENTRY — called by x-on:teller-new-notification.window ───
        handleNewRecord(record) {
            if (!record || !record.id) return;
            if (this.isSeen(record.id)) return;
            this.markSeen(record.id);
            this.queue.push(record);
            this.next();
        },

        // ── queue ─────────────────────────────────────────────────────────
        next() {
            if (this.showPopup || this.queue.length === 0) return;
            this.currentRecord = this.queue.shift();
            this.showPopup     = true;
            this.$nextTick(() => this.speak(this.currentRecord));
        },
        dismiss() {
            this.showPopup     = false;
            this.currentRecord = null;
            setTimeout(() => this.next(), 350);
        },

        // ── voice ─────────────────────────────────────────────────────────
        speak(rec) {
            try {
                if (!('speechSynthesis' in window)) return;
                window.speechSynthesis.cancel();
                const msg = rec.popup_type === 'completed'
                    ? `Transfer-OUT {{ __('message.Completed') }}. Invoice ${rec.invoice_number}. Customer ${rec.customer_name}. Net amount ${rec.currency} ${this.fmt(rec.net_amount)}.`
                    : `{{ __('message.Have new Transfer-OUT') }}. Invoice ${rec.invoice_number}. Account ${rec.acc_name}. Net amount ${rec.currency} ${this.fmt(rec.net_amount)}.`;
                const u  = new SpeechSynthesisUtterance(msg);
                u.lang   = document.documentElement.lang || 'en-US';
                u.rate   = 0.88;
                u.volume = 1;
                window.speechSynthesis.speak(u);
            } catch(e) { console.warn('[teller] speak error', e); }
        },

        // ── boot ─────────────────────────────────────────────────────────
        boot() {
            console.log('[TellerNotify] Booted — pending:', this.pending);
        },

        // ── helpers ───────────────────────────────────────────────────────
        fmt(n) {
            return Number(n || 0).toLocaleString('en-US', {
                minimumFractionDigits: 2, maximumFractionDigits: 2
            });
        },
        nowTime() {
            return new Date().toLocaleTimeString([], {
                hour: '2-digit', minute: '2-digit', second: '2-digit'
            });
        },
    };
}
</script>
@endpush