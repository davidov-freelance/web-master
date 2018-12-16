<?php

namespace App;

use Config;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Show extends Authenticatable {
    
    protected $table = 'shows';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'theater_id',
        'name',
        'image',
        'schedule',
        'roles',
        'preview_at',
        'opening_night_at',
        'engagement_at',
        'engagement_end',
        'closing_at',
    ];
    
    protected $casts = [
        'roles' => 'array',
        'schedule' => 'array',
    ];
    
    protected $appends = [
        'show_image'
    ];
    
    public function theater()
    {
        return $this->belongsTo('App\Theater');
    }
    
    public function gross()
    {
        return $this->hasMany('App\ShowGross', 'show_id')
            ->select('id', 'show_id', 'end_week_date', 'attendees_amount', 'performances_amount', 'earnings')
            ->orderBy('end_week_date', 'ASC');
    }
    
    public function news()
    {
        return $this->belongsToMany('App\Post', 'article_shows', 'show_id', 'post_id')
            ->select(
                'posts.id',
                'posts.title',
                'posts.description',
                'posts.published_date',
                'posts.image',
                'posts.created_at'
            );
    }
    
    public function setScheduleAttribute($scheduleData)
    {
        $scheduleIsEmpty = true;
        foreach ($scheduleData as $scheduleDatum) {
            if (!empty($scheduleDatum['start'])) {
                $scheduleIsEmpty = false;
                break;
            }
        }
        $this->attributes['schedule'] = $scheduleIsEmpty ? '' : json_encode($scheduleData);
    }

    public function setRolesAttribute($rolesData)
    {
        // Remapping indexes
        $remappedRoles = [];
        $rolesIsEmpty = true;
        foreach ($rolesData as $roleData) {
            // If the role field is filled then the persons field also is
            if (empty($roleData['role'])) {
                continue;
            }
            
            if ($rolesIsEmpty && !empty($roleData['role'])) {
                $rolesIsEmpty = false;
            }
            
            $roleData['person'] = array_values(array_filter($roleData['person'], function($person) {
                return trim($person);
            }));
            
            $remappedRoles[] = $roleData;
        }
        
        $this->attributes['roles'] = $rolesIsEmpty ? '' : json_encode($remappedRoles);
    }
    
    public function getShowImageAttribute()
    {
        $imageUrl = $this->image ? $this->image : Config::get('constants.front.default.postPic');
        return asset(Config::get('constants.front.dir.showsImagePath') . $imageUrl);
    }
    
    public function getFormatedPreviewAtAttribute()
    {
        return $this->_formatedDate($this->preview_at);
    }
    
    public function getFormatedOpeningNightAtAttribute()
    {
        return $this->_formatedDate($this->opening_night_at);
    }
    
    public function getFormatedEngagementAtAttribute()
    {
        return $this->_formatedDate($this->engagement_at);
    }
    
    public function getFormatedEngagementEndAttribute()
    {
        return $this->_formatedDate($this->engagement_end);
    }
    
    public function getFormatedClosingAtAttribute()
    {
        return $this->_formatedDate($this->closing_at);
    }
    
    private function _formatedDate($date)
    {
        return substr($date, 0, 16);
    }
}
