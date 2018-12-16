<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;


class BadgeCreateRequest extends Request {
    public function authorize() {
        return true;
    }

    public function messages() {
        return [];
    }

    public function rules()
    {
        $rules = [
			'name' => 'required|string',
        ];

        if (self::getMethod() == 'POST') {
            $rules['icon'] =  'required|mimes:jpeg,jpg,png';
        }

        return $rules;
    }
}
