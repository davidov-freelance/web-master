<?php

function backend_view($file) {
    return call_user_func_array( 'view', ['backend/' . $file] + func_get_args() );
}

function backend_form_view($file, $options) {
    $options['formLayout'] = $file;
    return backend_view('layouts.form_page', $options);
}

function backend_path($uri='') {
    return public_path( 'backend/' . $uri );
}

function backend_asset($uri='') {
    return asset( 'public/backend/' . ltrim($uri,'/') );
}

function backend_url($uri='/') {
    return call_user_func_array( 'url', ['backend/' . ltrim($uri,'/')] + func_get_args() );
}

function constants($key) {
    return config( 'constants.' . $key );
}

function frontend_view($file) {
    return call_user_func_array( 'view', ['frontend/' . $file] + func_get_args() );
}

function front_asset($uri='') {
    return asset( 'public/frontend/' . ltrim($uri,'/') );
}

function generate_digits($length = 10) {
    return rand(pow(10, $length - 1), pow(10, $length) - 1);
}

function check_date($verifiableDate, $format = 'Y-m-d H:i') {
    $date = DateTime::createFromFormat($format, $verifiableDate);
    return $date && $date->format($format) == $verifiableDate;
}

function change_date_format($verifiableDate, $oldFormat, $newFormat) {
    $date = DateTime::createFromFormat($oldFormat, $verifiableDate);
    return $date->format($newFormat);
}

function get_previous_sunday() {
    return date('Y-m-d', strtotime('last sunday last week'));
}

function get_current_date() {
    return date('Y-m-d');
}

function get_days_difference($startDate, $endDate) {
    $startDateObject = new DateTime($startDate);
    $endDateObject = new DateTime($endDate);
    $daysDifference = $endDateObject->diff($startDateObject);
    return $daysDifference->days;
}

function get_year_first_sunday($dateTime) {
    $firstSundayOfYearTime = strtotime('first sunday of january', $dateTime);
    return date('Y-m-d', $firstSundayOfYearTime);
}

function get_created_at() {
    return date('Y-m-d H:i:s');
}

function get_last_start_week_day_date($startWeekDayNumber, $dateString = 'now') {
    $startWeekDayName = get_week_name_by_number($startWeekDayNumber);
    return date('Y-m-d', strtotime("$dateString last $startWeekDayName"));
}

function get_nearest_end_week_day_date($endWeekDayNumber, $dateString = 'now') {
    $dayNumber = get_week_day_number($dateString);

    if ($dayNumber == $endWeekDayNumber) {
        return $dateString;
    }

    $endWeekDayName = get_week_name_by_number($endWeekDayNumber);

    return date('Y-m-d', strtotime("$dateString $endWeekDayName"));
}

function get_previous_start_week_day_date($startWeekDayNumber) {
    $startWeekDayName = get_week_name_by_number($startWeekDayNumber);
    return date('Y-m-d', strtotime('last week last ' . $startWeekDayName));
}

function get_previous_end_week_day_date($endWeekDayNumber) {
    $endWeekDayName = get_week_name_by_number($endWeekDayNumber);
    return date('Y-m-d', strtotime('last ' . $endWeekDayName));
}

function get_week_name_by_number($weekDayNumber) {
    $weekDaysMap = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
    return $weekDaysMap[$weekDayNumber];
}

function get_end_week_day_name($startWeekDayNumber) {
    return $startWeekDayNumber == 0 ? 'Saturday' : 'Sunday';
}

function get_saturday_date($dateString) {
    return date('Y-m-d', strtotime('saturday', strtotime($dateString)));
}

function format_date($dateString, $format = 'Y-m-d') {
    return date($format, strtotime($dateString));
}

function get_week_day_number($dateString) {
    return date('w', strtotime($dateString));
}

function date_is_start_week_day($date, $startWeekDayNumber) {
    $weekDayNumber = get_week_day_number($date);
    $result = $weekDayNumber == $startWeekDayNumber;
    return $weekDayNumber == $startWeekDayNumber;
}

function change_model_result($model, $fieldName, $fieldData) {
    $tempData = clone($fieldData);
    unset($model->$fieldName);
    $model->$fieldName = clone($tempData);
}

function get_current_est_date($format = 'Y-m-d') {
    $timezone = date_default_timezone_get();
    date_default_timezone_set('America/New_York');
    $estDate = date($format);
    date_default_timezone_set($timezone);
    return $estDate;
}

function datetime_from_est_to_utc($datetime) {
    $dateObject = new DateTime($datetime, new DateTimeZone('America/New_York'));
    $dateObject->setTimezone(new DateTimeZone('UTC'));
    return $dateObject->format('Y-m-d H:i:s');
}

function datetime_from_utc_to_est($datetime) {
    $dateObject = new DateTime($datetime, new DateTimeZone('UTC'));
    $dateObject->setTimezone(new DateTimeZone('America/New_York'));
    return $dateObject->format('Y-m-d H:i:s');
}

function datetime_from_local_to_utc($datetime, $offset) {
    return date('Y-m-d H:i:s', strtotime("$datetime $offset hours"));
}

function datetime_from_utc_to_local($datetime, $offset) {
    return date('Y-m-d H:i:s', strtotime("$datetime -$offset hours"));
}

function add_days_to_date($date, $daysAmount, $format = 'Y-m-d') {
    return date($format, strtotime($date . " +$daysAmount days"));
}

function get_start_end_week_dates($startDate, $endDate, $fullEndWeek = false) {
    $endWeekDay = (int) Config::get('constants.front.businessGrossDayIsMonday');
    $takeFirstDay = (int) Config::get('constants.front.businessGrossTakeFirstDay');

    $startEndWeekDates['start_date'] = $takeFirstDay
        ? get_last_start_week_day_date($endWeekDay, $startDate)
        : format_date($startDate);

    $lastWeekDate = get_last_start_week_day_date($endWeekDay);

    if ($endDate != '0000-00-00 00:00:00' && $endDate <= $lastWeekDate) {
        $startEndWeekDates['end_date'] = format_date($endDate);
    } else {
        $startEndWeekDates['end_date'] = $lastWeekDate;
    }

    if ($fullEndWeek && !$takeFirstDay) {
        $startEndWeekDates['end_date'] = get_nearest_end_week_day_date($endWeekDay, $startEndWeekDates['end_date']);
    }

    return $startEndWeekDates;
}