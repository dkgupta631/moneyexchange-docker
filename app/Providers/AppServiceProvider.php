<?php
namespace App\Providers;
use Illuminate\Support\ServiceProvider;

use BezhanSalleh\FilamentLanguageSwitch\Enums\Placement;
use BezhanSalleh\FilamentLanguageSwitch\LanguageSwitch;
use Illuminate\Support\Facades\Schema;

use Filament\Facades\Filament;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;

use Inertia\Inertia;
use App\Models\Language;
use Illuminate\Support\Facades\Session;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

         // Share languages globally with all Inertia pages for Website START
        Inertia::share([
            'languages' => function () {
                return \App\Models\Language::where('status', 1)
                    ->orderBy('order')
                    ->select('*')
                    ->get();
            },
             'locale' => fn () => Session::get('locale', 'en'), // 👈 share current locale
        ]);
        
         // Share languages globally with all Inertia pages for Website END
        // FilamentAsset::register([
        //     Js::make('custom-scriptdd', __DIR__ . '/../../resources/js/AdminCustom.js'),
        // ]);

        if (Schema::hasTable('languages')) {
            $locales = \App\Models\Language::get()->map(function ($language) {
                return [
                    'label' => $language->name,
                    'code' => $language->code,
                    'icon' => $language->icon, // Assuming you have a URL to the flag icon stored in your database
                ];
            })->toArray();
        
            LanguageSwitch::configureUsing(function (LanguageSwitch $languageSwitch) use ($locales) {
                $flags = [];
                foreach ($locales as $locale) {
                    // Generate the full URL for the image
                    $flags[$locale['code']] = asset('storage/' . $locale['icon']);
                }
                $languageSwitch
                    ->locales(array_column($locales, 'code'))
                    ->visible(outsidePanels: true)
                    ->labels(array_column($locales, 'label'))
                    ->flags($flags) // Pass the generated flag URLs
                    // ->circular()
                    ->outsidePanelPlacement(Placement::TopCenter);
            });
            
        }
    }
}
