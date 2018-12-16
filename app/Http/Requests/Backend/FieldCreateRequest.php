<?php
namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;

class FieldCreateRequest extends Request {

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
            'title.unique'    => 'Duplicate Field of Work title'
        ];
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [ 'title' => 'required|unique:fields_of_work,title|string', ];

        switch ( self::getMethod() ) {
            case 'PUT': // Edit/Update
                $rules['title'] = 'required|unique:fields_of_work,title,' . collect(self::segments())->last() . ',id';
                break;

        }

        return $rules;
    }

}