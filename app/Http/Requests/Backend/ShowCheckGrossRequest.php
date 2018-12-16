<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;
use Config;

use App\Show;

class ShowCheckGrossRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() {
        return true;
    }
    
    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages() {
        return [
            'gross_dates.*.required' => 'The Week Number field is required.',
            'gross_dates.*.date_format' => 'The Week Number has an incorrect format.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {
        $rules = [
            'gross_dates.*' => 'required|date_format:Y-m-d',
        ];
        
        return $rules;
    }

}
