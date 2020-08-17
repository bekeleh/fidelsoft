<?php

namespace App\Models;

use App\Models\Common\EntityModel;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as Eloquent;

/**
 * Class Bank.
 *
 * @property int $id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $public_id
 * @property string|null $name
 * @property string|null $remote_id
 * @property int $bank_library_id
 * @property string|null $config
 * @property string|null $created_at
 * @property string|null $updated_at
 * @property string|null $deleted_at
 * @property string|null $created_by
 * @property string|null $updated_by
 * @property string|null $deleted_by
 * @method static Builder|Bank newModelQuery()
 * @method static Builder|Bank newQuery()
 * @method static Builder|Bank query()
 * @method static Builder|Bank whereAccountId($value)
 * @method static Builder|Bank whereBankLibraryId($value)
 * @method static Builder|Bank whereConfig($value)
 * @method static Builder|Bank whereCreatedAt($value)
 * @method static Builder|Bank whereCreatedBy($value)
 * @method static Builder|Bank whereDeletedAt($value)
 * @method static Builder|Bank whereDeletedBy($value)
 * @method static Builder|Bank whereId($value)
 * @method static Builder|Bank whereName($value)
 * @method static Builder|Bank wherePublicId($value)
 * @method static Builder|Bank whereRemoteId($value)
 * @method static Builder|Bank whereUpdatedAt($value)
 * @method static Builder|Bank whereUpdatedBy($value)
 * @method static Builder|Bank whereUserId($value)
 * @mixin \Eloquent
 */
class Bank extends Eloquent
{
    /**
     * @var bool
     */
    public $timestamps = false;

    /**
     * @param $finance
     *
     * @return \App\Libraries\Bank
     */
    public function getOFXBank($finance)
    {
        $config = json_decode($this->config);

        return new \App\Libraries\Bank($finance, $config->fid, $config->url, $config->org);
    }
}
