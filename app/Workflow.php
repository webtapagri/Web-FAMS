<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $table = 'TR_WORKFLOW';
    public $timestamps = false;

    protected $fillable = [
        "workflow_code",
        "workflow_name",
        "menu_code"
    ]; 

    protected $primaryKey = 'workflow_code';

}
