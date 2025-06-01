<?php

namespace App\Notifications;

use App\Models\AssetRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ItemRequestNotification extends Notification
{
    use Queueable;

    protected $assetRequest;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(AssetRequest $assetRequest)
    {
        $this->assetRequest = $assetRequest;
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
        $user = $this->assetRequest->user;
        $inventory = $this->assetRequest->inventory;
        
        return (new MailMessage)
            ->subject('New Item Request: ' . $inventory->item_name)
            ->line('A new item request has been submitted:')
            ->line('Item: ' . $inventory->item_name)
            ->line('Requested By: ' . $user->first_name . ' ' . $user->last_name)
            ->line('Date Needed: ' . $this->assetRequest->date_needed->format('F d, Y'))
            ->line('Reason: ' . $this->assetRequest->reason)
            ->action('View Request', route('asset-requests.show', $this->assetRequest->id))
            ->line('Please review this request as soon as possible.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $user = $this->assetRequest->user;
        $inventory = $this->assetRequest->inventory;
        
        return [
            'request_id' => $this->assetRequest->id,
            'inventory_id' => $inventory->id,
            'item_name' => $inventory->item_name,
            'asset_tag' => $inventory->asset_tag,
            'user_id' => $user->id,
            'user_name' => $user->first_name . ' ' . $user->last_name,
            'date_needed' => $this->assetRequest->date_needed->format('Y-m-d'),
            'message' => 'New item request from ' . $user->first_name . ' ' . $user->last_name,
        ];
    }
} 