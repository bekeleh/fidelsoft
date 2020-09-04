<?php

namespace App\Notifications\Purchase;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

class NotifyBillCreated extends Notification
{
    use Dispatchable, Queueable, SerializesModels;

    protected $bill;
    protected $title;

    /**
     * Create a new notification instance.
     *
     * @param $bill
     * @param null $title
     */
    public function __construct($bill, $title = null)
    {
        $this->bill = $bill;
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
     * @return MailMessage
     */
    public function toMail($notifiable)
    {

        return (new MailMessage)
            ->subject($this->title)
            ->greeting($this->title)
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
            'Title' => $this->title,
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
