
<div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
        {{ Form::label('name', 'Name *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
        <div class="col-md-6 col-sm-6 col-xs-12">
                {{ Form::text('name', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
        </div>
        @if ( $errors->has('name') )
                <p class="help-block">{{ $errors->first('name') }}</p>
        @endif
</div>

<div class="form-group{{ $errors->has('uids') ? ' has-error' : '' }}" >
        {{ Form::label('User', 'Users List *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
        <div id="userCheckbox" class="col-md-6 col-sm-6 col-xs-12" style="border:2px solid #ddd;overflow-y:scroll;max-height:400px;width:70%;margin-left:10px;"> User List Will display here ... </div>
</div>

<div class="ln_solid"></div>
<div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                {{ Form::submit('Send', ['class' => 'btn btn-primary']) }}
                {{ Html::link( backend_url('groups'), 'Cancel', ['class' => 'btn btn-default']) }}
        </div>
</div>

