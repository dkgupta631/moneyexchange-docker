{{-- resources/views/filament/teller/modals/view-slip.blade.php --}}
<div class="space-y-4">
    <div class="overflow-hidden rounded-xl border border-gray-200 dark:border-gray-700 shadow-md">
        <img
            src="{{ $slipUrl }}"
            alt="{{ __('message.Transaction Slip') }} - {{ $invoiceNumber }}"
            class="w-full object-contain max-h-[60vh]"
            onerror="this.style.display='none'; document.getElementById('slip-error').style.display='block';"
        />
        <p id="slip-error" style="display:none;" class="p-4 text-center text-sm text-red-500">
            {{ __('message.No slip uploaded') }}
        </p>
    </div>

    {{-- Download button --}}
    <a
        href="{{ $slipUrl }}"
        download
        target="_blank"
        class="flex w-full items-center justify-center gap-2 rounded-xl border border-green-500/40 bg-green-50 px-4 py-2.5 text-sm font-semibold text-green-700 transition hover:bg-green-100 dark:bg-green-950/30 dark:text-green-300 dark:hover:bg-green-950/50"
    >
        <x-heroicon-s-arrow-down-tray class="h-4 w-4" />
        {{ $downloadLabel }}
    </a>
</div>