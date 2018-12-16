<?php
namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;
use Config;

class PostUpdateRequest extends Request {

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
            'post_id' 				=> 'required|integer',
            'user_id' 			    => 'required|numeric|min:1',
            'title' 			    => 'required|string|max:' . Config::get('constants.back.articleTitleMaxLength'),
            'description' 			=> 'required|string',
        ];
        

        return $rules;
    }
}