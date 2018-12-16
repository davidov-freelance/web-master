<?php

namespace App\Http\Controllers\Backend;

use Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Http\Requests\Backend\ShowCreateRequest;
use App\Http\Requests\Backend\TheaterCreateRequest;
use App\Http\Requests\Backend\ShowSaveGrossRequest;
use App\Http\Requests\Backend\ShowUpdateGrossRequest;
use App\Http\Requests\Backend\ShowCheckGrossRequest;
use App\Helpers\RESTAPIHelper;

use App\Post;
use App\Show;
use App\Theater;
use App\ShowGross;

class ShowsController extends BackendController
{
    public function getIndex()
    {
        $shows = Show::orderBy('id', 'DESC')->get();
        return backend_view('shows.index', compact('shows'));
    }

    public function getNews()
    {
        $currentDatetime = $this->getUserLocalTime();

        $condition['post_type'] = Post::POST_TYPE_ARTICLE;

        $offset = $this->getUserOffset();
        $publishedDateOffsetSubquery = DB::raw("DATE_SUB(`published_date`, INTERVAL $offset hour) AS published_date");

        $posts = Post::select('*', $publishedDateOffsetSubquery)
            ->with(['publisher'])
            ->has('shows')
            ->where($condition)
            ->orderBy('created_at', 'DESC')
            ->get();
        
        return backend_view('shows.news', compact('posts', 'currentDatetime'));
    }

    public function add()
    {
        $theaterOptions = $this->_getTheaterOptions();
        return backend_view('shows.add', compact('theaterOptions'));
    }

    public function create(ShowCreateRequest $request)
    {
        $showData = $request->all();
        
        if ($file = $request->hasFile('image')) {
        
            $file = $request->file('image');
            $fileName = \Illuminate\Support\Str::random(12) . '.' . $request->file('image')->getClientOriginalExtension();
            $destinationPath = base_path() . '/' . Config::get('constants.front.dir.showsImagePath');
            $file->move($destinationPath, $fileName);
            $showData['image'] = $fileName;
        }
    
        Show::create($showData);
        
        session()->flash('alert-success', 'Show has been added successfully!');
        return redirect('backend/shows');
    }

    public function edit(Show $show)
    {
        $theaterOptions = $this->_getTheaterOptions();
        
        // If closing_at is empty, clear it for datepicker
        if ($show->closing_at == '0000-00-00 00:00:00') {
            $show->closing_at = '';
        }
        
        if ($show->engagement_end == '0000-00-00 00:00:00') {
            $show->engagement_end = '';
        }
        
        return backend_view('shows.edit', compact('show', 'theaterOptions'));
    }

    public function update(ShowCreateRequest $request, Show $show)
    {
        $showData = $request->all();
    
        if ($file = $request->hasFile('image')) {
        
            $file = $request->file('image');
            $fileName = \Illuminate\Support\Str::random(12) . '.' . $request->file('image')->getClientOriginalExtension();
            $destinationPath = base_path() . '/' . Config::get('constants.front.dir.showsImagePath');
            $file->move($destinationPath, $fileName);
            $showData['image'] = $fileName;
        }
    
        $show->update($showData);

        session()->flash('alert-success', 'Show has been updated successfully!');
        return redirect('backend/shows');
    }

    public function remove(Request $request)
    {
        $showId = $request->input('id');
        Show::destroy($showId);
        
        session()->flash('alert-success', 'Show has been deleted successfully!');
        return redirect('backend/shows');
    }

    public function gross(Show $show) {
        $gross = ShowGross::select('*')->where('show_id', $show->id)->orderBy('end_week_date', 'desc')->get();

        $grossWeekDay = (int) Config::get('constants.front.businessGrossDayIsMonday');
        $grossTakeFirstDay = (int) Config::get('constants.front.businessGrossTakeFirstDay');

        $show->gross = !empty($gross) ? $gross : null;

        $show->start_date = $grossTakeFirstDay
            ? get_last_start_week_day_date($grossWeekDay, $show->preview_at)
            : format_date($show->preview_at);

        $lastWeekDate = get_last_start_week_day_date($grossWeekDay);

        if ($show->closing_at != '0000-00-00 00:00:00' && $show->closing_at <= $lastWeekDate) {
            $show->end_date = format_date($show->closing_at);
        } else {
            $show->end_date = $lastWeekDate;
        }

        return backend_view('shows.gross', compact('show', 'grossWeekDay', 'grossTakeFirstDay'));
    }
    
    public function addGross(Show $show)
    {
        $show = $this->_setStartEndWeekDates($show);
        return backend_view('shows.add_gross', compact('show'));
    }

    public function editGross(ShowGross $showGross)
    {
        $show = Show::find($showGross->show_id);
        $show = $this->_setStartEndWeekDates($show);
        return backend_view('shows.edit_gross', compact('show', 'showGross'));
    }
    
    public function updateGross(ShowUpdateGrossRequest $request, ShowGross $showGross)
    {
        $grossData = $request->all();

        $showGross->update($grossData);
        
        session()->flash('alert-success', 'Gross has been updated successfully!');
        return redirect('backend/shows/gross/' . $showGross->show_id);
    }

    public function removeGross(Request $request)
    {
        $grossId = $request->input('id');
        ShowGross::destroy($grossId);

        session()->flash('alert-success', 'Gross has been deleted successfully!');
        return back();
    }

    public function ajaxSaveGross(ShowSaveGrossRequest $request, Show $show)
    {
        $grosses = $request->input('gross');
        $this->_saveGross($grosses, $show->id);
        return RESTAPIHelper::response(true, 'Success', 'List of answered users', false);
    }

    public function saveGross(ShowSaveGrossRequest $request, Show $show)
    {
        $grosses = $request->input('gross');
        $this->_saveGross($grosses, $show->id);
        return redirect('backend/shows/gross/' . $show->id);
    }

    public function checkGross(ShowCheckGrossRequest $request, Show $show)
    {
        $grossDates = $request->input('gross_dates');

        $duplicatedGrosses = ShowGross::where('show_id', '=', $show->id)
            ->whereIn('end_week_date', $grossDates)
            ->pluck('end_week_date');

        return RESTAPIHelper::response($duplicatedGrosses, 'Success', 'List of duplicated grosses', false);
    }
    
    public function getTheaters()
    {
        $existsQuery = DB::raw('EXISTS(SELECT * FROM shows WHERE shows.theater_id = theaters.id) AS has_show');
        $theaters = Theater::select('*', $existsQuery)->orderBy('id', 'DESC')->get();
        return backend_view('shows.theaters', compact('theaters'));
    }
    
    public function addTheater()
    {
        return backend_view('shows.add_theater');
    }
    
    public function createTheater(TheaterCreateRequest $request)
    {
        $theaterData = $request->all();
        Theater::create($theaterData);
        session()->flash('alert-success', 'Theater has been added successfully!');
        return redirect('backend/shows/theaters');
    }
    
    public function editTheater(Theater $theater)
    {
        return backend_view('shows.edit_theater', compact('theater'));
    }
    
    public function updateTheater(TheaterCreateRequest $request, Theater $theater)
    {
        $theaterData = $request->all();
    
        $theater->update($theaterData);
        
        session()->flash('alert-success', 'Theater has been updated successfully!');
        return redirect('backend/shows/theaters');
    }
    
    public function removeTheater(Request $request)
    {
        $theaterId = $request->input('id');
        Theater::destroy($theaterId);
        
        session()->flash('alert-success', 'Theater has been deleted successfully!');
        return redirect('backend/shows/theaters');
    }
    
    private function _getTheaterOptions() {
        $theaters = Theater::orderBy('name', 'ASC')->get();
        
        if (empty($theaters)) {
            return false;
        }
        
        $theatersOptions = [];
        foreach ($theaters as $theater) {
            $theatersOptions[$theater->id] = $theater->name;
        }
        
        return $theatersOptions;
    }
    
    private function _addGross($showId, $showGross)
    {
        if (empty($showGross) || empty($showId)) {
            return;
        }
    
        $insertGrossData = [];
    
        foreach ($showGross as $gross) {
            // If the end_week_date field is filled then the other fields also are
            if (empty($gross['end_week_date'])) {
                continue;
            }
            
            $gross['show_id'] = $showId;
            $gross['created_at'] = get_created_at();
            $insertGrossData[] = $gross;
        }
        
        if (count($insertGrossData)) {
    
            ShowGross::insert($insertGrossData);
        }
    }

    private function _setStartEndWeekDates($show)
    {
        $startEndWeekDates = get_start_end_week_dates($show->preview_at, $show->closing_at);

        $show->end_week_day = (int) Config::get('constants.front.businessGrossDayIsMonday');
        $show->take_first_day = (int) Config::get('constants.front.businessGrossTakeFirstDay');
        $show->start_date = $startEndWeekDates['start_date'];
        $show->end_date = $startEndWeekDates['end_date'];
        return $show;
    }

    private function _saveGross($grosses, $showId) {
        $groupedGrossDates = [];

        foreach ($grosses as $gross) {
            $gross['show_id'] = $showId;
            $groupedGrossDates[$gross['end_week_date']] = $gross;
        }

        $existingGrosses = ShowGross::where('show_id', '=', $showId)
            ->whereIn('end_week_date', array_keys($groupedGrossDates))
            ->get();

        if (count($existingGrosses)) {
            foreach ($existingGrosses as $grossData) {
                ShowGross::where('end_week_date', '=', $grossData['end_week_date'])
                    ->where('show_id', '=', $showId)
                    ->update($groupedGrossDates[$grossData['end_week_date']]);

                unset($groupedGrossDates[$grossData['end_week_date']]);
            }
        }

        if (count($groupedGrossDates)) {
            $this->_addGross($showId, $groupedGrossDates);
        }
    }
}
