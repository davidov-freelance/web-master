<?php
namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;

class CategoryRequest extends Request {

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
            'CategoryName.unique'    => 'Duplicate Category Name'
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
			'CategoryName' => 'required|string',
			
        ];

        switch ( self::getMethod() ) {
            case 'PUT': // Edit/Update
                $rules['CategoryName'] = 'string|min:3';
                break;
            case 'POST': // New
                $rules['CategoryName'] = 'required|string|min:3';
                break;
        }

        return $rules;
    }
}