<?php

namespace App\Filament\Resources;

use App\Enums\InviteStatusEnum;
use App\Enums\UserTypeEnum;
use App\Filament\Pages\CustomResource;
use App\Filament\Resources\InviteResource\Pages;
use App\Filament\Resources\InviteResource\RelationManagers;
use App\Models\Invite;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions\Action as InfoListAction;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Pages\Page;
use Filament\Resources\Resource;
use Filament\Support\Enums\IconPosition;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class InviteResource extends CustomResource
{
    protected static ?string $model = Invite::class;
    // protected static ?string $navigationIcon = 'heroicon-o-user-plus';
    protected static ?string $navigationGroup = 'Users management';
    protected static ?string $navigationLabel, $label = 'Policy maker invites';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('status')
                    ->label("request status")
                    ->options([
                        "expired" => InviteStatusEnum::EXPIRED->label(),
                        "pending" => InviteStatusEnum::PENDING->label(),
                        "used" => InviteStatusEnum::USED->label(),
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->default("( DELETED ACCOUNT )")
                    ->label("invited by")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('email')
                    ->label("policy maker email")
                    ->searchable(),
                    //TODO
                Tables\Columns\TextColumn::make('token')
                    ->label("Secret code")
                    ->copyable()
                    ->icon("heroicon-o-document-duplicate")
                    ->copyMessage("copied")
                    ->copyMessageDuration(15000)
                    ->iconPosition(IconPosition::After)

                    ,
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(function ($state) {
                        return InviteStatusEnum::from($state)->label();
                    })
                    ->color(function ($state): string {
                        return InviteStatusEnum::from($state)->badgeColor();
                    }),
                parent::getDateFormattedColumn("expired_at"),
                Tables\Columns\IconColumn::make('expired')
                    ->boolean(),
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
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make(),

                ]),
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
                Section::make("Request information")
                    ->icon("heroicon-o-information-circle")
                    ->columns(3)
                    ->schema([
                        TextEntry::make("email")
                            ->icon('heroicon-o-envelope'),
                        TextEntry::make('status')
                            ->badge()
                            ->formatStateUsing(function ($state) {
                                return InviteStatusEnum::from($state)->label();
                            })
                            ->color(function ($state): string {
                                return InviteStatusEnum::from($state)->badgeColor();
                            }),
                        TextEntry::make('expired_at')
                            ->icon('heroicon-o-calendar')
                            ->formatStateUsing(function ($state) {
                                $date = Carbon::parse($state);
                                $readable_date = $date->diffForHumans();
                                $state = Carbon::parse($state)->format("d/M/Y");
                                return $state . " (" .  $readable_date . ")";
                            }),

                    ]),
                Section::make("Requested by")
                    ->icon("heroicon-m-user")
                    ->columns(3)
                    ->schema([
                        TextEntry::make('user.name')
                            ->default("( DELETED ACCOUNT )")
                            ->label("Invited by"),
                        TextEntry::make('user.email')
                            ->default("( DELETED ACCOUNT )")
                            ->label("Email"),
                        parent::getStatusEntry("user.type")
                            ->label("Type"),

                    ])
                    ->headerActions([
                        InfoListAction::make("view account")
                            ->color("gray")
                            ->url(function ($record) {
                                if ($record->user) {
                                    return UserResource::getUrl("view", ["record" => $record->user?->id]);
                                }
                            })
                    ])

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
            'index' => Pages\ListInvites::route('/'),
            'create' => Pages\CreateInvite::route('/create'),
            'edit' => Pages\EditInvite::route('/{record}/edit'),
            'view' => Pages\ViewInvite::route("{record}/view")
        ];
    }
}
