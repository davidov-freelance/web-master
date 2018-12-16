<?php

namespace App\Http\Controllers\Backend;

use Config;
use App\Http\Requests\Backend\BadgeCreateRequest;

use App\Badge;

class BadgesController extends BackendController
{

    public function getIndex()
    {
        $badges = Badge::get();
        return backend_view('badges.index', compact('badges'));
    }

    public function edit(Badge $badge)
    {
        return backend_view('badges.edit', compact('badge'));
    }

    public function add()
    {
        return backend_view('badges.add');
    }
    
    public function create(BadgeCreateRequest $request)
    {
        $badgeData = $request->all();

        if ($request->hasFile('icon')) {
            $badgeData['icon'] = $this->saveImage($request->file('icon'), Config::get('constants.front.dir.badgesIconPath'));
        }

        Badge::create($badgeData);

        session()->flash('alert-success', 'Badge has been added successfully!');
        return redirect('backend/badges');
    }

    public function update(BadgeCreateRequest $request, Badge $badge)
    {
        $badgeData = $request->all();

        if ($request->hasFile('icon')) {
            $badgeData['icon'] = $this->saveImage($request->file('icon'), Config::get('constants.front.dir.badgesIconPath'));
        }

        $badge->update($badgeData);

        session()->flash('alert-success', 'Record has been updated successfully!');
        return redirect('backend/badges');
    }

    public function destroy(Badge $badge)
    {
        $badge->delete();
        session()->flash('alert-success', 'Badge has been deleted successfully!');
        return redirect('backend/badges');
    }

}
