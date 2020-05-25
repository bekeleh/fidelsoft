<?php

namespace App\Http\Requests;

class ScheduledReportRequest extends EntityRequest
{
    protected $entityType = ENTITY_SCHEDULED_REPORT;

    public function authorize()
    {
        return true;
    }

}
