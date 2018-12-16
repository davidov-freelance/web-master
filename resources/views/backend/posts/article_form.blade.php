<?php

$imageUrl   =  $selectedMakeId = 0;
$topBar     = 'Broadway Connected';
$groupIds   = array();
$groupblock = 'none';

if(isset($post)) {
    $imageUrl                 =  $post->post_image;
    $topBar                   =  $post->top_bar;
    $group_ids                 =  $post->group_ids;

    $groupIds =  array_map('intval', explode(',', $group_ids));
    if(!empty($group_ids)){
        $groupblock = 'block';
    }
}

$postTag = isset($postTag) ? $postTag : null ;

$shows = old('show_ids');

if (empty($shows) && !empty($post->show_ids)) {
    $shows = $post->show_id_values;
}

$keys = array_keys($showOptions);

$postTag = old('tags');

if (empty($postTag) && !empty($post->tags)) {
    $postTag = $post->tags_ids;
}

$userId = null;

if (empty($post)) {

    $sendNotification = old('send_notification', false);
    $userId = Auth::user()->id;
}

?>

@section('inlineJS')
    <script>
        $(function () {
            var elemsOptions = {
                postedAs: '#model',
                availability: '#availability',
                groups: '#groups',
                tags: '.select2_multiple',
                publishedDate: '#published_date',
                publishedTime: '#published_time',
                title: '#title',
                titleCounter: '#title-counter',
                titleMaxLength: '{!! $titleMaxLength !!}',
                imageInput: '#image-input',
                imagePreviewWrapper: '#image-preview-wrapper',
                statusSelect: '#status-select',
                topBar: '#top_bar',
                publishedDateField: '#published-date-field',
                previewButton: '#preview-button',
                previewBackground: '/public/images/preview_screen.jpeg',
            };

            var notificationOptions = {
                notificationBlock: '#notification_block',
                notificationToggle: '#notification .iCheck-helper, #notification label',
                notificationCheckbox: '#send_notification',
                notificationTitle: '#notification_title',
                notificationDescription: '#notification_description',
                title: '#title',
                description: '#description',
            };

            var previewFieldsData = {
                title: {
                    name: 'title',
                    previewSelector: '#popup_body .preview-title'
                },
                description: {
                    name: 'description',
                    previewSelector: '#popup_body .preview-description'
                },
                headline: {
                    name: 'top_bar',
                    previewSelector: '#popup_body .preview-header'
                },
                image: {
                    name: 'image',
                    imageThumbSelector: '#image-preview',
                    previewSelector: '#popup_body .preview-image'
                },
            };

            initMainPostField(elemsOptions, notificationOptions, previewFieldsData);

            $("#shows-multiselect").select2({
                placeholder: "Select the shows that relate to the article",
                allowClear: true
            });
        });
    </script>
@endsection

@include( 'backend.layouts.preview_popup')

<div class="form-group with-counter{{ $errors->has('title') ? ' has-error' : '' }}">
    {{ Form::label('title', 'Title *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('title', null, ['class' => 'form-control col-md-7 col-xs-12', 'id' => 'title']) }}
        <span class="chars-counter">
            <span id="title-counter">{{!empty($post->title) ? strlen($post->title) : '0'}}</span>/<span>{{$titleMaxLength}}</span>
        </span>
    </div>
    @if ( $errors->has('title') )
        <p class="help-block">{{ $errors->first('title') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('tags') ? ' has-error' : '' }}">
    {{ Form::label('tags[]', 'Tags *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::select('tags[]',  $tags , $postTag, ['class' => 'form-control col-md-7 col-xs-12 select2_multiple','multiple'=>'multiple']) }}
    </div>
    @if ( $errors->has('tags') )
        <p class="help-block">{{ $errors->first('tags') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('posting_type') ? ' has-error' : '' }}">
    {{ Form::label('model', 'Posted As *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::select('posting_type',  array('admin' => 'Admin', 'user' => 'User'), null, ['class' => 'form-control col-md-7 col-xs-12', 'id' => 'model']) }}
    </div>
    @if ( $errors->has('posting_type') )
        <p class="help-block">{{ $errors->first('posting_type') }}</p>
    @endif
</div>

<div id="top_bar" class="form-group{{ $errors->has('top_bar') ? ' has-error' : '' }}">
    {{ Form::label('top_bar', 'Heading *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('top_bar', $topBar, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('top_bar') )
        <p class="help-block">{{ $errors->first('top_bar') }}</p>
    @endif
</div>

<div id="top_bar" class="form-group{{ $errors->has('user_id') ? ' has-error' : '' }}">
    {{ Form::label('user_id', 'Contributed By *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::select('user_id',  $userOptions , $userId, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('user_id') )
        <p class="help-block">{{ $errors->first('user_id') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
    {{ Form::label('description', 'Article Description', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::textArea('description', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('description') )
    <p class="help-block">{{ $errors->first('description') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('image') ? ' has-error' : '' }}">
    {{ Form::label('Image', 'Image', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::file('image', ['class' => 'form-control col-md-7 col-xs-12','style'=>'float:left;', 'id' => 'image-input']) }}

        {{ Form::hidden('image_url', $imageUrl) }}

        <div class="image-preview-wrapper{{ !$imageUrl ? ' hidden' : '' }}" id="image-preview-wrapper">
            <img src="{{$imageUrl}}" class="form-image-preview" id="image-preview" alt="No Image">
        </div>
    </div>
    @if ( $errors->has('image') )
        <p class="help-block">{{ $errors->first('image') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('source_url') ? ' has-error' : '' }}">
    {{ Form::label('source_url', 'Source Link *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('source_url', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('source_url') )
        <p class="help-block">{{ $errors->first('source_url') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('link_text') ? ' has-error' : '' }}">
    {{ Form::label('link_text', 'Link Text', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::text('link_text', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('link_text') )
        <p class="help-block">{{ $errors->first('link_text') }}</p>
    @endif
</div>

<div  class="form-group">
    {{ Form::label('source', 'Note ', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        If you add source url then it will appear as read more link on Mobile App. Valid source format "http://www.google.com"

    </div>

</div>

<div class="form-group{{ $errors->has('availability') ? ' has-error' : '' }}">
    {{ Form::label('availability', 'Availability *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::select('availability',  array('all' => 'All Users', 'groups' => 'Specific Groups'),null, ['class' => 'form-control col-md-7 col-xs-12', 'id' => 'availability']) }}
    </div>
    @if ( $errors->has('availability') )
        <p class="help-block">{{ $errors->first('availability') }}</p>
    @endif
</div>

<div id="groups" class="form-group{{ $errors->has('groups') ? ' has-error' : '' }}">
    {{ Form::label('groups', 'Group *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::select('groups[]',  $groups ,$groupIds, ['multiple'=>'multiple', 'class' => 'form-control col-md-7 col-xs-12']) }}
    </div>
    @if ( $errors->has('groups') )
        <p class="help-block">{{ $errors->first('groups') }}</p>
    @endif
</div>

<div  class="form-group">
    {{ Form::label('source', 'Please Note ', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        Availability<br>
        All : All user will have that post in their home screen<br>
        Group : Only users of that group will have this post in their home screen<br>
        Users will received Push Notification too.
    </div>
</div>

@if (empty($post))
    <div id="notification_block">
        <div  class="form-group" id="notification">
            {{ Form::label('send_notification', 'Send Notification ', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
            <div class="col-md-6 col-sm-6 col-xs-12">
                <div class="checkbox">
                    {{ Form::checkbox('send_notification',  '', null, ['class' => 'flat', 'checked' => $sendNotification]) }} Yes
                </div>
            </div>
        </div>

        <div class="form-group{{ $errors->has('notification_title') ? ' has-error' : '' }}">
            {{ Form::label('notification_title', 'Notification Title', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
            <div class="col-md-6 col-sm-6 col-xs-12">
                {{ Form::text('notification_title', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
            </div>
            @if ( $errors->has('notification_title') )
                <p class="help-block">{{ $errors->first('notification_title') }}</p>
            @endif
        </div>

        <div class="form-group{{ $errors->has('notification_description') ? ' has-error' : '' }}">
            {{ Form::label('notification_description', 'Notification Message', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
            <div class="col-md-6 col-sm-6 col-xs-12">
                {{ Form::textarea('notification_description', null, ['class' => 'form-control col-md-7 col-xs-12']) }}
            </div>
            @if ( $errors->has('notification_description') )
                <p class="help-block">{{ $errors->first('notification_description') }}</p>
            @endif
        </div>
    </div>
@endif

<div  class="form-group">
    {{ Form::label('status', 'Status ', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="checkbox">
            {{
                Form::select(
                    'status',
                    [
                        'pending' => 'Draft',
                        'approved' => 'Published',
                        'scheduled' => 'Scheduled'
                    ] ,
                    null,
                    ['class' => 'form-control col-md-7 col-xs-12', 'id' => 'status-select']
                )
            }}
        </div>
    </div>
</div>
<div id="published-date-field" class="form-group{{ $errors->has('published_date') || $errors->has('published_time') ? ' has-error' : '' }}">
    {{ Form::label('end_date', 'Publication Date Time', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="row">
        <div class="col-md-4 col-sm-6 col-xs-12">
            {{Form::text('published_date', null, ['class' => 'form-control col-md-12 col-xs-12', 'id' => 'published_date'])}}
        </div>

        <div class="col-md-2 col-sm-6 col-xs-12">
            {{Form::text('published_time', null, ['class' => 'form-control col-md-12 col-xs-12', 'id' => 'published_time', 'style'=>'margin-left:-10px;'])}}
        </div>
        @if ( $errors->has('published_date') )
            <p class="help-block">{{ $errors->first('published_date') }}</p>
        @elseif ( $errors->has('published_time') )
            <p class="help-block">{{ $errors->first('published_time') }}</p>
        @endif
    </div>
</div>

<div class="form-group{{ ($errors->has('show_ids') || $errors->has('show_ids.*')) ? ' has-error' : '' }}">
    {{ Form::label('shows-multiselect', 'Shows', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::select('show_ids[]',  $showOptions , $shows, ['class' => 'form-control col-md-7 col-xs-12', 'multiple' => 'multiple', 'id' => 'shows-multiselect']) }}
    </div>
    @if ( $errors->has('show_ids') )
        <p class="help-block">{{ $errors->first('show_ids') }}</p>
    @endif
</div>

<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        {{ Form::button('Preview', ['class' => 'btn btn-primary', 'data-toggle'=>'modal', 'id' => 'preview-button', 'data-target'=>'.preview-modal']) }}
        {{ Form::submit('Save', ['class' => 'btn btn-primary', 'onclick'=>'showLoader()', 'data-toggle'=>'modal', 'data-target'=>'.bs-example-modal-sm']) }}
        {{ Html::link( backend_url('articles'), 'Cancel', ['class' => 'btn btn-default']) }}
    </div>
</div>



