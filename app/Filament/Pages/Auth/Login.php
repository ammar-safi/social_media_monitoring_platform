<?php

namespace App\Filament\Pages\Auth;

use Filament\Pages\Auth\Login as BaseLogin;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Filament\Facades\Filament;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Validation\ValidationException;
use App\Enums\UserTypeEnum;
use Filament\Actions\Action;

class Login extends BaseLogin
{
    protected static string $view = 'filament.pages.auth.login';

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
            ! $user->active &&
            ! ($user->type == UserTypeEnum::ADMIN)
        ) {
            Filament::auth()->logout();

            throw ValidationException::withMessages([
                "data.email" => "Your account is not active"
            ]);
        } elseif (
            ($user instanceof FilamentUser) &&
            (! $user->canAccessPanel(Filament::getCurrentPanel()))
        ) {
            Filament::auth()->logout();

            $this->throwFailureValidationException();
        }

        session()->regenerate();

        return app(LoginResponse::class);
    }

    public function SignUpAsPolicyMakerAction(): Action
    {
        return Action::make('signup_as_policy_maker')
            ->link()
            ->label("sign up as policy maker")
            // ->url(PolicyMakerRegister::getUrl())
        ;
    }
}
