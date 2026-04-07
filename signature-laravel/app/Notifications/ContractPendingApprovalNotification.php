<?php

namespace App\Notifications;

use App\Models\Contract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ContractPendingApprovalNotification extends Notification
{
    use Queueable;

    public $contract;

    /**
     * Create a new notification instance.
     */
    public function __construct(Contract $contract)
    {
        $this->contract = $contract;
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
        $contract = $this->contract;
        $creatorName = $contract->creator ? $contract->creator->name : 'A user';

        return [
            'type' => 'contract_pending_approval',
            'message' => "Contract {$contract->contract_number} ({$contract->buyer_name}) has been signed and is pending your approval. Created by {$creatorName}.",
            'contract_id' => $contract->id,
            'contract_number' => $contract->contract_number,
            'buyer_name' => $contract->buyer_name,
        ];
    }
}
