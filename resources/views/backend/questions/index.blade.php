@extends( 'backend.layouts.app' )

@section('title', 'Questions')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">
        $(function () {
            var tableSelector = '#datatable';
            $(tableSelector).dataTable();
            initDeleteForm(tableSelector, '.form-delete');
            showAnsweredUsers(tableSelector, '.answers-amount');
        });
    </script>
@endsection

@section('content')
    @include('backend.layouts.modal')
    @include( 'backend.layouts.popups')
    <div class="right_col" role="main">
        <div>
            <div class="page-title">
                <div class="title_left">
                    <h3>Questions List</h3>
                </div>
                <div class="title_right">
                    <div class="col-md-5 col-sm-5 col-xs-12 form-group pull-right top_search">
                        <div class="input-group"></div>
                    </div>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_title">
                            <h2>Questions</h2>
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <table id="datatable" class="table table-striped table-bordered" data-form="deleteForm">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>Release date</th>
                                        <th>Question</th>
                                        <th>Options</th>
                                        <th>Number of users that answered correctly</th>
                                        <th>Number of users that answered incorrectly</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{--*/ $i = 1 /*--}}
                                    @foreach( $questions as $question )
                                        <tr class="">
                                            <td>{{$i++}}</td>
                                            <td>{{$question->formatted_release_datetime}}</td>
                                            <td>{{$question->question}}</td>
                                            <td>
                                                @for ($optionNumber = 1; $optionNumber <= 4; $optionNumber++)
                                                    <div class="option-row{{$optionNumber == $question->correct_answer ? ' correct' : ''}}">
                                                        {{$optionNumber}}. {{$question["option_$optionNumber"]}}
                                                    </div>
                                                @endfor
                                            </td>
                                            <td>
                                                <span class="answers-amount" data-id="{{$question->id}}" data-correct="1" data-amount="{{$question->genius_streak}}">
                                                    {{$question->genius_streak}}
                                                </span>
                                            </td>
                                            <td>
                                                <span class="answers-amount" data-id="{{$question->id}}" data-correct="0" data-amount="{{$question->stupid_streak}}">
                                                    {{$question->stupid_streak}}
                                                </span>
                                            </td>
                                            <td>
                                                <a
                                                        href="{{backend_url('questions/edit/'.$question->id)}}"
                                                        class="btn btn-info btn-xs edit"
                                                        style="float: left;"
                                                >
                                                    <i class="fa fa-pencil"></i>
                                                </a>

                                                {{Form::model($question, ['method' => 'delete', 'url' => 'backend/questions/remove/' . $question->id, 'class' => 'form-inline form-delete'])}}
                                                {{Form::hidden('id', $question->id) }}
                                                {{Form::button('<i class="fa fa-trash-o"></i> ', ['class' => 'btn btn-danger btn-xs', 'name' => 'delete_modal', 'data-toggle' => 'modal'])}}
                                                {{Form::close()}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- /#page-wrapper -->
@endsection