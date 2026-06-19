<?php

namespace App\Http\Middleware;

use Inertia\Middleware;
use Illuminate\Http\Request;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Defines the props that are shared by default.
     */
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
             // Synchronously...
            'appName' => config('app.name'),
            // Lazily...
            // 'auth.user' => fn () => $request->user()
            //     ? $request->user()->only('id', 'name', 'email')
            //     : null,
             'auth' => [
                        'user' => $request->user(),
                    ],
             'flash' => [
                    'greet' => fn () => $request->session()->get('greet')
                ],
            'appUrl' => config('app.url'),
            'locale' => fn () => app()->getLocale(),
            'translations' => function () {
                return require resource_path('lang/' . app()->getLocale() . '/message.php');
            },

            
        ]);
    }
}