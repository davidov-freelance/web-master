<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Backend\BackendController;

use App\User;

class DashboardController extends BackendController
{
    public function getIndex()
    {
        $totalUsers = User::where(['role_id' => User::ROLE_MEMBER])->count();

        return backend_view( 'dashboard', compact('totalUsers') );


//        $users =$count = DB::table('posts')
//            ->selectRaw('post_type, COUNT(*) as count')
//            ->groupBy('post_type')
//            ->orderBy('count', 'desc')
//            ->first();
    }
}
