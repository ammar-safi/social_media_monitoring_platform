<?php

namespace App\Filament\Resources;

use Althinect\FilamentSpatieRolesPermissions\Resources\RoleResource\RelationManager\UserRelationManager;
use App\Enums\UserTypeEnum;
use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Filament\Resources\UserResource\RelationManagers\RatingsRelationManager;
use App\Models\User;
use Doctrine\DBAL\Schema\Column;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Infolists\Components\Group;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section as InfoListSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationGroup = 'Users management';
    protected static ?int $navigationSort = 2;


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("User info")
                    ->columns(2)
                    ->schema([
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone_number')
                            ->tel()
                            ->required()
                            ->maxLength(255),
                    ]),
                Section::make("Password Section")
                    ->columns(2)
                    ->description("Leave blank to keep current password.")
                    ->schema([
                        Forms\Components\TextInput::make('password')
                            ->password()
                            ->revealable()
                            ->required(fn(string $context) => $context === 'create')
                            ->confirmed()
                            ->dehydrated(fn($state) => filled($state))
                            ->maxLength(255),
                        Forms\Components\TextInput::make('password_confirmation')
                            ->label("password confirmation")
                            ->password()
                            ->revealable()
                            ->required(fn(string $context) => $context === 'create')
                            ->dehydrated(false)
                            ->maxLength(255),
                    ]),
                Forms\Components\Toggle::make('active')
                    ->required()
                    ->columnSpanFull(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone_number')
                    ->searchable(),
                Tables\Columns\TextColumn::make('type')
                    ->formatStateUsing(function ($state) {
                        return UserTypeEnum::from($state->value)->label();
                    })
                    ->badge()
                    ->color(function ($state): string {
                        return UserTypeEnum::from($state->value)->badgeColor();
                    }),
                Tables\Columns\IconColumn::make('active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // SelectFilter::make('type')
                //     ->label("User type (Role)")
                //     ->options([
                //         UserTypeEnum::USER->value => UserTypeEnum::USER->label(), 
                //         UserTypeEnum::POLICY_MAKER->value => UserTypeEnum::POLICY_MAKER->label()
                //     ]),
                // SelectFilter::make("active")
                //     ->label("active accounts")
                //     ->options([
                //         '1' => 'active',
                //         '0' => 'not active'
                //     ])

            ])
            ->actions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                    DeleteAction::make()
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }


    public static function infolist(Infolist $infolist): infolist
    {
        return $infolist
            ->schema([
                InfoListSection::make("User information")
                    ->icon("heroicon-o-user")
                    ->columns(2)
                    ->schema([
                        Group::make()
                            ->schema([
                                TextEntry::make("first_name"),
                                TextEntry::make("last_name"),
                            ]),
                        Group::make()
                            ->schema([
                                TextEntry::make("email")
                                    ->icon("heroicon-o-envelope"),
                                TextEntry::make("phone_number")
                                    ->icon("heroicon-o-phone"),
                            ])
                    ]),
                InfoListSection::make("Other information")
                    ->columns(3)
                    ->icon("heroicon-o-information-circle")
                    ->schema([
                        TextEntry::make("type")
                            ->badge()
                            ->color(function ($state): string {
                                return UserTypeEnum::from($state->value)->badgeColor();
                            }),
                        IconEntry::make('active')
                            ->label("Account active")
                            ->boolean(),
                        TextEntry::make("created_at"),

                    ])

            ]);
    }

    public static function getRelations(): array
    {
        return [
            RatingsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
            'view' => Pages\ViewUsers::route('/{record}/view'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('type', '!=', 'admin')
            // ->where("active", 1)
            ;
    }
}
