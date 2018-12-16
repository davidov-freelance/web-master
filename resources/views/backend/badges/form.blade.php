<?php
$imageUrl  = 0;

if(isset($badge)) {
    $imageUrl = $badge->badge_icon;
}

?>


@section('inlineJS')
    <script>
        $(function () {
            initImagePreview('#image-input', '#image-preview-wrapper');
        });
    </script>
@endsection

<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
    {{ Form::label('name', 'Badge Name *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('name', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('name') )
    <p class="help-block">{{ $errors->first('name') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('icon') ? ' has-error' : '' }}">
    {{ Form::label('icon', 'Icon *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::file('icon', ['class' => 'form-control col-md-7 col-xs-12','style'=>'float:left;', 'id' => 'image-input']) }}
        <div class="image-preview-wrapper{{ !$imageUrl ? ' hidden' : '' }}" id="image-preview-wrapper">
            <img src="{{$imageUrl}}" class="form-image-preview" id="image-preview" alt="No Image">
        </div>
    </div>
    @if ( $errors->has('icon') )
        <p class="help-block">{{ $errors->first('icon') }}</p>
    @endif
</div>

<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
        {{ Html::link( backend_url('badges'), 'Cancel', ['class' => 'btn btn-default']) }}
    </div>
</div>



