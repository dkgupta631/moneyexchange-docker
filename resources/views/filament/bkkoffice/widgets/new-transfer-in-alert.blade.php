<div
    x-data="{
        showPopup:      @entangle('showPopup'),
        transfer:       @entangle('latestTransfer'),
        showReject:     @entangle('showRejectForm'),
        showUpload:     @entangle('showUploadForm'),
        rejectCategory: '',
        otherReason:    '',
        audioCtx: null,

        rejectOptions: [
            { value: 'Wrong Account details', label: '{{ __('message.Wrong Account details') }}' },
            { value: 'Wrong amount',          label: '{{ __('message.Wrong amount') }}' },
            { value: 'Change mind',           label: '{{ __('message.Change mind') }}' },
            { value: 'other',                 label: '{{ __('message.Other (specify below)') }}' },
        ],

        playAlertSound() {
            try {
                this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const beep = (f, s, d) => {
                    const o = this.audioCtx.createOscillator();
                    const g = this.audioCtx.createGain();
                    o.connect(g); g.connect(this.audioCtx.destination);
                    o.frequency.value = f; o.type = 'sine';
                    g.gain.setValueAtTime(0.3, this.audioCtx.currentTime + s);
                    g.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + s + d);
                    o.start(this.audioCtx.currentTime + s);
                    o.stop(this.audioCtx.currentTime + s + d);
                };
                beep(660, 0, 0.18); beep(880, 0.22, 0.18);
                beep(1100, 0.44, 0.25); beep(880, 0.72, 0.4);
            } catch(e) {}
        },

        speakAlert(t) {
            if ('speechSynthesis' in window && t) {
                window.speechSynthesis.cancel();
                const msg = new SpeechSynthesisUtterance(
                    'Attention! New Transfer In request received. Bank ' + (t.bank_name || '') +
                    '. Amount ' + (t.currency || '') + ' ' + (t.amount || '') +
                    '. Please process immediately.'
                );
                msg.rate = 0.9; msg.volume = 1.0;
                window.speechSynthesis.speak(msg);
            }
        },

        openRejectPanel() {
            this.rejectCategory = '';
            this.otherReason    = '';
            const errEl = document.getElementById('in-reject-error');
            if (errEl) errEl.style.display = 'none';
            @this.call('openRejectForm');
        },

        submitRejectNow(id) {
            const errEl = document.getElementById('in-reject-error');
            if (!this.rejectCategory) {
                if (errEl) { errEl.textContent = '{{ __('message.Please select a reason') }}'; errEl.style.display = 'block'; }
                return;
            }
            if (this.rejectCategory === 'other' && this.otherReason.trim().length < 3) {
                if (errEl) { errEl.textContent = '{{ __('message.Please enter at least 3 characters') }}'; errEl.style.display = 'block'; }
                return;
            }
            if (errEl) errEl.style.display = 'none';
            @this.call('submitReject', id, this.rejectCategory, this.otherReason.trim());
            this.rejectCategory = '';
            this.otherReason    = '';
        },

        reset() {
            this.rejectCategory = '';
            this.otherReason    = '';
            const e1 = document.getElementById('in-reject-error');
            if (e1) e1.style.display = 'none';
        }
    }"
    x-on:new-transferin-arrived.window="
        transfer    = $event.detail.transfer;
        showPopup   = true;
        reset();
        playAlertSound();
        setTimeout(() => speakAlert(transfer), 800);
    "
    x-on:transferin-popup-closed.window="reset()"
    wire:poll.8000ms="checkNewTransfers"
>

    {{-- ══ PENDING BANNER ══ --}}
    @if($pendingCount > 0)
    <div style="margin-bottom:16px; background:rgba(34,197,94,0.07); border:1px solid rgba(34,197,94,0.2); border-left:4px solid #22c55e; border-radius:12px; padding:14px 18px; display:flex; align-items:center; gap:12px;">
        <div style="position:relative; flex-shrink:0; width:38px; height:38px;">
            <div style="position:absolute; inset:0; border-radius:10px; background:rgba(34,197,94,0.2); animation:tin-ping 2s ease-in-out infinite;"></div>
            <div style="position:relative; width:38px; height:38px; border-radius:10px; background:rgba(34,197,94,0.12); border:1px solid rgba(34,197,94,0.3); display:flex; align-items:center; justify-content:center;">
                <svg width="18" height="18" fill="none" stroke="#22c55e" viewBox="0 0 24 24" stroke-width="2.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                </svg>
            </div>
        </div>
        <div style="flex:1;">
            <span style="font-size:14px; font-weight:700; color:#4ade80;">
                {{ $pendingCount }} {{ __('message.Transfer-IN') }} {{ Str::plural('request', $pendingCount) }}
            </span>
            <span style="font-size:10px; font-weight:700; color:#22c55e; background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); padding:2px 8px; border-radius:20px; margin-left:8px; text-transform:uppercase; letter-spacing:0.06em;">PENDING</span>
            <p style="font-size:12px; color:#94a3b8; margin:3px 0 0;">{{ __('message.awaiting your approval') }}</p>
        </div>
        <div style="display:flex; align-items:center; gap:5px; padding:5px 10px; border:1px solid rgba(255,255,255,0.06); border-radius:8px;">
            <span style="width:6px; height:6px; border-radius:50%; background:#4ade80; display:inline-block; animation:tin-blink 1.5s ease-in-out infinite;"></span>
            <span style="font-size:11px; color:#64748b; white-space:nowrap;">{{ __('message.Auto-refreshing every') }} 8s</span>
        </div>
    </div>
    @endif

    {{-- ══ POPUP OVERLAY ══ --}}
    <div x-show="showPopup"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display:none;"
    >
        {{-- Backdrop --}}
        <div style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:9998; background:rgba(0,0,0,0.82); backdrop-filter:blur(6px);"></div>

        {{-- Centered wrapper --}}
        <div style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; display:flex; align-items:center; justify-content:center; padding:20px; pointer-events:none;">
            <div style="width:100%; max-width:560px; pointer-events:all; font-family:'Inter',system-ui,sans-serif;">

                {{-- Card --}}
                <div style="background:#0a1628; border:1px solid rgba(34,197,94,0.15); border-radius:24px; overflow:hidden; box-shadow:0 40px 100px rgba(0,0,0,0.8), 0 0 0 1px rgba(34,197,94,0.08);">

                    {{-- Green shimmer bar --}}
                    <div style="height:4px; background:linear-gradient(90deg,#22c55e,#16a34a,#4ade80,#16a34a,#22c55e); background-size:200%; animation:tin-shimmer 2s linear infinite;"></div>

                    {{-- Header --}}
                    <div style="padding:22px 26px 18px; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; gap:14px;">
                        <div style="width:50px; height:50px; border-radius:14px; background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); display:flex; align-items:center; justify-content:center; flex-shrink:0; animation:tin-pulse 2s ease-in-out infinite;">
                            <svg width="26" height="26" fill="none" stroke="#22c55e" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                            </svg>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:3px;">
                                <span style="font-size:18px; font-weight:800; color:#f1f5f9; letter-spacing:-0.02em;">{{ __('message.New Transfer-IN Request!') }}</span>
                                <span style="font-size:10px; font-weight:700; color:#22c55e; background:rgba(34,197,94,0.15); border:1px solid rgba(34,197,94,0.3); padding:2px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:0.06em; animation:tin-blink 1.5s ease-in-out infinite;">LIVE</span>
                            </div>
                            <p style="font-size:12px; color:#64748b; margin:0;">{{ __('message.Requires your immediate attention') }}</p>
                        </div>
                        <button wire:click="closePopup" @click="reset()"
                            style="width:32px; height:32px; border-radius:9px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center; cursor:pointer; color:#64748b; flex-shrink:0; transition:all 0.15s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.12)'; this.style.color='#94a3b8';"
                            onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#64748b';">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </button>
                    </div>

                    {{-- ══ MAIN DETAILS VIEW ══ --}}
                    @if(!$showRejectForm && !$showUploadForm)
                    <div style="padding:22px 26px;">

                        {{-- Row 1: Invoice + Acc Name --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:13px 15px;">
                                <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 5px;">{{ __('message.Invoice') }}</p>
                                <p style="font-size:13px; font-weight:800; color:#e2e8f0; margin:0; font-family:monospace;" x-text="transfer?.invoice_number ?? '—'"></p>
                            </div>
                            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:13px 15px;">
                                <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 5px;">{{ __('message.Account Name') }}</p>
                                <p style="font-size:14px; font-weight:800; color:#e2e8f0; margin:0;" x-text="transfer?.acc_name || '—'"></p>
                            </div>
                        </div>

                        {{-- Row 2: Bank + Acc Number --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:13px 15px;">
                                <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 5px;">{{ __('message.Bank Name') }}</p>
                                <p style="font-size:14px; font-weight:800; color:#e2e8f0; margin:0;" x-text="transfer?.bank_name ?? '—'"></p>
                            </div>
                            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:13px 15px;">
                                <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 5px;">{{ __('message.Account Number') }}</p>
                                <p style="font-size:15px; font-weight:800; color:#e2e8f0; margin:0; font-family:monospace;" x-text="transfer?.acc_number || '—'"></p>
                            </div>
                        </div>

                        {{-- Row 3: Amount + Net Amount (BIG) --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
                            <div style="background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.25); border-radius:12px; padding:15px;">
                                <p style="font-size:10px; font-weight:600; color:#16a34a; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 6px;">{{ __('message.Amount') }}</p>
                                <p style="font-size:26px; font-weight:900; color:#4ade80; margin:0; letter-spacing:-0.03em; line-height:1;" x-text="(transfer?.currency ?? '') + ' ' + (transfer?.amount ?? '—')"></p>
                            </div>
                            <div style="background:rgba(59,130,246,0.08); border:1px solid rgba(59,130,246,0.25); border-radius:12px; padding:15px;">
                                <p style="font-size:10px; font-weight:600; color:#3b82f6; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 6px;">{{ __('message.Net Amount') }}</p>
                                <p style="font-size:26px; font-weight:900; color:#60a5fa; margin:0; letter-spacing:-0.03em; line-height:1;" x-text="(transfer?.currency ?? '') + ' ' + (transfer?.net_amount ?? '—')"></p>
                            </div>
                        </div>

                        <p style="font-size:11px; color:#334155; text-align:center; margin:0 0 16px;"
                           x-text="'{{ __('message.Received at') }}: ' + (transfer?.created_at ?? '')"></p>
                        <div style="height:1px; background:rgba(255,255,255,0.06); margin-bottom:16px;"></div>

                        {{-- Accept / Reject --}}
                        <div style="display:flex; gap:12px;">
                            <button
                                wire:click="acceptTransfer({{ $latestTransfer['id'] ?? 0 }})"
                                wire:loading.attr="disabled"
                                style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; padding:15px; background:rgba(34,197,94,0.12); border:1.5px solid rgba(34,197,94,0.4); border-radius:13px; color:#4ade80; font-size:16px; font-weight:800; cursor:pointer; transition:all 0.15s;"
                                onmouseover="this.style.background='rgba(34,197,94,0.22)'; this.style.transform='translateY(-2px)';"
                                onmouseout="this.style.background='rgba(34,197,94,0.12)'; this.style.transform='none';"
                            >
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                {{ __('message.Accept') }}
                            </button>
                            <button
                                @click="openRejectPanel()"
                                style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; padding:15px; background:rgba(239,68,68,0.1); border:1.5px solid rgba(239,68,68,0.35); border-radius:13px; color:#f87171; font-size:16px; font-weight:800; cursor:pointer; transition:all 0.15s;"
                                onmouseover="this.style.background='rgba(239,68,68,0.2)'; this.style.transform='translateY(-2px)';"
                                onmouseout="this.style.background='rgba(239,68,68,0.1)'; this.style.transform='none';"
                            >
                                <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                {{ __('message.Reject') }}
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- ══ REJECT FORM ══ --}}
                    @if($showRejectForm)
                    <div style="padding:22px 26px;">
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
                            <div style="width:42px; height:42px; border-radius:11px; background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="20" height="20" fill="none" stroke="#f87171" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <p style="font-size:17px; font-weight:800; color:#f1f5f9; margin:0;">{{ __('message.Reject Transfer-IN Request') }}</p>
                                <p style="font-size:12px; color:#64748b; margin:2px 0 0;"
                                   x-text="'{{ __('message.Invoice') }}: ' + (transfer?.invoice_number ?? '')"></p>
                            </div>
                        </div>

                        {{-- Dropdown --}}
                        <div style="margin-bottom:14px;">
                            <label style="font-size:12px; font-weight:600; color:#94a3b8; display:block; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.07em;">
                                {{ __('message.Reason for Rejection') }} <span style="color:#f87171;">*</span>
                            </label>
                            <select
                                x-model="rejectCategory"
                                style="width:100%; padding:13px 16px; background:rgba(255,255,255,0.04); border:1.5px solid rgba(255,255,255,0.1); border-radius:12px; color:#e2e8f0; font-size:14px; font-family:'Inter',system-ui,sans-serif; outline:none; box-sizing:border-box; cursor:pointer; appearance:none; -webkit-appearance:none;"
                                onfocus="this.style.borderColor='rgba(239,68,68,0.5)'"
                                onblur="this.style.borderColor='rgba(255,255,255,0.1)'"
                            >
                                <option value="" style="background:#1e293b; color:#94a3b8;">-- {{ __('message.Select reason') }} --</option>
                                <option value="Wrong Account details" style="background:#1e293b;">{{ __('message.Wrong Account details') }}</option>
                                <option value="Wrong amount"          style="background:#1e293b;">{{ __('message.Wrong amount') }}</option>
                                <option value="Change mind"           style="background:#1e293b;">{{ __('message.Change mind') }}</option>
                                <option value="other"                 style="background:#1e293b;">{{ __('message.Other (specify below)') }}</option>
                            </select>
                        </div>

                        {{-- Other textarea — shown only when "other" selected --}}
                        <div x-show="rejectCategory === 'other'" style="margin-bottom:14px;">
                            <label style="font-size:12px; font-weight:600; color:#94a3b8; display:block; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.07em;">
                                {{ __('message.Specify Reason') }} <span style="color:#f87171;">*</span>
                            </label>
                            <textarea
                                x-model="otherReason"
                                rows="3"
                                placeholder="{{ __('message.Enter detailed reason...') }}"
                                autocomplete="off"
                                style="width:100%; padding:13px 16px; background:rgba(255,255,255,0.04); border:1.5px solid rgba(255,255,255,0.1); border-radius:12px; color:#e2e8f0; font-size:14px; font-family:'Inter',system-ui,sans-serif; resize:vertical; outline:none; box-sizing:border-box; line-height:1.6;"
                                onfocus="this.style.borderColor='rgba(239,68,68,0.5)'; this.style.background='rgba(255,255,255,0.06)';"
                                onblur="this.style.borderColor='rgba(255,255,255,0.1)'; this.style.background='rgba(255,255,255,0.04)';"
                            ></textarea>
                        </div>

                        <p id="in-reject-error" style="display:none; font-size:12px; color:#f87171; margin:0 0 12px;">
                            ⚠ {{ __('message.Please select a reason') }}
                        </p>

                        <div style="display:flex; gap:10px;">
                            <button
                                @click="submitRejectNow({{ $latestTransfer['id'] ?? 0 }})"
                                style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; padding:14px; background:rgba(239,68,68,0.15); border:1.5px solid rgba(239,68,68,0.4); border-radius:12px; color:#f87171; font-size:15px; font-weight:800; cursor:pointer; transition:all 0.15s;"
                                onmouseover="this.style.background='rgba(239,68,68,0.25)'; this.style.transform='translateY(-1px)';"
                                onmouseout="this.style.background='rgba(239,68,68,0.15)'; this.style.transform='none';"
                            >
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                {{ __('message.Yes, Reject') }}
                            </button>
                            <button
                                wire:click="cancelReject"
                                @click="reset()"
                                style="padding:14px 20px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:12px; color:#94a3b8; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.15s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.05)'"
                            >
                                {{ __('message.Back') }}
                            </button>
                        </div>
                    </div>
                    @endif

                </div>{{-- end card --}}
            </div>
        </div>
    </div>

</div>

@once
@push('styles')
<style>
@keyframes tin-shimmer { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }
@keyframes tin-blink   { 0%,100%{opacity:1} 50%{opacity:.3} }
@keyframes tin-pulse   { 0%,100%{box-shadow:0 0 0 0 rgba(34,197,94,.35)} 50%{box-shadow:0 0 0 8px rgba(34,197,94,0)} }
@keyframes tin-ping    { 0%{transform:scale(1);opacity:.45} 70%,100%{transform:scale(1.5);opacity:0} }
</style>
@endpush
@endonce