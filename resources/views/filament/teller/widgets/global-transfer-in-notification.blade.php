{{--
    global-transfer-in-notification.blade.php
    ──────────────────────────────────────────
    Mounted globally on every teller page via PanelsRenderHook::BODY_START.

    FIX: Browsers (Chrome/Edge/Firefox) block AudioContext + speechSynthesis
    until the user has made a gesture (click / keydown / touchstart).
    We unlock both APIs on the FIRST interaction anywhere on the page,
    store a persistent AudioContext, and resume it before every beep.
--}}

<div
    x-data="globalTransferInNotify()"
    x-init="boot()"
    x-on:teller-transferin-notification.window="handleNewRecord($event.detail.record)"
    wire:poll.5000ms="checkNewNotifications"
    style="position:fixed;top:0;left:0;width:0;height:0;overflow:visible;z-index:99998;pointer-events:none;"
>

{{-- ══ POPUP — PURPLE theme, Transfer-IN Completed ══ --}}
<template x-if="showPopup && currentRecord">
    <div
        style="position:fixed;inset:0;z-index:99999;pointer-events:all;
               display:flex;align-items:center;justify-content:center;padding:20px;"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="dismiss()"
    >
        <div style="position:fixed;inset:0;background:rgba(0,0,0,0.84);
            backdrop-filter:blur(10px);-webkit-backdrop-filter:blur(10px);z-index:-1;"></div>

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
            <div style="height:4px;
                background:linear-gradient(90deg,#6d28d9,#8b5cf6,#c4b5fd,#8b5cf6,#6d28d9);
                background-size:200%;animation:gtinShimmer 2s linear infinite;"></div>

            {{-- Header --}}
            <div style="padding:22px 26px 18px;border-bottom:1px solid rgba(255,255,255,0.06);
                display:flex;align-items:center;gap:14px;
                background:radial-gradient(ellipse 80% 120% at 0 0,rgba(139,92,246,0.09) 0%,transparent 60%);">

                <div style="width:50px;height:50px;border-radius:14px;flex-shrink:0;
                    background:linear-gradient(135deg,#6d28d9,#8b5cf6);
                    border:1px solid rgba(167,139,250,0.30);
                    display:flex;align-items:center;justify-content:center;
                    box-shadow:0 8px 28px rgba(139,92,246,0.45);
                    animation:gtinIconPulse 2s ease-in-out infinite;">
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
                            letter-spacing:0.06em;animation:gtinBlink 1.5s ease-in-out infinite;">LIVE</span>
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
                    <p style="font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">{{ __('message.Invoice Number') }}</p>
                    <p style="font-size:13px;font-weight:800;color:#a78bfa;margin:0;font-family:monospace;letter-spacing:.04em;" x-text="currentRecord.invoice_number"></p>
                </div>

                <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:13px 15px;">
                    <p style="font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">{{ __('message.Account Name') }}</p>
                    <p style="font-size:14px;font-weight:800;color:#e2e8f0;margin:0;" x-text="currentRecord.acc_name || '—'"></p>
                </div>

                <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:13px 15px;">
                    <p style="font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">{{ __('message.Bank Name') }}</p>
                    <p style="font-size:14px;font-weight:800;color:#e2e8f0;margin:0;" x-text="currentRecord.bank_name || '—'"></p>
                </div>

                <div style="background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.07);border-radius:12px;padding:13px 15px;">
                    <p style="font-size:10px;font-weight:600;color:#475569;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 5px;">{{ __('message.Account Number') }}</p>
                    <p style="font-size:15px;font-weight:800;color:#e2e8f0;margin:0;font-family:monospace;" x-text="currentRecord.acc_number || '—'"></p>
                </div>

                <div style="background:rgba(139,92,246,0.08);border:1px solid rgba(139,92,246,0.25);border-radius:12px;padding:15px;">
                    <p style="font-size:10px;font-weight:600;color:#7c3aed;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 6px;">{{ __('message.Amount') }}</p>
                    <p style="font-size:26px;font-weight:900;color:#c4b5fd;margin:0;letter-spacing:-0.03em;line-height:1;"
                        x-text="(currentRecord.currency || '') + ' ' + fmt(currentRecord.entered_amount)"></p>
                </div>

                <div style="background:rgba(34,197,94,0.08);border:1px solid rgba(34,197,94,0.25);border-radius:12px;padding:15px;">
                    <p style="font-size:10px;font-weight:600;color:#16a34a;text-transform:uppercase;letter-spacing:0.08em;margin:0 0 6px;">{{ __('message.Net Amount') }}</p>
                    <p style="font-size:26px;font-weight:900;color:#4ade80;margin:0;letter-spacing:-0.03em;line-height:1;"
                        x-text="(currentRecord.currency || '') + ' ' + fmt(currentRecord.net_amount)"></p>
                </div>

            </div>

            <div style="height:1px;background:rgba(255,255,255,0.06);margin:0 26px 16px;"></div>

        </div>
    </div>
</template>

</div>

@once
@push('scripts')
<style>
@keyframes gtinShimmer   { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }
@keyframes gtinIconPulse { 0%,100%{box-shadow:0 8px 28px rgba(139,92,246,0.45)} 50%{box-shadow:0 8px 40px rgba(139,92,246,0.75)} }
@keyframes gtinBlink     { 0%,100%{opacity:1} 50%{opacity:.3} }
</style>
<script>
(function () {
    // ── Singleton audio context — shared across all calls ─────────────────────
    // We keep ONE context alive for the page lifetime so resume() works reliably.
    let _audioCtx = null;

    function getAudioCtx() {
        if (!_audioCtx) {
            _audioCtx = new (window.AudioContext || window.webkitAudioContext)();
        }
        return _audioCtx;
    }

    // ── Unlock on FIRST user gesture anywhere on the page ────────────────────
    // Browsers suspend AudioContext until a gesture happens.
    // We listen once at capture phase so it fires before Alpine/Livewire handlers.
    function unlockAudio() {
        try {
            const ctx = getAudioCtx();
            if (ctx.state === 'suspended') {
                ctx.resume().catch(() => {});
            }
            // Also warm up speechSynthesis (Chrome quirk — first call is often silent)
            if ('speechSynthesis' in window) {
                const warmup = new SpeechSynthesisUtterance('');
                warmup.volume = 0;
                window.speechSynthesis.speak(warmup);
                window.speechSynthesis.cancel();
            }
        } catch (e) {}
    }

    // One-time listeners on all common gesture types
    ['click', 'keydown', 'touchstart', 'pointerdown'].forEach(evt => {
        document.addEventListener(evt, function onGesture() {
            unlockAudio();
            // Remove after first gesture — audio is now unlocked for the session
            document.removeEventListener(evt, onGesture, true);
        }, { capture: true, once: true, passive: true });
    });

    // ── Expose global play functions for Alpine ───────────────────────────────
    window._gtinPlayBeep = function () {
        try {
            const ctx = getAudioCtx();
            // Resume if suspended (handles edge cases like tab switching)
            const play = () => {
                const tones = [
                    { freq: 660,  start: 0,    dur: 0.18 },
                    { freq: 880,  start: 0.22, dur: 0.18 },
                    { freq: 1100, start: 0.44, dur: 0.25 },
                    { freq: 1320, start: 0.72, dur: 0.40 },
                ];
                tones.forEach(({ freq, start, dur }) => {
                    const o = ctx.createOscillator();
                    const g = ctx.createGain();
                    o.connect(g);
                    g.connect(ctx.destination);
                    o.frequency.value = freq;
                    o.type = 'sine';
                    g.gain.setValueAtTime(0.35, ctx.currentTime + start);
                    g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + start + dur);
                    o.start(ctx.currentTime + start);
                    o.stop(ctx.currentTime + start + dur + 0.01);
                });
            };

            if (ctx.state === 'suspended') {
                ctx.resume().then(play).catch(() => {});
            } else {
                play();
            }
        } catch (e) {
            console.warn('[GlobalTransferIN] beep error', e);
        }
    };

    window._gtinSpeak = function (text) {
        try {
            if (!('speechSynthesis' in window)) return;

            // Cancel any ongoing speech first
            window.speechSynthesis.cancel();

            // Small delay so cancel() completes before new speak()
            setTimeout(() => {
                try {
                    const u = new SpeechSynthesisUtterance(text);
                    // Pick a language-appropriate voice if available
                    const voices = window.speechSynthesis.getVoices();
                    const preferred = voices.find(v =>
                        v.lang.startsWith(document.documentElement.lang || 'en') && !v.localService
                    ) || voices.find(v => v.lang.startsWith('en'));
                    if (preferred) u.voice = preferred;
                    u.rate   = 0.88;
                    u.volume = 1.0;
                    u.pitch  = 1.05;
                    window.speechSynthesis.speak(u);
                } catch (e2) {
                    console.warn('[GlobalTransferIN] speak inner error', e2);
                }
            }, 120);
        } catch (e) {
            console.warn('[GlobalTransferIN] speak error', e);
        }
    };

    // Chrome sometimes needs voices to load first
    if ('speechSynthesis' in window && window.speechSynthesis.onvoiceschanged !== undefined) {
        window.speechSynthesis.onvoiceschanged = () => {};
    }
})();

function globalTransferInNotify() {
    return {
        showPopup:     false,
        currentRecord: null,
        queue:         [],
        SEEN:          'tin_seen_v1',

        // ── seen-set (localStorage) ───────────────────────────────────────
        getSeenSet() {
            try { return new Set(JSON.parse(localStorage.getItem(this.SEEN) || '[]')); }
            catch { return new Set(); }
        },
        saveSeenSet(s) {
            let a = [...s];
            if (a.length > 800) a = a.slice(-800);
            try { localStorage.setItem(this.SEEN, JSON.stringify(a)); } catch {}
        },
        isSeen(id)   { return this.getSeenSet().has(String(id)); },
        markSeen(id) {
            const s = this.getSeenSet();
            s.add(String(id));
            this.saveSeenSet(s);
        },

        // ── entry — fired by Livewire window event ────────────────────────
        handleNewRecord(record) {
            if (!record || !record.id) return;
            if (this.isSeen(record.id)) return;
            this.markSeen(record.id);
            @this.call('sendFilamentNotification', record);

            // Play beep immediately (user already interacted — audio is unlocked)
            window._gtinPlayBeep();

            this.queue.push(record);
            this.next();
        },

        // ── queue ─────────────────────────────────────────────────────────
        next() {
            if (this.showPopup || this.queue.length === 0) return;
            this.currentRecord = this.queue.shift();
            this.showPopup     = true;
            // Speak after popup renders so the voice message is accurate
            this.$nextTick(() => {
                const rec = this.currentRecord;
                if (!rec) return;
                const msg =
                    '{{ __('message.Transfer-IN') }} {{ __('message.Completed') }}. ' +
                    '{{ __('message.Invoice') }} ' + rec.invoice_number + '. ' +
                    '{{ __('message.Bank Name') }} ' + (rec.bank_name || '') + '. ' +
                    '{{ __('message.Net Amount') }} ' + (rec.currency || '') + ' ' + this.fmt(rec.net_amount) + '.';
                // Delay voice slightly so beep plays first
                setTimeout(() => window._gtinSpeak(msg), 800);
            });
        },
        dismiss() {
            this.showPopup     = false;
            this.currentRecord = null;
            setTimeout(() => this.next(), 350);
        },

        // ── helpers ───────────────────────────────────────────────────────
        boot() {
            console.log('[GlobalTransferIN] Booted — audio will unlock on first user gesture.');
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
@endonce