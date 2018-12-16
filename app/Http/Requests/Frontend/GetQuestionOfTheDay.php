<?php
namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;
use Config;

class GetQuestionOfTheDay extends Request {

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
        ];

        return $rules;
    }
}