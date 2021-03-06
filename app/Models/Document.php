<?php

namespace App\Models;

use App\Models\EntityModel;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Rackspace\RackspaceAdapter;

/**
 * Class Document.
 *
 * @property int $id
 * @property int|null $public_id
 * @property int|null $account_id
 * @property int|null $user_id
 * @property int|null $invoice_id
 * @property int|null $bill_id
 * @property int|null $expense_id
 * @property string|null $document_key
 * @property string|null $path
 * @property string|null $preview
 * @property string|null $name
 * @property string|null $type
 * @property string|null $disk
 * @property string|null $hash
 * @property int $size
 * @property int|null $width
 * @property int|null $height
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $deleted_at
 * @property int|null $is_default
 * @property int $is_proposal
 * @property string $created_by
 * @property string $updated_by
 * @property string $deleted_by
 * @property-read Bill $BILL
 * @property-read Account|null $account
 * @property-read Expense|null $expense
 * @property-read Invoice|null $invoice
 * @property-read User|null $user
 * @method static Builder|Document newModelQuery()
 * @method static Builder|Document newQuery()
 * @method static Builder|Document proposalImages()
 * @method static Builder|Document query()
 * @method static Builder|EntityModel scope($publicId = false, $accountId = false)
 * @method static Builder|Document whereAccountId($value)
 * @method static Builder|Document whereBillId($value)
 * @method static Builder|Document whereCreatedAt($value)
 * @method static Builder|Document whereCreatedBy($value)
 * @method static Builder|Document whereDeletedAt($value)
 * @method static Builder|Document whereDeletedBy($value)
 * @method static Builder|Document whereDisk($value)
 * @method static Builder|Document whereDocumentKey($value)
 * @method static Builder|Document whereExpenseId($value)
 * @method static Builder|Document whereHash($value)
 * @method static Builder|Document whereHeight($value)
 * @method static Builder|Document whereId($value)
 * @method static Builder|Document whereInvoiceId($value)
 * @method static Builder|Document whereIsDefault($value)
 * @method static Builder|Document whereIsProposal($value)
 * @method static Builder|Document whereName($value)
 * @method static Builder|Document wherePath($value)
 * @method static Builder|Document wherePreview($value)
 * @method static Builder|Document wherePublicId($value)
 * @method static Builder|Document whereSize($value)
 * @method static Builder|Document whereType($value)
 * @method static Builder|Document whereUpdatedAt($value)
 * @method static Builder|Document whereUpdatedBy($value)
 * @method static Builder|Document whereUserId($value)
 * @method static Builder|Document whereWidth($value)
 * @method static Builder|EntityModel withActiveOrSelected($id = false)
 * @method static Builder|EntityModel withArchived()
 * @mixin Eloquent
 */
class Document extends EntityModel
{
    protected $table = 'documents';

    protected $fillable = [
        'invoice_id',
        'invoice_id',
        'expense_id',
        'is_default',
        'created_by',
        'updated_by',
        'deleted_by',
    ];


    public function getEntityType()
    {
        return ENTITY_DOCUMENT;
    }

    public function getRoute()
    {
        return "/documents/{$this->public_id}/edit";
    }


    public static $extraExtensions = [
        'jpg' => 'jpeg',
        'tif' => 'tiff',
    ];

    public static $allowedMimes = [// Used by Dropzone.js; does not affect what the server accepts
        'image/png', 'image/jpeg', 'image/tiff', 'application/pdf', 'image/gif', 'image/vnd.adobe.photoshop', 'text/plain',
        'application/msword',
        'application/excel', 'application/vnd.ms-excel', 'application/x-excel', 'application/x-msexcel',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/postscript', 'image/svg+xml',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.ms-powerpoint',
    ];


    public static $types = [
        'png' => [
            'mime' => 'image/png',
        ],
        'ai' => [
            'mime' => 'application/postscript',
        ],
        'svg' => [
            'mime' => 'image/svg+xml',
        ],
        'jpeg' => [
            'mime' => 'image/jpeg',
        ],
        'tiff' => [
            'mime' => 'image/tiff',
        ],
        'pdf' => [
            'mime' => 'application/pdf',
        ],
        'gif' => [
            'mime' => 'image/gif',
        ],
        'psd' => [
            'mime' => 'image/vnd.adobe.photoshop',
        ],
        'txt' => [
            'mime' => 'text/plain',
        ],
        'doc' => [
            'mime' => 'application/msword',
        ],
        'xls' => [
            'mime' => 'application/vnd.ms-excel',
        ],
        'ppt' => [
            'mime' => 'application/vnd.ms-powerpoint',
        ],
        'xlsx' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ],
        'docx' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ],
        'pptx' => [
            'mime' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ],
    ];

    public function fill(array $attributes)
    {
        parent::fill($attributes);

        if (empty($this->attributes['disk'])) {
            $this->attributes['disk'] = env('DOCUMENT_FILESYSTEM', 'documents');
        }

        return $this;
    }

    public function account()
    {
        return $this->belongsTo('App\Models\Common\Account');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\User')->withTrashed();
    }

    public function expense()
    {
        return $this->belongsTo('App\Models\Expense')->withTrashed();
    }

    public function invoice()
    {
        return $this->belongsTo('App\Models\Invoice')->withTrashed();
    }

    public function BILL()
    {
        return $this->belongsTo('App\Models\Bill')->withTrashed();
    }

    public function getDisk()
    {
        return Storage::disk(!empty($this->disk) ? $this->disk : env('DOCUMENT_FILESYSTEM', 'documents'));
    }

    public function setDiskAttribute($value)
    {
        $this->attributes['disk'] = $value ? $value : env('DOCUMENT_FILESYSTEM', 'documents');
    }

    public function getDirectUrl()
    {
        return static::getDirectFileUrl($this->path, $this->getDisk());
    }

    public function getDirectPreviewUrl()
    {
        return $this->preview ? static::getDirectFileUrl($this->preview, $this->getDisk(), true) : null;
    }

    public static function getDirectFileUrl($path, $disk, $prioritizeSpeed = false)
    {
        $adapter = $disk->getAdapter();
        $fullPath = $adapter->applyPathPrefix($path);

        if ($adapter instanceof AwsS3Adapter) {
            $client = $adapter->getClient();
            $command = $client->getCommand('GetObject', [
                'Bucket' => $adapter->getBucket(),
                'Key' => $fullPath,
            ]);

            return (string)$client->createPresignedRequest($command, '+10 minutes')->getUri();
        } elseif (!$prioritizeSpeed // Rackspace temp URLs are slow, so we don't use them for previews
            && $adapter instanceof RackspaceAdapter) {
            $secret = env('RACKSPACE_TEMP_URL_SECRET');
            if ($secret) {
                $object = $adapter->getContainer()->getObject($fullPath);

                if (env('RACKSPACE_TEMP_URL_SECRET_SET')) {
                    // Go ahead and set the secret too
                    $object->getService()->getAccount()->setTempUrlSecret($secret);
                }

                $url = $object->getUrl();
                $expiry = strtotime('+10 minutes');
                $urlPath = urldecode($url->getPath());
                $body = sprintf("%s\n%d\n%s", 'GET', $expiry, $urlPath);
                $hash = hash_hmac('sha1', $body, $secret);

                return sprintf('%s?temp_url_sig=%s&temp_url_expires=%d', $url, $hash, $expiry);
            }
        }

        return null;
    }

    public function getRaw()
    {
        $disk = $this->getDisk();

        return $disk->get($this->path);
    }

    public function getRawCached()
    {
        $key = 'image:' . $this->path;

        if ($image = cache($key)) {
            // do nothing
        } else {
            $image = $this->getRaw();
            cache([$key => $image], 120);
        }

        return $image;
    }

    public function getStream()
    {
        $disk = $this->getDisk();

        return $disk->readStream($this->path);
    }

    public function getRawPreview()
    {
        $disk = $this->getDisk();

        return $disk->get($this->preview);
    }

    public function getUrl()
    {
        return url('documents/' . $this->public_id . '/' . $this->name);
    }

    public function getClientUrl($invitation)
    {
        return url('client/documents/' . $invitation->invitation_key . '/' . $this->public_id . '/' . $this->name);
    }

    public function getVendorUrl($invitation)
    {
        return url('vendor/documents/' . $invitation->invitation_key . '/' . $this->public_id . '/' . $this->name);
    }

    public function getProposalUrl()
    {
        if (!$this->is_proposal || !$this->document_key) {
            return '';
        }

        return url('proposal/image/' . $this->account->account_key . '/' . $this->document_key . '/' . $this->name);
    }

    public function isPDFEmbeddable()
    {
        return $this->type == 'jpeg' || $this->type == 'png' || $this->preview;
    }

    public function getVFSJSUrl()
    {
        if (!$this->isPDFEmbeddable()) {
            return null;
        }

        return url('documents/js/' . $this->public_id . '/' . $this->name . '.js');
    }

    public function getClientVFSJSUrl()
    {
        if (!$this->isPDFEmbeddable()) {
            return null;
        }

        return url('client/documents/js/' . $this->public_id . '/' . $this->name . '.js');
    }

    public function getPreviewUrl()
    {
        return $this->preview ? url('documents/preview/' . $this->public_id . '/' . $this->name . '.' . pathinfo($this->preview, PATHINFO_EXTENSION)) : null;
    }

    public function toArray()
    {
        $array = parent::toArray();

        if (empty($this->visible) || in_array('url', $this->visible)) {
            $array['url'] = $this->getUrl();
        }
        if (empty($this->visible) || in_array('preview_url', $this->visible)) {
            $array['preview_url'] = $this->getPreviewUrl();
        }

        return $array;
    }

    public function cloneDocument()
    {
        $document = self::createNew($this);
        $document->path = $this->path;
        $document->preview = $this->preview;
        $document->name = $this->name;
        $document->type = $this->type;
        $document->disk = $this->disk;
        $document->hash = $this->hash;
        $document->size = $this->size;
        $document->width = $this->width;
        $document->height = $this->height;

        return $document;
    }

    public function scopeProposalImages($query)
    {
        return $query->where('is_proposal', 1);
    }
}

Document::deleted(function ($document) {
    $same_path_count = DB::table('documents')
        ->where('documents.account_id', $document->account_id)
        ->where('documents.path', $document->path)
        ->where('documents.disk', $document->disk)
        ->count();

    if (!$same_path_count) {
        $document->getDisk()->delete($document->path);
    }

    if ($document->preview) {
        $same_preview_count = DB::table('documents')
            ->where('documents.account_id', $document->account_id)
            ->where('documents.preview', $document->preview)
            ->where('documents.disk', $document->disk)
            ->count();
        if (!$same_preview_count) {
            $document->getDisk()->delete($document->preview);
        }
    }
});
