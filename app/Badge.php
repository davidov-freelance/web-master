<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Config;

class Badge extends Authenticatable
{
    protected $table = 'badges';

    protected $hidden = ['pivot', 'icon'];
    protected $appends = ['badge_icon'];

    protected $fillable = [
        'id',
        'name',
        'icon',
    ];

    public function getBadgeIconAttribute()
    {
        $imageUrl = $this->icon ? $this->icon : Config::get('constants.front.default.postPic');
        return asset(Config::get('constants.front.dir.badgesIconPath') . $imageUrl);
    }
}
