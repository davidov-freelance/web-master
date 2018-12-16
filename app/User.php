<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;
use App\UserFollowing;


class User extends Authenticatable
{
   // use SoftDeletes;

    const ROLE_ADMIN                = 1;
    const ROLE_MEMBER               = 2;
    const STATUS_ACTIVE             = 1;
    const STATUS_BLOCKED            = 0;
    const DEVICE_TYPE_ANDROID       = 'android';
    const DEVICE_TYPE_IOS           = 'ios' ;
    const DEVICE_TYPE_WEB           = 'web';
    const SOCIALMEDIA_PLATFORM_FB   = 'fb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'role_id', 'first_name', 'last_name','full_name',
        'email', 'password','dob','country','city',
        'gender', 'phone','handle','field_of_work','headline_position','previous_position',
        'device_type','device_token', 'profile_picture','cover_photo','verification_code','is_verified',
        'social_media_id','social_media_platform','is_featured','admin_role','private_profile','notification_status','is_featured','field_of_work_id'
    ];


//    protected $visible = [
//        'id','role_id', 'first_name', 'last_name','full_name', 'email', 'phone','country','city','handle','field_of_work','headline_position','previous_position',
//        'device_type','device_token', 'profile_picture','profile_image','status','social_media_id','social_media_platform','created_at','updated_at'
//    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
//    protected $hidden = [ 'remember_token' ];
    protected $hidden = [ 'remember_token', 'password', 'device_token' ];

    protected $appends = ['profile_image','following_count','follower_count','article_count','event_count','is_following'];

    public function getProfileImageAttribute() {
       $profileImage =  asset(Config::get('constants.front.dir.profilePicPath') . ($this->profile_picture ?: Config::get('constants.front.default.profilePic')));
       return $profileImage;
    }

    public function isAdmin() {
        return (bool) (intval($this->attributes['role_id']) === self::ROLE_ADMIN);
    }

    public function getEventCountAttribute() {

        $condition['user_id'] = $this->id;
        $condition['post_type'] = Post::POST_TYPE_EVENT;

        $totalPostedEvents = Post::where($condition)->count();
        return $totalPostedEvents ;
    }

    public function getArticleCountAttribute() {

        $condition['user_id'] = $this->id;
        $condition['post_type'] = Post::POST_TYPE_ARTICLE;

        $totalPostedArticles = Post::where($condition)->count();
        return $totalPostedArticles ;
    }

    public function getFollowerCountAttribute() {

        $condition['followee_id'] = $this->id;
        $totalFollowers = UserFollowing::where($condition)->count();
        return $totalFollowers ;
    }

    public function getFollowingCountAttribute() {

        $condition['follower_id'] = $this->id;
        $totalFollowing = UserFollowing::where($condition)->count();
        return $totalFollowing ;
    }

    public function getIsFollowingAttribute() {

        $condition['follower_id']   =  isset( $_REQUEST['user_id']) ?  $_REQUEST['user_id'] : 0;
        $condition['followee_id']   = isset( $this->id) ? $this->id : 0;

        $visitor_id  =  isset( $_REQUEST['visitor_id']) ?  $_REQUEST['visitor_id'] : 0; // id of loggedin user

        if($visitor_id > 0 ) {
            $condition['follower_id']  = $visitor_id;
        }

        $data = UserFollowing::where($condition)->first();

        $favourite = 0; //  isset($this->favourite['id']) ? 1 : 0 ;

        if($data) {

            $favourite = 1;
        }
        return $favourite;
    }


    public function fow()
    {
        return $this->belongsTo('App\FieldOfWork','field_of_work_id' );
    }

    public function badges()
    {
        return $this->belongsToMany('App\Badge', 'user_badges', 'user_id', 'badge_id');
    }




//    public function getFieldOfWorkAttribute()
//    {
//            return $this->getFieldOfWorkaAttribute();
//    }


    public function getFieldOfWorkAttribute() {

        $type = '';

        if(isset($this->fow)) {

            $type = isset($this->fow['title']) ? $this->fow['title'] :  '';
        }
        return $type;
    }


}
