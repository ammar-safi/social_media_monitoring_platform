<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ApproveUserResource\Pages;
use App\Filament\Resources\ApproveUserResource\RelationManagers;
use App\Models\ApproveUser;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
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
                Forms\Components\Select::make('admin_id')
                    ->relationship('admin', 'id'),
                Forms\Components\Select::make('user_id')
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
                Tables\Columns\TextColumn::make('admin.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('user.id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('expired_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\IconColumn::make('expired')
                    ->boolean(),
                Tables\Columns\TextColumn::make('status'),
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
                Tables\Actions\EditAction::make(),
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
