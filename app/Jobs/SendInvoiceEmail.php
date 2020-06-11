<?php

namespace App\Jobs;

use App;
use App\Models\Invoice;
use App\Ninja\Mailers\ContactMailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;

/**
 * Class SendInvoiceEmail.
 */
class SendInvoiceEmail extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $invoice;
    protected $reminder;
    protected $template;
    protected $userId;
    protected $server;
    protected $proposal;

    /**
     * Create a new job instance.
     *
     * @param Invoice $invoice
     * @param bool $userId
     * @param bool $reminder
     * @param bool $template
     * @param bool $proposal
     */
    public function __construct(Invoice $invoice, $userId = false, $reminder = false, $template = false, $proposal = false)
    {
        $this->invoice = $invoice;
        $this->userId = $userId;
        $this->reminder = $reminder;
        $this->template = $template;
        $this->proposal = $proposal;
        $this->server = config('database.default');
    }

    /**
     * Execute the job.
     *
     * @param ContactMailer $mailer
     */
    public function handle(ContactMailer $mailer)
    {
        // send email as user
        if (App::runningInConsole() && $this->userId) {
            Auth::onceUsingId($this->userId);
        }

        $mailer->sendInvoice($this->invoice, $this->reminder, $this->template, $this->proposal);

        if (App::runningInConsole() && $this->userId) {
            Auth::logout();
        }
    }

    /*
     * Handle a job failure.
     *
     * @param ContactMailer $mailer
     * @param Logger $logger
     */
    /*
   public function failed(ContactMailer $mailer, Logger $logger)
   {
       $this->jobName = $this->job->getName();

       parent::failed($mailer, $logger);
   }
   */
}
