<?php

namespace App\Notifications;

use App\Models\Task;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MeetingReminderNotification extends Notification
{
    use Queueable;

    public $task;

    /**
     * Create a new notification instance.
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $lead = $this->task->lead;
        $meetingTime = $this->task->due_date->format('M d, Y') . ' at ' . ($lead && $lead->scheduled_time ? \Carbon\Carbon::parse($lead->scheduled_time)->format('h:i A') : '');
        
        return [
            'type' => 'meeting_reminder',
            'message' => 'Meeting reminder: ' . $this->task->title . ' scheduled for ' . $meetingTime,
            'task_id' => $this->task->id,
            'lead_id' => $lead ? $lead->id : null,
        ];
    }
}
