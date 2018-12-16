<?php
namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;

class ChangedPasswordRequest extends Request {

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
 /*   public function messages()
    {
        return [
            'email.unique'    => 'Email already found in our system, please try another one.',
        ];
    }*/

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()

    {

        $rules = [];

        switch ( self::getMethod() ) {
            case 'PUT': // Edit/Update
                $rules['old_password'] = 'string|min:6';
                $rules['password'] = 'string|min:6|confirmed';
                break;
           /* case 'POST': // New
                $rules['email'] = 'required|email';
                break;*/
        }
        return $rules;
    }

}