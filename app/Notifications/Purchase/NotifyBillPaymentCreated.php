<?php

namespace App\Notifications\Purchase;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class NotifyBillPaymentCreated extends Notification
{
    use Queueable;

    public $bill;
    public $billPayment;

    /**
     * Create a new notification instance.
     *
     * @param $billPayment
     * @param $bill
     */
    public function __construct($billPayment, $bill)
    {
        $this->billPayment = $billPayment;
        $this->bill = $bill;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
