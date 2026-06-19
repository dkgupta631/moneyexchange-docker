<?php

namespace App\Providers\Filament;

use App\Filament\Helper\CustomLogin;
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
use Filament\Navigation\MenuItem;
use Filament\Forms\Components\ColorPicker;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->authGuard('web')
            ->login()
            ->profile()
            ->colors([
                // Gold accent — primary buttons, links, highlights
                'primary' => [
                    50  => '#FFF8E7',
                    100 => '#FEEFC3',
                    200 => '#FDDF8A',
                    300 => '#FCC948',
                    400 => '#F5B014',
                    500 => '#D4920A', // Main gold
                    600 => '#B07508',
                    700 => '#8A5B06',
                    800 => '#664304',
                    900 => '#3F2902',
                    950 => '#261800',
                ],

                // Deep navy — used for sidebar, header, focus rings
                'gray' => [
                    50  => '#F0F4FA',
                    100 => '#DCE5F5',
                    200 => '#B8CAEA',
                    300 => '#8AAAD8',
                    400 => '#5C87C4',
                    500 => '#3A68AE',
                    600 => '#1E3A6E', // Deep navy accent
                    700 => '#162D56',
                    800 => '#0E1F3D', // Sidebar background
                    900 => '#080F1E', // Darkest navy
                    950 => '#040810',
                ],
            ])
            ->maxContentWidth('full')
            ->font('Poppins')
            ->favicon(url('website/assets/logo/logo.png'))
            ->brandLogo(fn () => view('filament.logo'))
            ->brandName('G+ Services — Manager')
            ->sidebarCollapsibleOnDesktop()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
                Widgets\FilamentInfoWidget::class,
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