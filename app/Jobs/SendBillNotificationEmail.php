<?php

namespace App\Jobs;

use App\Models\Traits\SerialisesDeletedModels;
use App\Ninja\Mailers\UserMailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


/**
 * Class SendBillNotificationEmail.
 */
class SendBillNotificationEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels, SerialisesDeletedModels {
        SerialisesDeletedModels::getRestoredPropertyValue insteadof SerializesModels;
    }

    protected $user;
    protected $invoice;
    protected $type;
    protected $payment;
    protected $notes;
    protected $server;

    /**
     * Create a new job instance.
     * @param mixed $user
     * @param mixed $invoice
     * @param mixed $type
     * @param mixed $payment
     * @param $notes
     */
    public function __construct($user, $invoice, $type, $payment, $notes)
    {
        $this->user = $user;
        $this->invoice = $invoice;
        $this->type = $type;
        $this->payment = $payment;
        $this->notes = $notes;
        $this->server = config('database.default');
    }

    /**
     * Execute the job.
     *
     * @param UserMailer $userMailer
     */
    public function handle(UserMailer $userMailer)
    {
        if (config('queue.default') !== 'sync') {
            $this->user->account->loadLocalizationSettings();
        }

        $userMailer->sendNotification($this->user, $this->invoice, $this->type, $this->payment, $this->notes);
    }
}
