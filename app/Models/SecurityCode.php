<?php

namespace App\Models;

use App\Models\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class DatetimeFormat.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $contact_id
 * @property string|null $bot_user_id
 * @property int $attempts
 * @property string|null $code
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|SecurityCode newModelQuery()
 * @method static Builder|SecurityCode newQuery()
 * @method static Builder|SecurityCode query()
 * @method static Builder|SecurityCode whereAccountId($value)
 * @method static Builder|SecurityCode whereAttempts($value)
 * @method static Builder|SecurityCode whereBotUserId($value)
 * @method static Builder|SecurityCode whereCode($value)
 * @method static Builder|SecurityCode whereContactId($value)
 * @method static Builder|SecurityCode whereCreatedAt($value)
 * @method static Builder|SecurityCode whereCreatedBy($value)
 * @method static Builder|SecurityCode whereDeletedAt($value)
 * @method static Builder|SecurityCode whereDeletedBy($value)
 * @method static Builder|SecurityCode whereId($value)
 * @method static Builder|SecurityCode whereUpdatedAt($value)
 * @method static Builder|SecurityCode whereUpdatedBy($value)
 * @method static Builder|SecurityCode whereUserId($value)
 * @mixin Eloquent
 */
class SecurityCode extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = false;
}
