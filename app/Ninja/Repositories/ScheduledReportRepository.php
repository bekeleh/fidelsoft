<?php

namespace App\Ninja\Repositories;

use App\Models\ScheduledReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Log;

class ScheduledReportRepository extends BaseRepository
{
    private $model;

    public function __construct(ScheduledReport $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\ScheduledReport';
    }

    public function all()
    {
        return ScheduledReport::Scope()->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('scheduled_reports')
            ->where('scheduled_reports.account_id', '=', $accountId)
//            ->where('scheduled_reports.deleted_at', '=', null)
            ->select(
                'scheduled_reports.public_id',
                'scheduled_reports.user_id',
                'scheduled_reports.ip',
                'scheduled_reports.frequency',
                'scheduled_reports.send_date',
                'scheduled_reports.created_by',
                'scheduled_reports.updated_by',
                'scheduled_reports.deleted_by',
                'scheduled_reports.created_at',
                'scheduled_reports.updated_at',
                'scheduled_reports.deleted_at',
                'scheduled_reports.is_deleted'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('scheduled_reports.ip', 'like', '%' . $filter . '%')
                    ->orwhere('scheduled_reports.frequency', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_SCHEDULED_REPORT);

        return $query;
    }

    public function save($data, $ScheduledReport = false)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;
        if ($ScheduledReport) {
            $ScheduledReport->updated_by = Auth::user()->username;

        } elseif ($publicId) {
            $ScheduledReport = ScheduledReport::scope($publicId)->withArchived()->firstOrFail();
            Log::warning('Entity not set in schedule report repo save');
        } else {
            $ScheduledReport = ScheduledReport::createNew();
            $ScheduledReport->created_by = Auth::user()->name;
        }

        $ScheduledReport->fill($data);

        $ScheduledReport->save();

        return $ScheduledReport;
    }
}
