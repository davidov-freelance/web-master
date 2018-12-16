<?php
namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;
use Config;

class AnswerQuestion extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $rules = [
            'user_id' => 'required|numeric|min:1',
            'answer_number' => 'required|numeric|min:-1|max:4|not_in:0'
        ];

        return $rules;
    }
}