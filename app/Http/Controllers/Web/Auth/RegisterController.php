<?php
namespace App\Http\Controllers\Web\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class RegisterController extends Controller
{
    public function create()
    {
        return Inertia::render('Auth/Register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3', 'max:255', 'regex:/^[a-zA-Z0-9_\s]+$/', 'unique:users,name,'],
            'phoneNumber' => ['required', 'string', 'regex:/^\+?[0-9]{7,15}$/', 'unique:users,phoneNumber'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(8)
                ->mixedCase()
                ->numbers()
                ->symbols()
            ],
        ], [
            'name.regex'        => 'Username may only contain letters, numbers, underscores, and spaces.',
            'phoneNumber.regex' => 'Please enter a valid phone number (7–15 digits, optional + prefix).',
            'phoneNumber.unique'=> 'This phone number is already registered.',
            'password.confirmed'=> 'Password confirmation does not match.',
        ]);

        $data = [
            'name'        => $validated['name'],
            'phoneNumber' => $validated['phoneNumber'],
            'email'       => $validated['email'],
            'password'    => Hash::make($validated['password']),
            'role'        => 'teller',
        ];
        // dd($data);
        User::create($data);

        return redirect()->route('teller.login')->with('greet', __('message.Registration successful! Please log in.'));
    }
}
