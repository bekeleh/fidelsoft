<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Ninja\Mailers\ClientMailer as Mailer;
use App\Ninja\Repositories\AccountRepository;
use Illuminate\Console\Command;
use App\Libraries\Utils;
use Mail;
use Symfony\Component\Console\Input\InputOption;

/**
 * Class SendRenewalInvoices.
 */
class SendRenewalInvoices extends Command
{

    protected $name = 'ninja:send-renewals';


    protected $description = 'Send renewal invoices';

    protected $mailer;

    protected $accountRepo;

    /**
     *
     * SendRenewalInvoices constructor.
     *
     * @param Mailer $mailer
     * @param AccountRepository $accountRepo
     */
    public function __construct(Mailer $mailer, AccountRepository $accountRepo)
    {
        parent::__construct();

        $this->mailer = $mailer;
        $this->accountRepo = $accountRepo;
    }

    public function fire()
    {
        $this->info(date('r') . ' Running Send Renewal Invoices Command ...');

        if ($database = $this->option('database')) {
            config(['database.default' => $database]);
        }

        // get all accounts with plans expiring in 10 days
        $companies = Company::whereRaw("datediff(plan_expires, curdate()) = 10 and (plan = 'pro' or plan = 'enterprise')")
            ->orderBy('id')
            ->get();
        $this->info($companies->count() . ' companies found renewing in 10 days');

        foreach ($companies as $company) {
            if (!$company->accounts->count()) {
                continue;
            }

            $account = $company->accounts->sortBy('id')->first();
            $plan = [];
            $plan['plan'] = $company->plan;
            $plan['term'] = $company->plan_term;
            $plan['num_users'] = $company->num_users;
            $plan['price'] = min($company->plan_price, Utils::getPlanPrice($plan));

            if ($plan['plan'] == PLAN_FREE || !$plan['plan'] || !$plan['term'] || !$plan['price']) {
                continue;
            }

            $client = $this->accountRepo->getNinjaClient($account);
            $invitation = $this->accountRepo->createNinjaInvoice($client, $account, $plan, 0, false);

            // set the due date to 10 days from now
            $invoice = $invitation->invoice;
            $invoice->due_date = date('Y-m-d', strtotime('+ 10 days'));
            $invoice->save();

            $term = $plan['term'];
            $plan = $plan['plan'];

            if ($term == PLAN_TERM_YEARLY) {
                $this->mailer->sendInvoice($invoice);
                $this->info("Sent {$term}ly {$plan} invoice to {$client->getDisplayName()}");
            } else {
                $this->info("Created {$term}ly {$plan} invoice for {$client->getDisplayName()}");
            }
        }

        $this->info('Done');

        if ($errorEmail = env('ERROR_EMAIL')) {
            Mail::raw('EOM', function ($message) use ($errorEmail, $database) {
                $message->to($errorEmail)
                    ->from(CONTACT_EMAIL)
                    ->subject("SendRenewalInvoices [{$database}]: Finished successfully");
            });
        }
    }

    protected function getArguments()
    {
        return [];
    }

    protected function getOptions()
    {
        return [
            ['database', null, InputOption::VALUE_OPTIONAL, 'Database', null],
        ];
    }
}
