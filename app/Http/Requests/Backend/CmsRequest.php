<?php
namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;

class CmsRequest extends Request {

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
            'key.unique'    => 'You can not add same page again',
            'title.required'         => 'This field is required.',
            'body'          => 'This field is required.',
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
			'key' => 'required|unique:cms_pages,key',
			'title' => 'required|string',
			'body' => 'required|string',
        ];

        switch ( self::getMethod() ) {
            case 'PUT': // Edit/Update
                $rules['key'] = 'required|string';

                break;
            case 'POST': // New
                $rules['key'] = 'required|unique:cms_pages,key';
                break;
        }


        return $rules;
    }
}