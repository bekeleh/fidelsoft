<?php

namespace App\Jobs;

use App\Jobs\Job;
use App\Models\Invoice;
use App\Models\LookupAccount;
use App\Libraries\HistoryUtils;
use App\Libraries\Utils;
use Exception;
use Illuminate\Support\Facades\DB;

class PurgeClientData extends Job
{
    protected $client;

    /**
     * PurgeClientData constructor instance.
     * @param $client
     */
    public function __construct($client)
    {
        $this->client = $client;
    }

    /**
     * Execute the job.
     *
     * @return bool
     */
    public function handle()
    {
        try {
            $user = auth()->user();
            $client = $this->client;
            $contact = $client->getPrimaryContact();

            if (!auth()->check() || !$user->is_admin) {
                return false;
            }

            $message = sprintf('%s %s (%s) purged client: %s %s', date('Y-m-d h:i:s'),
                $user->email, request()->getClientIp(), $client->name, $contact->email);

            if (config('app.log') == 'single') {
                @file_put_contents(storage_path('logs/purged-clients.log'), $message, FILE_APPEND);
            } else {
                Utils::logError('[purged client] ' . $message);
            }

            $invoices = $client->invoices()->withTrashed()->get();
            $expenses = $client->expenses()->withTrashed()->get();
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
//        trash client histories
            HistoryUtils::deleteHistory($this->client);
//         $this->client->forceDelete()
            if ($this->client->delete()) {
                return true;
            }
        } catch (Exception $e) {
            // show_error($e->getMessage() . ' --- ' . $e->getTraceAsString());
        }
    }
}
