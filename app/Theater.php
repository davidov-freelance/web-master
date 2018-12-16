<?php

namespace App;

use Config;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Theater extends Authenticatable {
    
    protected $table = 'theaters';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'capacity',
        'location',
        'address',
        'city',
        'state',
        'zip',
        'latitude',
        'longitude',
    ];
}
