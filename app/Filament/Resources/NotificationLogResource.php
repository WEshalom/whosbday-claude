<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NotificationLogResource\Pages;
use App\Models\NotificationLog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class NotificationLogResource extends Resource
{
    protected static ?string $model = NotificationLog::class;

    protected static ?string $navigationIcon = 'heroicon-o-bell-alert';

    protected static ?string $navigationLabel = 'Notification Logs';

    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        // Read-only resource
        return $form->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('person.full_name')
                    ->label('Birthday Person')
                    ->searchable(['first_name', 'last_name'])
                    ->sortable(),
                Tables\Columns\TextColumn::make('notificationContact.contact_name')
                    ->label('Notified Contact')
                    ->placeholder('N/A')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notificationContact.phone')
                    ->label('Phone')
                    ->placeholder('N/A'),
                Tables\Columns\TextColumn::make('notificationContact.email')
                    ->label('Email')
                    ->placeholder('N/A'),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors([
                        'success' => 'success',
                        'danger' => 'failed',
                    ]),
                Tables\Columns\TextColumn::make('sent_at')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('error_message')
                    ->label('Error')
                    ->limit(50)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'success' => 'Success',
                        'failed' => 'Failed',
                    ]),
                Tables\Filters\Filter::make('sent_today')
                    ->label('Sent Today')
                    ->query(fn ($query) => $query->whereDate('sent_at', today())),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->defaultSort('sent_at', 'desc')
            ->poll('30s');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNotificationLogs::route('/'),
            'view' => Pages\ViewNotificationLog::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }
}
