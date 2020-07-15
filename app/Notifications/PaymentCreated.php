<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class PaymentCreated extends Notification implements ShouldQueue
{
    use Queueable;

    protected $payment;
    protected $invoice;

    /**
     * Create a new notification instance.
     *
     * @param $payment
     * @param $invoice
     */
    public function __construct($payment, $invoice)
    {
        $this->invoice = $invoice;
        $this->payment = $payment;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['slack'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable
     * @return SlackMessage
     */
    public function toSlack($notifiable)
    {
        $url = 'http://www.ninja.test/subscriptions/create';

        return (new SlackMessage)
        ->from(APP_NAME)
        ->image('https://app.invoiceninja.com/favicon-v2.png')
        ->content(trans('texts.received_new_payment'))
        ->attachment(function ($attachment) use($url) {
            $invoiceName = $this->invoice->present()->titledName;
            $invoiceLink = $this->invoice->present()->multiAccountLink;
            $attachment
            ->title($invoiceName, $invoiceLink)
            ->fields([
                trans('texts.client') => $this->invoice->client->getDisplayName(),
                trans('texts.amount') => $this->payment->present()->amount,
            ]);
        });
    }

/**
 * Get the Slack representation of the notification.
 *
 * @param  mixed  $notifiable
 * @return SlackMessage
 */
public function toSlackNotification($notifiable)
{
    $url = url('/invoices/'.$this->invoice->id);

    return (new SlackMessage)
    ->success()
    ->content('One of your invoices has been paid!')
    ->attachment(function ($attachment) use ($url) {
        $attachment
        ->title('Invoice 1322', $url)
        ->fields([
            'Title' => 'Server Expenses',
            'Amount' => '$1,234',
            'Via' => 'American Express',
            'Was Overdue' => ':-1:',
        ]);
    });
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
