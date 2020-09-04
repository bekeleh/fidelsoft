<?php

namespace App\Notifications\Sale;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class NotifyInvoiceUpdated extends Notification
{
    use Dispatchable, Queueable, SerializesModels;

    protected $invoice;
    protected $title;

    /**
     * Create a new notification instance.
     *
     * @param $invoice
     * @param $title
     */
    public function __construct($invoice, $title = null)
    {
        $this->invoice = $invoice;
        $this->title = $title;
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
        $url = "/invoices/{$this->invoice['public_id']}";

        return [
            'title' => $this->title,
            'link' => $url,
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
