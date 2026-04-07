<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class LeadConnectToManagerNotification extends Notification
{
    use Queueable;

    public $lead;

    /**
     * Create a new notification instance.
     * Sent to the manager (creator of the salesman) when a salesman sets a lead's status to "Connect to Manager".
     */
    public function __construct(Lead $lead)
    {
        $this->lead = $lead;
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
        $salesman = $this->lead->creator;
        $salesmanName = $salesman ? $salesman->name : 'A salesperson';

        return [
            'type' => 'lead_connect_to_manager',
            'message' => $salesmanName . ' has set lead "' . $this->lead->name . '" to Connect to Manager. Meeting requested.',
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->name,
            'salesman_id' => $salesman ? $salesman->id : null,
        ];
    }
}
