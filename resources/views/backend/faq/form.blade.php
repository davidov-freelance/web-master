<script>

        function addMore(){

                var formFields = '';
                formFields = '<div class="form-group">';
                formFields += '<label for="heading" class="control-label col-md-3 col-sm-3 col-xs-12">Heading *</label>';
                formFields += '<div class="col-md-6 col-sm-6 col-xs-12">';
                formFields += '<input type="text" name="heading[]"  class="form-control col-md-7 col-xs-12">';
                formFields += '</div>';
                formFields += '</div>';

                formFields += '<div class="form-group">';
                formFields += ' <label for="heading" class="control-label col-md-3 col-sm-3 col-xs-12">Description *</label>';
                formFields += '  <div class="col-md-6 col-sm-6 col-xs-12">';

                formFields += ' <textarea name="description[]" class="form-control col-md-7 col-xs-12"></textarea>';
                formFields += '  </div>';

                formFields += ' </div>';

              $('#content_area').append(formFields);
        }

</script>
<?php

        if(count($content) >  0) {

             $firstRow = $content[0];
                if($firstRow) {
                        $key = $firstRow->key;
                }
        }
?>

<div id="content_area">
@if(count($content) > 0 )
        @foreach($content as $data)
<div class="form-group{{ $errors->has('heading') ? ' has-error' : '' }}">
        {{ Form::label('heading', 'Heading *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
        <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="text" name="heading[]" value="{{ $data->heading }}" class="form-control col-md-7 col-xs-12">
        </div>
        @if ( $errors->has('heading') )
                <p class="help-block">{{ $errors->first('heading') }}</p>
        @endif
</div>

<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
        {{ Form::label('description', 'Description', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
        <div class="col-md-6 col-sm-6 col-xs-12">

                <textarea name="description[]" rows="8" class="form-control col-md-7 col-xs-12">{{ $data->description }}</textarea>
        </div>
        @if ( $errors->has('description') )
                <p class="help-block">{{ $errors->first('description') }}</p>
        @endif
</div>

@endforeach
@endif

</div>
<div class="form-group">
        {{ Form::label('', '', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
        <div class="col-md-6 col-sm-6 col-xs-12">
                <input type="button" class="btn btn-warning" style="float:right" name="ADD MORE" value="ADD MORE" onclick="addMore();"/>
                <input type="hidden" name="type" value="{{ $key }}" />
        </div>

</div>
<div class="ln_solid"></div>
<div class="form-group">
        <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
                {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
                {{ Html::link( backend_url('faq'), 'Cancel', ['class' => 'btn btn-default']) }}
        </div>
</div>

