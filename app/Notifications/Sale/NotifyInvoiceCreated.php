<?php

namespace App\Notifications\Sale;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class NotifyInvoiceCreated extends Notification implements ShouldQueue
{
    use Dispatchable, Queueable, SerializesModels;

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
        $url = "/invoices/{$this->invoice['public_id']}";

        return [
            'title' => 'invoice was updated',
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
            ->subject('Someone invoiceed')
            ->greeting('Someone invoiceed!')
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
