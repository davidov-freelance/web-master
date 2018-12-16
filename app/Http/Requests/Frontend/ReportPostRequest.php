<?php
namespace App\Http\Requests\Frontend;

use App\Http\Requests\Jsonify as Request;

class ReportPostRequest extends Request {

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
            'user_id'       => 'required|integer',
            'post_id'       => 'required|integer',
            'reason'        => 'required|string|max:250',
        ];
    }
}