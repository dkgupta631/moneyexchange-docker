<?php  

namespace App\Filament\Helper;

use Filament\Forms\Form;
use Filament\Pages\Auth\Login;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Checkbox;
use Illuminate\Validation\ValidationException;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;

class CustomLogin extends Login
{
    public function form(Form $form): Form
    {
        return $form->schema([
            TextInput::make('phoneNumber')
                ->label(__('message.Phone Number'))
                ->required()
                ->tel()
                ->prefixIcon('heroicon-m-phone')   
                ->maxLength(12)
                ->autofocus(),

            TextInput::make('password')
                ->label(__('message.Password'))
                ->prefixIcon('heroicon-m-lock-closed')
                ->required()
                ->revealable()
                ->maxLength(15)
                ->password(),

            Checkbox::make('remember')
                ->label(__('message.Remember me')),
        ]);
    }

    protected function getCredentialsFromFormData(array $data): array
    {
        return [
            'phoneNumber' => $data['phoneNumber'],
            'password'    => $data['password'],
        ];
    }

    public function authenticate(): ?LoginResponse
    {
        $data = $this->form->getState();

        $guardName = filament()->getCurrentPanel()->getAuthGuard();
        $panelId   = filament()->getCurrentPanel()->getId();

        // Step 1 — check credentials
        if (! auth()->guard($guardName)->attempt([
            'phoneNumber' => $data['phoneNumber'],
            'password'    => $data['password'],
        ], $data['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'data.phoneNumber' =>  __('message.invalid credentials'),
            ]);
        }

        $user = auth()->guard($guardName)->user();

        // Step 2 — check email_verified_at
        if (empty($user->email_verified_at) || $user->email_verified_at == 0) {
            auth()->guard($guardName)->logout();
            session()->invalidate();
            throw ValidationException::withMessages([
                'data.phoneNumber' => __('message.You do not have permission to access this panel!'),
            ]);
        }

        // Step 3 — check role matches panel
        $allowed = match($panelId) {
            'bkkoffice' => $user->role === 'bkkoffice',
            'teller' => $user->role === 'teller',
        };

        if (! $allowed) {
            auth()->guard($guardName)->logout();
            session()->invalidate();
            throw ValidationException::withMessages([
                'data.phoneNumber' => __('message.You do not have permission to access this panel!'),
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}