<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;
use App\FavouriteChef;


class Tag extends Authenticatable
{
   // use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    const TAG_TYPE_ADMIN            = 'admin';
    const TAG_TYPE_USER             = 'user';

    protected $fillable = [ 'tag','status','tag_type' ];

}
