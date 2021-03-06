<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;

class FoodCreateRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages() {
        return [
            'title.required' => 'title is required',

        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $rules = [
            'title'              => 'required|string|max:50',
            'category_id'        => 'required|integer|min:1',
            'price'              => 'required|numeric',
            'min_portion'        => 'required|integer|min:1',
            'location'           => 'required|string',
        ];

//        switch (self::getMethod()) {
//            case 'PUT': // Edit/Update
//                $rules['email'] = 'required|email|unique:users,email,' . collect(self::segments())->last() . ',id';
//                $rules['password'] = 'string|min:6';
//                break;
//            case 'POST': // New
//                $rules['password'] = 'required|string|min:6';
//                break;
//        }

        return $rules;
    }

}
