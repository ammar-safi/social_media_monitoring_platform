<?php

namespace App\Filament\Resources;

use App\Enums\PolicyRequestEnum;
use App\Filament\Pages\CustomResource;
use App\Filament\Resources\PolicyRequestResource\Pages;
use App\Filament\Resources\PolicyRequestResource\RelationManagers;
use App\Models\PolicyRequest;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

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
                    ->label("name"),
                Tables\Columns\TextColumn::make('policyMaker.email')
                    ->default("( DELETED ACCOUNT )")
                    ->label("email"),
                Tables\Columns\TextColumn::make('govWhoInvitePolicy.name')
                    ->default("( DELETED ACCOUNT )")
                    ->label("Invited by"),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return $state->label();
                    })
                    ->color(function ($state): string {
                        return $state->badgeColor();
                    }),

                parent::getDateFormattedColumn("expired_at"),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
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

    public static function getRelations(): array
    {
        return [
            //
        ];
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
