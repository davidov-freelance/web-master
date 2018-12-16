@extends( 'backend.layouts.app' )
@section('title', 'Gross')

@section('content')
    @include( 'backend.layouts.popups')
    <div class="right_col" role="main">
        <div class="">
            <div class="page-title">
                <div class="title_left" style="margin-bottom:10px;">
                    <h3>Edit Gross</h3>
                </div>
            </div>
            <div class="clearfix"></div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        @if ($errors->count())
                            <div class="alert alert-danger alert-dismissable">
                                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                                There was an error while saving your form, please review below.
                            </div>
                        @endif

                        @include('backend.layouts.notification_message')

                        <div class="x_title">
                            <h2>
                                Edit Gross
                                <small>-- Fill the required fields to modify record</small>
                            </h2>
						    <div class="clearfix"></div>
                        </div>
                        {{--<div class="x_content"> <br /> </div>--}}

                        {{Form::model($showGross, ['url' => ['backend/shows/gross/update/' . $showGross->id], 'method' => 'PUT', 'files' => true, 'class' => 'form-horizontal form-label-left', 'role' => 'form'])}}
                            @include('backend.shows.edit_gross_form')
                        {{Form::close()}}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
