<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Ninja\Mailers\UserMailer;
use Barracuda\ArchiveStream\Archive;

/**
 *
 * Class DownloadBill.
 */
//class DownloadInvoices extends Job implements ShouldQueue
class DownloadBill extends Job
{
    //use InteractsWithQueue,
    use SerializesModels;

    protected $user;

    protected $Bills;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param $Bills
     */
    public function __construct(User $user, $Bills)
    {
        $this->user = $user;
        $this->Bills = $Bills;
    }

    /**
     * Execute the job.
     *
     * @param UserMailer $userMailer
     */
    public function handle(UserMailer $userMailer)
    {
        if (!extension_loaded('GMP')) {
            die(trans('texts.gmp_required'));
        }

        $zip = Archive::instance_by_useragent(date('Y-m-d') . '_' . str_replace(' ', '_', trans('texts.invoice_pdfs')));

        foreach ($this->Bills as $invoice) {
            $zip->add_file($invoice->getFileName(), $invoice->getPDFString());
        }

        $zip->finish();

        exit;
    }
}
