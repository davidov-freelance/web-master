<?php
namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;

class UpdateDeviceRequest extends Request {

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
			'user_id'        => 'required|integer',
			'device_token'   => 'required|string|min:10',
            'device_type'      => 'required|string|min:3|max:10',
        ];
    }
}