<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NotificationContact extends Model
{
    use HasFactory;

    protected $fillable = [
        'person_id',
        'contact_name',
        'phone',
        'email',
        'method',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function scopeEnabled($query)
    {
        return $query->where('enabled', true);
    }
}
