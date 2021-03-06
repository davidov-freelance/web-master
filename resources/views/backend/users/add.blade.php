@extends( 'backend.layouts.app' )

@section('title', 'Add User')

@section('content')

    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Add User</h3>
                </div>

                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                        <div class="input-group">

                        </div>
                    </div>
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
                            <h2>Add User Form  <small>Fill all form fields</small></h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content"> <br /> </div>

                        {!! Form::open( ['url' => ['backend/user/create'], 'method' => 'POST','files' => true , 'class' => 'form-horizontal form-label-left', 'role' => 'form']) !!}
                        @include( 'backend.users.form' )
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
