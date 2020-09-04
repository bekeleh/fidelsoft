<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Ninja\Mailers\InvoiceMailer;
use Barracuda\ArchiveStream\Archive;

/**
 *
 * Class DownloadBill.
 */
//class DownloadBill extends Job implements ShouldQueue
class DownloadBill extends Job
{
    //use InteractsWithQueue,
    use SerializesModels;

    protected $user;

    protected $bills;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param $bills
     */
    public function __construct(User $user, $bills)
    {
        $this->user = $user;
        $this->bills = $bills;
    }

    /**
     * Execute the job.
     *
     * @param InvoiceMailer $userMailer
     */
    public function handle(InvoiceMailer $userMailer)
    {
        if (!extension_loaded('GMP')) {
            die(trans('texts.gmp_required'));
        }

        $zip = Archive::instance_by_useragent(date('Y-m-d') . '_' . str_replace(' ', '_', trans('texts.bill_pdfs')));

        foreach ($this->bills as $invoice) {
            $zip->add_file($invoice->getFileName(), $invoice->getPDFString());
        }

        $zip->finish();

        exit;
    }
}
