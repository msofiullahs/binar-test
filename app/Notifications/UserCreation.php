<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserCreation extends Notification implements ShouldQueue
{
    use Queueable;

    protected $newUser;

    /**
     * Create a new notification instance.
     */
    public function __construct($newUser)
    {
        $this->newUser = $newUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $subject = 'New User Created';
        $greeting = 'Hello,';
        $lines = [];

        if ($notifiable->id === $this->newUser->id) {
            // Email to the newly created user
            $subject = 'Welcome to Our Application';
            $greeting = "Welcome {$this->newUser->name},";
            $lines = [
                "Your account has been successfully created.",
                "Email: {$this->newUser->email}",
                "Role: {$this->newUser->role}",
                'You can now log in with your credentials.'
            ];
        } else {
            // Email to administrators
            $lines = [
                "A new user has been created.",
                "Name: {$this->newUser->name}",
                "Email: {$this->newUser->email}",
                "Role: {$this->newUser->role}"
            ];
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting($greeting)
            ->line(implode("\n", $lines))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'user_id' => $this->newUser->id,
            'user_name' => $this->newUser->name,
            'user_email' => $this->newUser->email,
            'user_role' => $this->newUser->role,
        ];
    }
}
