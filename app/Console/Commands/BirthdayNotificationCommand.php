<?php

namespace App\Console\Commands;

use App\Models\Person;
use App\Models\NotificationLog;
use Carbon\Carbon;
use Illuminate\Console\Command;

class BirthdayNotificationCommand extends Command
{
    protected $signature = 'birthdays:notify';

    protected $description = 'Check for birthdays today and send notifications to all associated contacts';

    public function handle(): int
    {
        $this->info('Checking for birthdays today...');

        // Get people with birthdays today
        $peopleWithBirthdaysToday = Person::with(['notificationContacts' => function ($query) {
            $query->where('enabled', true);
        }])->withBirthdayToday()->get();

        if ($peopleWithBirthdaysToday->isEmpty()) {
            $this->info('No birthdays today.');
            return self::SUCCESS;
        }

        $this->info("Found {$peopleWithBirthdaysToday->count()} birthday(s) today!");

        $totalNotificationsSent = 0;
        $totalNotificationsFailed = 0;

        foreach ($peopleWithBirthdaysToday as $person) {
            $this->newLine();
            $this->line("🎉 Birthday: {$person->full_name} (Age: {$person->age})");

            $contacts = $person->notificationContacts;

            if ($contacts->isEmpty()) {
                $this->warn("  ⚠️  No notification contacts configured for {$person->full_name}");
                continue;
            }

            $this->info("  Notifying {$contacts->count()} contact(s)...");

            foreach ($contacts as $contact) {
                $result = $this->sendNotification($person, $contact);

                if ($result['success']) {
                    $totalNotificationsSent++;
                    $this->line("  ✓ Sent to: " . ($contact->contact_name ?: $contact->phone ?: $contact->email));
                } else {
                    $totalNotificationsFailed++;
                    $this->error("  ✗ Failed to send to: " . ($contact->contact_name ?: $contact->phone ?: $contact->email));
                    if ($result['error']) {
                        $this->error("    Error: {$result['error']}");
                    }
                }

                // Log the notification attempt
                NotificationLog::create([
                    'person_id' => $person->id,
                    'notification_contact_id' => $contact->id,
                    'sent_at' => now(),
                    'status' => $result['success'] ? 'success' : 'failed',
                    'error_message' => $result['error'] ?? null,
                ]);
            }
        }

        $this->newLine();
        $this->info("Summary:");
        $this->info("  Notifications sent: {$totalNotificationsSent}");
        if ($totalNotificationsFailed > 0) {
            $this->warn("  Notifications failed: {$totalNotificationsFailed}");
        }

        return self::SUCCESS;
    }

    /**
     * Send notification to a contact (stubbed for now, logs only)
     *
     * @param Person $person
     * @param \App\Models\NotificationContact $contact
     * @return array
     */
    private function sendNotification(Person $person, $contact): array
    {
        try {
            $message = $this->buildMessage($person);

            // TODO: Implement actual SMS/Email sending here
            // For now, we'll just log the notification

            $this->comment("    Message: {$message}");
            $this->comment("    Method: {$contact->method}");

            switch ($contact->method) {
                case 'sms':
                    if (!$contact->phone) {
                        return ['success' => false, 'error' => 'Phone number not provided'];
                    }
                    // TODO: Send SMS via Twilio, Nexmo, etc.
                    $this->comment("    Phone: {$contact->phone}");
                    break;

                case 'email':
                    if (!$contact->email) {
                        return ['success' => false, 'error' => 'Email address not provided'];
                    }
                    // TODO: Send Email via Mail facade
                    $this->comment("    Email: {$contact->email}");
                    break;

                case 'both':
                    if (!$contact->phone || !$contact->email) {
                        return ['success' => false, 'error' => 'Phone or email not provided'];
                    }
                    // TODO: Send both SMS and Email
                    $this->comment("    Phone: {$contact->phone}");
                    $this->comment("    Email: {$contact->email}");
                    break;
            }

            return ['success' => true, 'error' => null];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Build notification message
     *
     * @param Person $person
     * @return string
     */
    private function buildMessage(Person $person): string
    {
        return "🎉 Happy Birthday to {$person->first_name} {$person->last_name}! " .
               "They turn {$person->age} today.";
    }
}
