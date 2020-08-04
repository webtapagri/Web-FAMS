<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FAILED_JOBS extends Model
{
    protected $table = 'failed_jobs';
    public $timestamps = false;

    protected $fillable = [
        "connection",
        "queue",
        "payload",
        "exception",
        "failed_at"
    ]; 

    protected $primaryKey = 'id';

}
