<?php

namespace App;


use Illuminate\Database\Eloquent\Model;

class Log extends Model {

    protected $table = 'logs';
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'log_type', 'log_generator', 'log_action_id', 'log_content'
    ];


    public function user()
    {
        return $this->belongsTo('App\User', 'log_generator');
    }

}
