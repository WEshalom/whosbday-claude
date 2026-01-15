<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ContactResource\Pages;
use App\Models\Contact;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class ContactResource extends Resource
{
    protected static ?string $model = Contact::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\FileUpload::make('avatar')
                            ->image()
                            ->avatar()
                            ->directory('avatars')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('first_name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('last_name')
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nickname')
                            ->maxLength(255),
                        Forms\Components\Select::make('relationship')
                            ->options([
                                'family' => 'Family',
                                'friend' => 'Friend',
                                'colleague' => 'Colleague',
                                'acquaintance' => 'Acquaintance',
                                'other' => 'Other',
                            ])
                            ->native(false),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Contact Details')
                    ->schema([
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('Birthday Information')
                    ->relationship('birthday')
                    ->schema([
                        Forms\Components\DatePicker::make('date')
                            ->required()
                            ->native(false)
                            ->displayFormat('M d')
                            ->label('Birthday'),
                        Forms\Components\TextInput::make('year')
                            ->numeric()
                            ->minValue(1900)
                            ->maxValue(now()->year)
                            ->label('Birth Year (Optional)'),
                        Forms\Components\Toggle::make('send_notification')
                            ->default(true)
                            ->label('Send Reminder'),
                        Forms\Components\TextInput::make('notification_days_before')
                            ->numeric()
                            ->default(7)
                            ->minValue(1)
                            ->maxValue(365)
                            ->label('Days Before to Notify'),
                        Forms\Components\Textarea::make('gift_ideas')
                            ->rows(3)
                            ->columnSpanFull()
                            ->label('Gift Ideas'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('avatar')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->full_name)),
                Tables\Columns\TextColumn::make('first_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('last_name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('relationship')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'family' => 'success',
                        'friend' => 'info',
                        'colleague' => 'warning',
                        default => 'gray',
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('birthday.date')
                    ->label('Birthday')
                    ->date('M d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('birthday.age')
                    ->label('Age')
                    ->suffix(' years old')
                    ->placeholder('Unknown'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('relationship')
                    ->options([
                        'family' => 'Family',
                        'friend' => 'Friend',
                        'colleague' => 'Colleague',
                        'acquaintance' => 'Acquaintance',
                        'other' => 'Other',
                    ]),
                Tables\Filters\Filter::make('has_birthday')
                    ->query(fn (Builder $query): Builder => $query->whereHas('birthday'))
                    ->label('Has Birthday'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('first_name');
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
            'index' => Pages\ListContacts::route('/'),
            'create' => Pages\CreateContact::route('/create'),
            'edit' => Pages\EditContact::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('user_id', auth()->id());
    }
}
