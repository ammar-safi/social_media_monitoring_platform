<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\GovOrg;
use App\Models\Rating;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use PhpParser\Node\Stmt\Label;

class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'ratings';

    public function isReadOnly(): bool
    {
        return false;
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('Rating')
                    ->required()
                    ->options(['1', '2', '3', '4', '5']),
                Forms\Components\Select::make('gov_org_id')
                    ->label("organization")
                    ->searchable()
                    ->preload()
                    ->options(function () {
                        return GovOrg::whereDoesntHave("ratings" , function ($q) {
                            $q->where("user_id" , $this->ownerRecord->id);
                        })->pluck("name" , "id")->toArray();
                    })
                    ->required(),
                Forms\Components\Textarea::make('comment')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('rating')
            ->columns([
                Tables\Columns\TextColumn::make('govOrg.name')
                    ->label("organization"),
                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->icon('heroicon-o-star')
                    ->color(function (string $state) {
                        return Rating::color($state);
                    }),
                Tables\Columns\TextColumn::make('comment'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->createAnother(false),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
