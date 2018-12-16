<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Authenticatable
{
    

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','CategoryId','ProductName','ProductDescription','Price','Quantity','ProductImage'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */





}
