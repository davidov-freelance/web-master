<?php

namespace App;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class UserFollowing extends Authenticatable
{

    protected $table = 'user_followings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'follower_id','followee_id' ];
    

}
