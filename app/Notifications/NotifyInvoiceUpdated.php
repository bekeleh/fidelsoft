<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Log;

class NotifyInvoiceUpdated extends Notification
{

    protected $invoice;

    /**
     * Create a new notification instance.
     *
     * @param $invoice
     */
    public function __construct($invoice)
    {
        $this->invoice = $invoice;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['database']; //'mail',
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {
        Log::info($this->invoice);
        $url = "/invoices/";
        $clientId = 'test';

        return [
            'title' => 'invoice was updated',
            'link' => $url,
            'client_id' => $clientId,
            'user_id' => auth::id(),
            'created_at' => Carbon::now(),
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject('Someone invoiced')
            ->greeting('Someone invoiced!')
            ->line($this->invoice->invoice)
            ->line($this->user->name . ' Said')
            ->line('"' . $this->invoice['invoice'] . '"')
            ->action('See All invoices', url('/invoices/' . $this->invoice->id . '/' . URL::get_slug($this->invoice)));
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [];
    }
}
