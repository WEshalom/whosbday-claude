<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Birthday extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'contact_id',
        'date',
        'year',
        'send_notification',
        'notification_days_before',
        'gift_ideas',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'date' => 'date',
            'send_notification' => 'boolean',
            'notification_days_before' => 'integer',
        ];
    }

    /**
     * Get the contact that owns the birthday.
     */
    public function contact(): BelongsTo
    {
        return $this->belongsTo(Contact::class);
    }

    /**
     * Get the age attribute.
     */
    public function getAgeAttribute(): ?int
    {
        if (!$this->year) {
            return null;
        }

        return Carbon::now()->year - $this->year;
    }

    /**
     * Get the next birthday date.
     */
    public function getNextBirthdayAttribute(): Carbon
    {
        $birthday = Carbon::parse($this->date);
        $currentYear = Carbon::now()->year;

        $nextBirthday = Carbon::create(
            $currentYear,
            $birthday->month,
            $birthday->day
        );

        if ($nextBirthday->isPast()) {
            $nextBirthday->addYear();
        }

        return $nextBirthday;
    }

    /**
     * Get the days until birthday.
     */
    public function getDaysUntilAttribute(): int
    {
        return Carbon::now()->diffInDays($this->next_birthday, false);
    }

    /**
     * Scope a query to only include upcoming birthdays.
     */
    public function scopeUpcoming($query, int $days = 30)
    {
        $startDate = Carbon::now();
        $endDate = Carbon::now()->addDays($days);

        return $query->whereRaw(
            "DATE_FORMAT(date, CONCAT(YEAR(NOW()), '-%m-%d')) BETWEEN ? AND ?",
            [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]
        )->orWhereRaw(
            "DATE_FORMAT(date, CONCAT(YEAR(NOW()) + 1, '-%m-%d')) BETWEEN ? AND ?",
            [$startDate->format('Y-m-d'), $endDate->format('Y-m-d')]
        );
    }
}
