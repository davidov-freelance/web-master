@extends( 'backend.layouts.app' )

@section('title', 'Edit Show')

@section('content')
    @include( 'backend.layouts.popups')
    <!-- page content -->
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left">
                    <h3>Edit Show</h3>
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
                            <h2>Edit Show <small>-- Fill the required fields to add / modify record</small></h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">
                            <br />
                        </div>

                        {{Form::model($show, ['url' => ['backend/shows/update', $show->id], 'method' => 'PUT', 'files' => true, 'class' => 'form-horizontal form-label-left', 'role' => 'form'])}}
                            @include('backend.shows.form')
                        {{Form::close()}}
                    </div>
                </div>
            </div>

        </div>
    </div>
    <!-- /page content -->
@endsection
