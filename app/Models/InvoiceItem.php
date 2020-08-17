<?php

namespace App\Models;

use App\Models\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\PresentableTrait;

/**
 * Class InvoiceItem.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $invoice_id
 * @property int|null $product_id
 * @property int|null $warehouse_id
 * @property int|null $invoice_item_type_id
 * @property string|null $product_key
 * @property float|null $cost
 * @property float|null $qty
 * @property float|null $demand_qty
 * @property string|null $tax_name1
 * @property float|null $tax_rate1
 * @property string|null $tax_name2
 * @property float|null $tax_rate2
 * @property string|null $custom_value1
 * @property string|null $custom_value2
 * @property float|null $discount
 * @property int|null $is_deleted
 * @property string|null $notes
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property Carbon|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @property-read Account|null $account
 * @property-read Invoice|null $invoice
 * @property-read Product|null $product
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newQuery()
 * @method static Builder|InvoiceItem onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereAccountId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCost($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCreatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCustomValue1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCustomValue2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereDeletedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereDemandQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereDiscount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereInvoiceItemTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereIsDeleted($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereNotes($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereProductKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem wherePublicId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereQty($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereTaxName1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereTaxName2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereTaxRate1($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereTaxRate2($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUpdatedBy($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereWarehouseId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withActiveOrSelected($id = false)
 * @method static \Illuminate\Database\Eloquent\Builder|EntityModel withArchived()
 * @method static Builder|InvoiceItem withTrashed()
 * @method static Builder|InvoiceItem withoutTrashed()
 * @mixin Eloquent
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
        return $this->belongsTo('App\Models\Common\Account');
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
