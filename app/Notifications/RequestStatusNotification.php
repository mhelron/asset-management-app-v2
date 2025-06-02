<?php

namespace App\Notifications;

use App\Models\AssetRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestStatusNotification extends Notification
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
        $status = $this->assetRequest->status;
        $inventory = $this->assetRequest->inventory;
        
        $mailMessage = (new MailMessage)
            ->subject("Asset Request {$status}: {$inventory->item_name}")
            ->line("Your request for {$inventory->item_name} has been {$status}.")
            ->line("Request Status: {$status}")
            ->action('View Request Details', route('asset-requests.my-requests'));
            
        if ($this->assetRequest->status_note) {
            $mailMessage->line("Note: {$this->assetRequest->status_note}");
        }
        
        return $mailMessage;
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        $inventory = $this->assetRequest->inventory;
        
        return [
            'request_id' => $this->assetRequest->id,
            'inventory_id' => $inventory->id,
            'item_name' => $inventory->item_name,
            'status' => $this->assetRequest->status,
            'status_note' => $this->assetRequest->status_note,
            'message' => "Your request for {$inventory->item_name} has been {$this->assetRequest->status}.",
        ];
    }
} 