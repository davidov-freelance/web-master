<?php

namespace App\Http\Controllers\Backend;

use Config;
use Illuminate\Http\Request;
use App\Http\Requests\Backend\GetAnsweredUsers;
use App\Http\Requests\Backend\QuestionCreateRequest;
use App\Helpers\RESTAPIHelper;

use App\User;
use App\Question;
use App\Answer;


class QuestionsController extends BackendController
{

    public function getIndex()
    {
        $questions = Question::orderBy('release_datetime', 'DESC')->get();
        return backend_view('questions.index', compact('questions'));
    }

    public function getUsers() {
        $users = User::select('users.*')->where(['role_id' => User::ROLE_MEMBER])->orderBy('id', 'ASC')->get();

        $answersInRow = Answer::getGroupedAnswersInRow();

        foreach ($users as $userData) {
            $userId = $userData->id;
            $userData->geek_streak = !empty($answersInRow[$userId]['days_row']) ? $answersInRow[$userId]['days_row'] : 0;
            $userData->genius_streak = !empty($answersInRow[$userId]['correct_answer_days_row'])
                ? $answersInRow[$userId]['correct_answer_days_row']
                : 0;
        }

        return backend_view( 'questions.users', compact('users') );
    }

    public function getAnsweredUsers(GetAnsweredUsers $request)
    {
        $questionId = $request->input('id');
        $showCorrect = $request->input('show_correct');

        $condition = [
            'answers.question_id' => $questionId,
            'is_correct' => $showCorrect
        ];

        $users = Answer::join('users', 'answers.user_id', '=', 'users.id')
            ->select('users.first_name', 'users.last_name')
            ->where($condition)
            ->orderBy('users.first_name', 'ASC')
            ->get()
            ->toArray();

        return RESTAPIHelper::response($users, 'Success', 'List of answered users', false);
    }

    public function add()
    {
        $disabledFields = $this->_getDisabledDates();
        return backend_view('questions.add', compact('disabledFields'));
    }

    public function create(QuestionCreateRequest $request)
    {
        $questionData = $request->all();

        // Check if there are questions for this day
        $whereRaw = $this->_prepareDateRaw($questionData['release_datetime']);

        if (Question::whereRaw($whereRaw)->first()) {
            return redirect('backend/questions/add')
                ->withErrors(['release_datetime' => 'The question for the selected day has been already created.'])
                ->withInput();
        }
        $questionData['created_at'] = null;
        Question::create($questionData);
        session()->flash('alert-success', 'Question has been added successfully!');
        return redirect('backend/questions');
    }

    public function edit(Question $question)
    {
        $question->release_datetime = $question->formatted_release_datetime;
        return backend_view('questions.edit', compact('question'));
    }

    public function update(QuestionCreateRequest $request, Question $question)
    {
        $questionDate = $request->all();
        // Check if there are questions for this day

        $whereRaw = $this->_prepareDateRaw($questionDate['release_datetime']);

        $sameDayQuestion = Question::whereRaw($whereRaw . ' AND id != ' . $question->id)->first();

        if ($sameDayQuestion) {
            return redirect('backend/questions/edit/' . $question->id)
                ->withErrors(['release_datetime' => 'The question for the selected day has been already created.'])
                ->withInput();
        }

        $question->update($questionDate);

        session()->flash('alert-success', 'Question has been updated successfully!');
        return redirect('backend/questions');
    }

    public function remove(Request $request)
    {
        $questionId = $request->input('id');
        if (Question::where('id', '=', $questionId)->delete()) {
            Answer::where('question_id', '=', $questionId)->delete();
        }

        session()->flash('alert-success', 'Question has been deleted successfully!');
        return redirect('backend/questions');
    }

    private function _prepareDateRaw($releaseDatetime)
    {
        $formattedDatetime = substr($releaseDatetime, 0, 10);
        return "DATE_FORMAT(release_datetime, '%Y-%m-%d') = '$formattedDatetime'";
    }
    
    private function _getDisabledDates() {
//        $questions = Question::whereRAW('DATE(questions.release_datetime) >= CURDATE()')->get()->toArray();
        $questions = Question::get()->toArray();
        $disabledFields = array();
        foreach ($questions as $question) {
            $disabledFields[] = substr($question['release_datetime'], 0, 10);
        }
        return $disabledFields;
    }

    function getAnswersInARow($userId, $current = false)
    {
        if ($current) {
            $compare = '<=';
        } else {
            $compare = '<';
        }
        $currentDate = date('Y-m-d');
        $currentDate .= ' 00:00:00';
        $questions = Question::select('*')->where('questions.release_datetime',$compare,$currentDate)->orderBy('release_datetime', 'DESC')->get();
//        dd($questions);
        $streak = array(
            'days_row' => 0,
            'correct_answer_days_row' => 0
        );
        foreach ($questions as $question) {
            $answer = Answer::where(['user_id' => $userId, 'question_id' => $question['id']])->first();
            if ($answer) {
                $streak['days_row']++;
                if ($answer->is_correct) {
                    $streak['correct_answer_days_row']++;
                }
            } else {
                break;
            }
        }
        return $streak;
    }
}
