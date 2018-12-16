@extends( 'backend.layouts.app' )

@section('title', 'Edit Event')

@section('CSSLibraries')
    <link href="{{ backend_asset('libraries/select2/dist/css/select2.min.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('libraries/jquery/dist/jquery-ui.css') }}" rel="stylesheet">
    <!-- TimePicker CSS -->
    <link href="{{ backend_asset('libraries/timpicker/jquery-ui-timepicker-addon.css') }}" rel="stylesheet">

    <link href="{{ backend_asset('libraries/mjolnic-bootstrap-colorpicker/dist/css/bootstrap-colorpicker.min.css') }}" rel="stylesheet">

@endsection


@section('JSLibraries')
    <script src="{{ backend_asset('libraries/select2/dist/js/select2.full.min.js') }}"></script>
    <script src="{{ backend_asset('js/jquery.geocomplete.js') }}"></script>
    <script src=" https://maps.googleapis.com/maps/api/js?sensor=false&libraries=places&language=en&key=AIzaSyDIYUxHaaB0ytvNmfb8qCCgEiFGePU4HjU"></script>
    <script src="{{ backend_asset('libraries/timpicker/jquery-ui-timepicker-addon.js') }}"></script>
    <script src="{{ backend_asset('libraries/mjolnic-bootstrap-colorpicker/dist/js/bootstrap-colorpicker.min.js') }}"></script>
    @endsection

@section('content')

    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Edit Event</h3>
                </div>


                <br />
            </div>
            <div class="clearfix"></div>

            @include('backend.layouts.modal')
            @include( 'backend.layouts.popups')
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
                            <h2>Edit Event <small>-- Fill the required fields to add / modify record</small></h2>


                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />

                        </div>

                        {!! Form::model($post, ['url' => ['backend/events/update',$post->id], 'method' => 'PUT','files' => true , 'class' => 'form-horizontal form-label-left', 'role' => 'form']) !!}
                        @include( 'backend.posts.event_form' )
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /page content -->




    <!-- /#page-wrapper -->


@endsection
