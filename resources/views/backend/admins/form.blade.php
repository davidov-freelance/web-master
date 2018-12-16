<?php
$imageUrl = !empty($admin->profile_image) ? $admin->profile_image : '';

$adminRightsObject	=	array();
$adminRightsObject['category']	=	0;
$adminRightsObject['product']	=	0;
$adminRightsObject['user']	=	0;
$adminRightsObject['cms']	=	0;
$adminRightsObject['noti']	=	0;

if(isset($admin->rights))
{
    $adminRightsString	= 	 $admin->rights;
    $adminRightsArray	 =	explode(",",$adminRightsString);

    #print_r($adminRightsArray);die();
    if(in_array("category",$adminRightsArray))
    {
        $adminRightsObject['category']	=	1;
    }

    if(in_array("product",$adminRightsArray))
    {
        $adminRightsObject['product']	=	1;
    }

    if(in_array("user",$adminRightsArray))
    {
        $adminRightsObject['user']	=	1;
    }

    if(in_array("cms",$adminRightsArray))
    {
        $adminRightsObject['cms']	=	1;
    }

    if(in_array("noti",$adminRightsArray))
    {
        $adminRightsObject['noti']	=	1;
    }

}

$adminRightsObject 	=	(object)$adminRightsObject;
?>


@section('inlineJS')
    <script>
        $(function () {
            $('#change-to-user').click(function() {
                var userId = $(this).data('id');
                var requestBody = {
                    user_id: userId
                };

                showConfirm('Are you sure?', function() {
                    sendPost('/backend/admin/change-to-user', requestBody);
                });
            });
        });
    </script>
@endsection

<div class="well" style="overflow: auto">
    <div class="row">
        <div class="col-md-2">
            <div id="reportrange_right" class="pull-left" style="background: #1b5Ac3;color:#fff; cursor: pointer; padding: 3px 8px; border: 1px solid #ccc">
                <i class="glyphicon glyphicon-calendar fa fa-user"></i>
                <span>Super Admin</span>
            </div>
        </div>
        <div class="col-md-10">
            <p>Super Admin will have full access of the admin console</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div id="reportrange_right" class="pull-left" style="background: #31b0d5;color:#fff; cursor: pointer; padding: 3px 8px; border: 1px solid #ccc">
                <i class="glyphicon glyphicon-calendar fa fa-users"></i>
                <span>Sub Admin</span>
            </div>
        </div>
        <div class="col-md-10">
            <p> Sub Admin : will have access for Articles and Events Management |  User Management | Push Notifications</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div id="reportrange_right" class="pull-left" style="background: #FBBc2F;color:#fff; cursor: pointer; padding: 3px 8px; border: 1px solid #ccc">
                <i class="glyphicon glyphicon-calendar fa fa-user"></i>
                <span>Moderator Admin</span>
            </div>
        </div>
        <div class="col-md-10">
            <p>Moderator * :will have read only access for Articles and Events |  User's List | Tags | Categories | Cms Pages</p>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <div id="reportrange_right" class="pull-left editor-label">
                <i class="glyphicon glyphicon-calendar fa fa-user"></i>
                <span>Editor</span>
            </div>
        </div>
        <div class="col-md-10">
            <p>Editor * :will have access only for Articles Management</p>
        </div>
    </div>

</div>

<div class="form-group{{ $errors->has('admin_role') ? ' has-error' : '' }}">
    {{ Form::label('admin_role', 'Select Role *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::select('admin_role',  array('moderator' => 'Moderator', 'super' => 'Super Admin', 'sub' => 'Sub Admin', 'editor' => 'Editor'),null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('admin_role') )
        <p class="help-block">{{ $errors->first('admin_role') }}</p>
    @endif


</div>

<div class="form-group{{ $errors->has('first_name') ? ' has-error' : '' }}">
    {{ Form::label('first_name', 'First Name *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('first_name', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('first_name') )
        <p class="help-block">{{ $errors->first('first_name') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('last_name') ? ' has-error' : '' }}">
    {{ Form::label('last_name', 'Last Name *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('last_name', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('last_name') )
        <p class="help-block">{{ $errors->first('last_name') }}</p>
    @endif
</div>


<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
    {{ Form::label('email', 'Email', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('email', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('email') )
        <p class="help-block">{{ $errors->first('email') }}</p>
    @endif
</div>

@if (empty($admin->id))
    <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
        {{ Form::label('password', 'Password', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
        <div class="col-md-6 col-sm-6 col-xs-12">
            {{ Form::password('password',['class' => 'form-control col-md-7 col-xs-12']) }}
        </div>
        @if ( $errors->has('password') )
            <p class="help-block">{{ $errors->first('password') }}</p>
        @endif
    </div>
@endif


<div class="form-group{{ $errors->has('handle') ? ' has-error' : '' }}">
    {{ Form::label('handle', 'Handle', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('handle', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('handle') )
        <p class="help-block">{{ $errors->first('handle') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('country') ? ' has-error' : '' }}">
    {{ Form::label('country', 'Country *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('country', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('country') )
        <p class="help-block">{{ $errors->first('country') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('city') ? ' has-error' : '' }}">
    {{ Form::label('city', 'City *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('city', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('city') )
        <p class="help-block">{{ $errors->first('city') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('field_of_work_id') ? ' has-error' : '' }}">
    {{ Form::label('field_of_work_id', 'Select Field Of Work ', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::select('field_of_work_id',  $fields ,null, ['class' => 'form-control col-md-7 col-xs-12']) }}

    </div>
    @if ( $errors->has('field_of_work_id') )
        <p class="help-block">{{ $errors->first('field_of_work_id') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('headline_position') ? ' has-error' : '' }}">
    {{ Form::label('headline_position', 'Headline Position *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('headline_position', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('headline_position') )
        <p class="help-block">{{ $errors->first('headline_position') }}</p>
    @endif
</div>


<div class="form-group{{ $errors->has('profile_picture') ? ' has-error' : '' }}">
    {{ Form::label('profile_picture', 'Profile picture', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::file('profile_picture', null, ['class' => 'form-control col-md-7 col-xs-12']) }}

        @if(!empty($imageUrl))
            <div class="image-preview-wrapper">
                <img src="{{$imageUrl}}" class="form-image-preview" alt="No Image">
            </div>
        @endif
    </div>
    @if ( $errors->has('profile_picture') )
        <p class="help-block">{{ $errors->first('profile_picture') }}</p>
    @endif
</div>
<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        @if (!empty($admin->id))
            {{ Form::button('Change to User', ['class' => 'btn btn-warning', 'id' => 'change-to-user', 'data-id' => $admin->id]) }}
        @endif
        {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
        {{ Html::link( backend_url('admin'), 'Cancel', ['class' => 'btn btn-default']) }}
    </div>
</div>

