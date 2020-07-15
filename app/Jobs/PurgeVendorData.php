<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Invoice;
use App\Models\LookupAccount;
use App\Libraries\HistoryUtils;
use Exception;
use Utils;
use DB;

class PurgeVendorData extends Job
{
    protected $vendor;

    public function __construct($vendor)
    {
        $this->vendor = $vendor;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try{
            $user = auth()->user();
            $vendor = $this->vendor;
            $contact = $vendor->getPrimaryContact();

            if (!$user->is_admin) {
                return false;
            }

            $message = sprintf('%s %s (%s) purged vendor: %s %s', date('Y-m-d h:i:s'), 
                $user->email, request()->getVendorIp(), $vendor->name, $contact->email);

            if (config('app.log') == 'single') {
                @file_put_contents(storage_path('logs/purged-vendors.log'), $message, FILE_APPEND);
            } else {
                Utils::logError('[purged vendor] ' . $message);
            }

            $invoices = $vendor->invoices()->withTrashed()->get();
            $expenses = $vendor->expenses()->withTrashed()->get();
//      trash all invoice and documents
            foreach ($invoices as $invoice) {
                foreach ($invoice->documents as $document) {
                    $document->delete();
                }
            }
//      trash all expense and documents
            foreach ($expenses as $expense) {
                foreach ($expense->documents as $document) {
                    $document->delete();
                }
            }
//        trash vendor histories
            HistoryUtils::deleteHistory($this->vendor)
//         $this->vendor->forceDelete()
            if($this->vendor->delete()){
                return true;
            }
        } catch (Exception $e) {
            // show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
}
