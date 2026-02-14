<?php

namespace App\Filament\Resources;

use App\Filament\Pages\CustomResource;
use App\Filament\Resources\HashtagResource\Pages;
use App\Filament\Resources\HashtagResource\RelationManagers;
use App\Models\Hashtag;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class HashtagResource extends CustomResource
{
    protected static ?string $model = Hashtag::class;

    // protected static ?string $navigationIcon = 'heroicon-o-hashtag';
    protected static ?string $navigationGroup = 'Posts';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->columnSpanFull()
                    ->maxLength(255),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(function (Builder $query) {
                $userId = Filament::auth()->user()->id;
                return $query
                    ->orderByRaw("CASE WHEN user_id = ? THEN 0 ELSE 1 END", [$userId])
                    ->orderByDesc("created_at");
            })
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->icon("heroicon-o-hashtag")
                    ->searchable(),
                Tables\Columns\TextColumn::make('user.name')
                    ->label("created by"),
                parent::getStatusColumn("user.type")
                    ->label("user type"),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                ActionGroup::make([

                    Tables\Actions\EditAction::make(),
                    DeleteAction::make()
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
            'index' => Pages\ListHashtags::route('/'),
            // 'create' => Pages\CreateHashtag::route('/create'),
            // 'edit' => Pages\EditHashtag::route('/{record}/edit'),
        ];
    }
}
