<?php
namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;

class GroupCreateRequest extends Request {

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
        $rules = [
            'name' => 'required|string',
        ];

        return $rules;
    }

}