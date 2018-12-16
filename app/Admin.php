<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class Admin extends Authenticatable
{
    use SoftDeletes;

    const ROLE_ADMIN = 1;
    const ROLE_MEMBER = 2;
    const STATUS_ACTIVE = 1;
    const STATUS_BLOCKED = 0;
    const DEVICE_TYPE_ANDROID = 'android';
    const DEVICE_TYPE_IOS = 'ios';
    const DEVICE_TYPE_WEB = 'web';
    const SOCIALMEDIA_PLATFORM_FB = 'fb';
    const ADMIN_ROLE_SUPER = 'super';
    const ADMIN_ROLE_SUB = 'sub';
    const ADMIN_ROLE_MODERATOR = 'moderator';
    const ADMIN_ROLE_EDITOR = 'editor';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $table = 'users';
    protected $fillable = [
        'role_id', 'first_name', 'last_name', 'full_name', 'email', 'password',
        'address', 'city', 'state', 'country', 'handle', 'headline_position',
        'phone', 'mobile_no', 'field_of_work', 'field_of_work_id', 'profile_picture', 'is_subadmin', 'is_available', 'admin_role',
    ];

    protected $appends = ['profile_image'];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function isAdmin()
    {
        return (bool)(intval($this->attributes['role_id']) === self::ROLE_ADMIN);
    }

    public function getProfileImageAttribute()
    {

        $profileImage = asset(Config::get('constants.front.dir.profilePicPath') . ($this->profile_picture ?: Config::get('constants.front.default.profilePic')));
        return $profileImage;
    }


}
