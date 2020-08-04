<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class JOBS extends Model
{
    protected $table = 'jobs';
    public $timestamps = false;

    protected $fillable = [
        "queue",
        "payload",
        "attempts",
        "reserved_at",
        "available_at",
        "created_at"
    ]; 

    protected $primaryKey = 'id';

}
