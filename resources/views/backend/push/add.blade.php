@extends( 'backend.layouts.app' )
@section('title', 'Send Push Notification')

@section('CSSLibraries')
    <link href="{{ backend_asset('libraries/select2/dist/css/select2.min.css') }}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <script src="{{ backend_asset('libraries/select2/dist/js/select2.full.min.js') }}"></script>
@endsection

@section('content')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Push Notification</h3>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        @if ( $errors->count() )
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                There was an error while saving your form, please review below.
                            </div>
                        @endif

                        @include( 'backend.layouts.notification_message' )

                        <div class="x_title">
                            <h2>Send Push Notification to users -  <small></small></h2>
                            <div class="clearfix"></div>
                            This Section is for sending push notifications to any individual user or to any group. You can send any custom message (max 90 characters) from this section to user.
                        </div>
                        <div class="x_content"><br /></div>
                        {!!Form::open(['url' => ['backend/push/sendpush'], 'method' => 'POST', 'class' => 'form-horizontal form-label-left', 'id'=>'push_form', 'role' => 'form'])!!}
                            @include( 'backend.push.push-form' )
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /page content -->
@endsection
