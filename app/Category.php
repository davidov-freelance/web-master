<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class Category extends Authenticatable
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'category_name','status','image','sort_order' ];
    protected $visible = ['id','category_name','status','image', 'image_url'];
    protected $appends = ['image_url'];

    public function getImageUrlAttribute() {

        return  asset(Config::get('constants.front.dir.categoryImagePath') . ($this->image ?: Config::get('constants.front.default.profilePic')));
    }

}
