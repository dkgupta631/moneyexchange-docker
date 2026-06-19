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
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use App\Filament\Helper\CustomLogin;

// ✅ Explicit widget imports
use App\Filament\Bkkoffice\Widgets\NewTransferAlertWidget;
use App\Filament\Bkkoffice\Widgets\NewTransferInAlertWidget;
use App\Filament\Bkkoffice\Widgets\TransferStatsWidget;
use App\Filament\Bkkoffice\Widgets\TransferInStatsWidget;

class BkkofficePanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('bkkoffice')
            ->path('bkkoffice')
            ->authGuard('bkkoffice')
            ->login(CustomLogin::class)
            ->profile()
            ->colors([
                'primary' => Color::Green,
            ])
            ->maxContentWidth('full')
            ->font('Poppins')
            ->favicon(url('website/assets/logo/logo.png'))
            ->brandLogo(fn () => view('filament.logo'))
            ->brandName('G+ Services — BkkOffice')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(
                in: app_path('Filament/Bkkoffice/Resources'),
                for: 'App\\Filament\\Bkkoffice\\Resources'
            )
            ->discoverPages(
                in: app_path('Filament/Bkkoffice/Pages'),
                for: 'App\\Filament\\Bkkoffice\\Pages'
            )
            ->pages([
                \App\Filament\Bkkoffice\Pages\Dashboard::class,
            ])
            ->discoverWidgets(
                in: app_path('Filament/Bkkoffice/Widgets'),
                for: 'App\\Filament\\Bkkoffice\\Widgets'
            )
            // ✅ Explicitly list ALL widgets so Livewire can find them
            ->widgets([
                Widgets\AccountWidget::class,
                NewTransferAlertWidget::class,
                NewTransferInAlertWidget::class,
                TransferStatsWidget::class,
                TransferInStatsWidget::class,
            ])
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