@extends( 'backend.layouts.app' )

@section('title', 'Gross list')

@section('CSSLibraries')
    <!-- DataTables CSS -->
    <link href="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.css') }}" rel="stylesheet">

    <!-- DataTables Responsive CSS -->
    <link href="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.css') }}" rel="stylesheet">
    <link href="{{backend_asset('libraries/jquery/dist/jquery-ui.css')}}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <!-- DataTables JavaScript -->
    <script src="{{ backend_asset('libraries/datatables/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-plugins/dataTables.bootstrap.min.js') }}"></script>
    <script src="{{ backend_asset('libraries/datatables-responsive/dataTables.responsive.js') }}"></script>
    <script src="{{ backend_asset('libraries/papaparse/papaparse.min.js') }}"></script>
@endsection

@section('inlineJS')
    <script type="text/javascript">
        $(function () {
            var tableSelector = '#datatable';
            var ungroupedPreviousGrosses = JSON.parse('{!!json_encode($show->gross)!!}')
            var previousGrosses = {};
            var showId = {{$show->id}};
            var startDate = '{{$show->start_date}}';
            var endDate = '{{$show->end_date}}';

            ungroupedPreviousGrosses.forEach(function(gross) {
                if (gross.end_week_date) {
                    // It need for correсе comparison
                    gross.earnings = parseInt(gross.earnings);
                    previousGrosses[gross.end_week_date] = gross;
                }
            });

            // Order fields
            var grossFieldsOrder = [
                'end_week_date',
                'attendees_amount',
                'performances_amount',
                'earnings',
            ];

            var grossValidatorRules = {
                end_week_date: {
                    rules: ['date', 'is_sunday', ['before', endDate], ['after', startDate]],
                    fieldName: 'End week date',
                },
                attendees_amount: {
                    rules: ['float'],
                    fieldName: 'Number of attendees',
                },
                performances_amount: {
                    rules: ['int'],
                    fieldName: 'Number of performances',
                },
                earnings: {
                    rules: ['int'],
                    fieldName: 'Earnings',
                },
            };

            var validator = new Validator();

            var importConfig = {
                delimiter: ',',
                skipEmptyLines: true,
                complete: function(importData) {
                    var ungroupedGross = importData.data;
                    if (ungroupedGross && ungroupedGross.length > 1) {
                        // Delete first row (header)
                        ungroupedGross.splice(0, 1);
                        // Clear dates array
                        var importedGrossDates = [];
                        var grossesForSaving = [];

                        ungroupedGross.forEach(function(gross) {
                            var convertedGross = importConvertation(gross);
                            var isCompleteDuplicate = checkCompleteDuplicate(convertedGross);
                            importedGrossDates.push(convertedGross.end_week_date);
                            if (!isCompleteDuplicate) {
                                grossesForSaving.push(convertedGross);
                            }
                        });

                        // Check duplicates in imported data
                        var duplicateGrosses = getDuplicates(importedGrossDates);

                        if (duplicateGrosses.length) {
                            showDuplicatesError(duplicateGrosses);
                            return;
                        }

                        // Validation
                        var grossErrors = validateGrosses(grossesForSaving);

                        if (Object.keys(grossErrors).length) {
                            showImportError(grossErrors);
                            return;
                        }

                        saveImportedData(grossesForSaving);
                    }
                }
            };

            $(tableSelector).dataTable();
            initDeleteForm(tableSelector, '.form-delete');

            $('.import-csv').change(function() {
                var file = this.files[0];
                importFromCSV(file, importConfig);
                $(this).val('');
            });

            function importFromCSV(file) {
                showLoader(true);
                setTimeout(function() {
                    Papa.parse(file, importConfig);
                }, 200);
            }

            function importConvertation(ungroupedGorss) {
                var gross = {};

                grossFieldsOrder.forEach(function(fieldName, index) {
                    gross[fieldName] = ungroupedGorss[index];

                    var grossFieldValue;
                    if (fieldName === 'end_week_date') {
                        grossFieldValue = convertImportDate(ungroupedGorss[index]);
                    }

                    // If date is incorrect or it isn't date
                    if (!grossFieldValue) {

                        grossFieldValue = ungroupedGorss[index];
                    }

                    gross[fieldName] = grossFieldValue;
                });

                return gross;
            }

            function checkCompleteDuplicate(currentGross) {
                var sameDateGross = previousGrosses[currentGross.end_week_date];

                if (!sameDateGross) {
                    return false;
                }

                var matches = grossFieldsOrder.filter(function(fieldName) {
                    return sameDateGross[fieldName] != currentGross[fieldName];
                });

                return !matches.length;
            }

            function validateGrosses(grossesData) {
                var grossErrors = [];
                grossesData.forEach(function(grossData) {
                    var errors = validator(grossData, grossValidatorRules);

                    if (errors.length) {
                        grossErrors[grossData.end_week_date] = errors;
                    }
                });

                return grossErrors;
            }

            function showDuplicatesError(duplicateGrosses) {
                var errorMessage = 'Importing file contains duplicated grosses:<br>- \n' + duplicateGrosses.join('<br>- ');
                showModal({message: errorMessage});
            }

            function showImportError(grossErrors) {
                var errorMessage = 'Importing date(s) have the following mistakes or errors: ';

                Object.keys(grossErrors).forEach(function(grossDate) {
                    errorMessage += '<div class="gross-error-row">' +
                        '<div class="gross-date">' + grossDate + '</div>' +
                        '<div class="gross-error-column">- ' + grossErrors[grossDate].join('<br>- ') + '</div>' +
                        '</div>';
                });

                showModal({
                    message: errorMessage,
                    open: true,
                    wide: true,
                });
            }

            function saveImportedData(grossesData) {
                var requestsAmount = Math.ceil(grossesData.length / importGrossLimit);
                if (!requestsAmount) {
                    hideLoader();
                    return;
                }

                saveImportingGrosses(grossesData, 0, requestsAmount);
            }

            function saveImportingGrosses(grossesData, cycleNumber, requestsAmount) {
                var start = cycleNumber * importGrossLimit;
                var end = (cycleNumber + 1) * importGrossLimit;
                var grossesPart = grossesData.slice(start, end);

                $.post('/backend/shows/gross/ajax-save/' + showId, {
                    gross: grossesPart,
                    show_id: showId,
                }, function() {
                    cycleNumber++;
                    if (cycleNumber >= requestsAmount) {
                        location.reload();
                        return;
                    }
                    saveImportingGrosses(grossesData, cycleNumber, requestsAmount)
                }, 'JSON').fail(showServerError);
            }
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
                    <h3>Gross List</h3>
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
                            <h2>Gross</h2>
                            @include('backend.shows.gross_buttons')
                            <div class="clearfix"></div>
                        </div>
                        <div class="x_content">

                            @include( 'backend.layouts.notification_message' )

                            <table id="datatable" class="table table-striped table-bordered" data-form="deleteForm">
                                <thead>
                                    <tr>
                                        <th>Id</th>
                                        <th>End Week Date</th>
                                        <th>Number of attendees</th>
                                        <th>Number of Performances</th>
                                        <th>Earnings</th>
                                        <th>Added/Changed</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{--*/ $i = 1 /*--}}
                                    @foreach ($show->gross as $gross)
                                        <tr>
                                            <td>{{$i++}}</td>
                                            <td>{{$gross->end_week_date}}</td>
                                            <td>{{$gross->attendees_amount}}</td>
                                            <td>{{$gross->performances_amount}}</td>
                                            <td>{{$gross->earnings}}</td>
                                            <td>{{$gross->updated_at}}</td>
                                            <td>
                                                <a href="{{backend_url('shows/gross/edit/' . $gross->id)}}" class="btn btn-info btn-xs edit-button">
                                                    <i class="fa fa-pencil"></i>
                                                </a>

                                                {{Form::model($show, ['method' => 'delete', 'url' => 'backend/shows/gross/remove/' . $show->id, 'class' => 'form-inline form-delete'])}}
                                                {{Form::hidden('id', $gross->id) }}
                                                {{Form::button('<i class="fa fa-trash-o"></i> ', ['class' => 'btn btn-danger btn-xs', 'name' => 'delete_modal', 'data-toggle' => 'modal'])}}
                                                {{Form::close()}}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="gray-delimiter"></div>
                            @include('backend.shows.gross_buttons')
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- /#page-wrapper -->
@endsection