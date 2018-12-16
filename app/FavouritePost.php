<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class FavouritePost extends Authenticatable
{
   // use SoftDeletes;

    protected $fillable = [
        'user_id', 'post_id'
    ];

}
