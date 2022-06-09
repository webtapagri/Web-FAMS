<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = "TBM_USER";
    public $timestamps = false;

    protected $fillable = [
        'username', 
        'name', 
        'email', 
        'role_id',
        'NIK',
        'gender',
        'img',
        'job_code',
        'area_code',
        'date_log',
        'time_log',
        'st_log',
        'status',
        'created_by',
        'updated_by',
        'deleted',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
}
