<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TM_BLANKO extends Model
{
    protected $table = 'TM_BLANKO';
    public $timestamps = false;

    protected $fillable = [
        "FILE_NAME",
        "FILE_DESCRIPTION",
        "JENIS_FILE",
        "DOC_SIZE",
        "FILE_UPLOAD",
    ]; 

    protected $primaryKey = 'ID';
}
