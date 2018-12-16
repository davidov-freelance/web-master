<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;
use Config;

use App\Question;

use Illuminate\Support\Facades\DB;

class QuestionCreateRequest extends Request {

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
            'question'          => 'required|string',
            'option_1'          => 'required|string',
            'option_2'          => 'required|string',
            'option_3'          => 'required|string',
            'option_4'          => 'required|string',
            'correct_answer'    => 'required|numeric|between:1,4',
            'release_datetime'  => 'required|date|date_format:Y-m-d',
        ];
        
        return $rules;
    }

}
