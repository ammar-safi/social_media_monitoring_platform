<?php

namespace App\Filament\Resources;

use App\Enums\PolicyRequestEnum;
use App\Enums\UserTypeEnum;
use App\Filament\Pages\CustomResource;
use App\Filament\Resources\PolicyRequestResource\Pages;
use App\Models\PolicyRequest;
use Filament\Actions\Action as ActionsAction;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action as InfoListAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Notifications\Notification;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Support\HtmlString;
use Nette\Utils\Html;

use function Laravel\Prompts\search;

class PolicyRequestResource extends CustomResource
{
    protected static ?string $model = PolicyRequest::class;

    protected static ?string $navigationGroup = 'Users management';
    protected static ?string $navigationLabel, $label = 'Policy Maker Requests';
    protected static ?int $navigationSort = 4;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('status')
                    ->required(),
                Forms\Components\DateTimePicker::make('expired_at'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('policyMaker.name')
                    ->default("( DELETED ACCOUNT )")
                    ->searchable(
                        query: function ($query, string $search) {
                            return $query->whereHas("policyMaker", function ($q) use ($search) {
                                return $q
                                    ->where("first_name", "LIKE", "%" . $search . "%")
                                    ->orWhere("last_name", "LIKE", "%" . $search . "%")
                                ;
                            });
                        }
                    )
                    ->label("name"),
                Tables\Columns\TextColumn::make('policyMaker.email')
                    ->default("( DELETED ACCOUNT )")
                    ->label("email"),
                Tables\Columns\TextColumn::make('UserWhoInvitePolicy.name')
                    ->default("( DELETED ACCOUNT )")
                    ->label("Invited by")
                    ->searchable(
                        query: function ($query, string $search) {
                            return $query->whereHas("UserWhoInvitePolicy", function ($q) use ($search) {
                                return $q
                                    ->where("first_name", "LIKE", "%" . $search . "%")
                                    ->orWhere("last_name", "LIKE", "%" . $search . "%")
                                ;
                            });
                        }
                    ),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return $state->label();
                    })
                    ->color(function ($state): string {
                        return $state->badgeColor();
                    }),

                parent::getDateFormattedColumn("expired_at"),

                parent::getDateFormattedColumn("created_at")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make("approve")
                    ->button()
                    ->requiresConfirmation()
                    ->color("primary")
                    ->action(function (PolicyRequest $policy_request, $record) {
                        if ($policy_request->approve()) {
                            Notification::make()
                                ->success()
                                ->icon("heroicon-o-check-circle")
                                ->title("Approved")
                                ->body("account approved and an email sent to " . $record->policyMaker?->first_name)
                                ->send();
                        } else {

                            Notification::make()
                                ->warning()
                                ->title("Error")
                                ->body("pleas try again")
                                ->send();
                        }
                    })
                    ->hidden(function ($record) {
                        if ($record->status != PolicyRequestEnum::PENDING) {
                            return true;
                        }
                        return false;
                    }),
                ActionGroup::make([
                    Action::make("reject")
                        ->icon("heroicon-o-x-circle")
                        ->requiresConfirmation()
                        ->modalIconColor("warning")
                        ->modalIcon("heroicon-o-no-symbol")
                        ->color("gray")
                        ->action(function (PolicyRequest $policy_request, $record) {
                            if ($policy_request->reject()) {
                                Notification::make()
                                    ->danger()
                                    ->icon("heroicon-o-x-circle")
                                    ->title("Rejected")
                                    ->body("account rejected and an email sent to " . $record->user?->first_name)
                                    ->send();
                            } else {
                                Notification::make()
                                    ->warning()
                                    ->title("Error")
                                    ->body("pleas try again")
                                    ->send();
                            }
                        })
                        ->hidden(function ($record) {
                            if ($record->status != PolicyRequestEnum::PENDING) {
                                return true;
                            }
                            return false;
                        }),
                    Action::make("delete")
                        ->requiresConfirmation()
                        ->modalIconColor("danger")
                        ->modalIcon("heroicon-o-trash")
                        ->color("danger")
                        ->icon("heroicon-o-trash")
                        ->action(function ($record) {
                            $record->delete();
                            Notification::make()
                                ->success()
                                ->title("the request deleted successfully")
                                ->send();
                        })
                ])

            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make("Policy maker information")
                    ->icon("heroicon-o-information-circle")
                    ->columns(2)
                    ->schema([
                        TextEntry::make('policyMaker.name')
                            ->default("( DELETED ACCOUNT )")
                            ->label("Name")
                            ->icon("heroicon-o-identification"),
                        TextEntry::make('policyMaker.email')
                            ->default("( DELETED ACCOUNT )")
                            ->icon("heroicon-o-envelope")
                            // ->limit(20)
                            // ->tooltip(fn($record) => $record?->policyMaker?->email)
                            ->label("Email"),
                        TextEntry::make('policyMaker.phone_number')
                            ->default("( DELETED ACCOUNT )")
                            ->icon("heroicon-o-phone")
                            ->label("Phone number"),
                        parent::getStatusEntry("status"),

                    ]),

                Section::make("Date section")
                    ->description()
                    ->icon("heroicon-o-calendar")
                    ->columns(3)
                    ->schema([
                        parent::getDateFormattedEntry("expired_at"),
                        parent::getDateFormattedEntry("created_at")
                            ->label("Request date"),
                        parent::getDateFormattedEntry("invite.created_at")
                            ->label(new HtmlString("Invite date <br><span style='color:gray;font-size:0.85em;';>
                            the date that policy maker reserve the invitation at
                            </span>")),
                    ]),

                Section::make("Invited by")
                    ->columns(2)
                    ->icon("heroicon-o-user-plus")
                    ->schema([
                        TextEntry::make('UserWhoInvitePolicy.name')
                            ->default("( DELETED ACCOUNT )")
                            ->icon("heroicon-o-identification")
                            ->label("User name"),
                        TextEntry::make('UserWhoInvitePolicy.email')
                            ->default("( DELETED ACCOUNT )")
                            ->icon("heroicon-o-envelope")
                            ->label("Email"),
                        TextEntry::make('UserWhoInvitePolicy.phone_number')
                            ->default("( DELETED ACCOUNT )")
                            ->icon("heroicon-o-phone")
                            ->label("Phone number"),
                        parent::getStatusEntry("UserWhoInvitePolicy.type")
                            ->label("type")

                    ])
                    ->headerActions([
                        InfoListAction::make("view account")
                            ->color("gray")
                            ->url(function ($record) {
                                if (
                                    $record->UserWhoInvitePolicy &&
                                    $record->UserWhoInvitePolicy->id != Filament::auth()->user()?->id
                                ) {
                                    return UserResource::getUrl("view", ["record" => $record->UserWhoInvitePolicy?->id]);
                                }
                            })

                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPolicyRequests::route('/'),
            'create' => Pages\CreatePolicyRequest::route('/create'),
            'edit' => Pages\EditPolicyRequest::route('/{record}/edit'),
            'view' => Pages\ViewPolicyRequest::route('/{record}/view'),
        ];
    }
}
