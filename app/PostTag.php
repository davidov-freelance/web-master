<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class PostTag extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [ 'id','post_id','tag_id'  ];

    protected $appends = [ 'tag_name'  ];


    public function tag()
    {
        return $this->belongsTo('App\Tag','tag_id' );
    }

    public function getTagNameAttribute() {

        return  $this->tag->tag;

    }

}
