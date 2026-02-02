<?php

namespace App\Filament\Pages\Auth;

use App\Enums\ApproveUserStatusEnum;
use App\Enums\InviteStatusEnum;
use App\Enums\UserTypeEnum;
use App\Filament\Resources\ApproveUserResource;
use App\Filament\Resources\PolicyRequestResource;
use App\Models\ApproveUser;
use App\Models\Invite;
use App\Models\PolicyRequest;
use App\Models\User;
use Carbon\Carbon;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use Exception;
use Filament\Actions\Action as ActionsAction;
use Filament\Events\Auth\Registered;
use Filament\Facades\Filament;
use Filament\Forms\Components\Checkbox;
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
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use PhpParser\Node\Expr\FuncCall;

/**
 * @property Form $form
 */
class Register extends BaseRegister
{
    protected static string $view = 'filament.pages.auth.register';
    public function form(Form $form): Form
    {
        return $form
            ->columns(2)
            ->schema([
                $this->getFirstNameInput(),
                $this->getLastNameInput(),
                $this->getEmailInput(),
                $this->getPhoneNumberInput(),
                $this->getPasswordInput(),
                $this->getPasswordConfirmationInput(),
                $this->getTokenInput(),
                $this->getPolicyCheckInput(),
            ]);
    }
    public function register(): ?RegistrationResponse
    {
        if ($this->data["sign_up_as_policy"]) {
            $this->PolicyRegister();
        } else {
            $this->GovRegister();
        }
        Notification::make()
            ->success()
            ->title("Your account has been created")
            ->body("Your account need to be verified , We will send an email to you once it's verified")
            ->send();


        return app(RegistrationResponse::class);
    }
    protected function handleRegistration(array $data): Model
    {
        if (isset($data['sign_up_as_policy']) && $data["sign_up_as_policy"] == true) {
            $data["type"] = UserTypeEnum::POLICY_MAKER->value;
        }
        return $this->getUserModel()::create($data);
    }
    public function GovRegister()
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
        DB::beginTransaction();
        try {
            $request = ApproveUser::create([
                "user_id" => $user->id,
                "admin_id" => null,
                "expired_at" => Carbon::now()->addDays(config("app.approve_expired", 5)),
                "expired" => 0,
                "status" => ApproveUserStatusEnum::PENDING,
            ]);
        } catch (Exception $e) {
            DB::rollBack();
        }
        Notification::make()
            ->title('New Government official')
            ->icon("heroicon-o-user")
            ->body($request->user?->name . " requested to verify a new account")
            ->actions([
                Action::make("goToRequest")
                    ->button()
                    ->color("primary")
                    ->url(ApproveUserResource::getUrl('view', ['record' => $request->id]))
                    ->markAsRead()
            ])
            ->sendToDatabase(User::where("type", UserTypeEnum::ADMIN->value)->first());
        DB::commit();
    }
    public function PolicyRegister()
    {
        try {
            $this->rateLimit(2);
        } catch (TooManyRequestsException $exception) {
            $this->getRateLimitedNotification($exception)?->send();

            return null;
        }


        [$user, $invite] = $this->wrapInDatabaseTransaction(function () {
            $this->callHook('beforeValidate');

            $data = $this->form->getState();

            $this->callHook('afterValidate');

            $data = $this->mutateFormDataBeforeRegister($data);


            $invite = Invite::where("email", $data["email"])->first();

            if (is_null($invite)) {
                throw ValidationException::withMessages([
                    "data.email" => "email is not correct"
                ]);
            }

            if ($invite->status == InviteStatusEnum::EXPIRED->value || $invite->status == InviteStatusEnum::USED->value) {
                throw ValidationException::withMessages([
                    "data.email" => "Your Invitation has been expired"
                ]);
            }
            if ($invite->token != $data["token"]) {
                throw ValidationException::withMessages([
                    "data.token" => "Token is not correct"
                ]);
            }

            $data = [
                "type" => UserTypeEnum::POLICY_MAKER->value,
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'phone_number' => $data['phone_number'],
                'password' => Hash::make($data['password']),
            ];

            $invite->update([
                "status" => InviteStatusEnum::USED->value
            ]);

            $this->callHook('beforeRegister');

            $user = $this->handleRegistration($data);

            $this->form->model($user)->saveRelationships();

            $this->callHook('afterRegister');



            $request = PolicyRequest::create([
                "invite_id" => $invite->id,
                "admin_id" => null,
                "expired_at" => Carbon::now()->addDays(config("app.approve_expired", 5)),
                "status" => ApproveUserStatusEnum::PENDING,
            ]);

            Notification::make()
                ->title('New Policy Maker')
                ->icon("heroicon-o-user")
                ->body($user?->name . " requested to verify a new account")
                ->actions([
                    Action::make("goToRequest")
                        ->button()
                        ->color("primary")
                        ->url(PolicyRequestResource::getUrl('view', ['record' => $request->id]))
                        ->markAsRead()
                ])
                ->sendToDatabase(User::where("type", UserTypeEnum::ADMIN->value)->first());
            return [$user, $invite];
        });
    }




    public function loginAction(): ActionsAction
    {
        return ActionsAction::make('login')
            ->link()
            ->label("already have an account")
            ->url(filament()->getLoginUrl());
    }

    public function getFirstNameInput()
    {
        return
            TextInput::make("first_name")
            ->markAsRequired(False)
            ->required();
    }
    public function getLastNameInput()
    {
        return
            TextInput::make("last_name")
            ->markAsRequired(False)
            ->required();
    }
    public function getEmailInput()
    {
        return
            TextInput::make("email")
            ->unique("users", "email")
            ->markAsRequired(False)
            ->hint("Enter the invited email")
            ->required();
    }
    public function getPhoneNumberInput()
    {
        return
            TextInput::make("phone_number")
            ->markAsRequired(False)
            ->required()
        ;
    }
    public function getPasswordInput()
    {
        return
            TextInput::make("password")
            ->revealable()
            ->confirmed()
            ->password()
            ->markAsRequired(False)
            ->required();
    }
    public function getPasswordConfirmationInput()
    {
        return
            TextInput::make("password_confirmation")
            ->dehydrated(false)
            ->revealable()
            ->password()
            ->same("password")
            ->markAsRequired(False)
            ->required();
    }
    public function getTokenInput()
    {
        return
            TextInput::make("token")
            ->length(config("app.otp_length", null))
            ->hidden(fn(callable $get) => !($get("sign_up_as_policy") ? true : False))
            ->dehydrated(fn(callable $get) => !($get("sign_up_as_policy") ? False : true))
            ->required()
            ->markAsRequired(False)
            ->hint("this token was sent to your email")
            ->columnSpanFull();
    }
    public function getPolicyCheckInput()
    {
        return
            Checkbox::make("sign_up_as_policy")
            ->label("sign up as policy maker")
            ->reactive()
            ->dehydrated(fn(callable $get) => ($get("sign_up_as_policy") ? true : False))
            ->columnSpanFull();
    }
}
