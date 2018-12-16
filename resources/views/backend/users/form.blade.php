<?php

use Illuminate\Support\Facades\Auth;
use App\Admin;

$badges = old('badges');

if (empty($badges) && !empty($userBadges) && count($userBadges)) {
    $badges = $userBadges;
}

?>

@section('CSSLibraries')
    <link href="{{backend_asset('libraries/jquery/dist/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{backend_asset('libraries/iconselect/css/lib/control/iconselect.css')}}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <script src="{{backend_asset('libraries/iconselect/lib/control/iconselect.js')}}"></script>
    {{--<script src="{{backend_asset('libraries/iconselect/lib/iscroll.js')}}"></script>--}}
@endsection
{{--/Users/mf839/Sites/broadway/public/backend/libraries/iconselect/css/lib/control/iconselect.css--}}

@section('inlineJS')
    <script>
        $(function () {
            var indexRegexp = /{index}/g;
            var $badgesBlock = $('#badges-block');
            var badgeTemplate = $('#badge-template').html();
            var badgeIndexes = [];
            var badgeIcons = JSON.parse('{!!json_encode($badgeIcons)!!}');

            initBadgesData();

            $('#dob').datepicker({
                dateFormat: 'yy-mm-dd',
                maxDate: 'now',
            });

            $('#add-badge').click(addBadge);

            $badgesBlock.on('click', '.delete-badge', function() {
                var index = $(this).data('index');
                deleteBadge(index);
            });

            $('#change-to-admin').click(function() {
                var userId = $(this).data('id');
                var requestBody = {
                    user_id: userId
                };

                showConfirm('Are you sure?', function() {
                    sendPost('/backend/user/change-to-admin', requestBody);
                });
            });

            function addIconSelect(index) {
                var iconSelectId = 'badge-select-' + index;
                var badgeInputSelector = '#badge-' + index + '-id';
                initIconSelect(iconSelectId, badgeInputSelector, badgeIcons);
            }

            function initBadgesData() {
                var $badgeRows = $badgesBlock.find('.badge-wrapper');

                $.each($badgeRows, function() {
                    var $badgeRow = $(this);
                    var index = $badgeRow.data('index');

                    badgeIndexes.push(index);

                    addIconSelect(index);
                });
            }

            function addBadge() {
                var index = getNextBadgeIndex();
                badgeIndexes.push(index);
                var preparedBadgeRow = replaceIndex(badgeTemplate, index);
                $badgesBlock.append(preparedBadgeRow);
                addIconSelect(index);
            }

            function deleteBadge(index) {
                $badgesBlock.find('.badge-wrapper[data-index=' + index + ']').remove();
                removeArrayItemByValue(badgeIndexes, index);
            }

            function getNextBadgeIndex() {
                if (!badgeIndexes.length) {
                    return 0;
                }
                return Math.max.apply(null, badgeIndexes) + 1;
            }

            function replaceIndex(template, index) {
                return template.replace(indexRegexp, index)
            }
        });
    </script>
@endsection

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

<div class="form-group{{ $errors->has('dob') ? ' has-error' : '' }}">
    {{ Form::label('dob', 'Birthday', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('dob', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('dob') )
        <p class="help-block">{{ $errors->first('dob') }}</p>
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

<div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
    {{ Form::label('password', 'Password', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::password('password',['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('password') )
        <p class="help-block">{{ $errors->first('password') }}</p>
    @endif
</div>

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
    </div>
    @if ( $errors->has('profile_picture') )
        <p class="help-block">{{ $errors->first('profile_picture') }}</p>
    @endif
</div>

@if (!empty($badgeIcons))
<div id="badges-block">
    <div class="form-group">
        <label for="badges" class="control-label col-md-3 col-sm-3 col-xs-12">Badges</label>
    </div>
    <div class="separator"></div>

    @if (!empty($badges))
        @foreach ($badges as $index => $badgeData)
            <div class="badge-wrapper" data-index="{{$index}}">
                <div class="delete-button delete-badge" data-index="{{$index}}"></div>
                <div class="form-group{{($errors->has("badges.$index.badge_id")) ? ' has-error' : ''}}">
                    {{Form::label("badge-select-$index", 'Badge', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        <div id="badge-select-{{$index}}"></div>
                        {{ Form::hidden("badges[$index][badge_id]", $badgeData['badge_id'], ['class' => 'form-control col-md-7 col-xs-12', 'id' => "badge-$index-id"]) }}
                    </div>
                    @if ($errors->has("badges.$index.badge_id"))
                        <p class="help-block">{{$errors->first("badges.$index.badge_id")}}</p>
                    @endif
                </div>
                <div class="form-group{{($errors->has("badges.$index.badge_amount")) ? ' has-error' : ''}}">
                    {{Form::label("badges-$index-amount", 'Amount', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        {{Form::text("badges[$index][badge_amount]", $badgeData['badge_amount'], ['class' => 'form-control', 'id' => "badges-$index-amount"])}}
                    </div>
                    @if ($errors->has("badges.$index.badge_amount"))
                        <p class="help-block">{{$errors->first("badges.$index.badge_amount")}}</p>
                    @endif
                </div>
                <div class="separator"></div>
            </div>
        @endforeach
    @endif
</div>
<div class="plus-button fa fa-plus-circle fa-fw col-md-offset-3 col-sm-offset-3" id="add-badge"></div>
@endif

<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        @if (!empty($user->id) && Auth::user()->admin_role == Admin::ADMIN_ROLE_SUPER)
            {{ Form::button('Change to Admin', ['class' => 'btn btn-warning', 'id' => 'change-to-admin', 'data-id' => $user->id]) }}
        @endif
        {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
        {{ Html::link( backend_url('user'), 'Cancel', ['class' => 'btn btn-default']) }}
    </div>
</div>

<script type="text/template" id="badge-template">
    <div class="badge-wrapper" data-index="{index}">
        <div class="delete-button delete-badge" data-index="{index}"></div>
        <div class="form-group">
            <label for="badge-{index}-id" class="control-label col-md-3 col-sm-3 col-xs-12">Badge</label>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <div id="badge-select-{index}"></div>
                <input type="hidden" class="form-control col-md-7 col-xs-12" name="badges[{index}][badge_id]" id="badge-{index}-id">
            </div>
        </div>
        <div class="form-group">
            <label for="badges-{index}-amount" class="control-label col-md-3 col-sm-3 col-xs-12">Amount</label>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <input class="form-control" value="1" name="badges[{index}][badge_amount]" type="text" id="badges-{index}-amount">
            </div>
        </div>
        <div class="separator"></div>
    </div>
</script>