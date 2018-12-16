<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class ConversationThread extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [  'id','post_id','sender_id', 'receiver_id','message', 'created_at'];

    public function receiverInfo()
    {
        return $this->belongsTo('App\User','receiver_id' ,'id' );
    }

    public function productInfo()
    {
        return $this->belongsTo('App\Post','post_id' ,'id' );
    }

    public function senderInfo()
    {
        return $this->belongsTo('App\User','sender_id' ,'id' );
    }

}
