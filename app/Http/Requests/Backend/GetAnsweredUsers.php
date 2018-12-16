<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;
use Config;

class GetAnsweredUsers extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $rules = [
            'id' 			    => 'required|numeric|min:1',
            'show_correct'      => 'required|numeric|min:0|max:1',
        ];

        return $rules;
    }

}
