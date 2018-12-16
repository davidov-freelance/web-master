<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class TagsNotificationSubscribers extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'tags_notification_subscribers';

    protected $fillable = [ 'id', 'user_id', 'tag_id', 'created_at' ];

    protected $appends = [ 'user_name', 'tag_name' ];

    public function user() {
        return $this->belongsTo('App\User','user_id' );
    }

    public function tag() {
        return $this->belongsTo('App\Tag','tag_id' );
    }

    public function getUserNameAttribute() {
        return  $this->user;
    }

    public function getTagNameAttribute() {
        return  $this->tag->tag;
    }

}
