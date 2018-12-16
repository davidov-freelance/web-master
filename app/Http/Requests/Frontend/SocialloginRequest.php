<?php
namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;

class SocialLoginRequest extends Request {

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
        return [
			'email.required'  => 'Email is required',
            'email.unique'    => 'Email already found in our system, please try another one.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'social_media_id'       => 'required',
			'social_media_platform' => 'required|string',
            'first_name'            => 'string|max:25',
            'last_name'             => 'string|max:25',
            'mobile_no'             => 'integer',
        ];
    }
}