<?php

namespace App\Console\Commands;

use App\Models\Birthday;
use App\Notifications\UpcomingBirthdayNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class SendBirthdayNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'birthdays:notify';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notifications for upcoming birthdays';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Checking for upcoming birthdays...');

        $birthdays = Birthday::where('send_notification', true)
            ->with(['contact.user'])
            ->get()
            ->filter(function ($birthday) {
                $daysUntil = $birthday->days_until;
                return $daysUntil >= 0 && $daysUntil <= $birthday->notification_days_before;
            });

        if ($birthdays->isEmpty()) {
            $this->info('No upcoming birthdays to notify about.');
            return self::SUCCESS;
        }

        $count = 0;
        foreach ($birthdays as $birthday) {
            if ($birthday->contact && $birthday->contact->user) {
                $birthday->contact->user->notify(new UpcomingBirthdayNotification($birthday));
                $count++;
                $this->info("Sent notification for {$birthday->contact->display_name} to {$birthday->contact->user->email}");
            }
        }

        $this->info("Sent {$count} birthday notifications.");

        return self::SUCCESS;
    }
}
