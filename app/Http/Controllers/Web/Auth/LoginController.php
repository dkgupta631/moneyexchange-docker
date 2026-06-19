<?php
namespace App\Http\Controllers\Web\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;

class LoginController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Rate limiting — 5 attempts per minute per IP + username
        $throttleKey = Str::transliterate(Str::lower($request->name) . '|' . $request->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            throw ValidationException::withMessages([
                'name' => __('message.too_many_attempts', ['seconds' => $seconds]),
            ]);
        }

        if (! Auth::attempt([
            'name'     => $request->name,
            'password' => $request->password,
        ], $request->boolean('remember'))) {
            RateLimiter::hit($throttleKey);
            throw ValidationException::withMessages([
                'name' => __('message.invalid credentials'),
            ]);
        }

        // Only staff / admin roles allowed here
        $user = Auth::user();
        if (!in_array($user->role, ['staff', 'teller', 'admin'])) {
            Auth::logout();

            throw ValidationException::withMessages([
                'name' => __('message.Access Denied!'),
            ]);
        }
        // dd(Auth::user());
        if ((int) $user->email_verified_at === 0) {
            Auth::logout();

            throw ValidationException::withMessages([
                'name' => __('message.Your account have been disabled!'),
            ]);
        }

        RateLimiter::clear($throttleKey);
        $request->session()->regenerate();

        // ↓ key must be 'greet' — matches your middleware: flash.greet
        return redirect()->route('home')->with('greet', __('message.Login Successfully!', ['name' => Auth::user()->name]));
    }

    public function destroy(Request $request)
    {
        Auth::logout(); // logout only auth user
        $request->session()->regenerateToken(); // regenerate CSRF token
        return redirect()->route('home');
    }
}
