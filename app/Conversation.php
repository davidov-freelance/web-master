<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class Conversation extends Authenticatable
{

    const POST_STATUS_APPROVED   = 'approved';
    const POST_STATUS_PENDING    = 'pending';
    const POST_STATUS_SOLD       = 'sold';
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */

    protected $fillable = [
        'id','post_id','sender_id', 'conversation_id', 'receiver_id','message',
    ];

    public function receiverInfo()
    {
        return $this->belongsTo('App\User','receiver_id' ,'id' );
    }

    public function senderInfo()
    {
        return $this->belongsTo('App\User','sender_id' ,'id' );
    }




}
