<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;

class PostChangeTrendingOrder extends Request {

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
            'image.required' => 'Image field is required , ( jpeg,jpg,png )',
            'image.mimes'    => 'max allow size is 1mb.',
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
            'posting_type'       => 'required|string',
            'description'        => 'required|string',
            'availability'       => 'required|string',
            'source_url'             => 'url'
        ];

        switch (self::getMethod()) {
            case 'PUT': // Edit/Update

                break;
            case 'POST': // New
                $rules['image'] =  'required|mimes:jpeg,jpg,png';
                break;
        }

        return $rules;
    }

}
