<?php
namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;

class TagCreateRequest extends Request {

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
            'tag.unique'    => 'Duplicate Tag Name'
        ];
    }



    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [ 'tag' => 'required|unique:tags,tag|string', ];

        switch ( self::getMethod() ) {
            case 'PUT': // Edit/Update
                $rules['tag'] = 'required|unique:tags,tag,' . collect(self::segments())->last() . ',id';
                break;

        }

        return $rules;
    }

}