<?php

namespace App\Notifications;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class InvoiceCreated extends Notification implements ShouldQueue
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
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toDatabase($notifiable)
    {

        return [
            'Title' => 'Server Expenses',
            'Amount' => '$1,234',
            'Via' => 'American Express',
            'Was Overdue' => ':-1:',
            'user_id' => $this->invoice['user_id'],
            'created_at' => Carbon::now(),
        ];
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
