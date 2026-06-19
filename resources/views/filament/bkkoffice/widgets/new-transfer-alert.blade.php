<div
    x-data="{
        showPopup: @entangle('showPopup'),
        transfer: @entangle('latestTransfer'),
        audioCtx: null,

        playAlertSound() {
            try {
                this.audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                const playBeep = (freq, start, dur) => {
                    const osc = this.audioCtx.createOscillator();
                    const gain = this.audioCtx.createGain();
                    osc.connect(gain); gain.connect(this.audioCtx.destination);
                    osc.frequency.value = freq; osc.type = 'sine';
                    gain.gain.setValueAtTime(0.35, this.audioCtx.currentTime + start);
                    gain.gain.exponentialRampToValueAtTime(0.001, this.audioCtx.currentTime + start + dur);
                    osc.start(this.audioCtx.currentTime + start);
                    osc.stop(this.audioCtx.currentTime + start + dur);
                };
                playBeep(880,0,0.2); playBeep(1100,0.25,0.2);
                playBeep(880,0.5,0.3); playBeep(1100,0.85,0.5);
            } catch(e) {}
        },

        speakAlert(t) {
            if ('speechSynthesis' in window && t) {
                window.speechSynthesis.cancel();
                const msg = new SpeechSynthesisUtterance(
                    'Attention! New Transfer Out request. Bank ' + (t.bank_name||'') +
                    '. Amount ' + (t.currency||'') + ' ' + (t.amount||'') + '. Please review immediately.'
                );
                msg.rate=0.9; msg.volume=1.0;
                window.speechSynthesis.speak(msg);
            }
        }
    }"
    x-on:new-transfer-arrived.window="
        transfer = $event.detail.transfer;
        showPopup = true;
        playAlertSound();
        setTimeout(() => speakAlert(transfer), 800);
    "
    wire:poll.8000ms="checkNewTransfers"
>

    {{-- ══ PENDING BANNER ══ --}}
    @if($pendingCount > 0)
    <div style="margin-bottom:16px; background:rgba(245,158,11,0.07); border:1px solid rgba(245,158,11,0.2); border-left:4px solid #f59e0b; border-radius:12px; padding:14px 18px; display:flex; align-items:center; gap:12px;">
        <div style="position:relative; flex-shrink:0; width:38px; height:38px;">
            <div style="position:absolute; inset:0; border-radius:10px; background:rgba(245,158,11,0.2); animation:bk-ping 2s ease-in-out infinite;"></div>
            <div style="position:relative; width:38px; height:38px; border-radius:10px; background:rgba(245,158,11,0.12); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center;">
                <svg width="18" height="18" fill="none" stroke="#f59e0b" viewBox="0 0 24 24" stroke-width="2.2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
        </div>
        <div style="flex:1;">
            <span style="font-size:14px; font-weight:700; color:#fbbf24;">
                {{ $pendingCount }} {{ __('message.Transfer-OUT') }} {{ Str::plural('request', $pendingCount) }}
            </span>
            <span style="font-size:10px; font-weight:700; color:#f59e0b; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); padding:2px 8px; border-radius:20px; margin-left:8px; text-transform:uppercase; letter-spacing:0.06em;">PENDING</span>
            <p style="font-size:12px; color:#94a3b8; margin:3px 0 0;">{{ __('message.awaiting your approval') }}</p>
        </div>
        <div style="display:flex; align-items:center; gap:5px; padding:5px 10px; border:1px solid rgba(255,255,255,0.06); border-radius:8px;">
            <span style="width:6px; height:6px; border-radius:50%; background:#4ade80; display:inline-block; animation:bk-blink 1.5s ease-in-out infinite;"></span>
            <span style="font-size:11px; color:#64748b; white-space:nowrap;">{{ __('message.Auto-refreshing every') }} 8s</span>
        </div>
    </div>
    @endif

    {{-- ══ POPUP OVERLAY — truly centered ══ --}}
    <div
        x-show="showPopup"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        style="display:none;"
    >
        {{-- Full screen fixed backdrop --}}
        <div style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:9998; background:rgba(0,0,0,0.82); backdrop-filter:blur(6px);"></div>

        {{-- Centered card wrapper --}}
        <div style="position:fixed; top:0; left:0; right:0; bottom:0; z-index:9999; display:flex; align-items:center; justify-content:center; padding:20px; pointer-events:none;">
            <div
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 transform scale-95 translateY(20px)"
                x-transition:enter-end="opacity-100 transform scale-100 translateY(0)"
                style="width:100%; max-width:560px; pointer-events:all; font-family:'Inter',system-ui,sans-serif;"
            >
                {{-- Card --}}
                <div style="background:#0f172a; border:1px solid rgba(255,255,255,0.09); border-radius:24px; overflow:hidden; box-shadow:0 40px 100px rgba(0,0,0,0.8), 0 0 0 1px rgba(251,191,36,0.1);">

                    {{-- shimmer bar --}}
                    <div style="height:4px; background:linear-gradient(90deg,#f59e0b,#f97316,#ef4444,#f97316,#f59e0b); background-size:200%; animation:bk-shimmer 2s linear infinite;"></div>

                    {{-- Header --}}
                    <div style="padding:22px 26px 18px; border-bottom:1px solid rgba(255,255,255,0.06); display:flex; align-items:center; gap:14px;">
                        <div style="width:50px; height:50px; border-radius:14px; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); display:flex; align-items:center; justify-content:center; flex-shrink:0; animation:bk-pulse 2s ease-in-out infinite;">
                            <svg width="24" height="24" fill="none" stroke="#f59e0b" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        </div>
                        <div style="flex:1; min-width:0;">
                            <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap; margin-bottom:3px;">
                                <span style="font-size:19px; font-weight:800; color:#f1f5f9; letter-spacing:-0.02em;">{{ __('message.New Transfer-OUT Request!') }}</span>
                                <span style="font-size:10px; font-weight:700; color:#f59e0b; background:rgba(245,158,11,0.15); border:1px solid rgba(245,158,11,0.3); padding:2px 8px; border-radius:20px; text-transform:uppercase; letter-spacing:0.06em; animation:bk-blink 1.5s ease-in-out infinite;">LIVE</span>
                            </div>
                            <p style="font-size:12px; color:#64748b; margin:0;">{{ __('message.Requires your immediate attention') }}</p>
                        </div>
                        <button wire:click="closePopup"
                            style="width:32px; height:32px; border-radius:9px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.08); display:flex; align-items:center; justify-content:center; cursor:pointer; color:#64748b; flex-shrink:0; transition:all 0.15s;"
                            onmouseover="this.style.background='rgba(255,255,255,0.12)'; this.style.color='#94a3b8';"
                            onmouseout="this.style.background='rgba(255,255,255,0.05)'; this.style.color='#64748b';">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    {{-- ══ TRANSFER DETAILS (main view) ══ --}}
                    @if(!$showRejectForm)
                    <div style="padding:22px 26px;">

                        {{-- Row 1: Invoice + Customer --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:13px 15px;">
                                <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 5px;">{{ __('message.Invoice') }}</p>
                                <p style="font-size:14px; font-weight:800; color:#e2e8f0; margin:0; font-family:'JetBrains Mono',monospace;" x-text="transfer?.invoice_number ?? '—'"></p>
                            </div>
                           
                             <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:13px 15px;">
                                <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 5px;">{{ __('message.Account Name') }}</p>
                                <p style="font-size:14px; font-weight:800; color:#e2e8f0; margin:0;" x-text="transfer?.acc_name || '—'"></p>
                            </div>
                        </div>

                        {{-- Row 2: Bank + Acc Name --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:10px;">
                            <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:13px 15px;">
                                <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 5px;">{{ __('message.Bank Name') }}</p>
                                <p style="font-size:14px; font-weight:800; color:#e2e8f0; margin:0;" x-text="transfer?.bank_name ?? '—'"></p>
                            </div>
                             <div style="background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.07); border-radius:12px; padding:13px 15px;">
                                <p style="font-size:10px; font-weight:600; color:#475569; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 5px;">{{ __('message.Account Number') }}</p>
                                <p style="font-size:15px; font-weight:800; color:#e2e8f0; margin:0; font-family:'JetBrains Mono',monospace;" x-text="transfer?.acc_number || '—'"></p>
                            </div>
                           
                        </div>

                       

                        {{-- Row 4: Amount + Net (BIG) --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; margin-bottom:16px;">
                            <div style="background:rgba(245,158,11,0.09); border:1px solid rgba(245,158,11,0.25); border-radius:12px; padding:15px;">
                                <p style="font-size:10px; font-weight:600; color:#d97706; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 6px;">{{ __('message.Amount') }}</p>
                                <p style="font-size:26px; font-weight:900; color:#fbbf24; margin:0; letter-spacing:-0.03em; line-height:1;" x-text="(transfer?.currency??'') + ' ' + (transfer?.amount??'—')"></p>
                            </div>
                            <div style="background:rgba(34,197,94,0.08); border:1px solid rgba(34,197,94,0.25); border-radius:12px; padding:15px;">
                                <p style="font-size:10px; font-weight:600; color:#16a34a; text-transform:uppercase; letter-spacing:0.08em; margin:0 0 6px;">{{ __('message.Net Amount') }}</p>
                                <p style="font-size:26px; font-weight:900; color:#4ade80; margin:0; letter-spacing:-0.03em; line-height:1;" x-text="(transfer?.currency??'') + ' ' + (transfer?.net_amount??'—')"></p>
                            </div>
                        </div>

                        <p style="font-size:11px; color:#334155; text-align:center; margin:0 0 16px;" x-text="'{{ __('message.Received at') }}: ' + (transfer?.created_at ?? '')"></p>
                        <div style="height:1px; background:rgba(255,255,255,0.06); margin-bottom:16px;"></div>

                        {{-- Buttons --}}
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
                                wire:click="openRejectForm"
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

                    {{-- ══ REJECT REASON FORM ══ --}}
                    @if($showRejectForm)
                    <div style="padding:22px 26px;">
                        <div style="display:flex; align-items:center; gap:12px; margin-bottom:20px;">
                            <div style="width:42px; height:42px; border-radius:11px; background:rgba(239,68,68,0.12); border:1px solid rgba(239,68,68,0.3); display:flex; align-items:center; justify-content:center; flex-shrink:0;">
                                <svg width="20" height="20" fill="none" stroke="#f87171" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                            </div>
                            <div>
                                <p style="font-size:17px; font-weight:800; color:#f1f5f9; margin:0;">{{ __('message.Reject Transfer Request') }}</p>
                                <p style="font-size:12px; color:#64748b; margin:2px 0 0;" x-text="'{{ __('message.Invoice') }}: ' + (transfer?.invoice_number ?? '')"></p>
                            </div>
                        </div>

                        <div style="margin-bottom:16px;">
                            <label style="font-size:12px; font-weight:600; color:#94a3b8; display:block; margin-bottom:8px; text-transform:uppercase; letter-spacing:0.07em;">
                                {{ __('message.Reason for Rejection') }} <span style="color:#f87171;">*</span>
                            </label>
                            <textarea
                                wire:model="rejectReason"
                                rows="4"
                                placeholder="{{ __('message.Enter reason for rejection...') }}"
                                style="width:100%; padding:14px 16px; background:rgba(255,255,255,0.04); border:1.5px solid rgba(255,255,255,0.1); border-radius:12px; color:#e2e8f0; font-size:14px; font-family:'Inter',system-ui,sans-serif; resize:vertical; outline:none; box-sizing:border-box; line-height:1.6;"
                                onfocus="this.style.borderColor='rgba(239,68,68,0.5)'"
                                onblur="this.style.borderColor='rgba(255,255,255,0.1)'"
                            ></textarea>
                            @error('rejectReason')
                                <p style="font-size:12px; color:#f87171; margin:6px 0 0;">{{ $message }}</p>
                            @enderror
                        </div>

                        <div style="display:flex; gap:10px;">
                            <button
                                wire:click="submitReject({{ $latestTransfer['id'] ?? 0 }})"
                                wire:loading.attr="disabled"
                                style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; padding:14px; background:rgba(239,68,68,0.15); border:1.5px solid rgba(239,68,68,0.4); border-radius:12px; color:#f87171; font-size:15px; font-weight:800; cursor:pointer; transition:all 0.15s;"
                                onmouseover="this.style.background='rgba(239,68,68,0.25)'; this.style.transform='translateY(-1px)';"
                                onmouseout="this.style.background='rgba(239,68,68,0.15)'; this.style.transform='none';"
                            >
                                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                                {{ __('message.Yes, Reject') }}
                            </button>
                            <button
                                wire:click="cancelReject"
                                style="padding:14px 20px; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.1); border-radius:12px; color:#94a3b8; font-size:14px; font-weight:600; cursor:pointer; transition:all 0.15s;"
                                onmouseover="this.style.background='rgba(255,255,255,0.1)'"
                                onmouseout="this.style.background='rgba(255,255,255,0.05)'"
                            >
                                {{ __('message.Cancel') }}
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
@keyframes bk-shimmer { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }
@keyframes bk-blink   { 0%,100%{opacity:1} 50%{opacity:.3} }
@keyframes bk-pulse   { 0%,100%{box-shadow:0 0 0 0 rgba(245,158,11,.35)} 50%{box-shadow:0 0 0 8px rgba(245,158,11,0)} }
@keyframes bk-ping    { 0%{transform:scale(1);opacity:.45} 70%,100%{transform:scale(1.5);opacity:0} }
</style>
@endpush
@endonce