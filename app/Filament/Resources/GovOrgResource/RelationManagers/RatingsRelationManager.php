<?php

namespace App\Filament\Resources\GovOrgResource\RelationManagers;

use App\Enums\UserTypeEnum;
use App\Models\GovOrg;
use App\Models\Rating;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class RatingsRelationManager extends RelationManager
{
    protected static string $relationship = 'Ratings';

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
                Forms\Components\Textarea::make('comment')
                    ->maxLength(255),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('Rating')
            ->modifyQueryUsing(function (Builder $query) {
                $userId = Filament::auth()->user()->id;
                return $query
                    ->orderByRaw("CASE WHEN user_id = ? THEN 0 ELSE 1 END", [$userId])
                    ->orderByDesc("created_at");
            })
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->default("( DELETED ACCOUNT )")
                    ->icon(function ($record) {
                        if ($record->user->type == UserTypeEnum::USER) {
                            return 'heroicon-o-user';
                        }
                        if ($record->user->type == UserTypeEnum::POLICY_MAKER) {
                            return 'heroicon-m-user';
                        }
                    })
                    ->label("user name"),
                    Tables\Columns\TextColumn::make('user.type')
                    ->label("user type")
                    ->formatStateUsing(function ($state) {
                        return UserTypeEnum::from($state->value)->label();
                    })
                    ->badge()
                    ->color(function ($state): string {
                        return UserTypeEnum::from($state->value)->badgeColor();
                    }),
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
                    ->createAnother(false)
                    ->hidden(function () {
                        if (Filament::auth()->user()->type == UserTypeEnum::ADMIN) {
                            return true;
                        }
                        if ($this->ownerRecord->my_rating) {
                            return true;
                        }
                        return false;
                    })
                    ->mutateFormDataUsing(function (array $data): array {
                        $data["user_id"] = Filament::auth()->user()->id;
                        return $data;
                    }),
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
