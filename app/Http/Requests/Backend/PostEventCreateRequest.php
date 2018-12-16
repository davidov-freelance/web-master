<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;
use Config;

class PostEventCreateRequest extends Request {

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

        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $rules = [
            'title'             => 'required|string|max:' . Config::get('constants.back.articleTitleMaxLength'),
            'description'       => 'required|string',
            'start_date'        => 'required|string',
            'end_date'          => 'required|string',
            'end_date'          => 'required|string',
            'published_date'    => 'required_if:status,scheduled|date',
            'published_time'    => 'required_if:status,scheduled|min:5|max:8',
            'location'              => 'required|string',
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
                $rules['image'] =  'required|mimes:jpeg,jpg,png';
                break;
        }


        return $rules;
    }

}
