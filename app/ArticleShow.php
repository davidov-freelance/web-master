<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class ArticleShow extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [ 'post_id', 'show_id'  ];
}
