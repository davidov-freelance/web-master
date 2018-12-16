<?php
namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;

class DeleteCartItemRequest extends Request {

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
            'user_id.required'  => 'user id is required',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'user_id'           => 'required|integer|min:1',
			'cart_id'           => 'required|integer|min:1',
            'item_id'           => 'required|integer|min:1',
        ];
    }
}