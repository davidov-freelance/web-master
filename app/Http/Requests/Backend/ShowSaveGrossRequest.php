<?php

namespace App\Http\Requests\Backend;

use App\Http\Requests\Request as Request;
use Config;

use App\Show;

class ShowSaveGrossRequest extends Request {

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
            'gross.*.end_week_date.required_with' => 'The Week Number field is required.',
            'gross.*.end_week_date.distinct' => 'The Week Number field has a duplicate value.',
            'gross.*.end_week_date.date_format' => 'The Week Number has an incorrect format.',
            'gross.*.end_week_date.in' => "The Week Number must be $startWeekDayName.",
            'gross.*.end_week_date.between' => 'The date has to be selected in a period between show opening and closing dates.',
            'gross.*.attendees_amount.required_with' => 'The Number of Attendees field is required.',
            'gross.*.attendees_amount.integer' => 'The Number of attendees must be an integer.',
            'gross.*.attendees_amount.min' => 'The Number of attendees must be at least 0.',
            'gross.*.performances_amount.required_with' => 'The Number of Performances field is required.',
            'gross.*.performances_amount.integer' => 'The Number of Performances must be an integer.',
            'gross.*.performances_amount.min' => 'The Number of Performances must be at least 0.',
            'gross.*.performances_amount.max' => 'The Number of Performances must be no more than 999.',
            'gross.*.earnings.required_with' => 'The Earnings field is required.',
            'gross.*.earnings.numeric' => 'The Earnings must be a number.',
            'gross.*.earnings.min' => 'The Earnings must be at least 0.',
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules() {

        ini_set('max_execution_time', 180);

        $rules = [
            'gross.*.end_week_date' => 'required_with:gross.*.earnings,gross.*.attendees_amount,gross.*.performances_amount|distinct|date_format:Y-m-d',
            'gross.*.attendees_amount' => 'required_with:gross.*.end_week_date,gross.*.earnings,gross.*.performances_amount|integer|min:0',
            'gross.*.performances_amount' => 'required_with:gross.*.attendees_amount,gross.*.end_week_date,gross.*.earnings|integer|min:0|max:999',
            'gross.*.earnings' => 'required_with:gross.*.end_week_date,gross.*.attendees_amount,gross.*.performances_amount|numeric|min:0',
        ];

        $grossData = self::input('gross');

        // Get show if from url parameters
        $pathParams = explode('/', self::path());

        if (count($pathParams)) {
            $showId = (int) end($pathParams);
        } else {
            $rules['show_id'] = 'required';
            return $rules;
        }

        $showData = Show::select('preview_at', 'closing_at')->where('id', $showId)->first();

        if (!$showData) {
            $rules['show_id'] = 'required';
            return $rules;
        }

        $startEndWeekDates = get_start_end_week_dates($showData->preview_at, $showData->closing_at, true);

        if ($grossData) {

            $grossWeekDay = (int) Config::get('constants.front.businessGrossDayIsMonday');

            $grossDayName = get_week_name_by_number($grossWeekDay);

            foreach ($grossData as $index => $gross) {
                // This is necessary to check the existence of the date if other fields filled
                if (!check_date($gross['end_week_date'], 'Y-m-d')) {
                    continue;
                }

                $weekDayNumber = date('w', strtotime($gross['end_week_date']));

                // All week days must be Sundays (Mondays)
                if ($weekDayNumber != $grossWeekDay) {
                    $rules["gross.$index.end_week_date"] = "in:$grossDayName";
                    continue;
                }

                if ($gross['end_week_date'] < $startEndWeekDates['start_date'] || $gross['end_week_date'] > $startEndWeekDates['end_date']) {
                    $rules["gross.$index.end_week_date"] = "between:{$startEndWeekDates['start_date']},{$startEndWeekDates['end_date']}";
                    continue;
                }
            }
        }
        
        return $rules;
    }

}
