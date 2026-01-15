<?php

namespace App\Filament\Widgets;

use App\Models\Birthday;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class UpcomingBirthdaysWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Upcoming Birthdays (Next 30 Days)')
            ->query(
                Birthday::query()
                    ->whereHas('contact', function (Builder $query) {
                        $query->where('user_id', auth()->id());
                    })
                    ->upcoming(30)
                    ->with('contact')
            )
            ->columns([
                Tables\Columns\ImageColumn::make('contact.avatar')
                    ->label('Avatar')
                    ->circular()
                    ->defaultImageUrl(fn ($record) => 'https://ui-avatars.com/api/?name=' . urlencode($record->contact->full_name)),
                Tables\Columns\TextColumn::make('contact.display_name')
                    ->label('Name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('date')
                    ->label('Birthday')
                    ->date('M d')
                    ->sortable(),
                Tables\Columns\TextColumn::make('days_until')
                    ->label('Days Until')
                    ->badge()
                    ->color(fn (int $state): string => match (true) {
                        $state === 0 => 'success',
                        $state <= 7 => 'warning',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (int $state): string => match ($state) {
                        0 => 'Today!',
                        1 => 'Tomorrow',
                        default => "$state days",
                    }),
                Tables\Columns\TextColumn::make('age')
                    ->label('Turning Age')
                    ->placeholder('Unknown')
                    ->suffix(' years old'),
                Tables\Columns\TextColumn::make('gift_ideas')
                    ->label('Gift Ideas')
                    ->limit(50)
                    ->placeholder('No ideas yet')
                    ->toggleable(),
            ])
            ->paginated(false);
    }
}
