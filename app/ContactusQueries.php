<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Config;

class ContactusQueries extends Authenticatable
{
    protected $table = 'contactus_queries';

    protected $fillable = [ 'id','user_id','message'];


    public function user()
    {
        return $this->belongsTo('App\User','user_id');
    }


}
