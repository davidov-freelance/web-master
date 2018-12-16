<?php
namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;

class FoodCreateRequest extends Request {

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize() { return true; }

    /**
     * Get the validation messages.
     *
     * @return array
     */
    public function messages()
    {
        return [
			'email.required'  => 'Email is required',
            'email.unique'    => 'Email already found in our system, please try another one.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $rules = [
            'title' 				=> 'required|string',
            'user_id' 			    => 'required|numeric|min:1',
            'category_id' 			=> 'required|numeric|min:1',
            'price' 				=> 'required|numeric',
            'min_portion' 	        => 'required|numeric',
            'location' 	            => 'required|string',
        ];
        

        return $rules;
    }
}