<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Authenticatable
{
    //use SoftDeletes;

    const ROLE_ADMIN = 1;
    const ROLE_MEMBER = 2;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'receiver_id', 'sender_id', 'is_read', 'message','action_type','action_id',
    ];

    protected $appends = ['time_ago'];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */

    public function receiverInfo()
    {
        return $this->belongsTo('App\User','receiver_id' ,'id' );
    }

    public function senderInfo()
    {
        return $this->belongsTo('App\User','sender_id' ,'id' );
    }

    public function getTimeAgoAttribute() {

        $timeago =  $this->getDateDifference($this->created_at);
        return $timeago;
    }
    public function getDateDifference($date)
    {

        $currentDate        = date("Y-m-d H:i:s");
        $date2               = date_create($date);
        $currentDateNew     = date_create($currentDate);

        $diff34 = date_diff($currentDateNew, $date2);


        $days = $diff34->d;

        $months = $diff34->m;

        $years = $diff34->y;

        $hours = $diff34->h;
//accesing minutes
        $minutes = $diff34->i;
//accesing seconds
        $seconds = $diff34->s;

        $timeAgo            = '';
        if($days > 0)       $timeAgo .= $days.' days';
        if($hours > 0 && $timeAgo=='')      $timeAgo .= $hours. ' hours ';
        if($minutes > 0 && $timeAgo=='')    $timeAgo .= $minutes. ' minutes';

        if(empty($timeAgo))  { $timeAgo.= 'just now'; } else { $timeAgo.= ' ago'; }

        return $timeAgo;


    }
    


}
