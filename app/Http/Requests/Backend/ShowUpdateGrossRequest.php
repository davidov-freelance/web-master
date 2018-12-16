<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;
use Config;

use App\Show;
use App\ShowGross;

class ShowUpdateGrossRequest extends Request {

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
        $startWeekDayName = get_week_name_by_number(intval(Config::get('constants.front.businessGrossDayIsMonday')));

        return [
            'end_week_date.required' => 'The Week Number field is required.',
            'end_week_date.not_in' => 'The Week Number field has a duplicate value.',
            'end_week_date.date_format' => 'The Week Number has an incorrect format.',
            'end_week_date.in' => "The Week Number must be $startWeekDayName.",
            'end_week_date.between' => 'The date has to be selected in a period between show opening and closing dates.',
            'attendees_amount.required' => 'The Number of Attendees field is required.',
            'attendees_amount.integer' => 'The Number of attendees must be an integer.',
            'attendees_amount.min' => 'The Number of attendees must be at least 0.',
            'performances_amount.required' => 'The Number of Performances field is required.',
            'performances_amount.integer' => 'The Number of Performances must be an integer.',
            'performances_amount.min' => 'The Number of Performances must be at least 0.',
            'performances_amount.max' => 'The Number of Performances must be no more than 999.',
            'earnings.required' => 'The Earnings field is required.',
            'earnings.numeric' => 'The Earnings must be a number.',
            'earnings.min' => 'The Earnings must be at least 0.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        $rules = [
            'end_week_date' => 'required|date_format:Y-m-d',
            'attendees_amount' => 'required|integer|min:0',
            'performances_amount' => 'required|integer|min:0|max:999',
            'earnings' => 'required|numeric|min:0',
        ];

        $gross = self::all();

        $showData = Show::select('preview_at', 'closing_at')->where('id', $gross['show_id'])->first();

        if (!$showData) {
            $rules['show_id'] = 'required';
            return $rules;
        }

        $grossDayNumber = (int) Config::get('constants.front.businessGrossDayIsMonday');
        $grossWeekDay = (int) Config::get('constants.front.businessGrossDayIsMonday');
        $weekDayNumber = date('w', strtotime($gross['end_week_date']));

        // All week days must be Sundays (Mondays)
        if ($weekDayNumber != $grossDayNumber) {
            $grossDayName = get_week_name_by_number($grossWeekDay);
            $rules['end_week_date'] = "in:$grossDayName";
            return $rules;
        }

        $startEndWeekDates = get_start_end_week_dates($showData->preview_at, $showData->closing_at, true);
        
        if ($gross['end_week_date'] < $startEndWeekDates['start_date'] || $gross['end_week_date'] > $startEndWeekDates['end_date']) {
            
            $rules['end_week_date'] = "between:{$startEndWeekDates['start_date']},{$startEndWeekDates['end_date']}";
            return $rules;
        }

        // Get duplicate gross            
        $duplicateGross = ShowGross::where('end_week_date', '=', $gross['end_week_date'])
            ->where('show_id', '=', $gross['show_id'])
            ->where('id', '!=', $gross['id'])
            ->first();
    
        if ($duplicateGross) {
            $rules['end_week_date'] = 'not_in:' . $gross['end_week_date'];
        }
        
        return $rules;
    }

}
