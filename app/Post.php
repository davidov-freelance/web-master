<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;
use App\FavouritePost;
use App\UserCalendar;
use App\UserFollowing;

class Post extends Authenticatable {
    
    const POST_STATUS_APPROVED  = 'approved';
    const POST_STATUS_SCHEDULED  = 'scheduled';
    const POST_STATUS_PENDING   = 'pending';
    const POST_STATUS_SOLD      = 'sold';
    const POST_TYPE_ARTICLE     = 'article';
    const POST_TYPE_EVENT       = 'event';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'user_id', 'title', 'post_type','posting_type','location','latitude','longitude','start_date','end_date',
        'start_time','end_time','is_all_day','color','category_id','top_bar','availability','group_ids','status',
        'description', 'image','published_date', 'reposted_at', 'no_of_times_reposted','created_at','source_url', 'link_text'
    ];

//    protected $visible = [
//        'id', 'user_id', 'title', 'post_type',
//        'status', 'description', 'image', 'reposted_at', 'no_of_times_reposted','created_at','updated_at'
//    ];
    protected $appends = ['time_ago','post_image','is_like','likes','is_in_calendar','share_url'];
    
    public function publisher()
    {
        return $this->belongsTo('App\User','user_id' );
    }
    
    public function tags()
    {
        return $this->hasMany('App\PostTag','post_id' );
    }
    
    public function getTagsIdsAttribute()
    {
        if (empty($this->tags)) {
            return false;
        }
        
        $tagsIds = [];
        
        foreach ($this->tags as $tagData) {
            // (int) need for multi select
            $tagsIds[] = (int) $tagData['tag_id'];
        }
        return $tagsIds;
    }
    
    
    public function favourites()
    {
        return $this->hasMany('App\FavouritePost','post_id' );
    }
    
    public function comments()
    {
        return $this->hasMany('App\PostComment','post_id' );
    }
    
    public function show_ids()
    {
        return $this->hasMany('App\ArticleShow' , 'post_id');
    }
    
    public function shows()
    {
        return $this->belongsToMany('App\Show', 'article_shows', 'post_id', 'show_id');
    }
    
    public function getShowIdValuesAttribute()
    {
        if (empty($this->show_ids)) {
            return false;
        }
        
        $showsIds = [];
        
        foreach ($this->show_ids as $showData) {
            // (int) need for multi select
            $showsIds[] = (int) $showData['show_id'];
        }
        return $showsIds;
    }
    
    public function getPostImageAttribute()
    {
        $url =  asset(Config::get('constants.front.dir.postsImagePath') . ($this->image ?: Config::get('constants.front.default.postPic')));
        return $url;
    }
    
    public function getIsLikeAttribute()
    {
        $user_id  =  isset( $_REQUEST['user_id']) ?  $_REQUEST['user_id'] : 0;
        $postId  = isset( $this->id) ? $this->id : 0;
        
        $data = FavouritePost::where('post_id',$postId)->where('user_id',$user_id)->first();
        
        $favourite = 0; //  isset($this->favourite['id']) ? 1 : 0 ;
        
        if($data) {
            
            $favourite = 1;
        }
        return $favourite;
    }
    
    public function getIsInCalendarAttribute()
    {
        $user_id  =  isset( $_REQUEST['user_id']) ?  $_REQUEST['user_id'] : 0;
        $visitor_id  =  isset( $_REQUEST['visitor_id']) ?  $_REQUEST['visitor_id'] : 0; // id of loggedin user
        
        if($visitor_id > 0 ) {
            $user_id  = $visitor_id;
        }
        
        $postId  = isset( $this->id) ? $this->id : 0;
        $publisherId  = isset( $this->user_id) ? $this->user_id : 0;
        
        $data = UserCalendar::where('post_id',$postId)->where('user_id',$user_id)->first();
        
        $favourite = 0;
        
        if(is_null($data)){
            
            $data = UserFollowing::where('follower_id',$user_id)
                ->where('followee_id',$publisherId)
                ->first();
            
        }
        
        if($data) {
            
            $favourite = 1;
        }
        return $favourite;
    }
    
    public function getLikesAttribute()
    {
        return count($this->favourites);
    }
    
    public function getTimeAgoAttribute()
    {
        // Time Ago Calculation using published at
        $timeago = $this->getDateDifference($this->published_date);
        return $timeago;
    }
    
    public function getDateDifference($date)
    {
        $currentDate = date("Y-m-d H:i:s");
        $date2 = date_create($date);
        $currentDateNew = date_create($currentDate);
        
        $diff34 = date_diff($currentDateNew, $date2);
        
        
        $days = $diff34->d;
        
        $months = $diff34->m;
        
        $years = $diff34->y;
        
        $hours = $diff34->h;
        
        //accesing minutes
        $minutes = $diff34->i;
        
        //accesing seconds
        $seconds = $diff34->s;
        
        $timeAgo = '';
        if ($days > 0) {
            $timeAgo .= date("Y-m-d", strtotime($date)); //$timeAgo .= $days.' days';
        }
        
        if ($hours > 0 && $timeAgo == '') {
            $hoursWord = $hours == 1 ? 'hour' : 'hours';
            $timeAgo .= "$hours $hoursWord ago";
        }
        
        if ($minutes > 0 && $timeAgo == '') {
            $minutesWord = $minutes == 1 ? 'minute' : 'minutes';
            $timeAgo .= "$minutes $minutesWord ago";
        }
        
        if (empty($timeAgo)) {
            $timeAgo.= 'Just Now';
        }
        
        return $timeAgo;
    }
    
    
    public function getShareUrlAttribute()
    {
        return url('/').'/post/'.$this->post_type.'/'.$this->id;
    }
}
