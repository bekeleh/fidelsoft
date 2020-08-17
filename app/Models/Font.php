<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Font.
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $folder
 * @property string|null $css_stack
 * @property int $css_weight
 * @property string|null $google_font
 * @property string|null $normal
 * @property string|null $bold
 * @property string|null $italics
 * @property string|null $bolditalics
 * @property int $sort_order
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @method static Builder|Font newModelQuery()
 * @method static Builder|Font newQuery()
 * @method static Builder|Font query()
 * @method static Builder|Font whereBold($value)
 * @method static Builder|Font whereBolditalics($value)
 * @method static Builder|Font whereCreatedAt($value)
 * @method static Builder|Font whereCssStack($value)
 * @method static Builder|Font whereCssWeight($value)
 * @method static Builder|Font whereDeletedAt($value)
 * @method static Builder|Font whereFolder($value)
 * @method static Builder|Font whereGoogleFont($value)
 * @method static Builder|Font whereId($value)
 * @method static Builder|Font whereItalics($value)
 * @method static Builder|Font whereName($value)
 * @method static Builder|Font whereNormal($value)
 * @method static Builder|Font whereSortOrder($value)
 * @method static Builder|Font whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Font extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = false;
}
