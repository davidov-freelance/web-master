<?php

namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;
use App\Http\Traits\JWTUserTrait;

class EditProfileRequest extends Request
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
        return [
            'email.unique'             => 'Email already found in our system, please try another one.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $userInstance = JWTUserTrait::getUserInstance();

        return [
            'email'           => 'email|unique:users,email,'.$userInstance->id.',id',
            'first_name'      => 'string',
            'last_name'       => 'string',
            'country'         => 'string'
        ];
    }
}
