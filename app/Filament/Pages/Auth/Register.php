<?php

namespace App\Filament\Pages\Auth;

use App\Enums\UserTypeEnum;
use App\Models\User;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Exception;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\RegistrationResponse;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Auth\Register as BaseRegister;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use PhpParser\Node\Expr\FuncCall;

/**
 * @property Form $form
 */
class Register extends BaseRegister
{
    public function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                TextInput::make("first_name")
                    ->required(),
                TextInput::make("last_name")
                    ->required(),
                TextInput::make("email")
                    ->unique("users", "email")
                    ->required(),
                TextInput::make("phone_number")
                    ->required(),
                TextInput::make("password")
                    ->revealable()
                    ->confirmed()
                    ->password()
                    ->required(),
                TextInput::make("password_confirmation")
                    ->dehydrated(false)
                    ->revealable()
                    ->password()
                    ->same("password")
                    ->required(),
            ]);
    }
    public function register(): ?RegistrationResponse
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }

        $user = $this->wrapInDatabaseTransaction(function (): Model {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');

            return $user;
        });

        // TODO 
        Notification::make()
            ->success()
            ->title("Your account has been created")
            ->body("Your account need to be verified , We will send an email to you once it's verified")
            ->send();
        Notification::make()
            ->title('New User')
            ->icon("heroicon-o-user")
            ->body("new User need to be verified")
            ->actions([
                Action::make("goToRequest")
                    ->button()
                    ->color("primary")
                    ->url()
            ])
            ->sendToDatabase(User::where("type", UserTypeEnum::ADMIN->value)->first());
        return app(RegistrationResponse::class);
    }
}
