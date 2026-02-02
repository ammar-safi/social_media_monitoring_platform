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
use Filament\Resources\Resource;
use Filament\Tables;
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
                    ->label("Policy maker name"),
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
            'index' => Pages\ListPolicyRequests::route('/'),
            'create' => Pages\CreatePolicyRequest::route('/create'),
            'edit' => Pages\EditPolicyRequest::route('/{record}/edit'),
            'view' => Pages\ViewPolicyRequest::route('/{record}/view'),
        ];
    }
}
