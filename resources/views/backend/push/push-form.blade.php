<?php

$uids = old('uids');

if ($uids) {
    $uids = json_encode($uids);
}

$postId = old('post_id');

?>

@section('CSSLibraries')
    <link href="{{ backend_asset('libraries/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <script src="{{ backend_asset('libraries/select2/dist/js/select2.full.min.js') }}"></script>
@endsection


@section('inlineJS')
    <script>

        $(function() {
            var type = $('#type').val();
            var notificationType = $('#notification_type').val();
            var uids = '<?=$uids?>';
            var postId = '{{$postId}}';
            var oldUids = uids && JSON.parse(uids);
            var $uidsBlock = $('#userCheckbox');

            var callback;
            if (oldUids) {
                callback = initOldUIDS;
            }

            callListing(type, callback);

            if (notificationType !== 'generic') {
                displayPostListing(notificationType, postId);
            }

            $('#push_form').on('keypress', function(e) {
                var keyCode = e.keyCode || e.which;
                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }
            });

            function initOldUIDS() {
                oldUids.forEach(function(uids) {
                    $uidsBlock.find('input[value=' + uids + ']').prop('checked', true);
                });
            }
        });

        // It needs for checkbox "all"
        function checkUncheckAll(value) {
            $('#userCheckbox input:checkbox').prop('checked', value);
        }

        function callListing(type, callback) {
            if (!type) {
                type = $('#search_type').val();
            } else {
                $('#keyword').val('');
            }
            var keyword = $('#keyword').val();

            if (type === 'group') {
                showGroupList('all', keyword, callback);
            } else if (type === 'users') {
                showUserList('all', keyword, callback);
            } else {
                showTagsList('all', keyword, callback);
            }

            $('#search_type').val(type);
        }

        function initSelect2() {
            $("#post_id").select2({
                placeholder: "Select the shows that relate to the article",
                allowClear: true
            });
        }
    </script>
@endsection

<div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
    {{ Form::label('type', 'Send To *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{
            Form::select(
                'type',
                [
                    'users' => 'Individual Users',
                    'group' => 'Group',
                    'tags' => 'Tags',
                ],
                null,
                [
                    'class' => 'form-control col-md-7 col-xs-12',
                    'onchange' => 'callListing(this.value)'
                ]
            )
        }}
    </div>
    @if ( $errors->has('type') )
        <p class="help-block">{{ $errors->first('type') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('uids') ? ' has-error' : '' }}">
    {{ Form::label('', '', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="input-group col-md-6 col-sm-6 col-xs-6" style="margin-bottom: -5px; margin-right: -5px;float: right">
            <input type="text" name="keyword" id="keyword" class="form-control " placeholder="Search for...">
            <input type="hidden" name="search_type" id="search_type">
            <span class="input-group-btn">
                <button onclick="callListing();" class="btn btn-default" type="button">Go!</button>
            </span>
        </div>
    </div>
</div>

<div class="form-group{{ $errors->has('uids') ? ' has-error' : '' }}">
    {{ Form::label('User', 'Select Users/Group/Tags *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div id="userCheckbox" class="col-md-6 col-sm-6 col-xs-12"
         style="border:2px solid #ddd;overflow-y:scroll;max-height:350px;width:48%;margin-left:10px;"> User/Group List
        Will display here ...
    </div>
    @if ( $errors->has('uids[]') )
        <p class="help-block">{{ $errors->first('uids[]') }}</p>
    @endif
</div>

<div class="form-group{{ $errors->has('notification_type') ? ' has-error' : '' }}">
    {{ Form::label('notification_type', 'Notification Type *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::select('notification_type', [
        'generic' => 'Generic ( Send Custom message - no redirection )',
        'article' => 'Articles ( Send Custom message - with redirection )',
        'event'=>'Events ( Send Custom message - with redirection )'],null, ['class' => 'form-control col-md-7 col-xs-12','onchange' => 'displayPostListing(this.value)'] ) }}
    </div>
    @if ( $errors->has('notification_type') )
        <p class="help-block">{{ $errors->first('notification_type') }}</p>
    @endif
</div>

<div id="post-area" style="display: none">
    {{ Form::label('select', 'Select Post *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
</div>

<div class="form-group{{ $errors->has('message') ? ' has-error' : '' }}">
    {{ Form::label('message', 'Message *', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12']) }}
    <div class="col-md-6 col-sm-6 col-xs-12">
        {{ Form::textArea('message', null, ['class' => 'form-control col-md-7 col-xs-12','rows'=>'3']) }}
    </div>
    @if ( $errors->has('message') )
        <p class="help-block">{{ $errors->first('message') }}</p>
    @endif
</div>

<div class="ln_solid"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        {{ Form::submit('Send', ['class' => 'btn btn-primary']) }}
        {{ Html::link( backend_url('push/send'), 'Cancel', ['class' => 'btn btn-default']) }}
    </div>
</div>
