<?php
namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;

class ConversationAddRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(){ return true; }


    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
			'sender_id'     => 'required|integer',
            'receiver_id'   => 'required|integer',
            'message'       => 'required|string|max:250',

        ];
    }
}