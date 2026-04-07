<?php

namespace App\Console\Commands;

use App\Models\Task;
use App\Models\Lead;
use App\Notifications\MeetingReminderNotification;
use Illuminate\Console\Command;
use Carbon\Carbon;

class SendMeetingReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'meetings:send-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send meeting reminder notifications 1 hour before scheduled meetings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for meetings that need reminders...');

        // Get tasks that are related to leads (meetings) and are pending
        $tasks = Task::whereNotNull('lead_id')
            ->where('status', 'pending')
            ->with('lead', 'user')
            ->get();

        $remindersSent = 0;
        $now = Carbon::now();

        foreach ($tasks as $task) {
            if (!$task->lead || !$task->lead->scheduled_date || !$task->lead->scheduled_time) {
                continue;
            }

            // Combine scheduled date and time
            $timeString = $task->lead->scheduled_time instanceof \Carbon\Carbon 
                ? $task->lead->scheduled_time->format('H:i:s')
                : $task->lead->scheduled_time;
            
            $scheduledDateTime = Carbon::createFromFormat(
                'Y-m-d H:i:s',
                $task->lead->scheduled_date->format('Y-m-d') . ' ' . $timeString
            );

            // Calculate time 1 hour before the meeting
            $reminderTime = $scheduledDateTime->copy()->subHour();

            // Check if current time is within 1 minute of the reminder time (to account for cron timing)
            $timeDiff = abs($now->diffInMinutes($reminderTime));

            if ($timeDiff <= 5 && $now->greaterThanOrEqualTo($reminderTime)) {
                // Check if notification was already sent
                $existingNotification = $task->user->notifications()
                    ->where('type', 'App\Notifications\MeetingReminderNotification')
                    ->where('data->task_id', $task->id)
                    ->first();

                if (!$existingNotification) {
                    // Send notification
                    $task->user->notify(new MeetingReminderNotification($task));
                    $remindersSent++;
                    $this->info("Reminder sent for meeting: {$task->title} at {$scheduledDateTime->format('M d, Y h:i A')}");
                }
            }
        }

        $this->info("Total reminders sent: {$remindersSent}");
        return 0;
    }
}
