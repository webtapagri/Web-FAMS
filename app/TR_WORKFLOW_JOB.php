<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TR_WORKFLOW_JOB extends Model
{
    protected $table = 'TR_WORKFLOW_JOB';
    public $timestamps = false;

    protected $fillable = [
        "workflow_job_code",
        "workflow_detail_code",
        "id_role",
        "seq",
        "operation",
        "lintas"
    ]; 

    protected $primaryKey = 'workflow_job_code';

}
