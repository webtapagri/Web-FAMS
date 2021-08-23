<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TR_QRDATA extends Model
{
    protected $table = 'TR_QRDATA';
    public $timestamps = false;

    protected $fillable = [
        "qr_code"
    ]; 

    // protected $primaryKey = 'workflow_job_code';

}
