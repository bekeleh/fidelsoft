<?php

namespace App\Jobs;

use App;
use App\Models\Bill;
use App\Ninja\Mailers\PurchaseContactMailer;
use App\Ninja\Mailers\VendorContactMailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Monolog\Logger;

/**
 * Class SendBillEmail.
 */
class SendBillEmail implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    protected $invoice;
    protected $reminder;
    protected $template;
    protected $userId;
    protected $server;
    protected $proposal;
    protected $jobName;

    /**
     * Create a new job instance.
     *
     * @param Bill $invoice
     * @param bool $userId
     * @param bool $reminder
     * @param bool $template
     * @param bool $proposal
     */
    public function __construct(
        Bill $invoice,
        $userId = false,
        $reminder = false,
        $template = false,
        $proposal = false)
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
     * @param PurchaseContactMailer $mailer
     */
    public function handle(PurchaseContactMailer $mailer)
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
     * @param VendorContactMailer $mailer
     * @param Logger $logger
     */

    /**
     * @param VendorContactMailer $mailer
     * @param Logger $logger
     */
    public function failed(VendorContactMailer $mailer, Logger $logger)
    {
        $this->jobName = $this->job->getName();

        if (config('queue.failed.notify_email')) {
            $mailer->sendTo(
                config('queue.failed.notify_email'),
                config('mail.from.address'),
                config('mail.from.name'),
                config('queue.failed.notify_subject', trans('texts.job_failed', ['name' => $this->jobName])),
                'job_failed',
                [
                    'name' => $this->jobName,
                ]
            );
        }

        $logger->error(
            trans('texts.job_failed', ['name' => $this->jobName])
        );

//        parent::failed();
    }

}
