<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TR_WORKFLOW_DETAIL extends Model
{
    protected $table = 'TR_WORKFLOW_DETAIL';
    public $timestamps = false;

    protected $fillable = [
        "workflow_detail_code",
        "workflow_code",
        "workflow_group_name",
        "seq",
        "description"
    ]; 

    protected $primaryKey = 'workflow_detail_code';

}
