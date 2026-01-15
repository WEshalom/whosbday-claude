<?php

namespace App\Filament\Resources\PersonResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationContactsRelationManager extends RelationManager
{
    protected static string $relationship = 'notificationContacts';

    protected static ?string $title = 'Notification Contacts';

    protected static ?string $recordTitleAttribute = 'contact_name';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('contact_name')
                            ->label('Contact Name')
                            ->placeholder('e.g., John Doe')
                            ->maxLength(255),
                        Forms\Components\Select::make('method')
                            ->options([
                                'sms' => 'SMS',
                                'email' => 'Email',
                                'both' => 'Both',
                            ])
                            ->default('sms')
                            ->required(),
                    ]),
                Forms\Components\Grid::make(2)
                    ->schema([
                        Forms\Components\TextInput::make('phone')
                            ->tel()
                            ->placeholder('+1 (555) 123-4567')
                            ->label('Phone Number')
                            ->required(fn (Forms\Get $get) => in_array($get('method'), ['sms', 'both'])),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->placeholder('person@example.com')
                            ->label('Email Address')
                            ->required(fn (Forms\Get $get) => in_array($get('method'), ['email', 'both'])),
                    ]),
                Forms\Components\Toggle::make('enabled')
                    ->label('Enabled')
                    ->default(true)
                    ->helperText('Disable to temporarily stop notifications to this contact'),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('contact_name')
            ->columns([
                Tables\Columns\TextColumn::make('contact_name')
                    ->label('Name')
                    ->placeholder('N/A')
                    ->searchable(),
                Tables\Columns\TextColumn::make('phone')
                    ->searchable()
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('email')
                    ->searchable()
                    ->placeholder('N/A'),
                Tables\Columns\BadgeColumn::make('method')
                    ->colors([
                        'primary' => 'sms',
                        'success' => 'email',
                        'warning' => 'both',
                    ]),
                Tables\Columns\IconColumn::make('enabled')
                    ->boolean()
                    ->trueColor('success')
                    ->falseColor('danger'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('method')
                    ->options([
                        'sms' => 'SMS',
                        'email' => 'Email',
                        'both' => 'Both',
                    ]),
                Tables\Filters\TernaryFilter::make('enabled')
                    ->label('Status')
                    ->placeholder('All contacts')
                    ->trueLabel('Enabled only')
                    ->falseLabel('Disabled only'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
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
            ->emptyStateHeading('No notification contacts yet')
            ->emptyStateDescription('Add contacts who should be notified on this person\'s birthday.')
            ->emptyStateIcon('heroicon-o-phone');
    }
}
