<?php

namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;
use Config;

class UserVerifyRequest extends Request {

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
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'code'      => 'required|digits:' . Config::get('constants.front.verificationCodeLength'),
            'user_id'   => 'required|numeric|min:1',
        ];
    }
}
