<?php

namespace App\Http\Controllers;

use App\Answer;
use App\Question;
use App\Helpers\RESTAPIHelper;
use App\Http\Requests\Frontend\GetQuestionOfTheDay;
use App\Http\Requests\Frontend\AnswerQuestion;

class QuestionsController extends ApiBaseController
{
    public function getQuestionOfTheDay(GetQuestionOfTheDay $request)
    {
        $userId = (int)$request->input('user_id');
        // Get date in EST
        $currentDate = get_current_est_date();
        
        $dailyQuestion = Question::leftJoin('answers', function ($join) use ($userId) {
            $join->on('answers.question_id', '=', 'questions.id')
                ->where('answers.user_id', '=', $userId);
        })
            ->select('questions.*', 'answers.user_id')
            ->where('questions.release_datetime', '=', $currentDate)
            ->first();
        
        // Get streaks for user
        $answersInRow = Answer::getGroupedAnswersInRow($userId);

        $geek_streak = !empty($answersInRow['days_row']) ? $answersInRow['days_row'] : 0;
        $genius_streak = !empty($answersInRow['correct_answer_days_row'])
            ? $answersInRow['correct_answer_days_row']
            : 0;
        
        if (empty($dailyQuestion)) {
            // Collect response data
            $responseData = [
                'answered' => (bool)0,
                'genius_streak' => $genius_streak,
                'geek_streak' => $geek_streak,
            ];
            return RESTAPIHelper::response($responseData, 'Success', 'Data retrieved successfully');
        }
        
        $dailyQuestion = $dailyQuestion->toArray();
        
        $options = [];
        for ($i = 1; $i <= 4; $i++) {
            $options[] = $dailyQuestion['option_' . $i];
        }
        
        // Collect response data
        $responseData = [
            'question' => $dailyQuestion['question'],
            'id'=>$dailyQuestion['id'],//Sending Question Id as a response
            'options' => $options,
            'answered' => (bool)$dailyQuestion['user_id'],
            'genius_streak' => $genius_streak,
            'geek_streak' => $geek_streak,
        ];
        
        return RESTAPIHelper::response($responseData, 'Success', 'Data retrieved successfully');
    }
    
    public function answerQuestion(AnswerQuestion $request)
    {
        
        $userId = (int)$request->input('user_id');
        $answerNumber = (int)$request->input('answer_number');
        $question_id = (int)$request->input('question_id');//Receiving The question id from the request//
        // Get date in EST
        $currentDate = get_current_est_date();
        
        $dailyQuestion = Question::where(['id' => $question_id])
            ->where('questions.release_datetime', '=', $currentDate)
            ->first();
        
        if (empty($dailyQuestion)) {
            return $this->_questionNotFound();
        }
        
        $previousAnswer = Answer::where('question_id', '=', $dailyQuestion->id)
            ->where('user_id', '=', $userId)
            ->first();
        
        if (!empty($previousAnswer)) {
            return RESTAPIHelper::errorResponse('You already answered this question');
        }
    
        // Get streaks for user
        $answersInRow = Answer::getGroupedAnswersInRow($userId);

        $geek_streak = !empty($answersInRow['days_row']) ? $answersInRow['days_row'] : 0;
        $genius_streak = !empty($answersInRow['correct_answer_days_row'])
            ? $answersInRow['correct_answer_days_row']
            : 0;
        
        $dailyQuestion->geek_streak++;
        $geek_streak++;
        
        $answerIsCorrect = false;
        if ($answerNumber == $dailyQuestion->correct_answer) {
            $answerIsCorrect = true;
            $dailyQuestion->genius_streak++;
            $genius_streak++;
        } else {
            $genius_streak = 0;
        }
        
        // Collect response data
        $insertAnswerData = [
            'user_id' => $userId,
            'question_id' => $dailyQuestion->id,
            'answer' => $answerNumber,
            'is_correct' => $answerIsCorrect,
            'created_at' => null,
        ];
        
        Answer::create($insertAnswerData);
        
        $dailyQuestion->save();
        
        $correctOptionName = "option_$dailyQuestion->correct_answer";
        
        $responseData = [
            'correct' => $answerIsCorrect,
            'correct_answer' => $dailyQuestion->$correctOptionName,
            'genius_streak' => $genius_streak,
            'geek_streak' => $geek_streak
        ];
        
        return RESTAPIHelper::response($responseData, 'Success', 'User response on question accepted');
    }
    
    private function _questionNotFound()
    {
        return RESTAPIHelper::errorResponse('This question is not found in our database.');
    }
}
