<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;
use Carbon\Carbon;
use Config;

class UserRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
    
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        $minUserAge = Config::get('constants.back.minUserAge');
        return [
            'email.unique' => 'Email already found in our system, please try another one.',
            'dob.before' => "User cannot be younger than $minUserAge years old.",
            'badges.*.badge_id.required' => "Badge id is required.",
            'badges.*.badge_id.distinct' => "You can not select the same badges.",
            'badges.*.badge_amount.required' => "Amount is required.",
            'badges.*.badge_amount.integer' => "Amount must be an integer.",
            'badges.*.badge_amount.min' => "Amount must be at least 1.",
        ];
    }
    
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        // The minimum age of the user
        $minUserAge = Config::get('constants.back.minUserAge');
        $carbon = new Carbon();
        $minBirthDate = $carbon->subYears($minUserAge)->format('Y-m-d');
        
        $rules = [
            'first_name' => 'required|min:2|max:30',
            'last_name' => 'required|min:2|max:30',
            'email' => 'required|email|unique:users,email',
            'mobile_no' => 'numeric',
            'dob' => 'date_format:Y-m-d|before:' . $minBirthDate,
            'badges.*.badge_id' => 'required|integer|distinct|min:1',
            'badges.*.badge_amount' => 'required|integer|min:1',
        ];

//        $data = self::all();

//        exit('<pre>' . print_r($data, 1) . '</pre>');
        
        switch (self::getMethod()) {
            case 'PUT': // Edit/Update
                $rules['email'] = 'required|email|unique:users,email,' . collect(self::segments())->last() . ',id';
                $rules['password'] = 'string|min:6';
                break;
            case 'POST': // New
                $rules['password'] = 'required|string|min:6';
                break;
        }
        
        return $rules;
    }
}