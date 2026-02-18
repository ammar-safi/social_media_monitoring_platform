<?php

namespace App\Filament\Resources;

use App\Filament\Pages\CustomResource;
use App\Filament\Resources\AnalystResource\Pages;
use App\Filament\Resources\AnalystResource\RelationManagers;
use App\Models\Analyst;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AnalystResource extends CustomResource
{
    protected static ?string $model = Analyst::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = "Analyst";


    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('post.content')
                    ->tooltip(fn($record) => $record?->post?->content)
                    ->limit(20),
                Tables\Columns\TextColumn::make('gov.name')
                    ->label("Government Organization"),
                parent::getStatusColumn("sentiment")
                    ->icon(function ($state) {
                        return $state->icon();
                    }),
                parent::getStatusColumn("stance")
                    ->icon(function ($state) {
                        return $state->icon();
                    }),
                parent::getDateFormattedColumn("created_at")
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    // Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
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
            'index' => Pages\ListAnalysts::route('/'),
            // 'create' => Pages\CreateAnalyst::route('/create'),
            // 'edit' => Pages\EditAnalyst::route('/{record}/edit'),
        ];
    }
}
