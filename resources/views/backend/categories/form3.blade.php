<?php
$imageUrl  = 0;

if(isset($category)) {
    $imageUrl                   =  $category->image_url;
   // $selectedImageUrl                  =  $category->selected_image_url;
}

?>

@section('JSLibraries')
@endsection


<div class="form-group{{ $errors->has('category_name') ? ' has-error' : '' }}">
    {{ Form::label('title', 'Category Name *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('category_name', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('category_name') )
    <p class="help-block">{{ $errors->first('category_name') }}</p>
    @endif
</div>


<div class="form-group{{ $errors->has('sort_order') ? ' has-error' : '' }}">
    {{ Form::label('sort_order', 'Sort Order *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('sort_order', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('sort_order') )
        <p class="help-block">{{ $errors->first('sort_order') }}</p>
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

{{--<div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">--}}
    {{--{{ Form::label('Image', 'Image', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}--}}
    {{--<div class="col-md-6 col-sm-6 col-xs-12">--}}
        {{--{{ Form::file('image', ['class' => 'form-control col-md-7 col-xs-12','style'=>'width:70%;float:left;']) }}--}}

         {{--@if($imageUrl !='0')--}}
        {{--<div style="float: left;padding: 10px 15px; border:1px solid #ddd; min-height:75px;margin-left:10px;" >  <img  src="{{$imageUrl}}" style="width:50px; max-height:50px; background: #eee;">  </div>--}}
        {{--@endif--}}
    {{--</div>--}}

    {{--@if ( $errors->has('image') )--}}
        {{--<p class="help-block">{{ $errors->first('image') }}</p>--}}
    {{--@endif--}}
{{--</div>--}}


<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        {{ Form::submit('Save', ['class' => 'btn btn-primary']) }}
        {{ Html::link( backend_url('categories'), 'Cancel', ['class' => 'btn btn-default']) }}
    </div>
</div>



