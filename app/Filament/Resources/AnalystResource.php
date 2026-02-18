<?php

namespace App\Filament\Resources;

use App\Enums\AnalystSentimentEnum;
use App\Enums\AnalystStanceEnum;
use App\Filament\Pages\CustomResource;
use App\Filament\Resources\AnalystResource\Pages;
use App\Filament\Resources\AnalystResource\RelationManagers;
use App\Models\Analyst;
use App\Models\GovOrg;
use App\Models\Post;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use League\CommonMark\Normalizer\TextNormalizer;

class AnalystResource extends CustomResource
{
    protected static ?string $model = Analyst::class;

    // protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $navigationGroup = "Analyst";

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make("details")
                    ->schema([
                        TextArea::make("post_id")
                            ->label("Post content")
                            ->formatStateUsing(function ($state) {
                                return Post::find($state)?->content;
                            })
                            ->columnSpanFull()
                            ->dehydrated()
                            ->disabled(),
                        TextInput::make("gov_id")
                            ->label("Post target")
                            ->formatStateUsing(function ($state) {
                                return GovOrg::find($state)?->name;
                            })
                            ->columnSpanFull()
                            ->dehydrated()
                            ->disabled(),

                    ]),

                Select::make("sentiment")
                    ->options(AnalystSentimentEnum::asSelectArray())
                    ->default(fn($state) => $state)
                    ->prefixIcon(function ($state) {
                        return $state ? AnalystSentimentEnum::from($state)->icon() : null;
                    })
                    ->prefixIconColor(function ($state) {
                        return $state ? AnalystSentimentEnum::from($state)->badgeColor() : null;
                    })
                    ->required()
                    ->markAsRequired(False)
                    ->live(),
                Select::make("stance")
                    ->options(AnalystStanceEnum::asSelectArray())
                    ->default(fn($state) => $state)
                    ->prefixIcon(function ($state) {
                        return $state ? AnalystStanceEnum::from($state)->icon() : null;
                    })
                    ->prefixIconColor(function ($state) {
                        return $state ? AnalystStanceEnum::from($state)->badgeColor() : null;
                    })
                    ->required()
                    ->markAsRequired(False)
                    ->live(),

            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([

                Tables\Columns\TextColumn::make('post.content')
                    ->tooltip(fn($record) => $record?->post?->content)
                    ->limit(20),
                Tables\Columns\TextColumn::make('post.content'),
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
