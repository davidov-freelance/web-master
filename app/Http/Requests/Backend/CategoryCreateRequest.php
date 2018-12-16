<?php
namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;
//use Illuminate\Validation\Rule;


class CategoryCreateRequest extends Request {

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
            'category_name.unique'    => 'Duplicate Category Name'
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
			'category_name' => 'required|string',
        ];

//        switch ( self::getMethod() ) {
//            case 'PUT': // Edit/Update
//                $rules['image'] = 'image|mimes:jpg,png,jpeg|max:2048';
//                break;
//            case 'POST': // New
//                $rules['image'] = 'required|image|mimes:jpg,png,jpeg|max:2048';
//                break;
//        }

        return $rules;
    }

}