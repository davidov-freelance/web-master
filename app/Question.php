<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Question extends Authenticatable {
    
    protected $table = 'questions';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'question',
        'option_1',
        'option_2',
        'option_3',
        'option_4',
        'correct_answer',
        'release_datetime',
        'geek_streak',
        'genius_streak',
        'created_at',
        'updated_at',
    ];
    
    public function getStupidStreakAttribute() {
        return $this->geek_streak - $this->genius_streak;
    }
    
    public function getFormattedReleaseDatetimeAttribute() {
        return substr($this->release_datetime, 0, 10);
    }
}
