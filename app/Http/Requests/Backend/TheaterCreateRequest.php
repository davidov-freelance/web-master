<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;
use Config;

class TheaterCreateRequest extends Request {

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
            'name' => 'required|string',
            'capacity' => 'required|integer|min:1',
            'location' => 'required|string',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
//            'city' => 'required|string',
//            'state' => 'required|string',
            'zip' => 'required|regex:/^[0-9]{5}(\-[0-9]{4})?$/',
        ];
        return $rules;
    }

}
