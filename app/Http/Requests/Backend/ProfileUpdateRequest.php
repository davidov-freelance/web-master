<?php
namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;

class ProfileUpdateRequest extends Request {

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
        $rules = [
			'email' => 'required|email|unique:users,email,' . \Auth::user()->id . ',id',
			'first_name' => 'required|string',
			'last_name' => 'string',
            'password' => 'string|min:6',
        ];

        return $rules;
    }
}