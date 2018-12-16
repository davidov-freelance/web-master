<?php

$gross = old('gross');

?>

@section('CSSLibraries')
    <!-- TimePicker CSS -->
    <link href="{{backend_asset('libraries/jquery/dist/jquery-ui.css')}}" rel="stylesheet">
@endsection

@section('JSLibraries')
    <script src="{{ backend_asset('libraries/weekpicker/jquery.weekpicker.js') }}"></script>
    <script src="{{ backend_asset('libraries/papaparse/papaparse.min.js') }}"></script>
@endsection

@section('inlineJS')
    <script>

        $(function () {
            var indexRegexp = /{index}/g;
            var grossIndexes = {};
            var $grossBlock = $('#gross-block');
            var $grossForm = $('#gross-form');
            var $addGrossButton = $('#add-gross');
            var grossTemplate = $('#gross-template').html();
            var startDate = '{{$show->start_date}}';
            var endDate = '{{$show->end_date}}';
            var grossWeekDay = {{$show->end_week_day}};
            var takeFirstDay = {{$show->take_first_day}};
            var showId = {{$show->id}};
            var cutExcessAlertMessage = 'More than ' + importGrossFormLimit + ' grosses was added into this form. Would you like to remove excess?';
            var grossesWereChecked = false;

            var importConfig = {
                delimiter: ',',
                skipEmptyLines: true,
                complete: function (importData) {
                    var ungroupedGross = importData.data;
                    if (ungroupedGross && ungroupedGross.length > 1) {
                        // Delete first row (header)
                        ungroupedGross.splice(0, 1);

                        // Clear all empty fields before importing
                        clearEmptyFields();

                        ungroupedGross.forEach(function (gross) {
                            addGross();
                            var index = getLastGrossIndex();
                            var formattedDate = convertImportDate(gross[0]);
                            $grossBlock.find('#week-input-' + index).val(formattedDate);
                            $grossBlock.find('#attendees-input-' + index).val(gross[1]);
                            $grossBlock.find('#performances-input-' + index).val(gross[2]);
                            $grossBlock.find('#earnings-input-' + index).val(gross[3]);
                            setWeekValue(index, formattedDate);
                        });

                        checkDisableAddButton();

                        hideLoader();
                    }
                }
            };

            var alertButtonsData = [
                {
                    text: 'Remove and Save',
                    callback: function () {
                        clearExcessGrosses();
                        $grossForm.submit();
                    },
                    id: 'removeAndSave',
                    class: 'btn btn-success'
                },
                {
                    text: 'Remove',
                    callback: clearExcessGrosses,
                    id: 'remove',
                    class: 'btn btn-danger'
                },
                {
                    text: 'Edit manually',
                    id: 'editManually',
                    class: 'btn btn-warning'
                },
            ];

            var duplicateButtonsData = [
                {
                    text: 'Update',
                    callback: endCheckingExistingGross,
                    id: 'confirm',
                    class: 'btn btn-success'
                },
                {
                    text: 'Close',
                    id: 'closePopup',
                    class: 'btn btn-default'
                }
            ];

            initGrossData();

            $('.import-limit').text(importGrossFormLimit);

            $addGrossButton.click(function () {
                if ($addGrossButton.is('.disabled')) {
                    return;
                }

                addGross();
                checkDisableAddButton();
            });

            $grossForm.submit(function (event) {
                var grossesAmount = $('.gross-row').length;

                if (grossesAmount > importGrossFormLimit) {
                    showButtonsDialog(cutExcessAlertMessage, alertButtonsData);
                    return false;
                }

                if (grossesWereChecked) {
                    return showSendFormLoader(grossesAmount);
                }

                event.preventDefault();

                checkExistingGross();
                return false;
            });

            $grossBlock.on('click', '.delete-gross', function () {
                var index = $(this).data('index');
                deleteGross(index);
            });

            $('.import-csv').change(function () {
                var file = this.files[0];
                importFromCSV(file, importConfig);
                $(this).val('');
            });

            function showSendFormLoader(grossesAmount) {
                var minTime = parseInt(0.05 * grossesAmount);
                var minTimeMessage = 'It will take at least ' + minTime + ' seconds';
                showLoader(true, minTimeMessage);
            }

            function initGrossData() {
                var $grossRows = $grossBlock.find('.gross-row');
                if ($grossRows.length > 1) {
                    $grossBlock.find('.delete-gross').show();
                }

                $.each($grossRows, function () {
                    var index = $(this).data('index');
                    grossIndexes[index] = [];
                    var $weekInput = getWeekInput(index);
                    initWeekpicker($weekInput, startDate, endDate, grossWeekDay, takeFirstDay);

                    var startWeekString = $weekInput.val();
                    if (startWeekString) {
                        setWeekValue(index, startWeekString);
                    }
                });
                checkDisableAddButton();
            }

            function endCheckingExistingGross() {
                grossesWereChecked = true;
                $grossForm.submit();
            }

            function deleteGross(index) {
                var grossAmount = $grossBlock.find('.gross-row').length;
                var $grossRow = $grossBlock.find('.gross-row[data-index=' + index + ']');

                if (grossAmount > 1) {
                    $grossRow.remove();
                    delete grossIndexes[index];
                    checkDisableAddButton();
                    return;
                }

                $grossRow.find('input').val('');
            }

            function setWeekValue(index, startWeekDate) {

                var weekDate = new Date(prepareDate(startWeekDate));
                var weekNumber = getWeekNumber(weekDate);
                var formatedWeekText = getWeekFormatedText(weekNumber, startWeekDate);
                $grossBlock.find('#week-input-' + index + '_weekpicker').val(formatedWeekText);
            }

            function clearEmptyFields() {
                $grossBlock.find('.gross-row').each(function () {
                    var $grossRow = $(this);
                    var isFilled = $grossRow.find('input').filter(function () {
                        return !!this.value;
                    });

                    if (!isFilled.length) {
                        var index = $grossRow.data('index');
                        $grossRow.remove();
                        delete grossIndexes[index];
                    }
                });
            }

            function clearExcessGrosses() {
                $('.gross-row').each(function (rowIndex, row) {
                    if (rowIndex >= importGrossFormLimit) {
                        var index = row.getAttribute('data-index');
                        row.remove();
                        delete grossIndexes[index];
                    }
                });
            }

            function getWeekFormatedText(weekNumber, startWeekDate) {
                var formattedDate = moment(startWeekDate, 'YYYY-MM-DD').format('MM-DD-YYYY');
                return '#' + weekNumber + ' (' + formattedDate + ')';
            }

            function addGross() {
                var index = getLastGrossIndex() + 1;
                var preparedGrossRow = replaceIndex(grossTemplate, index);
                $grossBlock.append(preparedGrossRow);

                // Init weekpicker
                var $weekInput = getWeekInput(index);
                initWeekpicker($weekInput, startDate, endDate, grossWeekDay, takeFirstDay);

                grossIndexes[index] = [];
                // Show delete buttons if amount of gross more than 1
                if (Object.keys(grossIndexes).length > 1) {
                    var $deleteGrossButtons = getDeleteGrossButtons();
                    $deleteGrossButtons.show();
                }
            }

            function checkDisableAddButton() {
                $addGrossButton.toggleClass('disabled', Object.keys(grossIndexes).length >= importGrossFormLimit);
            }

            function getWeekInput(index) {
                return $grossBlock.find('#week-input-' + index);
            }

            function getLastGrossIndex() {
                var indexes = Object.keys(grossIndexes);
                if (indexes.length) {
                    return Math.max.apply(null, indexes);
                } else {
                    return -1;
                }
            }

            function replaceIndex(template, index) {
                return template.replace(indexRegexp, index)
            }

            function getDeleteGrossButtons() {
                return $grossBlock.find('.delete-gross');
            }

            function importFromCSV(file) {
                showLoader(true);
                setTimeout(function () {
                    Papa.parse(file, importConfig);
                }, 200);
            }

            function collectGrossDates() {
                var grossDates = [];
                $('.week-input').each(function (index, weekInput) {
                    grossDates.push(weekInput.value);
                });
                return grossDates;
            }

            function checkExistingGross() {
                $.post('/backend/shows/gross/check/' + showId, {
                    gross_dates: collectGrossDates(),
                }, function (response) {
                    if (response.Response === '2000') {
                        if (response.Result && response.Result.length) {
                            var duplicatedGrossesMessage = 'You are going to update the following grosses.' +
                                'The current values will be rewritten and lost. Please confirm you would like to do that.<br>- ' +
                                response.Result.join('<br>- ');

                            showButtonsDialog(duplicatedGrossesMessage, duplicateButtonsData);
                            return;
                        }
                        endCheckingExistingGross();
                    }
                }, 'JSON').fail(showServerError);
            }
        });
    </script>
@endsection

<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        <label class="btn btn-warning">
            <span>Import</span>
            <input class="hidden import-csv" type="file" accept="text/csv">
        </label>
        {{Form::submit('Save', ['class' => 'btn btn-primary'])}}
        {{Html::link( backend_url('shows/gross/' . $show->id), 'Cancel', ['class' => 'btn btn-default'])}}
    </div>
</div>
<div class="gross-ln"></div>

<div id="gross-block">
    @if (!empty($gross))
        @foreach ($gross as $index => $grossDatum)
            <div class="gross-row" data-index="{{$index}}">
                <div class="delete-button delete-gross" data-index="{{$index}}"></div>
                <div class="form-group week-row{{$errors->has("gross.$index.end_week_date") ? ' has-error' : ''}}">
                    {{Form::label("week-input-$index", 'Week Number', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        {{Form::text("gross[$index][end_week_date]", $grossDatum['end_week_date'], ['class' => 'form-control col-md-7 col-xs-12 week-input', 'id' => "week-input-$index"])}}
                    </div>
                    @if($errors->has("gross.$index.end_week_date"))
                        <p class="help-block">{{$errors->first("gross.$index.end_week_date")}}</p>
                    @endif
                </div>

                <div class="form-group{{$errors->has("gross.$index.attendees_amount") ? ' has-error' : ''}}">
                    {{Form::label("attendees-input-$index", 'Number of attendees', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        {{Form::text("gross[$index][attendees_amount]", $grossDatum['attendees_amount'], ['class' => 'form-control col-md-7 col-xs-12', 'id' => "attendees-input-$index", 'autocomplete' => 'off'])}}
                    </div>
                    @if($errors->has("gross.$index.attendees_amount"))
                        <p class="help-block">{{$errors->first("gross.$index.attendees_amount")}}</p>
                    @endif
                </div>

                <div class="form-group{{$errors->has("gross.$index.performances_amount") ? ' has-error' : ''}}">
                    {{Form::label('performances-input-0', 'Number of Performances', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        {{Form::text("gross[$index][performances_amount]", $grossDatum['performances_amount'], ['class' => 'form-control col-md-7 col-xs-12', 'id' => "performances-input-$index", 'autocomplete' => 'off'])}}
                    </div>
                    @if($errors->has("gross.$index.performances_amount"))
                        <p class="help-block">{{$errors->first("gross.$index.performances_amount")}}</p>
                    @endif
                </div>

                <div class="form-group{{$errors->has("gross.$index.earnings") ? ' has-error' : ''}}">
                    {{Form::label("earnings-input-$index", 'Earnings', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                    <div class="col-md-6 col-sm-6 col-xs-12">
                        {{Form::text("gross[$index][earnings]", $grossDatum['earnings'], ['class' => 'form-control col-md-7 col-xs-12', 'id' => "earnings-input-$index", 'autocomplete' => 'off'])}}
                    </div>
                    @if($errors->has("gross.$index.earnings"))
                        <p class="help-block">{{$errors->first("gross.$index.earnings")}}</p>
                    @endif
                </div>
                <div class="separator"></div>
            </div>
        @endforeach
    @else
        <div class="gross-row" data-index="0">
            <div class="delete-button delete-gross" data-index="0"></div>
            <div class="form-group week-row">
                {{Form::label('week-input-0', 'Week Number', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {{Form::text('gross[0][end_week_date]', null, ['class' => 'form-control col-md-7 col-xs-12 week-input', 'id' => 'week-input-0'])}}
                </div>
            </div>

            <div class="form-group">
                {{Form::label('attendees-input-0', 'Number of Attendees', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {{Form::text('gross[0][attendees_amount]', null, ['class' => 'form-control col-md-7 col-xs-12', 'id' => 'attendees-input-0', 'autocomplete' => 'off'])}}
                </div>
            </div>

            <div class="form-group">
                {{Form::label('performances-input-0', 'Number of Performances', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {{Form::text('gross[0][performances_amount]', null, ['class' => 'form-control col-md-7 col-xs-12', 'id' => 'performances-input-0', 'autocomplete' => 'off'])}}
                </div>
            </div>

            <div class="form-group">
                {{Form::label('earnings-input-0', 'Earnings', ['class'=>'control-label col-md-3 col-sm-3 col-xs-12'])}}
                <div class="col-md-6 col-sm-6 col-xs-12">
                    {{Form::text('gross[0][earnings]', null, ['class' => 'form-control col-md-7 col-xs-12', 'id' => 'earnings-input-0', 'autocomplete' => 'off'])}}
                </div>
            </div>
            <div class="separator"></div>
        </div>
    @endif
</div>

<div class="plus-button fa fa-plus-circle fa-fw add-gross col-md-offset-3 col-sm-offset-6" id="add-gross"></div>

<div class="gross-ln"></div>
<div class="form-group">
    <div class="col-md-6 col-sm-6 col-xs-12 col-md-offset-3">
        <label class="btn btn-warning">
            <span>Import</span>
            <input class="hidden import-csv" type="file" accept="text/csv">
        </label>
        {{Form::submit('Save', ['class' => 'btn btn-primary'])}}
        {{Html::link( backend_url('shows/gross/' . $show->id), 'Cancel', ['class' => 'btn btn-default'])}}
    </div>
</div>

<script type="text/template" id="gross-template">
    <div class="gross-row week-row" data-index="{index}">
        <div class="delete-button delete-gross" data-index="{index}"></div>
        <div class="form-group">
            <label for="week-input-{index}" class="control-label col-md-3 col-sm-3 col-xs-12">Week Number</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input class="form-control week-input" name="gross[{index}][end_week_date]" type="text"
                       id="week-input-{index}">
            </div>
        </div>

        <div class="form-group">
            <label for="attendees-input-{index}" class="control-label col-md-3 col-sm-3 col-xs-12">Number of
                Attendees</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input class="form-control" name="gross[{index}][attendees_amount]" type="text"
                       id="attendees-input-{index}" autocomplete="off">
            </div>
        </div>

        <div class="form-group">
            <label for="performances-input-{index}" class="control-label col-md-3 col-sm-3 col-xs-12">Number of
                Performances</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input class="form-control" name="gross[{index}][performances_amount]" type="text"
                       id="performances-input-{index}" autocomplete="off">
            </div>
        </div>

        <div class="form-group">
            <label for="earnings-input-{index}" class="control-label col-md-3 col-sm-3 col-xs-12">Earnings</label>
            <div class="col-md-6 col-sm-6 col-xs-12">
                <input class="form-control" name="gross[{index}][earnings]" type="text" id="earnings-input-{index}"
                       autocomplete="off">
            </div>
        </div>
        <div class="separator"></div>
    </div>
</script>