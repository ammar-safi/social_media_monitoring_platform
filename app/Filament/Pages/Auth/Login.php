<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BasePage;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Validation\ValidationException;
use App\Enums\UserTypeEnum;

class Login extends BasePage
{
    public function authenticate(): ?LoginResponse
    {
        try {
            $this->rateLimit(5);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $data = $this->form->getState();

        if (! Filament::auth()->attempt($this->getCredentialsFromFormData($data), $data['remember'] ?? false)) {
            $this->throwFailureValidationException();
        }

        $user = Filament::auth()->user();

        if (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        } elseif (
            ! $user -> active && 
            ! $user -> type == UserTypeEnum::ADMIN->value
        ) {
            Filament::auth()->logout();

            throw ValidationException::withMessages([
                "data.email" => "Your account is not active , please contact with the admin"
            ]);
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }
}
