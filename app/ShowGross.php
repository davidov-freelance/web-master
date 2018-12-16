<?php

namespace App;

use Config;
use Illuminate\Foundation\Auth\User as Authenticatable;

class ShowGross extends Authenticatable {
    
    protected $table = 'show_gross';

    public $timestamps = false;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'show_id',
        'end_week_date',
        'attendees_amount',
        'performances_amount',
        'earnings',
    ];
}
