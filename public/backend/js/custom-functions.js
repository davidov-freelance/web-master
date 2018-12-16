// JavaScript Document


// GENERAL FUNCTIONS ===================================================
function getCookie(name) {
    var matches = document.cookie.match(new RegExp(
        "(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"
    ));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, options) {
    options = options || {};

    var expires = options.expires;

    if (typeof expires == "number" && expires) {
        var d = new Date();
        d.setTime(d.getTime() + expires * 1000);
        expires = options.expires = d;
    }
    if (expires && expires.toUTCString) {
        options.expires = expires.toUTCString();
    }

    value = encodeURIComponent(value);

    var updatedCookie = name + "=" + value;

    for (var propName in options) {
        updatedCookie += "; " + propName;
        var propValue = options[propName];
        if (propValue !== true) {
            updatedCookie += "=" + propValue;
        }
    }

    document.cookie = updatedCookie;
}

function deleteCookie(name) {
    setCookie(name, "", {
        expires: -1
    });
}

function setTimezoneOffsetInCookie() {
    var offset = getCookie('timezone_offset');
    if (offset === undefined) {
        offset = new Date().getTimezoneOffset() / 60;
        setCookie('timezone_offset', offset, {path: '/'});
        location.reload();
    }
}

function isEmail(em_address) {
    var email = /^[A-Za-z0-9]+([_\.-][A-Za-z0-9]+)*@[A-Za-z0-9]+([_\.-][A-Za-z0-9]+)*\.([A-Za-z]){2,4}$/i;
    return (email.test(em_address));
}

function isNumberKey(evt) {
    var charCode = (evt.which) ? evt.which : event.keyCode;
    if (charCode != 46 && charCode > 31
        && (charCode < 48 || charCode > 57))
        return false;

    return true;
}
function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] == needle)
            return true;
    }
    return false;
}

function SearchByEnter(e, page) {

    if (window.event) {
        e = window.event;
    }
    if ((e.keyCode == 13) || (e.type == 'click')) {
        if (page == 'Chatbox' || page == 'Chatbox') {
            //CallTeamSearch();
            SendChatMessageToUser();
        }

    }
}

function is_array(mixed_var) {
    // From: http://phpjs.org/functions
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Legaev Andrey
    // +   bugfixed by: Cord
    // +   bugfixed by: Manish
    // +   improved by: Onno Marsman
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // +   improved by: Nathan Sepulveda
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    // %        note 1: In php.js, javascript objects are like php associative arrays, thus JavaScript objects will also
    // %        note 1: return true in this function (except for objects which inherit properties, being thus used as objects),
    // %        note 1: unless you do ini_set('phpjs.objectsAsArrays', 0), in which case only genuine JavaScript arrays
    // %        note 1: will return true
    // *     example 1: is_array(['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: true
    // *     example 2: is_array('Kevin van Zonneveld');
    // *     returns 2: false
    // *     example 3: is_array({0: 'Kevin', 1: 'van', 2: 'Zonneveld'});
    // *     returns 3: true
    // *     example 4: is_array(function tmp_a(){this.name = 'Kevin'});
    // *     returns 4: false
    var ini,
        _getFuncName = function (fn) {
            var name = (/\W*function\s+([\w\$]+)\s*\(/).exec(fn);
            if (!name) {
                return '(Anonymous)';
            }
            return name[1];
        },
        _isArray = function (mixed_var) {
            // return Object.prototype.toString.call(mixed_var) === '[object Array]';
            // The above works, but let's do the even more stringent approach: (since Object.prototype.toString could be overridden)
            // Null, Not an object, no length property so couldn't be an Array (or String)
            if (!mixed_var || typeof mixed_var !== 'object' || typeof mixed_var.length !== 'number') {
                return false;
            }
            var len = mixed_var.length;
            mixed_var[mixed_var.length] = 'bogus';
            // The only way I can think of to get around this (or where there would be trouble) would be to have an object defined
            // with a custom "length" getter which changed behavior on each call (or a setter to mess up the following below) or a custom
            // setter for numeric properties, but even that would need to listen for specific indexes; but there should be no false negatives
            // and such a false positive would need to rely on later JavaScript innovations like __defineSetter__
            if (len !== mixed_var.length) { // We know it's an array since length auto-changed with the addition of a
                // numeric property at its length end, so safely get rid of our bogus element
                mixed_var.length -= 1;
                return true;
            }
            // Get rid of the property we added onto a non-array object; only possible
            // side-effect is if the user adds back the property later, it will iterate
            // this property in the older order placement in IE (an order which should not
            // be depended on anyways)
            delete mixed_var[mixed_var.length];
            return false;
        };

    if (!mixed_var || typeof mixed_var !== 'object') {
        return false;
    }

    // BEGIN REDUNDANT
    this.php_js = this.php_js || {};
    this.php_js.ini = this.php_js.ini || {};
    // END REDUNDANT

    ini = this.php_js.ini['phpjs.objectsAsArrays'];

    return _isArray(mixed_var) ||
        // Allow returning true unless user has called
        // ini_set('phpjs.objectsAsArrays', 0) to disallow objects as arrays
        ((!ini || (// if it's not set to 0 and it's not 'off', check for objects as arrays
                (parseInt(ini.local_value, 10) !== 0 && (!ini.local_value.toLowerCase || ini.local_value.toLowerCase() !== 'off')))
        ) && (
            Object.prototype.toString.call(mixed_var) === '[object Object]' && _getFuncName(mixed_var.constructor) === 'Object' // Most likely a literal and intended as assoc. array
        ));
}


function SubmitAdminLogin(e) {
    // look for window.event in case event isn't passed in
    if (window.event) {
        e = window.event;
    }
    if ((e.keyCode == 13) || (e.type == 'click')) {
        AdminLogin();
    }
}

//////////// top  header Search Bar .................................
function initChangePostStatus(tableSelector, statusButtonsSelector) {
    var status, id, $statusButton, publishedDate, publishedTime, fullPublishedDate;
    var currentDate = new Date();
    var datepickerOptions = {
        dateFormat: 'yy-mm-dd',
        onSelect: handleChangeDate
    };
    var timepickerOptions = {
        onSelect: handleChangeTime
    };

    var $popupBody = $(
        '<div>' +
            '<div class="form-group">' +
                '<label for="Select Status " class="control-label">Select Status *</label>'+
                '<select class="form-control" name="product_status" id="product_status">' +
                    '<option value="approved">Published</option>' +
                    '<option value="pending">Draft</option>' +
                    '<option value="scheduled">Scheduled</option>' +
                '</select>' +
            '</div>' +
            '<div class="form-group" id="published_date_block">' +
                '<label for="published_date" class="control-label">Publication date *</label>'+
                '<div>' +
                    '<input type="text" id="published_date" class="modal-date-input" />' +
                    '<input type="text" id="published_time" class="modal-time-input" />' +
                '</div>' +
            '</div>' +
        '</div>'
    );

    var $footerButtons = $(
        '<div>' +
        '<button type="button" id="CloseButton" class="btn btn-default" data-dismiss="modal">Close</button>' +
        '<button type="button" class="btn btn-primary" id="SaveButton">Save</button>' +
        '</div>'
    );

    var $publishedDateBlock = $popupBody.find('#published_date_block');
    var $productStatus = $popupBody.find('#product_status');
    var $publishedDate = $publishedDateBlock.find('#published_date');
    var $publishedTime = $publishedDateBlock.find('#published_time');
    var $saveButton = $footerButtons.find('#SaveButton');
    var $closeButton = $footerButtons.find('#CloseButton');

    var statusValues = {
        approved: {
            statusClass: 'btn btn-xs btn-success',
            statusText: 'Published'
        },
        pending: {
            statusClass: 'btn btn-xs btn-danger',
            statusText: 'Draft'
        },
        scheduled: {
            statusClass: 'btn btn-xs btn-warning',
            statusText: 'Scheduled'
        },
        publishedScheduled: {
            statusClass: 'btn btn-xs btn-success',
            statusText: 'Published<br>Scheduled'
        },
    }

    $(tableSelector).on('click', statusButtonsSelector, clickHandler);

    function clickHandler() {
        $statusButton = $(this);
        status = $statusButton.data('status');
        id = $statusButton.data('id');
        fullPublishedDate = status === 'scheduled' && $statusButton.data('published_date');

        if (fullPublishedDate) {
            publishedDate = fullPublishedDate.substr(0, 10);
            publishedTime = fullPublishedDate.substr(11, 5);
        } else {
            publishedDate = publishedTime = '';
        }

        fillModal();

        $saveButton.click(sendStatus);

        $productStatus.change(function() {
            status = $productStatus.val();
            needHideDate();
        });
    }

    function initDatepicker() {
        $publishedDate
            .removeClass('hasDatepicker')
            .removeClass('error')
            .val(publishedDate)
            .datepicker(datepickerOptions);

        $publishedDate.on('input', handleChangeDate);

        $publishedTime
            .removeClass('hasDatepicker')
            .removeClass('error')
            .val(publishedTime)
            .timepicker(timepickerOptions);

        $publishedTime.on('input', handleChangeTime);
    }

    function handleChangeDate() {
        $publishedDate.removeClass('error');
        publishedDate = $publishedDate.val();
    }

    function handleChangeTime() {
        $publishedTime.removeClass('error');
        publishedTime = $publishedTime.val();
    }

    function needHideDate() {
        if (status === 'scheduled') {
            $publishedDateBlock.show();
        } else {
            $publishedDateBlock.hide();
        }
    }

    function fillModal() {
        $productStatus.val(status);
        $('#popup_body').html($popupBody);
        $('#PopupFooter').html($footerButtons);
        initDatepicker();
        needHideDate();
    }

    function sendStatus() {
        var postData = { id: id, status: status };
        if (status === 'scheduled') {
            var error;

            if (!checkDateFormat(publishedDate)) {
                $publishedDate.addClass('error');
                error = true;
            }

            if (!checkTimeFormat(publishedTime)) {
                $publishedTime.addClass('error');
                error = true;
            }

            if (error) {
                return;
            }

            postData.published_date = getFullPublishDate();
        } else {
            postData.published_date = '';
        }

        $.ajax({
            type: 'POST',
            url: API_URL + 'posts/changePostStatus',
            data: postData,
            success: function (data) {
                if (data.Response === '2000') {
                    fullPublishedDate = postData.published_date;
                    updateStatusButton();
                    $closeButton.click();
                }
            }
        });
    }

    function updateStatusButton() {
        $statusButton.data('status', status);
        $statusButton.data('published_date', fullPublishedDate);

        // Scheduled published posts have other button than unpublished posts
        if (status === 'scheduled' && checkIsPublished()) {
            status = 'publishedScheduled';
        }

        $statusButton.html(statusValues[status].statusText);
        $statusButton.attr('class', statusValues[status].statusClass);

        // Handle event click again (modal remove this event)
        $statusButton.click(clickHandler);
    }

    function getFullPublishDate() {
        return publishedDate + ' ' + publishedTime;
    }

    function checkIsPublished() {
        return moment(fullPublishedDate).diff(currentDate) <= 0;
    }
}

function checkDateFormat(date) {
    return date && (/^2[0-9]{3}-[0-1][0-9]-[0-3][0-9]$/).test(date);
}

function checkTimeFormat(time) {
    return time && (/^[0-2][0-9]:[0-5][0-9]$/).test(time);
}

function showYesNowDialog(header, selectId, updateFunction, id, value) {
    $('#popup_heading').html(header);
    var popupBody = '<div class="form-group">';
    popupBody += '<label for="Change Value " class="control-label">Select Value *</label>';
    popupBody += '<select class="form-control" name="product_in_trending" id="' + selectId + '">';
    popupBody += '<option value="1"' + (value == 1 ? ' selected' : '') + '>Yes</option>';   // Need == (not ===)
    popupBody += '<option value="0"' + (value == 0 ? ' selected' : '') + '>No</option>';    // Need == (not ===)
    popupBody += '</select>';
    popupBody += '<input type="hidden" name="post_id" id="post_id" value="' + id + '">';
    popupBody += '</div>';

    $('#popup_body').html(popupBody);
    var footerData = [
        '<button type="button" id="CloseButton" class="btn btn-default" data-dismiss="modal">Close</button>',
        '<button type="button" class="btn btn-primary" onclick="' + updateFunction + '(' + id + ', \'' + value + '\')">Save</button>'
    ];
    $('#PopupFooter').html(footerData);
}

function changeInTrending(id) {
    var inTrending = $('#in_trending_' + id + ' a').data('in_trending');
    showYesNowDialog('In Trending', 'product_in_trending', 'updateInTrending', id, inTrending);
}

function updateInTrending(id, oldInTrending) {
    var inTrending = $('#product_in_trending').val();

    if (inTrending === oldInTrending) {
        $('#CloseButton').click();
        return;
    }

    //Ajax Call to activate function
    $.ajax({
        type: 'POST',
        url: AJAX_URL + 'posts/changePostInTrending',
        data: { id: id, in_trending: inTrending },
        success: function (data) {
            if (data.Response === '2000') {
                var inTrendingClass, inTrendingText;

                if (inTrending === '1') {
                    inTrendingClass = 'btn btn-xs btn-success';
                    inTrendingText = 'Yes';
                } else {
                    inTrendingClass = 'btn btn-xs btn-danger';
                    inTrendingText = 'No';
                }

                var $inTrendingButton = $('#in_trending_' + id + ' a');
                $inTrendingButton.text(inTrendingText);
                $inTrendingButton.attr('class', inTrendingClass);
                $inTrendingButton.data('in_trending', inTrending);
            }
        }
    });

    $('#CloseButton').trigger('click');
}


function changeInFeed(id) {
    var inFeed = $('#in_feed_' + id + ' a').data('in_feed');
    showYesNowDialog('In Feed', 'product_in_feed', 'updateInFeed', id, inFeed);
}

function updateInFeed(id, oldInFeed) {
    var inFeed = $('#product_in_feed').val();

    if (inFeed === oldInFeed) {
        $('#CloseButton').click();
        return;
    }

    //Ajax Call to activate function
    $.ajax({
        type: 'POST',
        url: AJAX_URL + 'posts/changePostInFeed',
        data: { id: id, in_feed: inFeed },
        success: function (data) {
            if (data.Response === '2000') {
                var inFeedClass, inFeedText;

                if (inFeed === '1') {
                    inFeedClass = 'btn btn-xs btn-success';
                    inFeedText = 'Yes';
                } else {
                    inFeedClass = 'btn btn-xs btn-danger';
                    inFeedText = 'No';
                }

                var $inFeedButton = $('#in_feed_' + id + ' a');
                $inFeedButton.text(inFeedText);
                $inFeedButton.attr('class', inFeedClass);
                $inFeedButton.data('in_feed', inFeed);
            }
        }
    });

    $('#CloseButton').trigger('click');
}

function initStatusSelect(statusSelector, hidingSelector) {
    var $statusSelect = $(statusSelector);
    var $hiding = $(hidingSelector);
    var $dateInputs = $hiding.find('input');
    changeVisibility();

    $statusSelect.change(changeVisibility);

    function changeVisibility() {
        var status = $statusSelect.val();
        if (status === 'scheduled') {
            $hiding.show();
        } else {
            $dateInputs.val('');
            $hiding.hide();
        }
    }
}

function initImagePreview(inputSelector, previewWrapperSelector) {
    var $input = $(inputSelector);
    var $wrapper = $(previewWrapperSelector);
    var $preview = $wrapper.find('img');
    var isHidden = $wrapper.is('.hidden');
    var imageReader = new FileReader();

    imageReader.onload = function (event) {
        $preview.attr('src', event.target.result);
    };

    $input.change(function() {
        if (isHidden) {
            $wrapper.removeClass('hidden');
            isHidden = false;
        }
        imageReader.readAsDataURL(this.files[0]);
    });
}

function initPostPreview(previewButtonSelector, fieldsData) {
    var $previewButton = $(previewButtonSelector);
    var $imageThumb = $(fieldsData.image.imageThumbSelector);
    var $headlinePreview = $(fieldsData.headline.previewSelector);
    var $imagePreview = $(fieldsData.image.previewSelector);
    var $titlePreview = $(fieldsData.title.previewSelector);
    var $descriptionPreview = $(fieldsData.description.previewSelector);
    var imageInputSelector = '[name=' + fieldsData.image.name + ']';
    var textFieldSelectorsList;
    var fieldValues = {};
    var fieldAliases = {};
    init();

    function init() {
        collectFieldAliases();
        collectFieldValues();
        initFieldsChanging();
        addButtonHandler();
    }

    function collectFieldAliases() {
        var textSelectors = []
        Object.keys(fieldsData).map(function(item) {
            fieldAliases[item] = fieldsData[item].name;
            if (item !== 'image') {
                textSelectors.push('form [name=' + fieldsData[item].name + ']');
            }
        });
        textFieldSelectorsList = textSelectors.join(',');
    }

    function addButtonHandler() {
        $previewButton.click(function(e) {
            if ($previewButton.is('.disabled')) {
                e.stopPropagation();
                return;
            }
            showInPreview();
        });
    }

    function initFieldsChanging() {
        $(textFieldSelectorsList).on('input', function() {
            fieldValues[this.name] = this.value;
            checkRequiredFields();
        });

        $(imageInputSelector).change(function() {
            // Call after change preview
            setTimeout(function() {
                getImageSrc();
                checkRequiredFields();
            }, 100);
        });
    }

    function collectFieldValues() {
        $.each($(textFieldSelectorsList), function() {
            fieldValues[this.name] = this.value;
        });
        getImageSrc();
        checkRequiredFields();
    }

    function getImageSrc() {
        fieldValues[fieldsData.image.name] = $imageThumb.attr('src');
    }

    function checkRequiredFields() {
        var disabled = Object.keys(fieldValues).some(function(fieldName) {
            if (!fieldValues[fieldName]) {
                return true;
            }
        });

        if (disabled) {
            $previewButton.addClass('disabled');
        } else {
            $previewButton.removeClass('disabled');
        }
    }

    function showInPreview() {
        $headlinePreview.text(fieldValues[fieldAliases.headline]);
        $imagePreview.css('background-image', 'url(' + fieldValues[fieldAliases.image] + ')');
        $titlePreview.text(fieldValues[fieldAliases.title]);
        $descriptionPreview.text(fieldValues[fieldAliases.description].substr(0, 110));
    }
}

function showModal(options) {
    var $modal = $('.bs-example-modal-sm');

    var poupuHeader = options.header || 'BROADWAY CONNECTED';

    $('#popup_heading').html(poupuHeader);


    var popupBody = '<div class="form-group">' + options.message + '</div>';
    $('#popup_body').html(popupBody);

    var footerData;

    if (options.footer !== undefined) {
        footerData = options.footer;
    } else {
        footerData = '<button type="button" id="CloseButton" class="btn btn-default" data-dismiss="modal">Close</button>';
    }

    $('#PopupFooter').html(footerData);

    if (options.wide) {
        var $modalDialog = $modal.find('.modal-dialog');

        $modal.on('show.bs.modal', function() {
            $modalDialog.attr('class', 'modal-dialog modal-md');
        });

        $modal.on('hidden.bs.modal', function() {
            $modal.unbind('show.bs.modal, hidden.bs.modal');
            $modalDialog.attr('class', 'modal-dialog modal-sm');
        });
    }

    if (options.open) {
        $modal.modal();
    }

    if (options.callback) {
        options.callback();
        // $modal.on('show.bs.modal', options.callback);
    }
}

function showConfirm(message, callback) {
    var duplicateButtonsData = [
        {
            text: 'Confirm',
            callback: callback,
            id: 'confirm',
            class: 'btn btn-success'
        },
        {
            text: 'Close',
            id: 'closePopup',
            class: 'btn btn-default'
        }
    ];

    showButtonsDialog(message, duplicateButtonsData);
}

function showButtonsDialog(alertMessage, buttonsData) {
    var buttonCallbacks = {};

    var footerButtons = '';

    buttonsData.forEach(function(buttonData) {
        if (buttonData.callback) {
            buttonCallbacks['#' + buttonData.id] = buttonData.callback;
        }

        footerButtons += '<button type="button" id="' + buttonData.id + '" class="' + buttonData.class + '" data-dismiss="modal">' +
            buttonData.text +
            '</button>';
    });

    var footerData = '<div>' + footerButtons + '</div>';

    $('#PopupFooter').html(footerData);

    var callback = function() {
        Object.keys(buttonCallbacks).forEach(function(buttonSelector) {
            var $button = $(buttonSelector);
            $button.unbind();
            $button.click(buttonCallbacks[buttonSelector]);
        });
    }

    showModal({
        message: alertMessage,
        footer: footerData,
        callback: callback,
        open: true,
        wide: true
    });
}

function showLoader(open, additionalText) {
    var message = 'Please Wait .. , Data uploading is in progress. ';

    if (additionalText) {
        message += additionalText;
    }

    showModal({
        message: message,
        open: open,
        footer: '',
    });
}

function hideLoader() {
    $('.bs-example-modal-sm').modal('hide');
}

function showServerError(error) {
    if (error.status === 0 || error.status === 404) {
        showModal({message: 'Server connection error: ' + error.statusText});
        return;
    }

    if (error.status === 422) {
        showModal({message: error.responseText});
    }
}

function repostPost(id) {
    //Ajax Call to activate function
    $.ajax({
        type: "POST",
        url: API_URL + 'posts/repostPost',
        data: { id: id },
        success: function (data) {
            if (data.Response == '2000' || data.Response == 2000) {
                location.reload();
            }
        }
    });

    $('#CloseButton').trigger('click');
}


function FeatureUser(user_id, value) {

    //Ajax Call to activate function
    $.ajax({
        type: "POST",
        url: API_URL + 'user/feature',
        data: { user_id: user_id, value: value },
        success: function (data) {
            if (data.Response == '2000' || data.Response == 2000) {
                location.reload();
            }

        }
    });


    $('#CloseButton').trigger('click');

}


///////////////////////////// SELLER PROFILE ///////////////////////////////////////////////////
function GetUrlParms(name) {
    name = name.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");
    var regexS = "[\\?&]" + name + "=([^&#]*)";
    var regex = new RegExp(regexS);
    var results = regex.exec(window.location.href);
    if (results == null)
        return "";
    else
        return results[1];
}

function showUserList(devicetype, keyword, callback) {
    var userId, brandTitle;
    var userCheckbox = '';
    $.ajax({
        type: "GET",
        async: false,
        dataType: "json",
        url: API_URL + 'users',
        data: { device_type: devicetype, keyword: keyword },
        success: function (data) {
            var result = data['Result'];
            var totalRecords = data['Result'].length;

            if (totalRecords > 0) {

                userCheckbox += ' <div class="checkbox" style="float: left;width:31%">';
                userCheckbox += ' <label>';
                userCheckbox += '  <input type="checkbox" class="flat" onchange="checkUncheckAll(this.checked)">All';
                userCheckbox += ' </label>';
                userCheckbox += ' </div>';

                for (i = 0; i < totalRecords; i++) {
                    // userCheckbox += '<div class="col-md-9 col-sm-9 col-xs-12">';
                    userCheckbox += ' <div class="checkbox" style="float: left;width:31%">';
                    userCheckbox += ' <label>';
                    userCheckbox += ' <input type="checkbox" name="uids[]" value="' + result[i]['id'] + '" class="flat">@' + result[i]['handle'];
                    userCheckbox += ' </label>';
                    userCheckbox += ' </div>';
                }
            } else {
                userCheckbox += '<div> No User Found </div>';
            }

            $('#userCheckbox').html(userCheckbox);

            if (callback) {
                callback();
            }
        }
    });
}

function showGroupList(devicetype, keyword, callback) {
    var userId, brandTitle;
    var userCheckbox = '';
    $.ajax({
        type: "GET",
        async: false,
        dataType: "json",
        url: API_URL + 'groups',
        data: { device_type: devicetype, keyword: keyword },
        success: function (data) {
            var result = data.Result;
            var totalRecords = data.Result.length;

            if (totalRecords > 0) {
                userCheckbox += ' <div class="checkbox" style="float: left;width:30%">';
                userCheckbox += ' <label>';
                userCheckbox += '  <input type="checkbox" class="flat" onchange="checkUncheckAll(this.checked)">All';
                userCheckbox += ' </label>';
                userCheckbox += ' </div>';

                for (i = 0; i < totalRecords; i++) {
                    // userCheckbox += '<div class="col-md-9 col-sm-9 col-xs-12">';
                    userCheckbox += ' <div class="checkbox" style="float: left; width:30%">';
                    userCheckbox += ' <label>';
                    userCheckbox += ' <input type="checkbox" name="uids[]" value="' + result[i].id + '" class="flat">' + result[i].name;
                    userCheckbox += ' </label>';
                    userCheckbox += ' </div>';
                }
            } else {
                userCheckbox += '<div> No User Found </div>';
            }


            $('#userCheckbox').html(userCheckbox);

            if (callback) {
                callback();
            }
        }
    });
}

function showTagsList(devicetype, keyword, callback) {
    var userCheckbox = '';
    $.ajax({
        type: "GET",
        async: false,
        dataType: "json",
        url: API_URL + 'notification/tags',
        data: { device_type: devicetype, keyword: keyword },
        success: function (data) {
            var result = data.Result;
            var totalRecords = data.Result.length;

            if (totalRecords > 0) {

                userCheckbox += ' <div class="checkbox" style="float: left;width:31%">';
                userCheckbox += ' <label>';
                userCheckbox += '  <input type="checkbox" class="flat" onchange="checkUncheckAll(this.checked)">All';
                userCheckbox += ' </label>';
                userCheckbox += ' </div>';

                for (i = 0; i < totalRecords; i++) {
                    // userCheckbox += '<div class="col-md-9 col-sm-9 col-xs-12">';
                    userCheckbox += ' <div class="checkbox" style="float: left;width:31%">';
                    userCheckbox += ' <label>';
                    userCheckbox += ' <input type="checkbox" name="uids[]" value="' + result[i].id + '" class="flat">' + result[i].tag;
                    userCheckbox += ' </label>';
                    userCheckbox += ' </div>';
                }
            } else {
                userCheckbox += '<div> No User Found </div>';
            }

            $('#userCheckbox').html(userCheckbox);

            if (callback) {
                callback();
            }
        }
    });
}

function showUserListForGroup(devicetype) {

    var userId, brandTitle;
    var userCheckbox = '';
    $.ajax({
        type: "GET",
        async: false,
        dataType: "json",
        url: API_URL + 'all-users',
        data: { device_type: devicetype },
        success: function (data) {

            var result = data['Result'];
            var totalRecords = data['Result'].length;

            if (totalRecords > 0) {

                userCheckbox += ' <div class="checkbox" style="float: left;width:31%">';
                userCheckbox += ' <label>';
                userCheckbox += '  <input type="checkbox" class="flat" onchange="checkUncheckAll(this.checked)">All';
                userCheckbox += ' </label>';
                userCheckbox += ' </div>';

                for (i = 0; i < totalRecords; i++) {
                    // userCheckbox += '<div class="col-md-9 col-sm-9 col-xs-12">';
                    userCheckbox += ' <div class="checkbox" style="float: left;width:31%">';
                    userCheckbox += ' <label>';
                    userCheckbox += ' <input type="checkbox" name="uids[]" value="' + result[i]['id'] + '" class="flat">' + result[i]['first_name'] + ' ' + result[i]['last_name'];
                    userCheckbox += ' </label>';
                    userCheckbox += ' </div>';
                }
            } else {

                userCheckbox += '<div> No User Found </div>';

            }

        }
    });
    $('#userCheckbox').html(userCheckbox);
}

function checkUncheckAll(value) {
    $('input[type=checkbox]').prop('checked', value);
}

function showAllUserList(devicetype, selectedUsers) {
    var userCheckbox = '';
    $.ajax({
        type: "GET",
        async: false,
        dataType: "json",
        url: API_URL + 'users',
        data: { device_type: devicetype },
        success: function (data) {
            var result = data['Result'];
            var totalRecords = data['Result'].length;

            if (totalRecords > 0) {
                userCheckbox += ' <div class="checkbox" style="float: left;width:31%">';
                userCheckbox += ' <label>';
                userCheckbox += ' <input type="checkbox" class="flat" onchange="checkUncheckAll(this.checked)">All';
                userCheckbox += ' </label>';
                userCheckbox += ' </div>';

                for (i = 0; i < totalRecords; i++) {
                    var haveSelected = 0;
                    var checked = '';
                    haveSelected = selectedUsers.indexOf(result[i]['id']) != -1; // true

                    if (haveSelected == true) {
                        checked = 'checked';
                    }
                    // userCheckbox += '<div class="col-md-9 col-sm-9 col-xs-12">';
                    userCheckbox += ' <div class="checkbox" style="float: left;width:31%">';
                    userCheckbox += ' <label>';
                    userCheckbox += ' <input type="checkbox" ' + checked + ' name="uids[]" value="' + result[i]['id'] + '" class="flat">' + result[i]['first_name'];
                    userCheckbox += ' </label>';
                    userCheckbox += ' </div>';
                }
            } else {
                userCheckbox += '<div> No User Found </div>';
            }
        }
    });
    $('#userCheckbox').html(userCheckbox);
}

function ActiveInActiveUser(UserId) {
    //Ajax Call to activate function
    $.ajax({
        type: "GET",
        url: API_URL + "user/changeStatus/" + UserId,
        data: {},
        success: function (data) {
            if (data == '1' || data == 1) {

                var DataToset = '<button type="button" class="btn btn-warning btn-xs status_change" data-toggle="modal" data-id="' + UserId + '" data-target=".bs-example-modal-sm">Blocked</button>';
                $('#CurerntStatusDiv' + UserId).html(DataToset);
            } else {
                var DataToset = '<button type="button" class="btn btn-success btn-xs status_change" data-toggle="modal" data-id="' + UserId + '" data-target=".bs-example-modal-sm">Active</button>'
                $('#CurerntStatusDiv' + UserId).html(DataToset);
            }
        }
    });

    $('#CloseButton').trigger('click');
}


function showStats() {
    var userId, brandTitle;
    var userCheckbox = '';
    $.ajax({
        type: "GET",
        async: false,
        dataType: "json",
        url: API_URL + 'stats/user',
        data: {},
        success: function (data) {


            var result = data['Result'];
            var total_users = data['Result']['total_users'];
            var total_active_users = data['Result']['total_active_users'];
            var total_blocked_users = data['Result']['total_blocked_users'];
            var total_android_users = data['Result']['total_android_users'];
            var total_android_users_percentage = data['Result']['total_android_users_percentage'];
            var total_ios_users = data['Result']['total_ios_users'];
            var total_ios_users_percentage = data['Result']['total_ios_users_percentage'];
            var total_web_users = data['Result']['total_web_users'];
            var total_web_users_percentage = data['Result']['total_web_users_percentage'];
            var total_fb_users = data['Result']['total_fb_users'];

            var total_articles = data['Result']['total_articles'];
            var total_events = data['Result']['total_events'];

            var total_categories = data['Result']['total_categories'];
            var total_tag = data['Result']['total_tag'];

            var total_moderator = data['Result']['total_moderator'];
            var total_sub_admins = data['Result']['total_sub_admins'];


            $('#totalUser').html(total_users);
            // $('#totalAndroidUser').html(total_android_users);
            //$('#totalIosUser').html(total_ios_users);
            $('#totalFbUser').html(total_fb_users);
            $('#totalArticles').html(total_articles);
            $('#totalEvents').html(total_events);
            $('#totalTag').html(total_events);
            $('#totalCategories').html(total_categories);

            $('#totalSubAdmins').html(total_sub_admins);
            $('#totalModerator').html(total_moderator);


            var labels = '"Android" , "Ios" , "Web"';
            var labelsValue = '"' + total_android_users + '","' + total_ios_users + '"';


            //populateDonutChart(total_android_users,total_ios_users,total_web_users);

        }
    });
    //$('#userCheckbox').html(userCheckbox);
}

function showDynamicStats(startdate, enddate) {

    var userId;
    var userCheckbox = '';
    $.ajax({
        type: "GET",
        async: false,
        dataType: "json",
        url: API_URL + 'statistics',
        data: { start_date: startdate, end_date: enddate },
        success: function (data) {

            var result = data['Result'];
            var total_posts = data['Result']['total_posts'];
            var total_articles = data['Result']['total_articles'];
            var total_events = data['Result']['total_events'];
            var feedbacks = data['Result']['feedbacks'];
            var fb_users = data['Result']['fb_users'];
            var total_users = data['Result']['total_user'];

            var article_percentage = data['Result']['article_percentage'];
            var event_percentage = data['Result']['event_percentage'];


            var and_user_per = data['Result']['and_user_per'];
            var ios_user_per = data['Result']['ios_user_per'];

            $('#totalPosts').html(total_posts);
            $('#total_articles').html(total_articles);
            $('#total_events').html(total_events);
            $('#totalFeedbacks').html(feedbacks);
            $('#total_users').html(total_users);
            $('#fb_users').html(fb_users);


            $('#articles_count').html(total_articles);
            $('#articles_per').css("width", article_percentage + "%");

            $('#event_count').html(total_events);
            $('#event_per').css("width", event_percentage + "%");


            populateDonutChart(and_user_per, ios_user_per);

            $('#andUserPer').html(and_user_per + '%');
            $('#iosUserPer').html(ios_user_per + '%');


        }
    });

}

function populateDonutChart(total_android_users, total_ios_users) {

    var options = {
        legend: false,
        responsive: false
    };

    new Chart(document.getElementById("canvas1"), {
        type: 'doughnut',
        tooltipFillColor: "rgba(51, 51, 51, 0.55)",
        data: {
            labels: [
                "Articles",
                "Events"
            ],
            datasets: [{
                data: [total_android_users, total_ios_users],
                backgroundColor: [
                    "#1b5Ac3",
                    "#9B59B6",
                    "#26B99A",
                    "#E74C3C",

                    "#3498DB"
                ],
                hoverBackgroundColor: [
                    "#BDC3C7",
                    "#9B59B6",

                    "#36CAAB",
                    "#E95E4F",
                    "#B370CF",
                ]
            }]
        },
        options: options
    });

}

function displayPostListing(type, postId) {
    var userCheckbox = '';

    if (type === 'generic') {
        $('#post-area').hide();
    } else if (type === 'article') {
        $('#post-area').show();
        var link = 'posts/articles';
    } else if (type === 'event') {
        $('#post-area').show();
        var link = 'posts/events';
    }

    $.ajax({
        type: "GET",
        async: false,
        dataType: "json",
        url: API_URL + link,
        data: {},
        success: function (data) {

            var result = data['Result'];
            var totalRecords = data['Result'].length;

            userCheckbox += '<div class="form-group">';
            userCheckbox += '<label for="notification_type" class="control-label col-md-3 col-sm-3 col-xs-12">Select Post *</label>';
            userCheckbox += '<div class="col-md-6 col-sm-6 col-xs-12">';
            userCheckbox += '<select class="form-control col-md-7 col-xs-12" id="post_id" name="post_id">';

            if (totalRecords > 0) {
                for (i = 0; i < totalRecords; i++) {
                    userCheckbox += ' <option value="' + result[i]['id'] + '">' + result[i]['title'] + '</option>';
                }
                userCheckbox += ' </select>';
                userCheckbox += ' </div>';
                userCheckbox += ' </div>';
            } else {
                userCheckbox += ' <option value="0">Select Post</option>';
                userCheckbox += ' </select>';
                userCheckbox += ' </div>';
                userCheckbox += ' </div>';
            }

            $('#post-area').html(userCheckbox);

            if (postId) {
                $('#post_id').val(postId);
            }
            initSelect2('#post_id');
        }
    });
}

function initSelect2(elemSelector) {
    $(elemSelector).select2({
        placeholder: "Select the shows that relate to the article",
        allowClear: true
    });
}

function initCharsCounter(inputSelector, resultSelector, maxLength) {
    $(inputSelector).on('input', function () {
        var inputText = $(this).val();
        if (inputText.length > maxLength) {
            inputText = inputText.substr(0, maxLength);
            $(this).val(inputText);
        }
        $(resultSelector).text(inputText.length);
    });
}

function initDeleteForm(tableSelector, formSelector) {
    $(tableSelector).on('click', formSelector, function (e) {
        e.preventDefault();
        var $form = $(this);
        $('#confirm').modal({ backdrop: 'static', keyboard: false })
            .on('click', '#delete-btn', function () {
                $form.submit();
            });
    });
}

function preloadImage(link) {
    (new Image()).src = link;
}

function showAnsweredUsers(tableSelector, buttonSelector) {
    var correctlyWord;
    var closeButton = '<button type="button" id="CloseButton" class="btn btn-default" data-dismiss="modal">Close</button>';

    $(tableSelector).on('click', buttonSelector, function() {
        var $button = $(this);
        var id = $button.data('id');
        var showCorrect = $button.data('correct');
        var amount = $button.data('amount');

        correctlyWord = showCorrect ? 'correctly' : 'incorrectly';

        if (!amount) {
            showUsersInModal();
            return;
        }

        $.ajax({
            type: 'GET',
            url: AJAX_URL + 'questions/get-answered-users',
            data: {
                id: id,
                show_correct: showCorrect
            },
            success: function (data) {
                if (data.Response === '2000') {
                    showUsersInModal(data.Result);
                }
            }
        });
    });

    function showUsersInModal(users) {
        var usersHTML;

        if (users) {
            usersHTML = collectUsers(users);
        } else {
            usersHTML = 'No one answered this question ' + correctlyWord + ' yet.';
        }

        var popupHeader = 'Users that answered ' + correctlyWord;

        showModal({
            header: popupHeader,
            message: usersHTML,
            open: true,
        });
    }

    function collectUsers(users) {
        var usersFullNames = '';
        users.forEach(function(userData) {
            usersFullNames += '<div>' + userData.first_name + ' ' + userData.last_name + '</div>';
        });

        return usersFullNames;
    }
}

function removeArrayItemByValue(array, value) {
    var index = array.indexOf(value);
    if (index !== -1) {
        array.splice(index, 1);
    }
}

function getWeekDayDate(dateString, weekDay, takeFirstDay) {
    var date = new Date(prepareDate(dateString));
    var day = date.getDay();
    var diff = date.getDate() - day + weekDay;
    if (!takeFirstDay && day != weekDay) {
        diff +=  7;
    }
    var newDate = new Date(date.setDate(diff));
    return getFormatedDate(newDate);
}

function initWeekpicker($weekInput, startDate, endDate, grossWeekDay, takeFirstDay) {

    $weekInput.weekpicker({
        weekFormat: 'w',
        dateFormat: 'yy-mm-dd',
        minDate: startDate,
        maxDate: endDate,
        firstDay: grossStartWeekDay,
        calculateWeek: getWeekNumber,
        onSelectCallback: function(date, instance) {
            var startWeekDayDate = getWeekDayDate(date, grossWeekDay, takeFirstDay);
            var weekNumber = instance.weekPickerInput.val();
            var weekText = getWeekFormatedText(weekNumber, startWeekDayDate);
            instance.weekPickerInput.val(weekText);
            instance.datePickerInput.val(startWeekDayDate);
        },
    });
}

function getWeekFormatedText(weekNumber, startWeekDate) {
    var formattedDate = moment(startWeekDate, 'YYYY-MM-DD').format('MM-DD-YYYY');
    return '#' + weekNumber + ' (' + formattedDate + ')';
}

function getFormatedDate(date) {
    var year = date.getFullYear();
    var month = date.getMonth() + 1;
    var day = date.getDate();

    if (month < 10) {
        month = '0' + month;
    }

    if (day < 10) {
        day = '0' + day;
    }

    return year + '-' + month + '-' + day;
}

function prepareDate(dateString) {
    return dateString && dateString.replaceAll('-', '/');
}

function getWeekNumber(date, startWeekDay) {
    date = new Date(Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()));
    date.setUTCDate(date.getUTCDate() - ((date.getUTCDay() - startWeekDay) || 0));
    var yearStart = new Date(Date.UTC(date.getUTCFullYear(), 0, 1));
    return Math.ceil((((date - yearStart) / 86400000) + 1) / 7);
}

function initGeocomplete(locationSelector, mapSelector, warningSelector, changePositionMessage) {
    var $location = $(locationSelector);
    var $map = $(mapSelector);
    var $warning = $(warningSelector);
    var $latitude = $('input[data-geo=lat]');
    var $longitude = $('input[data-geo=lng]');
    var $address = $('[data-geo=address]');
    var initLocation = $location.val();

    init();

    if (initLocation) {
        $location.trigger('geocode', initLocation);
    }

    function init() {
        $location.geocomplete({
            map: $map,
            details: 'form',
            markerOptions: {
                draggable: true,
            },
            mapOptions: {
                language: 'en',
            },
            detailsAttribute: 'data-geo',
        });

        $location.on('geocode:dragged', function(event, latLng) {
            $latitude.val(latLng.lat());
            $longitude.val(latLng.lng());
            if (changePositionMessage) {
                $warning.html(changePositionMessage);
            }
        });

        if ($address) {
            $location.bind('geocode:result', function(event, location) {
                var address = getAddress(location.address_components);
                $address.val(address);
            });
        }
    }

    function getAddress(location) {
        var address, streetNumber;

        location.forEach(function(locationDatum) {
            if (locationDatum.types.indexOf('street_number') !== -1) {
                streetNumber = locationDatum.long_name;
                return;
            }

            if (locationDatum.types.indexOf('route') !== -1) {
                address = locationDatum.long_name;
            }
        });

        // If the street number exists, the route also exists.
        if (address && streetNumber) {
            address = streetNumber + ' ' + address;
        }

        return address;
    }
}

function initPostNotification(options) {
    var $postedAs = $(options.postedAs);
    var $notificationBlock = $(options.notificationBlock);
    var notificationDescriptionLength = options.notificationDescriptionLength || 75;
    var $notificationCheckbox = $(options.notificationCheckbox);
    var $notificationCheckboxLabel = $notificationBlock.find('label:first');
    var $notificationLabels = $notificationBlock.find('label:not(:first)');
    var $notificationSwitcher = {};
    var $notificationSwitcherWrapper = {};
    var $notificationTitle = $(options.notificationTitle);
    var $notificationDescription = $(options.notificationDescription);
    var $title = $(options.title);
    var $description = $(options.description);
    var descriptionValue = $description.val();
    var $statusSelect = $(options.statusSelect);
    var titleAutofill = true;
    var descriptionAutofill = true;

    checkAutofill();

    if (titleAutofill) {
        $title.on('input', function() {
            if (!titleAutofill) {
                return;
            }

            var title = $title.val();
            $notificationTitle.val(title);
        });

        // Disable autocompletion if the title is entered manually
        $notificationTitle.on('input', function() {
            offTitleAutofill();
        });
    }

    if (descriptionAutofill) {
        $description.on('input', function() {
            if (!descriptionAutofill) {
                return;
            }

            descriptionValue = $description.val();
            fillNotificationDescription();
        });

        // Disable description if the title is entered manually
        $notificationDescription.on('input', function() {
            offDescriptionAutofill();
        });

    }

    // Without setTimeout for some reason does not work
    setTimeout(function() {
        $(options.notificationToggle).on('click', changeNotificationActivity);
        $statusSelect.change(changeNotificationActivity);
        $notificationSwitcher = $notificationBlock.find('.icheckbox_flat-green');
        $notificationSwitcherWrapper = $notificationSwitcher.parent();
        changeNotificationActivity();
    });

    $postedAs.change(changeNotificationActivity);

    function offTitleAutofill() {
        titleAutofill = false;
        $notificationTitle.unbind();
    }

    function offDescriptionAutofill() {
        descriptionAutofill = false;
        $notificationDescription.unbind();
    }

    function checkAutofill() {
        var title = $title.val();

        if (!$notificationCheckbox.is(':checked')) {
            $notificationTitle.val(title);
            fillNotificationDescription();
            return;
        }

        var notificationTitle = $notificationTitle.val();
        var descriptionPart = descriptionValue.substr(0, notificationDescriptionLength);
        var notificationDescription = removeElipsis($notificationDescription.val());

        if (title !== notificationTitle) {
            offTitleAutofill();
        }

        if (descriptionPart !== notificationDescription) {
            offDescriptionAutofill();
        }
    }

    function fillNotificationDescription() {
        var descriptionPart = descriptionValue.substr(0, notificationDescriptionLength);

        if (descriptionValue.length > notificationDescriptionLength) {
            descriptionPart += '...';
        }

        $notificationDescription.val(descriptionPart);
    }

    function removeElipsis(string) {
        return string.replace(/\.{3}$/, '');
    }

    function changeNotificationActivity() {
        var disabled = $postedAs.val() === 'user' || $statusSelect.val() !== 'approved';

        $notificationCheckbox.prop('disabled', disabled);
        $notificationCheckboxLabel.toggleClass('disabled', disabled);
        $notificationSwitcher.toggleClass('disabled', disabled);
        $notificationSwitcherWrapper.toggleClass('disabled', disabled);

        if (!disabled) {
            disabled = !$notificationCheckbox.is(':checked');
        }

        $notificationLabels.toggleClass('disabled', disabled);
        $notificationTitle.prop('disabled', disabled);
        $notificationDescription.prop('disabled', disabled);
    }
}

function initMainPostField(options, notificationOptions, previewFieldsData) {
    var $postedAs = $(options.postedAs);
    var $availability = $(options.availability);
    var $notificationBlock = $(notificationOptions.notificationBlock);
    var $groups = $(options.groups);
    var $groupsLabel = $groups.find('label');
    var $groupsInput = $groups.find('select');
    var $topBar = $(options.topBar);
    var $topBarLabel = $topBar.find('label');
    var $topBarInput = $topBar.find('input');

    if ($notificationBlock) {
        notificationOptions.postedAs = options.postedAs;
        notificationOptions.statusSelect = options.statusSelect;
        initPostNotification(notificationOptions);
    }

    $(options.tags).select2({
        maximumSelectionLength: 5,
        placeholder: 'With Max Selection limit 5',
        allowClear: true
    });

    $postedAs.change(changeHeadingActivity);
    $availability.change(changeGroupsActivity);

    $(options.publishedDate).datepicker({ dateFormat: 'yy-mm-dd' });
    $(options.publishedTime).timepicker();

    initCharsCounter(options.title, options.titleCounter, options.titleMaxLength);
    initImagePreview(options.imageInput, options.imagePreviewWrapper);
    initStatusSelect(options.statusSelect, options.publishedDateField);
    changeHeadingActivity();
    changeGroupsActivity();

    initPostPreview(options.previewButton, previewFieldsData);
    preloadImage(options.previewBackground);

    function changeHeadingActivity() {
        var disabled = $postedAs.val() === 'user';
        $topBarLabel.toggleClass('disabled', disabled);
        $topBarInput.prop('disabled', disabled);
    }

    function changeGroupsActivity() {
        var disabled = $availability.val() === 'all';
        $groupsLabel.toggleClass('disabled', disabled);
        $groupsInput.prop('disabled', disabled);
    }
}

function convertImportDate(grossDataFromImport) {
    var formattedDate = moment(grossDataFromImport, 'MM-DD-YYYY').format('YYYY-MM-DD');
    return formattedDate != 'Invalid date' && formattedDate;
}

String.prototype.replaceAll = function(search, replacement) {
    var target = this;
    return target.split(search).join(replacement);
};

function Validator() {

    var dateExpression = /^[0-9]{4}-[0-1][0-9]-[0-3][0-9]$/;
    var dateValidator = new RegExp(dateExpression);
    var errors;

    var errorMessages = {
        int: '{fieldName} must be integer',
        float: '{fieldName} must be float',
        date: '{fieldName} must be date',
        is_sunday: '{fieldName} must be Sunday',
        before: '{fieldName} must be before {value}',
        after: '{fieldName} must be after {value}',
    };

    var validatingMethods = {
        int: function(value) {
            return value == parseInt(value);
        },
        float: function(value) {
            return !!+value;
        },
        date: function(value) {
            return dateValidator.test(value);
        },
        is_sunday: function(value) {
            var weekDayNumber = (new Date(value)).getDay();
            return !isNaN(weekDayNumber) && weekDayNumber === 0;
        },
        before: function(value, verifiedValue) {
            return value <= verifiedValue;
        },
        after: function(value, verifiedValue) {
            return value >= verifiedValue;
        }
    }

    function check(fieldsData, rules) {
        errors = [];
        Object.keys(fieldsData).forEach(function(field) {
            if (!rules[field] || !rules[field].rules) {
                return;
            }

            var fieldError = false;
            rules[field].rules.some(function(rule) {
                if (typeof rule === 'string') {
                    fieldError = checkOneRule(rule, fieldsData[field], rules[field].fieldName);
                } else if (rule.length === 2) {
                    fieldError = checkOneRule(rule[0], fieldsData[field], rules[field].fieldName, rule[1]);
                }

                if (fieldError) {
                    errors.push(fieldError);
                    return true;
                }
            });
        });

        return !!errors.length && errors;
    }

    function checkOneRule(rule, value, fieldName, verifiedValue) {
        if (!validatingMethods[rule](value, verifiedValue)) {
            return generateErrorMessage(rule, fieldName, verifiedValue);
        }

        return false;
    }

    function generateErrorMessage(rule, fieldName, value) {
        var errorMessage = errorMessages[rule].replaceAll('{fieldName}', fieldName);

        if (value) {
            errorMessage = errorMessage.replaceAll('{value}', value);
        }

        return errorMessage;
    }

    return check;
}

function getDuplicates(array) {
    var sortedArray = array.slice().sort();
    var results = [];
    for (var i = 0; i < sortedArray.length - 1; i++) {
        if (sortedArray[i + 1] == sortedArray[i]) {
            results.push(sortedArray[i]);
        }
    }

    return results;
}

function initIconSelect(selectorId, badgeInputSelector, iconsData) {
    var $input = $(badgeInputSelector);

    var initIconValue = parseInt($input.val());
    var initIconIndex = initIconValue && getIconIndex(initIconValue);

    var iconSelect = new IconSelect(selectorId, {
        'selectedIconWidth': 48,
        'selectedIconHeight': 48,
        'iconsWidth': 48,
        'iconsHeight': 48,
    });

    document.getElementById(selectorId).addEventListener('changed', setSelectedIcon);

    iconSelect.refresh(iconsData);
    var $scrollBox = $('#' + selectorId + '-box-scroll');

    if (initIconIndex) {
        iconSelect.setSelectedIndex(initIconIndex);
    } else {
        setSelectedIcon();
    }

    setIconTitles();
    setHeight();

    function setSelectedIcon() {
        $input.val(iconSelect.getSelectedValue());
    }

    function getIconIndex(iconId) {
        var iconIndex = 0;
        iconsData.some(function(iconData, index) {
            if (iconData.iconValue === iconId) {
                iconIndex = index;
                return true;
            }
        });
        return iconIndex;
    }

    function setIconTitles() {

        $.each($scrollBox.find('.icon'), function(index, icon) {
            $(icon).attr('data-title', iconsData[index].iconTitle);
        });
    }

    function setHeight() {
        var rowsAmount = Math.floor(iconsData.length / 3);

        if (rowsAmount > 3) {
            var height = Math.floor(iconsData.length / 3) * 70 + 3;
            $scrollBox.find('div:first').height(height);
        }

    }
}

function sendPost(url, requestBody) {
    var $postForm = $('#post-form');
    $postForm.attr('action', url);

    $postForm.find('input[type=hidden]:not([name=_token])').remove();

    var $inputs = Object.keys(requestBody).map(function(field) {
        return $('<input type="hidden" name="' + field + '" value="' + requestBody[field] + '">');
    });

    $postForm.append($inputs).submit();
}