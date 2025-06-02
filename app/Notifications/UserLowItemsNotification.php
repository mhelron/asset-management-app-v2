<?php

namespace App\Notifications;

use App\Models\ItemDistribution;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserLowItemsNotification extends Notification
{
    use Queueable;

    protected $distribution;
    protected $threshold;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(ItemDistribution $distribution, $threshold = 3)
    {
        $this->distribution = $distribution;
        $this->threshold = $threshold;
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
        
        return (new MailMessage)
            ->subject('Low Item Alert: ' . $inventory->item_name)
            ->line("Your assigned items are running low.")
            ->line("Item: {$inventory->item_name}")
            ->line("Remaining quantity: {$this->distribution->quantity_remaining}")
            ->action('View My Items', route('distributions.my-items'))
            ->line('You may need to request additional items soon.');
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
        
        return [
            'distribution_id' => $this->distribution->id,
            'inventory_id' => $inventory->id,
            'item_name' => $inventory->item_name,
            'quantity_remaining' => $this->distribution->quantity_remaining,
            'threshold' => $this->threshold,
            'message' => "Low quantity alert: You have only {$this->distribution->quantity_remaining} units of {$inventory->item_name} remaining.",
        ];
    }
} 