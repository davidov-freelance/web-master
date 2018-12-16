<?php

namespace App\Http\Controllers\Backend;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Request as FacadeRequest;
use Auth;

use App\Http\Requests;
use App\Http\Controllers\Backend\BackendController;
use App\Http\Requests\Backend\ProfileUpdateRequest;

use App\Setting;
use App\User;

class SettingController extends BackendController
{
    public function getProfileSetting()
    {
        $user = Auth::user();

        return backend_view( 'profile_setting', compact('user') );
    }

    public function postProfileSetting(ProfileUpdateRequest $request)
    {
        $user = Auth::user();

        $dataToUpdate = $request->only(['first_name', 'last_name', 'email']);

        if ( $request->has('password') && $request->get('password', '') !== '' ) {
            $dataToUpdate['password'] = \Hash::make( $request->get('password') );
        }

        $user->update( $dataToUpdate );

        session()->flash('alert-success', 'Profile has been updated successfully!');

        return redirect()->back();
    }

    public function processSetting()
    {
        $setting = new \stdClass;
        $setting->tutorial_video = Setting::extract('app.link.tutorial_video');
        $setting->guide_book = Setting::extract('app.link.guide_book');

        if ( FacadeRequest::getMethod() == 'POST' ) {

            $update = [
                [
                    'config_value' => FacadeRequest::get('tutorial_video'),
                    'config_key' => 'app.link.tutorial_video',
                ],
                [
                    'config_value' => FacadeRequest::get('guide_book'),
                    'config_key' => 'app.link.guide_book',
                ],
            ];

            Setting::updateSettingArray( $update, 'config_key' );

            session()->flash('alert-success', 'Setting has been updated successfully!');
            return redirect()->back();
        }

        return backend_view( 'app_setting', compact('setting') );
    }
}
