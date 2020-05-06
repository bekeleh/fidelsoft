<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class BankAccountDatatable extends EntityDatatable
{
    public $entityType = ENTITY_BANK_ACCOUNT;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'bank_name',
                function ($model) {
                    return link_to("bank_accounts/{$model->bank_public_id}/edit", $model->bank_name)->toHtml();
                },
            ],
            [
                'bank_library_id',
                function ($model) {
                    return 'OFX';
                },
            ],
        ];
    }

    public function actions()
    {
        return [
            [
                uctrans('texts.edit_bank_account'),
                function ($model) {
                    return URL::to("bank_accounts/{$model->public_id}/edit");
                },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_BANK_ACCOUNT]);
                },
            ],
            [
                uctrans('texts.clone_bank_account'),
                function ($model) {
                    return URL::to("bank_accounts/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', [ENTITY_BANK_ACCOUNT]);
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_BANK_ACCOUNT]);
                },
            ],
        ];
    }
}
