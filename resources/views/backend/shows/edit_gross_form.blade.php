<?php

$gross = old('gross');

?>

@section('CSSLibraries')
    <!-- TimePicker CSS -->
    <link href="{{backend_asset('libraries/jquery/dist/jquery-ui.css')}}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <script src="{{ backend_asset('libraries/weekpicker/jquery.weekpicker.js') }}"></script>
    <script src="{{ backend_asset('libraries/papaparse/papaparse.min.js') }}"></script>
@endsection

@section('inlineJS')
    <script>

        $(function () {
            var $grossForm = $('#gross-form');
            var $weekInput = $('#week-input');
            var startDate = '{{$show->start_date}}';
            var endDate = '{{$show->end_date}}';
            var grossWeekDay = {{$show->end_week_day}};
            var takeFirstDay = {{$show->take_first_day}};

            initGrossData();

            $grossForm.submit(function() {
                showLoader(true);
            });

            function initGrossData() {
                initWeekpicker($weekInput, startDate, endDate, grossWeekDay, takeFirstDay);

                var startWeekString = $weekInput.val();
                if (startWeekString) {
                    setWeekValue(startWeekString);
                }
            }

            function setWeekValue(startWeekDate) {
                var weekDate = new Date(prepareDate(startWeekDate));
                var weekNumber = getWeekNumber(weekDate);
                var formatedWeekText = getWeekFormatedText(weekNumber, startWeekDate);
                $('#week-input_weekpicker').val(formatedWeekText);
            }
        });
    </script>
@endsection

<div id="gross-block">
    {{Form::hidden('show_id', $showGross->show_id)}}
    {{Form::hidden('id', $showGross->id)}}
    <div class="gross-row" data-index="0">
        <div class="form-group week-row{{$errors->has('end_week_date') ? ' has-error' : ''}}">
            {{Form::label('week-input', 'Week Number *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
            <div class="col-md-6 col-sm-6 col-xs-12">
                {{Form::text('end_week_date', null, ['class' => 'form-control col-md-7 col-xs-12 week-input', 'id' => 'week-input'])}}
            </div>
            @if($errors->has('end_week_date'))
                <p class="help-block">{{$errors->first('end_week_date')}}</p>
            @endif
        </div>

        <div class="form-group{{$errors->has('attendees_amount') ? ' has-error' : ''}}">
            {{Form::label('attendees_amount', 'Number of attendees*', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
            <div class="col-md-6 col-sm-6 col-xs-12">
                {{Form::text('attendees_amount', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
            </div>
            @if($errors->has('attendees_amount'))
                <p class="help-block">{{$errors->first('attendees_amount')}}</p>
            @endif
        </div>

        <div class="form-group{{$errors->has('performances_amount') ? ' has-error' : ''}}">
            {{Form::label('performances_amount', 'Number of Performances *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
            <div class="col-md-6 col-sm-6 col-xs-12">
                {{Form::text('performances_amount', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
            </div>
            @if($errors->has('performances_amount'))
                <p class="help-block">{{$errors->first('performances_amount')}}</p>
            @endif
        </div>

        <div class="form-group{{$errors->has('earnings') ? ' has-error' : ''}}">
            {{Form::label('earnings', 'Earnings *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
            <div class="col-md-6 col-sm-6 col-xs-12">
                {{Form::text('earnings', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
            </div>
            @if($errors->has('earnings'))
                <p class="help-block">{{$errors->first('earnings')}}</p>
            @endif
        </div>
    </div>
</div>

<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        {{Form::submit('Save', ['class' => 'btn btn-primary', 'id' => 'save_gross'])}}
        {{Html::link( backend_url('shows/gross/' . $show->id), 'Cancel', ['class' => 'btn btn-default'])}}
    </div>
</div>