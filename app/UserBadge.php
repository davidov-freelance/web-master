<?php

namespace App;

use Config;
use Illuminate\Foundation\Auth\User as Authenticatable;

class UserBadge extends Authenticatable {
    
    protected $table = 'user_badges';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'badge_id',
        'badge_amount',
        'created_at'
    ];
}
