<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cms extends Model
{

    protected $table = 'cms_pages';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'key', 'title', 'body',
    ];




}
