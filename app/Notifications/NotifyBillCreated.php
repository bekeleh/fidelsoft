<?php

namespace App\Notifications;

use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyBillCreated extends Notification
{

    protected $bill;

    /**
     * Create a new notification instance.
     *
     * @param $bill
     */
    public function __construct($bill)
    {
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
            ->subject('Someone billed')
            ->greeting('Someone billed!')
            ->line($this->bill->bill)
            ->line($this->user->name . ' Said')
            ->line('"' . $this->bill['bill'] . '"')
            ->action('See All bills', url('/bills/' . $this->bill->id . '/' . URL::get_slug($this->bill)));
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
            'Amount' => $this->bill->amount,
            'Via' => 'American Express',
            'Was Overdue' => ':-1:',
            'user_id' => auth::id(),
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
