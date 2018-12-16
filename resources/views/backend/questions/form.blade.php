@section('inlineJS')
    <script>
        $(function () {
            var disabledDatesJSON = '<?= !empty($disabledFields) ? json_encode($disabledFields) : ''?>';
            var disabledDates = [];
            if (disabledDatesJSON) {
                disabledDates = JSON.parse(disabledDatesJSON);
            }

            $('#release-datetime').datepicker({
                dateFormat: 'yy-mm-dd',
                minDate: 'now',
                beforeShowDay: function (date) {
                    var string = jQuery.datepicker.formatDate('yy-mm-dd', date);
                    return [disabledDates.indexOf(string) == -1]
                }
            });
        });
    </script>
@endsection

<div class="form-group{{$errors->has('question') ? ' has-error' : ''}}">
    {{Form::label('question', 'Question *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::text('question', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
    </div>
    @if($errors->has('question'))
        <p class="help-block">{{$errors->first('question')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('option_1') ? ' has-error' : ''}}">
    {{Form::label('option_1', 'Option 1 *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::text('option_1', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
    </div>
    @if($errors->has('option_1'))
        <p class="help-block">{{$errors->first('option_1')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('option_2') ? ' has-error' : ''}}">
    {{Form::label('option_2', 'Option 2 *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::text('option_2', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
    </div>
    @if($errors->has('option_2'))
        <p class="help-block">{{$errors->first('option_2')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('option_3') ? ' has-error' : ''}}">
    {{Form::label('option_3', 'Option 3 *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::text('option_3', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
    </div>
    @if($errors->has('option_3'))
        <p class="help-block">{{$errors->first('option_3')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('option_4') ? ' has-error' : ''}}">
    {{Form::label('option_4', 'Option 4 *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::text('option_4', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
    </div>
    @if($errors->has('option_4'))
        <p class="help-block">{{$errors->first('option_4')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('correct_answer') ? ' has-error' : ''}}">
    {{Form::label('correct_answer', 'Correct Answer *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::number('correct_answer', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
    </div>
    @if($errors->has('correct_answer'))
        <p class="help-block">{{$errors->first('correct_answer')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('release_datetime') ? ' has-error' : ''}}">
    {{Form::label('release_datetime', 'Release Date*', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            {{Form::text('release_datetime', null, ['class' => 'form-control col-md-12 col-xs-12', 'id' => 'release-datetime'])}}
        </div>
        @if ( $errors->has('release_datetime') )
            <p class="help-block">{{$errors->first('release_datetime')}}</p>
        @endif
    </div>
</div>

<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        {{Form::submit('Save', ['class' => 'btn btn-primary', 'onclick'=>'showLoader()', 'data-toggle'=>'modal', 'data-target'=>'.bs-example-modal-sm'])}}
        {{Html::link( backend_url('questions'), 'Cancel', ['class' => 'btn btn-default'])}}
    </div>
</div>

