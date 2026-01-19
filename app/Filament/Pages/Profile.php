<?php

namespace App\Filament\Pages;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Hash;
use Filament\Facades\Filament;
use Illuminate\Validation\Rules\Password;
use Symfony\Component\Console\Color;

class Profile extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-user-circle';

    protected static string $view = 'filament.pages.profile';

    protected static ?string $navigationLabel = 'Profile';

    protected static ?int $navigationSort = 99;

    public ?array $data = [];

    public function mount(): void
    {
        $this->fillForm();
    }

    protected function fillForm(): void
    {
        $user = auth()->user();

        $this->form->fill([
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'phone_number' => $user->phone_number,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Basic Information')
                    ->description('Update your basic profile information.')
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->label('First Name')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\TextInput::make('last_name')
                            ->label('Last Name')
                            ->maxLength(255),

                        Forms\Components\TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->required()
                            ->maxLength(255)
                            ->unique('users', 'email', ignoreRecord: Filament::auth()->user()->id),

                        Forms\Components\TextInput::make('phone_number')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Change Password')
                    ->description('Update your password. Leave blank to keep current password.')
                    ->schema([
                        Forms\Components\TextInput::make('current_password')
                            ->label('Current Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn($get) => filled($get('password')))
                            ->rules([
                                fn() => function (string $attribute, $value, \Closure $fail) {
                                    if (filled($value) && !Hash::check($value, auth()->user()->password)) {
                                        $fail('The current password is incorrect.');
                                    }
                                },
                            ]),

                        Forms\Components\TextInput::make('password')
                            ->label('New Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(fn($state) => filled($state))
                            ->rules([Password::default()]),

                        Forms\Components\TextInput::make('password_confirmation')
                            ->label('Confirm New Password')
                            ->password()
                            ->revealable()
                            ->dehydrated(false)
                            ->required(fn($get) => filled($get('password')))
                            ->same('password'),
                    ])
                    ->columns(1),
            ])
            ->statePath('data')
            ->model(auth()->user());
    }
    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Changes')
                ->submit('save'),
            Action::make('cancel')
                ->label('Cancel')
                ->action('cancel')
                ->color('gray')

        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $user = auth()->user();

        // Update basic information
        $user->update([
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
        ]);

        // Update password if provided
        if (filled($data['password'])) {
            $user->update([
                'password' => Hash::make($data['password']),
            ]);
        }

        Notification::make()
            ->title('Profile updated successfully')
            ->success()
            ->send();

        // Refresh the form to clear password fields
        $this->fillForm();
    }

    public static function shouldRegisterNavigation(): bool
    {
        // Only show profile page for users who have 'show user' permission
        // This excludes Policy Makers who don't have this permission
        return auth()->user()?->can('show user') ?? false;
    }

    public static function canAccess(): bool
    {
        // Allow access if user has 'show user' permission
        return auth()->user()?->can('show user') ?? false;
    }

    public function cancel(): void
    {
        $this->fillForm();
    }
}
