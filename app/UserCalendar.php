<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class UserCalendar extends Authenticatable {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'user_calender';

    protected $fillable = [ 'id', 'user_id', 'post_id','created_at' ];


    public function user()
    {
        return $this->belongsTo('App\User','user_id' );
    }

    public function post()
    {
        return $this->belongsTo('App\Pos','post_id' );
    }

}
