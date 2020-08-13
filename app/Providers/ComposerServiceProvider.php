<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register bindings in the container.
     *
     * @return void
     */
    public function boot()
    {
        // view composer with dedicated class
        view()->composer(
            [
                'accounts.details',
                'clients.edit',
                'vendors.edit',
                'payments.edit',
                'invoices.edit',
                'BILLs.edit',
                'expenses.edit',
                'accounts.localization',
                'payments.credit_card',
                'invited.details',
                'products.edit',
                'item_prices.edit',
                'item_requests.show',
            ],
            'App\Http\ViewComposers\TranslationComposer'
        );

        view()->composer(
            [
                'header',
                'tasks.edit',
            ],
            'App\Http\ViewComposers\AppLanguageComposer'
        );

        view()->composer(
            [
                'public.header',
            ],
            'App\Http\ViewComposers\ClientPortalHeaderComposer'
        );

        view()->composer(
            [
                'proposals.edit',
                'proposals.templates.edit',
                'proposals.snippets.edit',
            ],
            'App\Http\ViewComposers\ProposalComposer'
        );

    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
    }
}
