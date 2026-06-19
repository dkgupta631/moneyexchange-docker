<?php

namespace App\Providers\Filament;

use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Support\Enums\MaxWidth;
use Filament\View\PanelsRenderHook;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Illuminate\Support\Facades\Blade;

use App\Filament\Helper\CustomLogin;
use App\Filament\Teller\Pages\TellerDashboard;

// ✅ All widgets explicitly imported
use App\Filament\Teller\Widgets\TransferInLiveNotificationWidget;
use App\Filament\Teller\Widgets\TransferInStatsWidget;
use App\Filament\Teller\Widgets\TransferOutLiveNotificationWidget;
use App\Filament\Teller\Widgets\TransferOutStatsWidget;
use App\Filament\Teller\Widgets\GlobalTransferInNotificationWidget;

class TellerPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('teller')
            ->path('teller')
            ->authGuard('teller')
            ->login(CustomLogin::class)
            ->profile()
            ->colors([
                'primary' => Color::Purple,
            ])
            ->maxContentWidth('full')
            ->font('Poppins')
            ->favicon(url('website/assets/logo/logo2.png'))
            ->brandLogo(fn () => view('filament.logo'))
            ->brandName('G+ Services — Teller')
            ->sidebarCollapsibleOnDesktop()
            ->pages([
                TellerDashboard::class,
            ])
            ->discoverResources(
                in: app_path('Filament/Teller/Resources'),
                for: 'App\\Filament\\Teller\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Teller/Pages'),
                for: 'App\\Filament\\Teller\\Pages'
            )
            ->discoverWidgets(
                in: app_path('Filament/Teller/Widgets'),
                for: 'App\\Filament\\Teller\\Widgets'
            )
            // ✅ Explicitly list so Livewire registers component aliases
            ->widgets([
                Widgets\AccountWidget::class,
                TransferInLiveNotificationWidget::class,
                TransferInStatsWidget::class,
                TransferOutLiveNotificationWidget::class,
                TransferOutStatsWidget::class,
                GlobalTransferInNotificationWidget::class,
            ])

            // ══════════════════════════════════════════════════════════════
            // ✅ GLOBAL POPUP — injected into EVERY teller page via RenderHook
            // The GlobalTransferInNotificationWidget Livewire component is
            // mounted inside BODY_START so it polls and fires the popup
            // regardless of which page/resource the teller is viewing.
            // ══════════════════════════════════════════════════════════════
            ->renderHook(
                PanelsRenderHook::BODY_START,
                fn (): \Illuminate\View\View => view('filament.teller.global-notification-hook'),
            )

            // ─── Middleware ───────────────────────────────────────────────
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}