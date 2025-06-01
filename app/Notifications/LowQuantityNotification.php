<?php

namespace App\Notifications;

use App\Models\Inventory;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LowQuantityNotification extends Notification
{
    use Queueable;

    protected $inventory;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Low Inventory Alert: ' . $this->inventory->item_name)
            ->line('This is to inform you that the following inventory item is running low:')
            ->line('Item: ' . $this->inventory->item_name)
            ->line('Current Quantity: ' . $this->inventory->quantity)
            ->line('Minimum Quantity: ' . $this->inventory->min_quantity)
            ->line('Maximum Quantity: ' . $this->inventory->max_quantity)
            ->action('View Item', route('inventory.show', $this->inventory->id))
            ->line('Please restock this item as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'inventory_id' => $this->inventory->id,
            'item_name' => $this->inventory->item_name,
            'asset_tag' => $this->inventory->asset_tag,
            'quantity' => $this->inventory->quantity,
            'min_quantity' => $this->inventory->min_quantity,
            'max_quantity' => $this->inventory->max_quantity,
            'message' => 'Low quantity alert for ' . $this->inventory->item_name,
        ];
    }
} 