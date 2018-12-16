@section('JSLibraries')
@endsection

<div class="form-group{{ $errors->has('tag') ? ' has-error' : '' }}">
    {{ Form::label('tag', 'Tag *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('tag', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('tag') )
    <p class="help-block">{{ $errors->first('tag') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('status') ? ' has-error' : '' }}">
    {{ Form::label('model', 'Status *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::select('status',  array('1' => 'Active', '0' => 'In Active'),null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('status') )
        <p class="help-block">{{ $errors->first('status') }}</p>
    @endif
</div>


<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
        {{ Html::link( backend_url('tags'), 'Cancel', ['class' => 'btn btn-default']) }}
    </div>
</div>



