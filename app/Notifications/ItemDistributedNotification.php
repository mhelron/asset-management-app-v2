<?php

namespace App\Notifications;

use App\Models\ItemDistribution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemDistributedNotification extends Notification
{
    use Queueable;

    protected $distribution;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ItemDistribution $distribution)
    {
        $this->distribution = $distribution;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database', 'mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $inventory = $this->distribution->inventory;
        $assigner = $this->distribution->assigner;
        
        return (new MailMessage)
            ->subject('Items Distributed: ' . $inventory->item_name)
            ->line("You have been assigned {$this->distribution->quantity_assigned} units of {$inventory->item_name}.")
            ->line("Assigned by: {$assigner->first_name} {$assigner->last_name}")
            ->line($this->distribution->notes ? "Notes: {$this->distribution->notes}" : "")
            ->action('View My Items', route('distributions.my-items'));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $inventory = $this->distribution->inventory;
        $assigner = $this->distribution->assigner;
        
        return [
            'distribution_id' => $this->distribution->id,
            'inventory_id' => $inventory->id,
            'item_name' => $inventory->item_name,
            'quantity' => $this->distribution->quantity_assigned,
            'assigned_by' => $assigner->first_name . ' ' . $assigner->last_name,
            'notes' => $this->distribution->notes,
            'message' => "You have been assigned {$this->distribution->quantity_assigned} units of {$inventory->item_name}.",
        ];
    }
} 