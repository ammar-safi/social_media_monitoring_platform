<?php

namespace App\Filament\Pages;

use App\Enums\InviteStatusEnum;
use App\Enums\UserTypeEnum;
use App\Events\EmailEvent;
use App\Models\Invite;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Nette\Utils\Random;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;

class InvitePolicyMaker extends Page implements HasTable
{
    use InteractsWithTable;
    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static string $view = 'filament.pages.invite-policy-maker';
    protected static ?string $navigationGroup = "Other options";
    protected static ?int $navigationSort = 2;

    public ?array $data = [];


    public static function canAccess(): bool
    {
        if (Filament::auth()->user()->type == UserTypeEnum::USER || Filament::auth()->user()->type == UserTypeEnum::ADMIN) {
            return true;
        }
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("Invite card")
                    ->description("The Policy maker will receive an invitation on his email")
                    ->schema([
                        TextInput::make("email")
                            ->required()
                            ->email()
                            ->maxLength(255)
                            ->markAsRequired(False)
                            ->prefixIcon("heroicon-o-envelope")
                    ])
            ])
            ->statePath('data')
            ->model(Invite::class);;
    }

    protected function getFormActions(): array
    {
        return [
            Action::make("send")
                ->color("info")
                ->label("Send Invitation")
                ->requiresConfirmation()
                ->modalIcon("heroicon-o-info")
                ->modalDescription(function ($form): string {
                    return "";
                })
                ->submit("save")
        ];
    }

    public function save(): void
    {
        $user = Filament::auth()->user();
        $data = $this->form->getState();

        $invite = $user->PolicyMakersThatUserInvites()->create([
            "email" => $data["email"],
            "status" => InviteStatusEnum::PENDING->value
        ]);

        event(new EmailEvent($invite, "You are invited to Sign up in our site " . $this->user->first_name, "Invitation", $data["email"]));

        Notification::make()
            ->success()
            ->title("Success")
            ->body("Invitation Sent")
            ->send();


        $this->form->fill([]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Invite::where('user_id', Filament::auth()->user()->id)
            )
            ->columns([
                TextColumn::make('email')
                    ->label("My invites")
                    ->searchable(),
                TextColumn::make('status')
                    ->badge(),
            ]);
    }
}
