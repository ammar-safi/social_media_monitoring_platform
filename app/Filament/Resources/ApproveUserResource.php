<?php

namespace App\Filament\Resources;

use App\Enums\ApproveUserStatusEnum;
use App\Filament\Resources\ApproveUserResource\Pages;
use App\Filament\Resources\ApproveUserResource\RelationManagers;
use App\Models\ApproveUser;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ApproveUserResource extends Resource
{
    protected static ?string $model = ApproveUser::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationLabel = 'Government official Requests';
    protected static ?int $navigationSort = 3;
    protected static ?string $navigationGroup = "Users management";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('admin.first_name')
                    ->relationship('admin', 'id'),
                Forms\Components\Select::make('user.first_name')
                    ->relationship('user', 'id')
                    ->required(),
                Forms\Components\DateTimePicker::make('expired_at')
                    ->required(),
                Forms\Components\Toggle::make('expired')
                    ->required(),
                Forms\Components\TextInput::make('status')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.first_name')
                    ->searchable()
                    ->label("first name")
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.last_name')
                    ->searchable()
                    ->label("last name")
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired_at')
                    ->formatStateUsing(function ($state) {
                        $date = Carbon::parse($state);
                        $readable_date = $date->diffForHumans();
                        return $readable_date;
                    })
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired')
                    ->formatStateUsing(function ($state) {
                        if ($state) {
                            return "request is expired";
                        }

                        return "request is active";
                    })
                    ->badge()
                    ->color(function ($state) {
                        if ($state) {
                            return "danger";
                        }

                        return "success";
                    }),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return ApproveUserStatusEnum::from($state)->label();
                    })
                    ->color(function ($state): string {
                        return ApproveUserStatusEnum::from($state)->badgeColor();
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('deleted_at')
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
                    ->color("success")
                    ->action(function (ApproveUser $approve) {
                        if ($approve->approve()) {
                            Notification::make()
                                ->success()
                                ->icon("heroicon-o-check")
                                ->title("Approved")
                                ->body("account approved and an email sent to the government official")
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
                        if ($record->status != ApproveUserStatusEnum::PENDING->value) {
                            return true;
                        }
                        if ($record->expired) {
                            return true;
                        }
                        return false;
                    }),
                ActionGroup::make([
                    Action::make("reject")
                        ->icon("heroicon-o-x-circle")
                        ->color("warning")
                        ->action(function (ApproveUser $approve) {
                            if ($approve->reject()) {
                                Notification::make()
                                    ->danger()
                                    ->icon("heroicon-o-x-circle")
                                    ->title("Rejected")
                                    ->body("account rejected and an email sent to the government official")
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
                            if ($record->status != ApproveUserStatusEnum::PENDING->value) {
                                return true;
                            }
                            if ($record->expired) {
                                return true;
                            }
                            return false;
                        }),
                    Action::make("delete")
                        ->requiresConfirmation()
                        ->color("danger")
                        ->icon("heroicon-o-trash")
                        ->action(function ($record) {
                            $record->user->delete();
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
            'index' => Pages\ListApproveUsers::route('/'),
            'create' => Pages\CreateApproveUser::route('/create'),
            'edit' => Pages\EditApproveUser::route('/{record}/edit'),
        ];
    }
}
