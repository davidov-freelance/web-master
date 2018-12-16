<?php
namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;

class PushNotificationRequest extends Request {

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
            'key.unique'    => 'You can not add same page again',
            'title.required'         => 'This field is required.',
            'uids[].required'          => ' This field is required.',
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
			'message' => 'required|string|max:150'

        ];

        return $rules;
    }
}