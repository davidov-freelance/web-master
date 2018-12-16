<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class Group extends Authenticatable {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $table = 'groups';

    protected $fillable = [ 'id', 'name','created_at' ];


    public function member()
    {
        return $this->hasMany('App\GroupMember','group_id' );
    }

}
