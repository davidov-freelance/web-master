<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class ReportPost extends Authenticatable {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'reports';

    protected $fillable = [ 'id', 'user_id','post_id','reason','created_at' ];


    public function user()
    {
        return $this->hasMany('App\User','user_id' );
    }

    public function post()
    {
        return $this->hasMany('App\Post','post_id' );
    }

}
