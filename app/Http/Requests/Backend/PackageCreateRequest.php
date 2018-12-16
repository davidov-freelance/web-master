<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;

class PackageCreateRequest extends Request {

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
            'ProductName.required' => 'Product Name Is Required',
            'ProductDescription.required' => 'Product Description Is Required',
            'Price.required' => 'Product Price Is Required.',
            'Quantity.required' => 'Product Quantity Is Required.',
            'ProductImage.required' => 'Product Image Is Required.',
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
            'price'              => 'required|numeric',
            'post_count'         => 'required|numeric',
            'short_description'           => 'required|string|max:220',
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
