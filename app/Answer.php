<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;

class Answer extends Authenticatable {
    
    protected $table = 'answers';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'user_id',
        'question_id',
        'answer',
        'is_correct',
        'created_at',
        'updated_at',
    ];
    
    public function users()
    {
        return $this->belongsTo('App\User','user_id' );
    }

    static function getGroupedAnswersInRow($forUserId = false)
    {
        $dateFormatRaw = DB::raw('DATE_FORMAT(created_at, "%Y-%m-%d") AS date');

        $answersObj = Answer::select('user_id', 'is_correct', $dateFormatRaw);

        if ($forUserId) {
            $answersObj = $answersObj->where('user_id', '=', $forUserId);
        }

        $answersData = $answersObj->orderBy('user_id', 'ASC')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();

        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', time() - (60 * 60 * 24));

        $secondsInDay = 60 * 60 * 24;

        $answersInRowData = [];

        foreach ($answersData as $answerData) {
            $userId = $answerData['user_id'];

            if (empty($answersInRowData[$userId])) {
                if ($answerData['date'] == $today || $answerData['date'] == $yesterday) {
                    $answersInRowData[$userId] = [
                        'last_date' => $answerData['date'],
                        'days_row' => 1,
                        'correct_answer_last_date' => $answerData['is_correct'] ? $answerData['date'] : '',
                        'correct_answer_days_row' => $answerData['is_correct']
                    ];
                }
                continue;
            }

            $lastDate = strtotime($answersInRowData[$userId]['last_date']);
            $previousDate = strtotime($answerData['date']);

            if ($lastDate - $previousDate == $secondsInDay) {
                $answersInRowData[$userId]['days_row']++;
                $answersInRowData[$userId]['last_date'] = $answerData['date'];
            }

            if (!empty($answersInRowData[$userId]['correct_answer_last_date']) && $answerData['is_correct']) {
                $correctLastDate = strtotime($answersInRowData[$userId]['correct_answer_last_date']);

                if ($correctLastDate - $previousDate == $secondsInDay) {
                    $answersInRowData[$userId]['correct_answer_last_date'] = $answerData['date'];
                    $answersInRowData[$userId]['correct_answer_days_row']++;
                }
            }
        }

        if ($forUserId) {
            return !empty($answersInRowData[$forUserId]) ? $answersInRowData[$forUserId] : false;
        }

        return $answersInRowData;
    }
}
