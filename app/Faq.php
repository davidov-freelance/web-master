<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{

    protected $table = 'faq_contents';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [ 'key', 'heading', 'description' ];
    

}
