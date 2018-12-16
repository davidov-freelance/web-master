@extends( 'backend.layouts.app' )

@section('title', 'Edit Cms Page')

@section('JSLibraries')
    <script src="{{ backend_asset('libraries/tinymce/js/tinymce/tinymce.min.js') }}"></script>

@endsection


@section('inlineJS')

    {{--<script>tinymce.init({ selector:'textarea' });</script>--}}

@endsection
@section('content')

    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Edit Cms Page</h3>
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
                            <h2>Edit Cms Page <small></small></h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />

                        </div>
<?php //dd($cms); ?>
                        {!! Form::model($cms, ['url' => ['backend/cms',$cms->id], 'method' => 'PUT', 'class' => 'form-horizontal form-label-left', 'role' => 'form']) !!}
                        @include( 'backend.cms.form' )
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /page content -->
    <!-- /#page-wrapper -->


@endsection
