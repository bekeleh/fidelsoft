<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class InvoiceItem.
 */
class InvoiceItem extends EntityModel
{
    use PresentableTrait;
    use SoftDeletes;

    protected $presenter = 'App\Ninja\Presenters\InvoiceItemPresenter';

    protected $dates = ['created_at', 'updated_at', 'deleted_at'];

    protected $fillable = [
        'cost',
        'qty',
        'notes',
        'invoice_item_type_id',
        'tax_name1',
        'tax_rate1',
        'tax_name2',
        'tax_rate2',
        'discount',
        'created_by',
        'updated_by',
        'deleted_by',
    ];

    public function getEntityType()
    {
        return ENTITY_INVOICE_ITEM;
    }

    public function getRoute()
    {
        return "/invoice_items/{$this->public_id}/edit";
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice');
    }


    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Account');
    }

    public function getPreTaxAmount()
    {
        $amount = $this->cost * $this->qty;

        if ($this->discount != 0) {
            if ($this->invoice->is_amount_discount) {
                $amount -= $this->discount;
            } else {
                $amount -= round($amount * $this->discount / 100, 4);
            }
        }

        return $amount;
    }

    public function getTaxAmount()
    {
        $tax = 0;
        $preTaxAmount = $this->getPreTaxAmount();

        if ($this->tax_rate1) {
            $tax += round($preTaxAmount * $this->tax_rate1 / 100, 2);
        }

        if ($this->tax_rate2) {
            $tax += round($preTaxAmount * $this->tax_rate2 / 100, 2);
        }

        return $tax;
    }

    public function amount()
    {
        return $this->getPreTaxAmount() + $this->getTaxAmount();
    }

    public function markFeePaid()
    {
        if ($this->invoice_item_type_id == INVOICE_ITEM_TYPE_PENDING_GATEWAY_FEE) {
            $this->invoice_item_type_id = INVOICE_ITEM_TYPE_PAID_GATEWAY_FEE;
            $this->save();
        }
    }

    public function hasTaxes()
    {
        if ($this->tax_name1 || $this->tax_rate1) {
            return true;
        }

        if ($this->tax_name2 || $this->tax_rate2) {
            return false;
        }

        return false;
    }

    public function costWithDiscount()
    {
        $unitCost = $this->cost;

        if ($this->discount != 0) {
            if ($this->invoice->is_amount_discount) {
                $unitCost -= $this->discount / $this->qty;
            } else {
                $unitCost -= $unitCost * $this->discount / 100;
            }
        }

        return $unitCost;
    }

}
