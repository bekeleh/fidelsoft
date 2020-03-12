<?php

namespace App\Ninja\Datatables;

class ProjectTaskDatatable extends TaskDatatable
{
    public $sortCol = 1;
    public function columns()
    {
        $columns = parent::columns();

        unset($columns[1]);

        return $columns;
    }
}
