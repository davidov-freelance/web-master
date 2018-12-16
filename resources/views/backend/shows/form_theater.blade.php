@section('CSSLibraries')
    <!-- TimePicker CSS -->
    <link href="{{backend_asset('libraries/jquery/dist/jquery-ui.css')}}" rel="stylesheet">
    <link href="{{backend_asset('libraries/timpicker/jquery-ui-timepicker-addon.css')}}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <script src=" https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&language=en&key=AIzaSyDIYUxHaaB0ytvNmfb8qCCgEiFGePU4HjU"></script>
    <script src="{{backend_asset('libraries/timpicker/jquery-ui-timepicker-addon.js')}}"></script>
    <script src="{{ backend_asset('js/jquery.geocomplete.js') }}"></script>
@endsection

@section('inlineJS')
    <script>
        $(function () {
            $('.datetime-input').datetimepicker({
                dateFormat: 'yy-mm-dd',
                timeFormat: 'HH:mm',
                minDate: 0
            });

            $('.time-input').timepicker({
                timeFormat: 'HH:mm',
            });

            var changeCoordsMessage = '<i class="fa fa-exclamation-triangle"></i>' +
                'The pointer has been moved and its position may not coincide with the value of the field "Address"';

            initGeocomplete('#geocomplete', '.map_canvas', '.warning-message', changeCoordsMessage);
            initImagePreview('#image-input', '#image-preview-wrapper');
        });
    </script>
@endsection

<div class="form-group{{$errors->has('name') ? ' has-error' : ''}}">
    {{Form::label('name', 'Theater Name *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::text('name', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
    </div>
    @if($errors->has('name'))
        <p class="help-block">{{$errors->first('name')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('capacity') ? ' has-error' : ''}}">
    {{Form::label('capacity', 'Theater Capacity *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::text('capacity', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
    </div>
    @if($errors->has('capacity'))
        <p class="help-block">{{$errors->first('capacity')}}</p>
    @endif
</div>

<div class="form-group{{$errors->has('location') ? ' has-error' : ''}}">
    {{Form::label('location', 'Theater Address *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::text('location', null, ['class' => 'form-control col-md-7 col-xs-12', 'id' => 'geocomplete'])}}
    </div>
    @if($errors->has('location'))
        <p class="help-block">{{$errors->first('location')}}</p>
    @endif
</div>

{{Form::hidden('address', null, ['data-geo' => 'address'])}}
{{Form::hidden('city', null, ['data-geo' => 'locality'])}}
{{Form::hidden('state', null, ['data-geo' => 'administrative_area_level_1'])}}

<div class="form-group{{$errors->has('zip') ? ' has-error' : ''}}">
    {{Form::label('v', 'Theater Zip *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{Form::text('zip', null, ['class' => 'form-control col-md-7 col-xs-12'])}}
    </div>
    @if($errors->has('zip'))
        <p class="help-block">{{$errors->first('zip')}}</p>
    @endif
</div>

<div class="form-group">
    {{ Form::label('latitude', 'latitude', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('latitude', null, ['class' => 'form-control col-md-7 col-xs-12', 'data-geo' => 'lat', 'readonly']) }}
    </div>
</div>

<div class="form-group">
    {{ Form::label('longitude', 'longitude', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('longitude', null, ['class' => 'form-control col-md-7 col-xs-12', 'data-geo' => 'lng', 'readonly']) }}
    </div>
</div>

<div class="form-group">
    {{ Form::label('Graphical View', 'Graphical View', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="map_canvas"></div>
        <span class="warning-message"></span>
    </div>
</div>

<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        {{Form::submit('Save', ['class' => 'btn btn-primary', 'onclick' => 'showLoader()', 'data-toggle' => 'modal', 'data-target' => '.bs-example-modal-sm'])}}
        {{Html::link( backend_url('shows/theaters'), 'Cancel', ['class' => 'btn btn-default'])}}
    </div>
</div>

