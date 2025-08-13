<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OfficeSpaceResource\Pages;
use App\Models\OfficeSpace;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class OfficeSpaceResource extends Resource
{
    protected static ?string $model = OfficeSpace::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Office Space Tabs')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('General')
                            ->schema([
                                Section::make('Office Space')
                                    ->schema([
                                        Forms\Components\TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\FileUpload::make('thumbnail')
                                            ->required()
                                            ->image()
                                            ->maxSize(2048),

                                        Forms\Components\Select::make('city_id')
                                            ->relationship('city', 'name')
                                            ->searchable()
                                            ->preload()
                                            ->required(),

                                        Forms\Components\TextInput::make('price')
                                            ->required()
                                            ->numeric()
                                            ->prefix('IDR'),

                                        Forms\Components\Select::make('is_open')
                                            ->options([
                                                true => 'Open',
                                                false => 'Not Open',
                                            ]),

                                        Forms\Components\Select::make('is_fullbook')
                                            ->options([
                                                true => 'Not Available',
                                                false => 'Available',
                                            ])
                                            ->required(),

                                        Forms\Components\TextInput::make('duration')
                                            ->required()
                                            ->numeric()
                                            ->prefix('Days'),

                                        Forms\Components\TextInput::make('address')
                                            ->required()
                                            ->maxLength(255),

                                        Forms\Components\Textarea::make('about')
                                            ->required()
                                            ->columnSpanFull(),
                                    ])
                                    ->columns(2),
                            ]),

                        Tab::make('Photos & Benefits')
                            ->schema([
                                Section::make('Photos')
                                    ->schema([
                                        Forms\Components\Repeater::make('photos')
                                            ->relationship('photos')
                                            ->schema([
                                                Forms\Components\FileUpload::make('photo')
                                                    ->image()
                                                    ->maxSize(2048)
                                                    ->required(),
                                            ]),
                                    ]),

                                Section::make('Benefits')
                                    ->schema([
                                        Forms\Components\Repeater::make('benefits')
                                            ->relationship('benefits')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                            ]),
                                    ]),
                            ]),

                        Tab::make('Contacts')
                            ->schema([
                                Section::make('Contact Persons')
                                    ->schema([
                                        Forms\Components\Repeater::make('contacts')
                                            ->relationship('contacts')
                                            ->schema([
                                                Forms\Components\TextInput::make('name')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('position')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\TextInput::make('phone')
                                                    ->required()
                                                    ->maxLength(255),
                                                Forms\Components\FileUpload::make('picture')
                                                    ->image()
                                                    ->maxSize(2048)
                                                    ->nullable()
                                                    ->columnSpanFull(),
                                            ]),
                                    ]),
                            ]),

                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->searchable(),

                Tables\Columns\ImageColumn::make('thumbnail')
                    ->circular(),
                Tables\Columns\TextColumn::make('city.name')

                    ->sortable(),
                Tables\Columns\IconColumn::make('is_fullbook')
                    ->boolean()
                    ->trueColor('danger')
                    ->falseColor('success')
                    ->trueIcon('heroicon-s-x-circle')
                    ->falseIcon('heroicon-s-check-circle')
                    ->label('Available'),

                Tables\Columns\TextColumn::make('deleted_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
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
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
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
            'index' => Pages\ListOfficeSpaces::route('/'),
            'create' => Pages\CreateOfficeSpace::route('/create'),
            'edit' => Pages\EditOfficeSpace::route('/{record}/edit'),
        ];
    }
}
