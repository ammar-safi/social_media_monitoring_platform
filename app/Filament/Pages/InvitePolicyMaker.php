<?php

namespace App\Filament\Pages;

use App\Enums\InviteStatusEnum;
use App\Enums\UserTypeEnum;
use App\Events\EmailEvent;
use App\Filament\Resources\InviteResource;
use App\Filament\Resources\InviteResource\Pages\CreateInvite;
use App\Filament\Resources\InviteResource\Pages\EditInvite;
use App\Filament\Resources\InviteResource\Pages\ListInvites;
use App\Models\Invite;
use Carbon\Carbon;
use Exception;
use Filament\Facades\Filament;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Nette\Utils\Random;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
    public static function shouldRegisterNavigation(): bool
    {
        if (Filament::auth()->user()->type == UserTypeEnum::USER) {
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
                ->button()
                ->submit("save")
        ];
    }

    public function save(): void
    {
        DB::beginTransaction();
        try {
            $user = Filament::auth()->user();
            $data = $this->form->getState();
            $token = Str::random(config("app.otp_length", '5'));
            $invite = $user->PolicyMakersThatUserInvites()->create([
                "email" => $data["email"],
                "status" => InviteStatusEnum::PENDING->value,
                "token" => $token,
            ]);

            $message =  "You have been invited to Sign up in our site by " . $user->first_name . ", Use this secret code when you sign up : " . $token;
            event(new EmailEvent($user, $message, "Invitation", $data["email"]));

            Notification::make()
                ->success()
                ->title("Success")
                ->body("Invitation Sent")
                ->send();


            $this->form->fill([]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
        }
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
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return InviteStatusEnum::from($state)->label();
                    })
                    ->color(function ($state): string {
                        return InviteStatusEnum::from($state)->badgeColor();
                    }),
                TextColumn::make('expired_at')
                    ->formatStateUsing(function ($state) {
                        $date = Carbon::parse($state);
                        $readable_date = $date->diffForHumans();
                        $state = Carbon::parse($state)->format("d/M/Y");
                        return $state . " (" .  $readable_date . ")";
                    })
            ])
            ->actions([
                ActionGroup::make([
                    DeleteAction::make("delete"),
                    Action::make("edit")
                        ->color("info")
                        ->icon("heroicon-m-pencil-square")
                        ->url(function ($record) {
                            return InviteResource::getUrl("edit", ['record' => $record->id]);
                        })
                ])

            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
