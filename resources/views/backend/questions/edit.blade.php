@extends( 'backend.layouts.app' )

@section('title', 'Edit Article')

@section('CSSLibraries')
    <!-- TimePicker CSS -->
    <link href="{{ backend_asset('libraries/jquery/dist/jquery-ui.css') }}" rel="stylesheet">
    <link href="{{ backend_asset('libraries/timpicker/jquery-ui-timepicker-addon.css') }}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <script src="{{ backend_asset('libraries/timpicker/jquery-ui-timepicker-addon.js') }}"></script>
@endsection

@section('content')
    @include( 'backend.layouts.popups')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Edit Question</h3>
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
                            <h2>Edit Question <small>-- Fill the required fields to add / modify record</small></h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />
                        </div>

                        {{Form::model($question, ['url' => ['backend/questions/update', $question->id], 'method' => 'PUT' , 'class' => 'form-horizontal form-label-left', 'role' => 'form'])}}
                            @include('backend.questions.form')
                        {{Form::close()}}
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /page content -->
@endsection
