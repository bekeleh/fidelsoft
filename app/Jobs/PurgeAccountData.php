<?php

namespace App\Jobs;

use App\Models\Document;
use App\Models\LookupAccount;
use App\Ninja\Mailers\InvoiceMailer;
use Auth;
use DB;
use Exception;

class PurgeAccountData extends Job
{
    /**
     * Execute the job.
     *
     * @param InvoiceMailer $userMailer
     * @return void
     * @throws Exception
     */
    public function handle(InvoiceMailer $userMailer)
    {
        $user = Auth::user();
        $account = $user->account;

        if (!auth()->check() || !$user->is_admin) {
            throw new Exception(trans('texts.forbidden'));
        }

        // delete the documents from cloud storage
        Document::scope()->each(function ($item, $key) {
            $item->delete();
        });

//      list of table
        $tables = [
            'activities',
            'invitations',
            'account_gateway_tokens',
            'payment_methods',
            'credits',
            'expense_categories',
            'expenses',
            'recurring_expenses',
            'invoice_items',
            'payments',
            'invoices',
            'bills',
            'tasks',
            'projects',
            'products',
            'contacts',
            'vendors',
            'contacts',
            'clients',
            'vendors',
            'vendor_contacts',
            'proposals',
            'proposal_templates',
            'proposal_snippets',
            'proposal_categories',
            'proposal_invitations',
            'tax_rates',
        ];

        foreach ($tables as $table) {
            DB::table($table)->where('account_id', $user->account_id)->delete();
        }
//     invoice
        $account->invoice_number_counter = 1;
        $account->quote_number_counter = 1;
        $account->credit_number_counter = $account->credit_number_counter > 0 ? 1 : 0;
        $account->client_number_counter = $account->client_number_counter > 0 ? 1 : 0;
//        bill
        $account->invoice_number_counter = 1;
        $account->bill_quote_number_counter = 1;
        $account->vendor_credit_number_counter = $account->credit_number_counter > 0 ? 1 : 0;
        $account->vendor_number_counter = $account->client_number_counter > 0 ? 1 : 0;

        $account->save();

        session([RECENTLY_VIEWED => false]);

        if (env('MULTI_DB_ENABLED')) {
            $current = config('database.default');
            config(['database.default' => DB_NINJA_LOOKUP]);

            $lookupAccount = LookupAccount::where('account_key', $account->account_key)->firstOrFail();
            DB::table('lookup_contacts')->where('lookup_account_id', $lookupAccount->id)->delete();
            DB::table('lookup_invitations')->where('lookup_account_id', $lookupAccount->id)->delete();
            DB::table('lookup_proposal_invitations')->where('lookup_account_id', $lookupAccount->id)->delete();

            config(['database.default' => $current]);
        }

        $subject = trans('texts.purge_successful');
        $message = trans('texts.purge_details', ['account' => $user->account->getDisplayName()]);

        $userMailer->sendMessage($user, $subject, $message);
    }
}
