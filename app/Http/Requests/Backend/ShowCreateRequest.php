<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;
use Config;

class ShowCreateRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages() {
        return [
            'opening_night_at.after' => 'The "Opening Night Date and Time" cannot be selected before "Preview Date and Time".',
            'engagement_at.after' => 'The "Engagement Date and Time" cannot be selected before "Opening Night Date and Time".',
            'engagement_end.after' => 'The "Engagement End Date and Time" cannot be selected before "Engagement Date and Time".',
            'closing_at.after' => 'The "Closing Date and Time" cannot be selected before "Engagement Date and Time" or "Engagement End Date and Time".',
            'theater_id.required' => 'The theater field is required.',
            'schedule.*.start.required_with' => 'The beginning time is required when finishing time is present.',
            'schedule.*.start.date_format' => 'The beginning time value does not match the format H:i.',
            'schedule.*.end.date_format' => 'The finishing time value does not match the format H:i.',
            'schedule.*.end.after' => 'Time of beginning can not be later than finishing one',
            'roles[*].role.required' => 'The role field is required.',
            'roles[*].person[*].required' => 'You must enter at least one person\'s name.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = [
            'name' => 'required|string',
            'theater_id' => 'required|integer|min:1',
            'preview_at' => 'required|date|date_format:Y-m-d H:i',
            'opening_night_at' => 'required|date|date_format:Y-m-d H:i|after:preview_at',
            'engagement_at' => 'required|date|date_format:Y-m-d H:i|after:opening_night_at',
            'engagement_end' => 'date|date_format:Y-m-d H:i|after:engagement_at',
            'closing_at' => 'date|date_format:Y-m-d H:i|after:engagement_at',
            'schedule.*.start' => 'required_with:schedule.*.end|date_format:H:i',
            'schedule.*.end' => 'date_format:H:i|after:schedule.*.start|date_format:H:i',
        ];
        
        $roles = self::input('roles');
        
        foreach ($roles as $index => $roleData) {
            $roleIsEmpty = empty(trim($roleData['role']));
            $noPersons = true;
            
            foreach ($roleData['person'] as $personName) {
                if (!empty(trim($personName))) {
                    $noPersons = false;
                }
            }
            
            if ($roleIsEmpty && !$noPersons) {
                $rules["roles[$index].role"] = 'required|string';
            } elseif (!$roleIsEmpty && $noPersons) {
                $firstPersonIndex = key($roleData['person']);
                $rules["roles[$index].person[$firstPersonIndex]"] = 'required|string';
            }
        }
        
        $engagementEnd = self::input('engagement_end');
        $closingAt = self::input('closing_at');
        
        if (!empty($engagementEnd) && !empty($closingAt)) {
            if ($closingAt < $engagementEnd) {
                $rules["closing_at"] = 'after:engagement_end';
            }
        }
        
        
        
        if (self::getMethod() == 'POST') {
            $rules['image'] =  'required|mimes:jpeg,jpg,png';
        }
        
        return $rules;
    }

}
