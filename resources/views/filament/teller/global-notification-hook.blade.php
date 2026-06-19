{{--
    resources/views/filament/teller/global-notification-hook.blade.php
    ────────────────────────────────────────────────────────────────────
    Mounted by TellerPanelProvider via PanelsRenderHook::BODY_START.
    Renders on EVERY teller panel page.

    We use the full PHP class name (::class constant) so Livewire resolves
    the component directly — no string alias needed, no ComponentNotFoundException.
--}}
@livewire(\App\Filament\Teller\Widgets\GlobalTransferInNotificationWidget::class)