<?php

namespace App\Notifications;

use App\Models\Birthday;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UpcomingBirthdayNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Birthday $birthday
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $contact = $this->birthday->contact;
        $daysUntil = $this->birthday->days_until;

        $message = (new MailMessage)
            ->subject("Upcoming Birthday: {$contact->display_name}")
            ->greeting("Hello {$notifiable->name}!")
            ->line("This is a reminder that {$contact->display_name}'s birthday is coming up!");

        if ($daysUntil === 0) {
            $message->line("🎉 **Today is {$contact->display_name}'s birthday!**");
        } elseif ($daysUntil === 1) {
            $message->line("**Tomorrow** is {$contact->display_name}'s birthday!");
        } else {
            $message->line("**{$daysUntil} days** until {$contact->display_name}'s birthday on " .
                          $this->birthday->date->format('F d') . ".");
        }

        if ($this->birthday->age) {
            $message->line("They will be turning **{$this->birthday->age} years old**.");
        }

        if ($this->birthday->gift_ideas) {
            $message->line("Gift Ideas: {$this->birthday->gift_ideas}");
        }

        return $message
            ->action('View Contact', url("/admin/contacts/{$contact->id}/edit"))
            ->line('Don\'t forget to wish them a happy birthday!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'birthday_id' => $this->birthday->id,
            'contact_id' => $this->birthday->contact_id,
            'contact_name' => $this->birthday->contact->display_name,
            'birthday_date' => $this->birthday->date->format('Y-m-d'),
            'days_until' => $this->birthday->days_until,
            'age' => $this->birthday->age,
        ];
    }
}
