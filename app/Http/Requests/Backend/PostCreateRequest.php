<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;
use Config;

class PostCreateRequest extends Request
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
            'ProductName.required' => 'Product Name Is Required',
            'ProductDescription.required' => 'Product Description Is Required',
            'Price.required' => 'Product Price Is Required.',
            'image.required' => 'Image field is required , ( jpeg,jpg,png )',
            'image.mimes' => 'max allow size is 1mb.',
            'notification_description.required' => 'The notification message field is required.',
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
            'user_id' => 'required|exists:users,id',
            'title' => 'required|string|max:' . Config::get('constants.back.articleTitleMaxLength'),
            'posting_type' => 'required|string',
            'description' => 'required|string',
            'availability' => 'required|string',
            'published_date' => 'required_if:status,scheduled|date',
            'published_time' => 'required_if:status,scheduled|regex:/[0-2][0-9]:[0-5][0-9]$/',
            'source_url' => 'url',
            'show_ids' => 'array',
            'show_ids.*' => 'integer',
        ];

        $postData = self::all();

        if (isset($postData['send_notification']) && $postData['posting_type'] == 'admin') {
            $rules['notification_title'] = 'required';
            $rules['notification_description'] = 'required';
        }

        switch (self::getMethod()) {
            case 'PUT': // Edit/Update

                break;
            case 'POST': // New
                $rules['image'] = 'required|mimes:jpeg,jpg,png';
                break;
        }

        return $rules;
    }

}
