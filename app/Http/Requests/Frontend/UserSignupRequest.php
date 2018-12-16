<?php
namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;

class UserSignupRequest extends Request {

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
			'mobile_no.required'  => 'Number is required',
            'mobile_no.unique'    => 'Mobile Number already found in our system, please try another one.',
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
            //'mobile_no'         => 'required|numeric|unique:users,mobile_no',
            'mobile_no'         => 'required|numeric',
        ];
    }
}