<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PersonResource\Pages;
use App\Filament\Resources\PersonResource\RelationManagers;
use App\Models\Person;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class PersonResource extends Resource
{
    protected static ?string $model = Person::class;

    protected static ?string $navigationIcon = 'heroicon-o-user-group';

    protected static ?string $navigationLabel = 'People';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Personal Information')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\TextInput::make('first_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('First Name'),
                                Forms\Components\TextInput::make('last_name')
                                    ->required()
                                    ->maxLength(255)
                                    ->label('Last Name'),
                            ]),
                        Forms\Components\DatePicker::make('birth_date')
                            ->required()
                            ->label('Birth Date')
                            ->displayFormat('M d, Y')
                            ->native(false)
                            ->maxDate(now()),
                        Forms\Components\Textarea::make('notes')
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(['first_name', 'last_name']),
                Tables\Columns\TextColumn::make('birth_date')
                    ->date('M d, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('age')
                    ->label('Age')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('days_until_birthday')
                    ->label('Days Until Birthday')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === 0 => 'danger',
                        $state <= 7 => 'warning',
                        default => 'info',
                    })
                    ->formatStateUsing(fn ($state) => $state === 0 ? 'Today!' : $state . ' days'),
                Tables\Columns\TextColumn::make('notificationContacts_count')
                    ->counts('notificationContacts')
                    ->label('Contacts')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('birthday_soon')
                    ->label('Birthday This Week')
                    ->query(fn ($query) => $query->whereIn('id',
                        Person::all()->filter(fn ($p) => $p->days_until_birthday <= 7)->pluck('id')
                    )),
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
            ])
            ->defaultSort('first_name');
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Personal Information')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('full_name')
                                    ->label('Full Name'),
                                Infolists\Components\TextEntry::make('age')
                                    ->label('Age')
                                    ->suffix(' years old'),
                            ]),
                        Infolists\Components\TextEntry::make('birth_date')
                            ->date('F j, Y'),
                        Infolists\Components\TextEntry::make('days_until_birthday')
                            ->label('Next Birthday')
                            ->formatStateUsing(fn ($state) => $state === 0 ? 'Today! 🎉' : "In {$state} days"),
                        Infolists\Components\TextEntry::make('notes')
                            ->columnSpanFull(),
                    ]),
                Infolists\Components\Section::make('Notification Statistics')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('notificationContacts_count')
                                    ->counts('notificationContacts')
                                    ->label('Total Contacts'),
                                Infolists\Components\TextEntry::make('notificationLogs_count')
                                    ->counts('notificationLogs')
                                    ->label('Notifications Sent'),
                            ]),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\NotificationContactsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPeople::route('/'),
            'create' => Pages\CreatePerson::route('/create'),
            'view' => Pages\ViewPerson::route('/{record}'),
            'edit' => Pages\EditPerson::route('/{record}/edit'),
        ];
    }
}
