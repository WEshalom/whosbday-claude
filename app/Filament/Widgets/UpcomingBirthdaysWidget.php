<?php

namespace App\Filament\Widgets;

use App\Models\Person;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class UpcomingBirthdaysWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Upcoming Birthdays (Next 30 Days)';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Person::query()
            )
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->label('Name')
                    ->weight('bold')
                    ->icon('heroicon-m-cake')
                    ->iconColor(fn (Person $record) => $record->days_until_birthday === 0 ? 'danger' : 'primary'),
                Tables\Columns\TextColumn::make('birth_date')
                    ->label('Birth Date')
                    ->date('M d, Y'),
                Tables\Columns\TextColumn::make('age')
                    ->label('Turning')
                    ->suffix(' years old')
                    ->badge()
                    ->color('success'),
                Tables\Columns\TextColumn::make('days_until_birthday')
                    ->label('Days Until')
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        $state === 0 => 'danger',
                        $state <= 3 => 'warning',
                        $state <= 7 => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state) => $state === 0 ? 'Today! 🎉' : $state . ' days'),
                Tables\Columns\TextColumn::make('notificationContacts_count')
                    ->counts('notificationContacts')
                    ->label('Contacts to Notify')
                    ->badge()
                    ->color('primary')
                    ->icon('heroicon-m-bell'),
            ])
            ->paginated([10, 25, 50])
            ->defaultPaginationPageOption(10)
            ->poll('60s')
            ->striped()
            ->modifyQueryUsing(function ($query) {
                // Get all people and filter by upcoming birthdays
                $upcomingPeople = Person::all()
                    ->filter(function ($person) {
                        $days = $person->days_until_birthday;
                        return $days >= 0 && $days <= 30;
                    })
                    ->sortBy('days_until_birthday')
                    ->pluck('id');

                return $query->whereIn('id', $upcomingPeople)
                    ->orderByRaw("FIELD(id, " . $upcomingPeople->implode(',') . ")");
            });
    }
}
