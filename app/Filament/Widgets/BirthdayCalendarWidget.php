<?php

namespace App\Filament\Widgets;

use App\Models\Birthday;
use Filament\Widgets\Widget;
use Illuminate\Database\Eloquent\Builder;

class BirthdayCalendarWidget extends Widget
{
    protected static string $view = 'filament.widgets.birthday-calendar-widget';

    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function getBirthdays(): array
    {
        $birthdays = Birthday::whereHas('contact', function (Builder $query) {
            $query->where('user_id', auth()->id());
        })
        ->with('contact')
        ->get();

        return $birthdays->map(function ($birthday) {
            return [
                'id' => $birthday->id,
                'title' => $birthday->contact->display_name,
                'start' => $birthday->next_birthday->format('Y-m-d'),
                'backgroundColor' => '#ef4444',
                'borderColor' => '#dc2626',
                'extendedProps' => [
                    'age' => $birthday->age,
                    'contact_id' => $birthday->contact_id,
                ],
            ];
        })->toArray();
    }
}
