<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Person extends Model
{
    use HasFactory;

    protected $table = 'people';

    protected $fillable = [
        'first_name',
        'last_name',
        'birth_date',
        'notes',
    ];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function notificationContacts(): HasMany
    {
        return $this->hasMany(NotificationContact::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getAgeAttribute(): int
    {
        return Carbon::parse($this->birth_date)->age;
    }

    public function getNextBirthdayAttribute(): Carbon
    {
        $birthday = Carbon::parse($this->birth_date);
        $nextBirthday = Carbon::create(
            Carbon::now()->year,
            $birthday->month,
            $birthday->day
        );

        if ($nextBirthday->isPast()) {
            $nextBirthday->addYear();
        }

        return $nextBirthday;
    }

    public function getDaysUntilBirthdayAttribute(): int
    {
        return Carbon::now()->startOfDay()->diffInDays($this->next_birthday, false);
    }

    public function isBirthdayToday(): bool
    {
        $today = Carbon::now();
        $birthday = Carbon::parse($this->birth_date);

        return $today->month === $birthday->month && $today->day === $birthday->day;
    }

    public function scopeWithBirthdayToday($query)
    {
        $today = Carbon::now();
        return $query->whereMonth('birth_date', $today->month)
                     ->whereDay('birth_date', $today->day);
    }

    public function scopeUpcoming($query, int $days = 30)
    {
        return $query->get()->filter(function ($person) use ($days) {
            $daysUntil = $person->days_until_birthday;
            return $daysUntil >= 0 && $daysUntil <= $days;
        })->sortBy('days_until_birthday');
    }
}
