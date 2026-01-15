<?php

namespace App\Filament\Widgets;

use App\Models\Birthday;
use App\Models\Contact;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Database\Eloquent\Builder;

class BirthdayStatsWidget extends BaseWidget
{
    protected static ?int $sort = 0;

    protected function getStats(): array
    {
        $totalContacts = Contact::where('user_id', auth()->id())->count();

        $contactsWithBirthdays = Contact::where('user_id', auth()->id())
            ->whereHas('birthday')
            ->count();

        $upcomingWeek = Birthday::whereHas('contact', function (Builder $query) {
            $query->where('user_id', auth()->id());
        })->upcoming(7)->count();

        $upcomingMonth = Birthday::whereHas('contact', function (Builder $query) {
            $query->where('user_id', auth()->id());
        })->upcoming(30)->count();

        return [
            Stat::make('Total Contacts', $totalContacts)
                ->description('In your contact list')
                ->descriptionIcon('heroicon-o-user-group')
                ->color('primary'),

            Stat::make('Contacts with Birthdays', $contactsWithBirthdays)
                ->description('Have birthday information')
                ->descriptionIcon('heroicon-o-cake')
                ->color('success'),

            Stat::make('This Week', $upcomingWeek)
                ->description('Birthdays in next 7 days')
                ->descriptionIcon('heroicon-o-calendar')
                ->color('warning'),

            Stat::make('This Month', $upcomingMonth)
                ->description('Birthdays in next 30 days')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('info'),
        ];
    }
}
