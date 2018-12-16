<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\RESTAPIHelper;
use App\Helpers\Func;
use Config;

use App\Show;
use App\ShowGross;
use App\Post;

class ShowsController extends ApiBaseController
{
    private $_sortAliaces = [
        'gross' => 'earnings',
        'audience' => 'audience',
        'date' => 'end_week_date'
    ];

    public function getShows(Request $request)
    {
        $offset = (int) $request->input('offset');
        $limit = $request->input('limit');
        $limit = isset($limit) ? intval($limit) : 20;

        if ($limit <= 0) {
            return RESTAPIHelper::response('', 'Error', 'Limit must be at least 1', false);
        }

        if ($offset < 0) {
            return RESTAPIHelper::response('', 'Error', 'Offset must be at least 0', false);
        }
        
        $week = $request->input('week');
        $week = !empty($week) ? $week : 'latest';

        $grossWeekDayNumber = (int) Config::get('constants.front.businessGrossDayIsMonday');
        $grossTakeFirstDay = (int) Config::get('constants.front.businessGrossTakeFirstDay');

        $grossWeekDayName = get_week_name_by_number($grossWeekDayNumber);
    
        if ($week != 'latest') {
            
            if (!check_date($week, 'Y-m-d')) {
                return RESTAPIHelper::response('', 'Error', 'The week parameter has to contain a \'latest\' value or a date in the \'yyyy-mm-dd\' format', false);
            }
            
            if (!date_is_start_week_day($week, $grossWeekDayNumber)) {
                return RESTAPIHelper::response('', 'Error', "The week date must be $grossWeekDayName", false);
            }
            
        } else {
            $week = $grossTakeFirstDay ? get_previous_start_week_day_date($grossWeekDayNumber) : get_previous_end_week_day_date($grossWeekDayNumber);
        }
        
        $orderBy = $request->input('order_by');
        $orderBy = !empty($orderBy) ? $orderBy : 'desc';
    
        if ($orderBy != 'asc' && $orderBy != 'desc') {
            return RESTAPIHelper::response('', 'Error', 'Order by must be equal to \'asc\' or \'desc\'', false);
        }
        
        $sort = $request->input('sort');
        $sort = !empty($sort) ? $sort : 'gross';
    
        if ($sort != 'gross' && $sort != 'audience') {
            return RESTAPIHelper::response('', 'Error', 'Sort must be equal to \'gross\' or \'audience\'', false);
        }
        
        $audienceQuery = DB::raw('IF(show_gross.performances_amount = 0, 0, FLOOR(show_gross.attendees_amount / show_gross.performances_amount / theaters.capacity * 100)) AS audience');

        $showsQuery = Show::select(
                'shows.id',
                'shows.name',
                'shows.image',
                'theaters.capacity',
                'show_gross.earnings',
                $audienceQuery
            )->with('gross')
            ->leftJoin('show_gross', function ($join) use ($week) {
                $join->on('shows.id', '=', 'show_gross.show_id')->where('show_gross.end_week_date', '=', $week);
            })
            ->join('theaters', 'shows.theater_id', '=', 'theaters.id')
            ->where('show_gross.earnings', '!=', null);

        $totalRecord = $showsQuery->count();

        $shows = $showsQuery->orderBy($this->_sortAliaces[$sort], $orderBy)->offset($offset)->limit($limit)->get();

        if ($shows) {
    
            foreach ($shows as $index => $showData) {
                $shows[$index]->total_gross = $this->_calculateTotalGross($showData->gross);
                $this->_calculateAdditionalGrossData($showData, true, Config::get('constants.front.showListGrossAmount'), $week);
                unset($showData->audience);
                unset($showData->earnings);
            }
        }
    
        $responseArray = [
            'TotalRecords' => $totalRecord,
            'Shows' => $shows
        ];
        
        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }
    
    public function showDetails(Request $request)
    {
        $showId = (int) $request->input('show_id');
        
        if (!$showId) {
            return RESTAPIHelper::response('', 'Error', 'Show id is required', false);
        }
        
        $selectClosingAtRaw = DB::raw('IF (shows.closing_at = "0000-00-00 00:00:00", NULL, shows.closing_at) AS closing_at');
        $selectEngagementEndRaw = DB::raw('IF (shows.engagement_end = "0000-00-00 00:00:00", NULL, shows.engagement_end) AS engagement_end');
        $totalGrossRaw = DB::raw('(SELECT SUM(earnings) FROM show_gross WHERE show_id = ' . $showId . ') AS total_gross');

        $showData = Show::select(
                'shows.id',
                'shows.theater_id',
                'shows.name',
                'shows.image',
                'shows.schedule',
                'shows.roles',
                'shows.preview_at',
                'shows.opening_night_at',
                'shows.engagement_at',
                'shows.engagement_end',
                'shows.closing_at',
                'theaters.capacity',
                $selectEngagementEndRaw,
                $selectClosingAtRaw,
                $totalGrossRaw
            )->where('shows.id', '=', $showId)
            ->with(['theater' => function($query) {
                $query->select(
                    'id',
                    'name',
                    'capacity',
                    'location',
                    'address',
                    'city',
                    'state',
                    'zip',
                    'longitude',
                    'latitude'
                );
            }, 'gross'])
            ->join('theaters', 'shows.theater_id', '=', 'theaters.id')
            ->first();
        
        if (empty($showData)) {
            
            return RESTAPIHelper::response('', 'Error', 'Wrong show id given', false);
        }

        $this->_calculateAdditionalGrossData($showData);

        return RESTAPIHelper::response($showData, 'Success', 'Data retrieved successfully');
    }

    public function getGrosses(Request $request)
    {
        $showId = $request->input('show_id');
        $offset = (int) $request->input('offset');
        $limit = $request->input('limit');
        $limit = isset($limit) ? intval($limit) : 20;

        $orderBy = $request->input('order_by');
        $orderBy = !empty($orderBy) ? $orderBy : 'desc';

        if ($orderBy != 'asc' && $orderBy != 'desc') {
            return RESTAPIHelper::response('', 'Error', 'Order by must be equal to \'asc\' or \'desc\'', false);
        }

        if ($limit <= 0) {
            return RESTAPIHelper::response('', 'Error', 'Limit must be at least 1', false);
        }

        if ($offset < 0) {
            return RESTAPIHelper::response('', 'Error', 'Offset must be at least 0', false);
        }

        $sort = $request->input('sort');
        $sort = !empty($sort) ? $sort : 'gross';

        if (!$this->_sortAliaces[$sort]) {
            return RESTAPIHelper::response('', 'Error', 'Sort must be equal to \'gross\' or \'audience\'', false);
        }

        if ($showId == 0) {
            return RESTAPIHelper::response('', 'Error', 'Show id is required', false);
        }

        $grossesQuery = ShowGross::select('id', 'end_week_date', 'attendees_amount', 'performances_amount', 'earnings');


        $grossesQuery->where('show_id', '=', $showId);

        $totalRecords = $grossesQuery->count();

        $audienceRaw = DB::raw('ROUND(attendees_amount / performances_amount) AS audience');
        $grossesQuery = $grossesQuery->addSelect($audienceRaw);

        $grossesData = $grossesQuery->orderBy($this->_sortAliaces[$sort], $orderBy)
            ->offset($offset)
            ->limit($limit)
            ->get();

        $grossesWithWeekNumber = $this->_setWeekNumber($showId, $grossesData);

        $responseArray = [
            'TotalRecords' => $totalRecords,
            'Grosses' => $grossesWithWeekNumber
        ];

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }
    
    public function getNews(Request $request)
    {
        $showId = (int) $request->input('show_id');
        
        $offset = (int) $request->input('offset');
        $limit = $request->input('limit');
        $limit = isset($limit) ? intval($limit) : 20;

        if ($limit <= 0) {
            return RESTAPIHelper::response('', 'Error', 'Limit must be at least 1', false);
        }

        if ($offset < 0) {
            return RESTAPIHelper::response('', 'Error', 'Offset must be at least 0', false);
        }

        $orderBy = $request->input('order_by');
        $orderBy = !empty($orderBy) ? $orderBy : 'desc';
    
        if ($orderBy != 'asc' && $orderBy != 'desc') {
            return RESTAPIHelper::response('', 'Error', 'Order by must be equal to \'asc\' or \'desc\'', false);
        }

        $current_time = date('Y-m-d H:i:s');    // Change to reflect published_date with php time
        $selectQuery = DB::raw('IF(status = "' . POST::POST_STATUS_SCHEDULED . '", published_date, created_at) as created_at ');
        $whereRaw = '(status = "' . POST::POST_STATUS_APPROVED . '" OR (status = "' . POST::POST_STATUS_SCHEDULED . '" AND published_date <= "' . $current_time . '"))';
        
        if ($showId) {
            $shows = Show::find($showId);
        
            if (!$shows) {
            
                return RESTAPIHelper::response('', 'Error', 'Wrong show id given', false);
            }
        }

        $newsQuery = Post::select('id', 'title', 'description', 'source_url', 'link_text', 'published_date', 'image', $selectQuery)
            ->has('shows')
            ->whereRaw($whereRaw)
            ->whereExists(function($query) use ($showId) {
                $whereRaw = 'posts.id = article_shows.post_id';
                
                if ($showId) {
                    $whereRaw .= ' AND article_shows.show_id = ' . $showId;
                }
                
                $query->select(DB::raw(1))->from('article_shows')->whereRaw($whereRaw);
            });
        
        $totalRecords = $newsQuery->count();
        $newsData = $newsQuery->orderBy('created_at', $orderBy)->offset($offset)->limit($limit)->get();
    
        $responseArray = [
            'TotalRecords' => $totalRecords,
            'News' =>$newsData
        ];

        return RESTAPIHelper::response($responseArray, 'Success', 'Data retrieved successfully');
    }

    private function _getTotalGross($showId)
    {
        $totalNumber = DB::raw('COUNT(*) AS grosses_total_number');
        $totalGross = DB::raw('SUM(earnings) AS total_gross');
        return ShowGross::select($totalNumber, $totalGross)
            ->where('show_id', '=', $showId)
            ->first();
    }

    private function _setWeekNumber($showId, $grossesData) {

        $ids = ShowGross::where('show_id', '=', $showId)
            ->orderBy('end_week_date', 'asc')
            ->pluck('id')->toArray();

        $ids = array_flip($ids);

        foreach($grossesData as $grossData) {
            $grossData->week_number = $ids[$grossData->id] + 1;

            if (!empty($grossData->audience)) {
                $grossData->avarage_audience = $grossData->audience;
                unset($grossData->audience);
            }
        }

        return $grossesData;
    }

    private function _calculateTotalGross($grossData)
    {
        $totalGross = 0;
        if ($grossData) {
            foreach ($grossData as $grossDatum) {
                unset($grossDatum->show_id);
                $totalGross += $grossDatum->earnings;
            }
        }
        return $totalGross;
    }

    private function _calculateAdditionalGrossData($showData, $forGrossList = false, $limit = false, $grossWeekDate = false)
    {
        if (count($showData->gross)) {
            $startWeekNumber = 0;

            $returnedGross = [];
            $amountGross = 0;
            foreach ($showData->gross as $index => $grossDatum) {
                $startWeekNumber++;
    
                if (!empty($grossWeekDate) && $grossDatum->end_week_date > $grossWeekDate) {
                    continue;
                }
                
                $grossDatum->week_number = $startWeekNumber;

                if ($grossDatum->performances_amount) {
                    $grossDatum->audience = intVal($grossDatum->attendees_amount / $grossDatum->performances_amount / $showData->capacity * 100);
                } else {
                    $grossDatum->audience = 0;
                }
                unset($grossDatum->performances_amount);
                unset($grossDatum->attendees_amount);
                $returnedGross[] = $grossDatum;
                $amountGross++;
            }
            
            unset($showData->capacity);
            unset($showData->gross);
            
            if ($limit) {
                $returnedGross = array_slice($returnedGross, -$limit);
            }
            
            
            $showData->gross = $forGrossList ? array_reverse($returnedGross) : $returnedGross;
        }
    }
}
