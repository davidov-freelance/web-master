<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class GroupMember extends Authenticatable {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'group_members';

    protected $fillable = [ 'id', 'user_id','group_id','created_at' ];


    public function user()
    {
        return $this->belongsTo('App\User','user_id' );
    }

    public function group()
    {
        return $this->belongsTo('App\Group','group_id' );
    }
    
}
