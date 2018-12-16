<?php
namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;
use Carbon\Carbon;
use Config;

class UserRegisterRequest extends Request {
    
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
            'email.required' => 'Email is required',
            'email.unique' => 'Email already found in our system, please try another one.',
            'dob.before' => "User cannot be younger than $minUserAge years old.",
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
        
        return [
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'first_name' => 'required|string|max:25',
            'last_name' => 'required|string|max:25',
            'phone' => 'regex:/^\+[0-9]{11,12}$/',
            'dob' => 'date_format:Y-m-d|before:' . $minBirthDate
        ];
    }
}