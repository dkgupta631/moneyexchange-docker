{{--
    transfer-in-live-notification.blade.php
    Purple theme (matches Color::Purple primary) — mirrors Transfer-OUT amber design exactly
    Popup fires when status = completed
    Banner shows when pending_bkk_approval OR accepted_bkk count > 0
--}}

<div
    x-data="tellerTransferInNotify()"
    x-init="boot()"
    x-on:teller-transferin-notification.window="handleNewRecord($event.detail.record)"
    wire:poll.5000ms="checkNewNotifications"
>

{{-- ══ PENDING BANNER — purple theme, matches Color::Purple primary ══ --}}
<div x-show="pending > 0" x-cloak style="margin-bottom:24px;">
    <div style="
        position:relative; overflow:hidden; border-radius:16px;
        padding:18px 22px; display:flex; align-items:center;
        justify-content:space-between; gap:16px;
        background:linear-gradient(135deg,#0d0a1e 0%,#1a1040 40%,#0d0a1e 100%);
        border:1px solid rgba(139,92,246,0.30); border-left:4px solid #8b5cf6;
        box-shadow:0 0 0 1px rgba(139,92,246,0.06), 0 8px 40px rgba(0,0,0,0.60),
                   inset 0 1px 0 rgba(255,255,255,0.04);
    ">
        {{-- glow --}}
        <div style="position:absolute;inset:0;pointer-events:none;
            background:radial-gradient(ellipse 50% 100% at 0% 50%,rgba(139,92,246,0.12) 0%,transparent 65%),
                       radial-gradient(ellipse 30% 80% at 100% 50%,rgba(167,139,250,0.06) 0%,transparent 60%);"></div>

        {{-- LEFT --}}
        <div style="display:flex;align-items:center;gap:16px;position:relative;flex:1;min-width:0;">
            <div style="position:relative;flex-shrink:0;">
                <div style="position:absolute;inset:-6px;border-radius:50%;background:rgba(139,92,246,0.18);
                    animation:tinPing 1.8s cubic-bezier(0,0,.2,1) infinite;"></div>
                <div style="position:absolute;inset:-12px;border-radius:50%;background:rgba(139,92,246,0.07);
                    animation:tinPing 1.8s cubic-bezier(0,0,.2,1) infinite;animation-delay:.3s;"></div>
                <div style="position:relative;width:46px;height:46px;border-radius:50%;
                    background:linear-gradient(135deg,#7c3aed,#8b5cf6);
                    display:flex;align-items:center;justify-content:center;
                    box-shadow:0 0 24px rgba(139,92,246,0.55);">
                    <svg width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="white" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>

            <div style="min-width:0;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;flex-wrap:wrap;">
                    <span style="font-size:15px;font-weight:800;color:#e9d5ff;letter-spacing:.01em;">
                        <span x-text="pending"></span>&nbsp;{{ __('message.Transfer-IN Requests') }}
                    </span>
                    <span style="background:rgba(139,92,246,0.20);border:1px solid rgba(139,92,246,0.45);
                        color:#a78bfa;font-size:10px;font-weight:800;padding:2px 10px;
                        border-radius:6px;text-transform:uppercase;letter-spacing:.08em;">
                        {{ __('message.Pending') }}
                    </span>
                </div>
                <p style="font-size:12px;color:rgba(233,213,255,0.45);margin:0;letter-spacing:.01em;">
                    {{ __('message.awaiting your approval') }}
                </p>
            </div>
        </div>

        {{-- RIGHT: live indicator --}}
        <div style="display:flex;align-items:center;gap:8px;flex-shrink:0;
            background:rgba(0,0,0,0.25);border:1px solid rgba(255,255,255,0.06);
            border-radius:10px;padding:7px 12px;">
            <div style="width:7px;height:7px;border-radius:50%;background:#a78bfa;
                box-shadow:0 0 10px rgba(139,92,246,0.9);
                animation:tinDotBlink 1.2s ease-in-out infinite;"></div>
            <span style="font-size:10px;color:rgba(233,213,255,0.45);letter-spacing:.05em;
                font-family:monospace;white-space:nowrap;">LIVE · ↻ 5s</span>
        </div>
    </div>
</div>

{{-- Livewire → Alpine sync --}}
<div
    style="display:none;" x-ref="lwSync"
    data-pending="{{ $pendingCount }}"
    wire:key="tin-pending-sync-{{ $pendingCount }}"
    x-effect="pending = parseInt($refs.lwSync.dataset.pending) || 0"
></div>

{{-- ══ POPUP — PURPLE primary theme, completed Transfer-IN ══ --}}
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
        {{-- Backdrop --}}
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.84);
            backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);z-index:-1;"></div>

        {{-- Card --}}
        <div
            style="width:100%;max-width:560px;border-radius:24px;overflow:hidden;
                   background:#0f172a;border:1px solid rgba(139,92,246,0.15);
                   box-shadow:0 40px 100px rgba(0,0,0,0.85),
                              0 0 0 1px rgba(139,92,246,0.12),
                              0 0 80px -10px rgba(139,92,246,0.35);"
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.stop
        >
            {{-- Purple shimmer top bar --}}
            <div style="height:4px;
                background:linear-gradient(90deg,#6d28d9,#8b5cf6,#c4b5fd,#8b5cf6,#6d28d9);
                background-size:200%;animation:tinShimmer 2s linear infinite;"></div>

            {{-- Header --}}
            <div style="padding:22px 26px 18px;border-bottom:1px solid rgba(255,255,255,0.06);
                display:flex;align-items:center;gap:14px;
                background:radial-gradient(ellipse 80% 120% at 0 0,rgba(139,92,246,0.09) 0%,transparent 60%);">

                <div style="width:50px;height:50px;border-radius:14px;flex-shrink:0;
                    background:linear-gradient(135deg,#6d28d9,#8b5cf6);
                    border:1px solid rgba(167,139,250,0.30);
                    display:flex;align-items:center;justify-content:center;
                    box-shadow:0 8px 28px rgba(139,92,246,0.45);
                    animation:tinIconPulse 2s ease-in-out infinite;">
                    <svg width="26" height="26" fill="none" stroke="white" viewBox="0 0 24 24" stroke-width="2.2">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>

                <div style="flex:1;min-width:0;">
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:3px;">
                        <span style="font-size:19px;font-weight:800;color:#f1f5f9;letter-spacing:-0.02em;">
                            {{ __('message.Transfer-IN') }} {{ __('message.Completed') }} ✓
                        </span>
                        <span style="font-size:10px;font-weight:700;color:#a78bfa;
                            background:rgba(139,92,246,0.15);border:1px solid rgba(139,92,246,0.30);
                            padding:2px 8px;border-radius:20px;text-transform:uppercase;
                            letter-spacing:0.06em;animation:tinBlink 1.5s ease-in-out infinite;">LIVE</span>
                    </div>
                    <p style="font-size:12px;color:#64748b;margin:0;">
                        {{ __('message.Transfer-IN') }} {{ __('message.Completed') }} — {{ __('message.Bank Details') }}
                    </p>
                </div>

                <button @click="dismiss()"
                    style="width:32px;height:32px;border-radius:9px;
                        background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.08);
                        display:flex;align-items:center;justify-content:center;
                        cursor:pointer;color:#64748b;flex-shrink:0;transition:all 0.15s;"
                    onmouseover="this.style.background='rgba(255,255,255,0.12)';this.style.color='#94a3b8';"
                    onmouseout="this.style.background='rgba(255,255,255,0.05)';this.style.color='#64748b';">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>

            {{-- Invoice + status row --}}
            <div style="padding:14px 26px 10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                <div style="background:rgba(139,92,246,0.12);border:1px solid rgba(139,92,246,0.35);
                    color:#a78bfa;border-radius:8px;padding:4px 13px;
                    font-size:12px;font-weight:700;letter-spacing:.04em;font-family:monospace;">
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

            {{-- Details grid --}}
            <div style="padding:2px 26px 16px;display:grid;grid-template-columns:1fr 1fr;gap:10px;">

                <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:13px 15px;">
                    <p style="font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">
                        {{ __('message.Invoice Number') }}
                    </p>
                    <p style="font-size:13px;font-weight:800;color:#a78bfa;margin:0;font-family:monospace;letter-spacing:.04em;"
                        x-text="currentRecord.invoice_number"></p>
                </div>

                <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:13px 15px;">
                    <p style="font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">
                        {{ __('message.Account Name') }}
                    </p>
                    <p style="font-size:14px;font-weight:800;color:#e2e8f0;margin:0;"
                        x-text="currentRecord.acc_name || '—'"></p>
                </div>

                <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:13px 15px;">
                    <p style="font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">
                        {{ __('message.Bank Name') }}
                    </p>
                    <p style="font-size:14px;font-weight:800;color:#e2e8f0;margin:0;"
                        x-text="currentRecord.bank_name || '—'"></p>
                </div>

                <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:13px 15px;">
                    <p style="font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">
                        {{ __('message.Account Number') }}
                    </p>
                    <p style="font-size:15px;font-weight:800;color:#e2e8f0;margin:0;font-family:monospace;"
                        x-text="currentRecord.acc_number || '—'"></p>
                </div>

                {{-- Amount — purple accent --}}
                <div style="background:rgba(139,92,246,0.08);border:1px solid rgba(139,92,246,0.25);border-radius:12px;padding:15px;">
                    <p style="font-size:10px;font-weight:600;color:#7c3aed;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 6px;">
                        {{ __('message.Amount') }}
                    </p>
                    <p style="font-size:26px;font-weight:900;color:#c4b5fd;margin:0;letter-spacing:-0.03em;line-height:1;"
                        x-text="(currentRecord.currency ?? '') + ' ' + fmt(currentRecord.entered_amount)"></p>
                </div>

                {{-- Net Amount — green accent (matches Transfer-OUT style) --}}
                <div style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);border-radius:12px;padding:15px;">
                    <p style="font-size:10px;font-weight:600;color:#16a34a;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 6px;">
                        {{ __('message.Net Amount') }}
                    </p>
                    <p style="font-size:26px;font-weight:900;color:#4ade80;margin:0;letter-spacing:-0.03em;line-height:1;"
                        x-text="(currentRecord.currency ?? '') + ' ' + fmt(currentRecord.net_amount)"></p>
                </div>

            </div>

            <div style="height:1px;background:rgba(255,255,255,0.06);margin:0 26px 16px;"></div>

        </div>{{-- /card --}}
    </div>{{-- /overlay --}}
</template>

</div>{{-- /x-data --}}

@push('scripts')
<style>
@keyframes tinPing      { 75%,100%{transform:scale(2.2);opacity:0} }
@keyframes tinDotBlink  { 0%,100%{opacity:1;box-shadow:0 0 10px rgba(139,92,246,0.9)} 50%{opacity:.3;box-shadow:0 0 4px rgba(139,92,246,0.3)} }
@keyframes tinBlink     { 0%,100%{opacity:1} 50%{opacity:.3} }
@keyframes tinShimmer   { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }
@keyframes tinIconPulse { 0%,100%{box-shadow:0 8px 28px rgba(139,92,246,0.45)} 50%{box-shadow:0 8px 40px rgba(139,92,246,0.75)} }
[x-cloak] { display:none !important; }
</style>
<script>
function tellerTransferInNotify() {
    return {
        pending:       {{ (int) $pendingCount }},
        showPopup:     false,
        currentRecord: null,
        queue:         [],
        SEEN:          'tin_seen_v1',

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

        handleNewRecord(record) {
            if (!record || !record.id) return;
            if (this.isSeen(record.id)) return;
            this.markSeen(record.id);
            @this.call('sendFilamentNotification', record);
            this.queue.push(record);
            this.playBeep();
            this.next();
        },

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

        playBeep() {
            try {
                const ctx = new (window.AudioContext || window.webkitAudioContext)();
                const beep = (f, s, d) => {
                    const o = ctx.createOscillator();
                    const g = ctx.createGain();
                    o.connect(g); g.connect(ctx.destination);
                    o.frequency.value = f; o.type = 'sine';
                    g.gain.setValueAtTime(0.3, ctx.currentTime + s);
                    g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + s + d);
                    o.start(ctx.currentTime + s);
                    o.stop(ctx.currentTime + s + d);
                };
                beep(660, 0,    0.18);
                beep(880, 0.22, 0.18);
                beep(1100, 0.44, 0.25);
                beep(1320, 0.72, 0.40);
            } catch(e) {}
        },

        speak(rec) {
            try {
                if (!('speechSynthesis' in window)) return;
                window.speechSynthesis.cancel();
                const msg = new SpeechSynthesisUtterance(
                    '{{ __('message.Transfer-IN') }} {{ __('message.Completed') }}. ' +
                    '{{ __('message.Invoice') }} ' + rec.invoice_number + '. ' +
                    '{{ __('message.Bank Name') }} ' + (rec.bank_name || '') + '. ' +
                    '{{ __('message.Net Amount') }} ' + (rec.currency || '') + ' ' + this.fmt(rec.net_amount) + '.'
                );
                msg.rate = 0.88; msg.volume = 1.0;
                window.speechSynthesis.speak(msg);
            } catch(e) {}
        },

        boot() {
            console.log('[TellerTransferIN] Booted — pending:', this.pending);
        },

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