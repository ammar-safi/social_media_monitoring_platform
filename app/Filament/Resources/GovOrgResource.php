<?php

namespace App\Filament\Resources;

use App\Enums\UserTypeEnum;
use App\Filament\Resources\GovOrgResource\Pages;
use App\Filament\Resources\GovOrgResource\RelationManagers;
use App\Filament\Resources\GovOrgResource\RelationManagers\RatingsRelationManager;
use App\Models\GovOrg;
use App\Models\Rating;
use Filament\Actions\DeleteAction as ActionsDeleteAction;
use Filament\Facades\Filament;
use Filament\Infolists\Components\Section as InfoListSection;
use Filament\Forms;
use Filament\Forms\Components\Group;
use Filament\Forms\Form;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Group as ComponentsGroup;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ViewAction;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class GovOrgResource extends Resource
{
    protected static ?string $model = GovOrg::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-library';
    protected static ?string $navigationLabel = 'Government Organizations';
    protected static ?string $navigationGroup = 'management';
    protected static ?int $navigationSort = 2;



    public static function form(Form $form): Form
    {
        return $form
            ->schema(GovOrg::getForm());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('rating')
                    ->badge()
                    ->icon('heroicon-o-star')
                    ->color(function (string $state) {
                        return Rating::color($state);
                    })
                    ->formatStateUsing(function ($record, $state) {
                        if (! GovOrg::IsThereRating($record->id)) {
                            return "No rating";
                        }
                        return $state;
                    }),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
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
                InfoListSection::make("Organization information")
                    ->icon("heroicon-o-building-library")
                    ->columns(3)
                    ->schema([
                        TextEntry::make("name")
                            ->label("Organization name"),
                        TextEntry::make("email")
                            ->icon("heroicon-o-envelope"),
                    ]),
            ]);
    }


    public static function getRelations(): array
    {
        return [
            RatingsRelationManager::class
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListGovOrgs::route('/'),
            'create' => Pages\CreateGovOrg::route('/create'),
            'edit' => Pages\EditGovOrg::route('/{record}/edit'),
            'view' => Pages\ViewGovOrg::route('/{record}/view'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
