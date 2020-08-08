<?php

namespace App\Providers;

use Form;
use App\Http\Macros\FormMixins;
use App\Http\Macros\RuleMixins;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rule;

class MacroServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {

        Form::mixin(new FormMixins());
        Rule::mixin(new RuleMixins());
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }
}
