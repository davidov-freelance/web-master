<?php

$imageUrl = null;
$schedule = null;

if (isset($show)) {
    $imageUrl = $show->show_image;
    $schedule = $show->schedule;
}

$weekDaysAliases = [
    'Sunday' => 'sun',
    'Monday' => 'mon',
    'Tuesday' => 'tue',
    'Wednesday' => 'wed',
    'Thursday' => 'thu',
    'Friday' => 'fri',
    'Saturday' => 'sat',
];

$roles = old('roles');

if (empty($roles) && !empty($show->roles)) {
    $roles = $show->roles;
}

?>

@section('CSSLibraries')
    <!-- TimePicker CSS -->
    <link href="{{backend_asset('libraries/jquery/dist/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{backend_asset('libraries/timpicker/jquery-ui-timepicker-addon.css')}}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <script src="{{backend_asset('libraries/timpicker/jquery-ui-timepicker-addon.js')}}"></script>
@endsection

@section('inlineJS')
    <script>

        $(function () {
            var indexRegexp = /{index}/g;
            var personIndexRegexp = /{personIndex}/g;
            var $rolesBlock = $('#roles-block');
            var roleTemplate = $('#role-template').html();
            var rolePersonTemplate = $('#role-person-template').html();
            var personsData = {};

            initPersonsData();

            $('.datetime-input').datetimepicker({
                dateFormat: 'yy-mm-dd',
                timeFormat: 'HH:mm',
            });

            $('.time-input').timepicker({
                timeFormat: 'HH:mm',
            });

            initImagePreview('#image-input', '#image-preview-wrapper');

            $('#add-role').click(addRole);

            $rolesBlock.on('click', '.add-person', function() {
                var index = $(this).data('index');
                addPerson(index);
            });

            $rolesBlock.on('click', '.delete-role', function() {
                var index = $(this).data('index');
                deleteRole(index);
            });

            $rolesBlock.on('click', '.delete-person', function() {
                var $deleteButton = $(this);
                deletePerson($deleteButton);
            });

            function addRole() {
                var index = getLastRoleIndex() + 1;
                var preparedRoleRow = replaceIndex(roleTemplate, index);
                $rolesBlock.append(preparedRoleRow);

                personsData[index] = [0];
                var $deleteRoleButtons = getDeleteRoleButtons();
                $deleteRoleButtons.show();
            }

            function addPerson(index) {
                var $personsBlock = $rolesBlock.find('#role-person-' + index);
                var personIndex = getLastPersonIndex(index) + 1;
                var preparedRoleRow = replaceIndex(rolePersonTemplate, index);
                preparedRoleRow = replacePersonIndex(preparedRoleRow, personIndex);
                $personsBlock.append(preparedRoleRow);

                personsData[index].push(personIndex);
                var $deletePersonButtons = getDeletePersonButtons($personsBlock);
                $deletePersonButtons.show();
            }

            function deleteRole(index) {
                $rolesBlock.find('.role-wrapper[data-index=' + index + ']').remove();

                delete personsData[index];
                // Hide delete buttons if amount of roles is 1
                if (Object.keys(personsData).length === 1) {
                    var $deleteRoleButtons = getDeleteRoleButtons();
                    $deleteRoleButtons.hide();
                }
            }

            function deletePerson($deleteButton) {
                var $personRow = $deleteButton.closest('.form-group');
                var index = $personRow.data('index');
                var personIndex = $deleteButton.data('person-index');
                var $personsBlock = $personRow.parent();

                removeArrayItemByValue(personsData[index], personIndex);
                $personRow.remove();

                // Hide delete buttons if amount of persons is 1
                if (personsData[index].length === 1) {
                    var $deletePersonButtons = getDeletePersonButtons($personsBlock);
                    $deletePersonButtons.hide();
                }
            }

            function getDeleteRoleButtons() {
                return $rolesBlock.find('.delete-role');
            }

            function getDeletePersonButtons($personsBlock) {
                return $personsBlock.find('.delete-person');
            }

            function initPersonsData() {
                var $roleRows = $rolesBlock.find('.role-wrapper');
                if ($roleRows.length > 1) {
                    $rolesBlock.find('.delete-role').show();
                }

                $.each($roleRows, function() {
                    var $roleRow = $(this);
                    var index = $roleRow.data('index');

                    personsData[index] = [];

                    var $personsBlock = $roleRow.find('.role-persons .form-group')

                    $.each($personsBlock, function(personIndex) {
                        personsData[index].push(personIndex);
                    });

                    if ($personsBlock.length > 1) {
                        $roleRow.find('.delete-person').show();
                    }
                });
            }

            function getLastRoleIndex() {
                var roleIndexes = Object.keys(personsData);
                return Math.max.apply(null, roleIndexes) || 0;
            }

            function getLastPersonIndex(index) {
                return Math.max.apply(null, personsData[index]) || 0;
            }

            function replaceIndex(template, index) {
                return template.replace(indexRegexp, index)
            }

            function replacePersonIndex(template, index) {
                return template.replace(personIndexRegexp, index)
            }
        });
    </script>
@endsection

<div class="form-group{{$errors->has('name') ? ' has-error' : ''}}">
    {{Form::label('name', 'Show Name *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::text('name', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
    </div>
    @if($errors->has('name'))
        <p class="help-block">{{$errors->first('name')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('theater_id') ? ' has-error' : ''}}">
    {{Form::label('theater_id', 'Theater *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::select('theater_id', $theaterOptions, null, ['class' => 'form-control col-md-7 col-xs-12', 'placeholder' => 'Select a theater...'])}}
    </div>
    @if($errors->has('theater_id'))
        <p class="help-block">{{$errors->first('theater_id')}}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
    {{ Form::label('image', 'Image *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::file('image', ['class' => 'form-control col-md-7 col-xs-12','style'=>'float:left;', 'id' => 'image-input']) }}
        <div class="image-preview-wrapper{{ !$imageUrl ? ' hidden' : '' }}" id="image-preview-wrapper">
            <img src="{{$imageUrl}}" class="form-image-preview" id="image-preview" alt="No Image">
        </div>
    </div>
    @if ( $errors->has('image') )
        <p class="help-block">{{ $errors->first('image') }}</p>
    @endif
</div>

<div class="form-group{{$errors->has('preview_at') ? ' has-error' : ''}}">
    {{Form::label('preview_at', 'Preview Date and Time *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-4 col-sm-6 col-xs-12">
        {{Form::text('preview_at', !empty($show) ? $show->formated_preview_at : null, ['class' => 'form-control datetime-input col-md-12 col-xs-12'])}}
    </div>
    @if ( $errors->has('preview_at') )
        <p class="help-block">{{$errors->first('preview_at')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('opening_night_at') ? ' has-error' : ''}}">
    {{Form::label('opening_night_at', 'Opening Night Date and Time *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-4 col-sm-6 col-xs-12">
        {{Form::text('opening_night_at', !empty($show) ? $show->formated_opening_night_at : null, ['class' => 'form-control datetime-input col-md-12 col-xs-12'])}}
    </div>
    @if ( $errors->has('opening_night_at') )
        <p class="help-block">{{$errors->first('opening_night_at')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('engagement_at') ? ' has-error' : ''}}">
    {{Form::label('engagement_at', 'Engagement Date and Time *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-4 col-sm-6 col-xs-12">
        {{Form::text('engagement_at', !empty($show) ? $show->formated_engagement_at : null, ['class' => 'form-control datetime-input col-md-12 col-xs-12'])}}
    </div>
    @if ( $errors->has('engagement_at') )
        <p class="help-block">{{$errors->first('engagement_at')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('engagement_end') ? ' has-error' : ''}}">
    {{Form::label('engagement_end', 'Engagement End Date and Time', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-4 col-sm-6 col-xs-12">
        {{Form::text('engagement_end', !empty($show) ? $show->formated_engagement_end : null, ['class' => 'form-control datetime-input col-md-12 col-xs-12'])}}
    </div>
    @if ( $errors->has('engagement_end') )
        <p class="help-block">{{$errors->first('engagement_end')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('closing_at') ? ' has-error' : ''}}">
    {{Form::label('closing_at', 'Closing Date and Time', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-4 col-sm-6 col-xs-12">
        {{Form::text('closing_at', !empty($show) ? $show->formated_closing_at : null, ['class' => 'form-control datetime-input col-md-12 col-xs-12'])}}
    </div>
    @if ( $errors->has('closing_at') )
        <p class="help-block">{{$errors->first('closing_at')}}</p>
    @endif
</div>

<div class="form-group">
    {{Form::label('schedule', 'Schedule', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
</div>

@foreach ($weekDaysAliases as $dayName => $dayAlias)
    <div class="form-group schedule-row{{($errors->has("schedule.$dayAlias.start") || $errors->has("schedule.$dayAlias.end")) ? ' has-error' : ''}}">
        {{Form::label("schedule[$dayAlias][start]", $dayName, ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
        <div class="col-md-3 col-sm-6 col-xs-12">
            {{Form::text("schedule[$dayAlias][start]", !empty($schedule) ? $schedule[$dayAlias]['start'] : null, ['class' => 'form-control time-input'])}}
        </div>
        <div class="col-md-3 col-md-offset-0 col-sm-6 col-sm-offset-3 col-xs-12 schedule-end">
            {{Form::text("schedule[$dayAlias][end]", !empty($schedule) ? $schedule[$dayAlias]['end'] : null, ['class' => 'form-control time-input'])}}
        </div>
        @if ($errors->has("schedule.$dayAlias.start"))
            <p class="help-block">{{$errors->first("schedule.$dayAlias.start")}}</p>
        @elseif ($errors->has("schedule.$dayAlias.end"))
            <p class="help-block">{{$errors->first("schedule.$dayAlias.end")}}</p>
        @endif
    </div>
@endforeach

<div id="roles-block">
    <div class="form-group">
        {{Form::label('roles', 'Roles', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    </div>
    <div class="separator"></div>
    @if (!empty($roles))
        @foreach ($roles as $index => $roleData)
            <div class="role-wrapper" data-index="{{$index}}">
                <div class="delete-button delete-role" data-index="{{$index}}"></div>
                <div class="form-group{{($errors->has("roles[$index].role")) ? ' has-error' : ''}}">
                    {{Form::label("roles[$index][role]", 'Role', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                    <div class="col-md-3 col-sm-6 col-xs-12">
                        {{Form::text("roles[$index][role]", $roleData['role'], ['class' => 'form-control'])}}
                    </div>
                    @if ($errors->has("roles[$index].role"))
                        <p class="help-block">{{$errors->first("roles[$index].role")}}</p>
                    @endif
                </div>
                <div class="role-persons" id="role-person-{{$index}}" data-index="{{$index}}">
                    @foreach ($roleData['person'] as $personIndex => $personName)
                        <div class="form-group{{($errors->has("roles[$index].person[$personIndex]")) ? ' has-error' : ''}}" data-index="{{$personIndex}}">
                            {{Form::label("roles[$index][person][$personIndex]", 'Person', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                            <div class="person-row col-md-3 col-sm-6 col-xs-12">
                                {{Form::text("roles[$index][person][$personIndex]", $personName, ['class' => 'form-control'])}}
                                <div class="delete-button delete-person col-md-offset-9 col-sm-offset-9" data-person-index="0"></div>
                            </div>
                            @if ($errors->has("roles[$index].person[$personIndex]"))
                                <p class="help-block">{{$errors->first("roles[$index].person[$personIndex]")}}</p>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="plus-button fa fa-plus-circle fa-fw add-person col-md-offset-6 col-sm-offset-9" data-index="{{$index}}"></div>
                <div class="separator"></div>
            </div>
        @endforeach
    @else
        <div class="role-wrapper" data-index="0">
            <div class="delete-button delete-role" data-index="0"></div>
            <div class="form-group">
                {{Form::label("roles[0][role]", 'Role', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                <div class="col-md-3 col-sm-6 col-xs-12">
                    {{Form::text("roles[0][role]", null, ['class' => 'form-control'])}}
                </div>
            </div>
            <div class="role-persons" id="role-person-0" data-index="0">
                <div class="form-group" data-index="0">
                    {{Form::label("roles[0][person][0]", 'Person', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                    <div class="person-row col-md-3 col-sm-6 col-xs-12">
                        {{Form::text("roles[0][person][0]", null, ['class' => 'form-control'])}}
                        <div class="delete-button delete-person col-md-offset-9 col-sm-offset-9" data-person-index="0"></div>
                    </div>
                </div>
            </div>
            <div class="plus-button fa fa-plus-circle fa-fw add-person col-md-offset-6 col-sm-offset-9" data-index="0"></div>
            <div class="separator"></div>
        </div>
    @endif
</div>
<div class="plus-button fa fa-plus-circle fa-fw col-md-offset-3 col-sm-offset-3" id="add-role"></div>

<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        {{Form::submit('Save', ['class' => 'btn btn-primary', 'onclick' => 'showLoader()', 'data-toggle' => 'modal', 'data-target' => '.bs-example-modal-sm'])}}
        {{Html::link( backend_url('shows'), 'Cancel', ['class' => 'btn btn-default'])}}
    </div>
</div>

<script type="text/template" id="role-template">
    <div class="role-wrapper" data-index="{index}">
        <div class="delete-button delete-role" data-index="{index}"></div>
        <div class="form-group">
            <label for="roles[{index}][role]" class="control-label col-md-3 col-sm-3 col-xs-12">Role</label>
            <div class="col-md-3 col-sm-6 col-xs-12">
                <input class="form-control" name="roles[{index}][role]" type="text" id="roles[{index}][role]">
            </div>
        </div>
        <div class="role-persons" id="role-person-{index}" data-index="{index}">
            <div class="form-group" data-index="{index}">
                <label for="roles[{index}][person][0]" class="control-label col-md-3 col-sm-3 col-xs-12">Person</label>
                <div class="person-row col-md-3 col-sm-6 col-xs-12">
                    <input class="form-control" name="roles[{index}][person][0]" type="text" id="roles[{index}][person][0]">
                    <div class="delete-button delete-person col-md-offset-9 col-sm-offset-9" data-person-index="0"></div>
                </div>
            </div>
        </div>
        <div
                class="plus-button fa fa-plus-circle fa-fw add-person col-md-offset-6 col-sm-offset-9 col-xs-offset-12"
                data-index="{index}"
        ></div>
        <div class="separator"></div>
    </div>
</script>

<script type="text/template" id="role-person-template">
    <div class="form-group" data-index="{index}">
        <label for="roles[{index}][person][{personIndex}]" class="control-label col-md-3 col-sm-3 col-xs-12">Person</label>
        <div class="person-row col-md-3 col-sm-6 col-xs-12">
            <input class="form-control" name="roles[{index}][person][{personIndex}]" type="text" id="roles[{index}][person][{personIndex}]">
            <div class="delete-button delete-person col-md-offset-9 col-sm-offset-9" data-person-index="{personIndex}"></div>
        </div>
    </div>
</script>

