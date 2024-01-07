// global variables
var base_url = $("#base_url").val();

function getUserType() {
    var userType = false;
    $.ajax({
        type: 'GET',
        url: $('#base_url').val() + 'login/get_user_type',
        dataType: 'text',
        async: false,
        success: function(data) {
            userType = (data == '') ? false : data;
        },
        error: function() {
            userType = false;
        }
    });
    return userType;
}

$(function() {
    /* $('ul.nav.nav-sub > li.active').parent('ul').parent('li').addClass('active'); */

    $("form").attr('autocomplete', 'off');

    $(window).on('shown.bs.modal', function() {
        $('input').attr('autocomplete', 'off');
    });

    setJQueryValidationDefaults();

    setJQueryInputFilter();

    loadUserAutoList();
    
});

function setJQueryInputFilter() {
    $.fn.inputFilter = function (inputFilter) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function () {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            }
        });
    };
}

function setJQueryValidationDefaults() {
    jQuery.validator.setDefaults({
        errorPlacement: function(error, element) {
            element.next('.help-block-2').remove();
            if ($(element).hasClass('ckeditor')) {
                var element_id = $(element).attr('id');
                error.insertAfter($('#cke_' + element_id));
            } else if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    /* Rules */

    // greaterThanEqual
    jQuery.validator.addMethod("greaterThanEqual",
        function(value, element, param) {
            var otherElementVal = $(param).val();
            if (!value || !otherElementVal) {
                return true;
            }
            return parseFloat(value) >= parseFloat(otherElementVal);
        }
    );

    // greaterThanNum
    jQuery.validator.addMethod('greaterThanNum', function (value, el, param) {
        return value > param;
    });

    // alpha_numeric
    $.validator.addMethod("alpha_numeric", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z0-9]+$/);
    }, trans('only_alphabets_numerals'));

    $.validator.addMethod("phone_number", function(value, element) {
        return this.optional(element) || value == value.match(/^[0-9+()-\s]+$/);
    }, trans('phone_number'));

    //  alpha_numeric_some_special
    $.validator.addMethod("alpha_numeric_some_special", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z0-9_.-]+$/);
    });

    // Alpha password
    $.validator.addMethod("alpha_password", function(value, element) {
        return this.optional(element) || value == value.match(/^[0-9a-zA-Z\s\r\n@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?\_\`\~]+$/);
    }, trans('alpha_password'));

    $.validator.addMethod("pan_format", function(value, element) {
        return this.optional(element) || value == value.match(/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/);
    }, trans('please_follow_the_pan_format'));

    $.validator.addMethod("alpha_space", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z ]+$/);
    }, trans('only_characters_allowed'));

     $.validator.addMethod("valid_time", function(value, element) {
        return this.optional(element) || value == value.match(/^(0?[1-9]|1[012])(:[0-5]\d) [APap][mM]$/);
    }, trans('enter_valid_time'));

    $.validator.addMethod("alpha_dash", function(value, element) {
        return this.optional(element) || /^[a-z0-9A-Z$@$!%*#?& _~\-!@#\$%\^&\*\(\)?,.:|\\+\\[\]{}''"";`~=]*$/i.test(value);
    }, trans('special_characters_not_allowed'));

    $.validator.addMethod("alpha_city", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z0-9\s\.\,\-]+$/);
    }, trans('alpha_space', trans('city')));

    $.validator.addMethod("alpha_address", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z0-9\s\.\,]+$/);
    }, trans('alpha_space', trans('address')));

    $.validator.addMethod("cardExpiry", function() {
        if ($("#card_expiry_mm").val() != "" && $("#card_expiry_yyyy").val() != "") {
            return true;
        } else {
            return false;
        }
    }, trans('please_select_month_year'));

    $.validator.addMethod("valid_value", function() {
        var limit = $('#p_scents p').size();
        for (var i = 0; i <= limit; i++) {
            if ($('#epin' + i).val() != "") {
                return true;
            } else {
                return false;
            }
        }
    }, trans('valid_transaction_password'));

    $.validator.addMethod("alpha", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z]+$/);
    }, trans('only_characters_allowed'));

    // Not Equal to
    $.validator.addMethod("notEqual", function(value, element, param) {
     return this.optional(element) || value != $(param).val();
    }, "This has to be different...");

    // password policy
    if ($("#passwordPolicyJson").length) {
        var passwordPolicyObj = JSON.parse($("#passwordPolicyJson").val());
        if(passwordPolicyObj.disableHelper != 1) {
            // form validator
            $.validator.addMethod("valid_password", function (value, element) {
                var lowerCaseStatus = true;
                if (passwordPolicyObj.lowercase > 0) {
                    var regexString = '[a-z]';
                    for (var i = 0; i < (passwordPolicyObj.lowercase - 1); i++) {
                        regexString = regexString + '.*[a-z]';
                    }
                    var lowercaseRegExp = new RegExp(regexString);
                    lowerCaseStatus = lowercaseRegExp.test(value);
                }

                var upperCaseStatus = true;
                if (passwordPolicyObj.uppercase > 0) {
                    var regexString = '[A-Z]';
                    for (var i = 0; i < (passwordPolicyObj.uppercase - 1); i++) {
                        regexString = regexString + '.*[A-Z]';
                    }
                    var uppercaseRegExp = new RegExp(regexString);
                    upperCaseStatus = uppercaseRegExp.test(value);
                }

                var numberStatus = true;
                if (passwordPolicyObj.number > 0) {
                    var regexString = '[0-9]';
                    for (var i = 0; i < (passwordPolicyObj.number - 1); i++) {
                        regexString = regexString + '.*[0-9]';
                    }
                    var numberRegExp = new RegExp(regexString);
                    numberStatus = numberRegExp.test(value);
                }

                var spCharStatus = true;
                if (passwordPolicyObj.sp_char > 0) {
                    var regexString = '[\\W|_]';
                    for (var i = 0; i < (passwordPolicyObj.sp_char - 1); i++) {
                        regexString = regexString + '.*[\\W|_]';
                    }
                    var spCharRegExp = new RegExp(regexString);
                    spCharStatus = spCharRegExp.test(value);
                }

                var minLengthStatus = (value.length >= passwordPolicyObj.min_length);

                return (lowerCaseStatus && upperCaseStatus && numberStatus && spCharStatus && minLengthStatus);
            });
            // popover helper
            var checkedView = '<i class="fa fa-check-circle" style="color: green;"></i>';
            var unCheckedView = '<i class="fa fa-times-circle" style="color: #f05050;"></i>';

            var lowerCaseStatus = false;
            if (passwordPolicyObj.lowercase > 0) {
                var regexString = '[a-z]';
                for (var i = 0; i < (passwordPolicyObj.lowercase - 1); i++) {
                    regexString = regexString + '.*[a-z]';
                }
                var lowercaseRegExp = new RegExp(regexString);
                lowerCaseStatus = true;
            }

            var upperCaseStatus = false;
            if (passwordPolicyObj.uppercase > 0) {
                var regexString = '[A-Z]';
                for (var i = 0; i < (passwordPolicyObj.uppercase - 1); i++) {
                    regexString = regexString + '.*[A-Z]';
                }
                var uppercaseRegExp = new RegExp(regexString);
                upperCaseStatus = true;
            }

            var numberStatus = false;
            if (passwordPolicyObj.number > 0) {
                var regexString = '[0-9]';
                for (var i = 0; i < (passwordPolicyObj.number - 1); i++) {
                    regexString = regexString + '.*[0-9]';
                }
                var numberRegExp = new RegExp(regexString);
                numberStatus = true;
            }

            var spCharStatus = false;
            if (passwordPolicyObj.sp_char > 0) {
                var regexString = '[\\W|_]';
                for (var i = 0; i < (passwordPolicyObj.sp_char - 1); i++) {
                    regexString = regexString + '.*[\\W|_]';
                }
                var spCharRegExp = new RegExp(regexString);
                spCharStatus = true;
            }

            $(".act-pswd-popover").popover({
                placement: 'bottom',
                html: true,
                title: '<b>' + trans('your_password_must') + '</b>',
                trigger: "focus",
                content: function () {
                    var value = $(this).val();

                    var lowerCaseCheck = unCheckedView;
                    var upperCaseCheck = unCheckedView;
                    var numberCheck = unCheckedView;
                    var spCharCheck = unCheckedView;
                    var minLengthCheck = unCheckedView;
                    
                    if (lowerCaseStatus && lowercaseRegExp.test(value))
                        lowerCaseCheck = checkedView;
                    if (upperCaseStatus && uppercaseRegExp.test(value))
                        upperCaseCheck = checkedView;
                    if (numberStatus && numberRegExp.test(value))
                        numberCheck = checkedView;
                    if (spCharStatus && spCharRegExp.test(value))
                        spCharCheck = checkedView;
                    if (value.length >= passwordPolicyObj.min_length)
                        minLengthCheck = checkedView;

                    var htmlContent = '<div id="pswdPopOverContent" width="150px">';

                    if (lowerCaseStatus)
                        htmlContent = htmlContent + '<p id="lowerCase">' + lowerCaseCheck + ' ' + trans('atleast_lowercase_letter', passwordPolicyObj.lowercase) +'</p>';

                    if (upperCaseStatus)
                        htmlContent = htmlContent + '<p id="upperCase">' + upperCaseCheck + ' ' + trans('atleast_uppercase_letter', passwordPolicyObj.uppercase) +'</p>';

                    if (numberStatus)
                        htmlContent = htmlContent + '<p id="number">' + numberCheck + ' ' + trans('atleast_number', passwordPolicyObj.number) +'</p>';

                    if (spCharStatus)
                        htmlContent = htmlContent + '<p id="sp_char">' + spCharCheck + ' ' + trans('atleast_sp_char', passwordPolicyObj.sp_char) +'</p>';

                    htmlContent = htmlContent + '</div><p id="minlength">' + minLengthCheck + ' ' + trans('atleast_length', passwordPolicyObj.min_length) +'</p></div>';

                    return htmlContent;
                }
            });
            $(".act-pswd-popover").on("keyup", function (e) {
                var value = $(this).val();

                var lowerCaseCheck = unCheckedView;
                var upperCaseCheck = unCheckedView;
                var numberCheck = unCheckedView;
                var spCharCheck = unCheckedView;
                var minLengthCheck = unCheckedView;

                if (lowerCaseStatus && lowercaseRegExp.test(value))
                    lowerCaseCheck = checkedView;
                if (upperCaseStatus && uppercaseRegExp.test(value))
                    upperCaseCheck = checkedView;
                if (numberStatus && numberRegExp.test(value))
                    numberCheck = checkedView;
                if (spCharStatus && spCharRegExp.test(value))
                    spCharCheck = checkedView;
                if (value.length >= passwordPolicyObj.min_length)
                    minLengthCheck = checkedView;

                if (lowerCaseStatus >= 0)
                    $("#lowerCase").html(lowerCaseCheck + ' ' + trans('atleast_lowercase_letter', passwordPolicyObj.lowercase));
                if (upperCaseStatus >= 0)
                    $("#upperCase").html(upperCaseCheck + ' ' + trans('atleast_uppercase_letter', passwordPolicyObj.uppercase));
                if (numberStatus >= 0)
                    $("#number").html(numberCheck + ' ' + trans('atleast_number', passwordPolicyObj.number));
                if (spCharStatus >= 0)
                    $("#sp_char").html(spCharCheck + ' ' + trans('atleast_sp_char', passwordPolicyObj.sp_char));
                $("#minlength").html(minLengthCheck + ' ' + trans('atleast_length', passwordPolicyObj.min_length));
            });
        } else {
            $.validator.addMethod("valid_password", function (value, element) {
                return (value.length >= passwordPolicyObj.min_length);
            });
        }
        
    } else {
        $.validator.addMethod("valid_password", function (value, element) {
            return (value.length >= 6);
        });
    }
    
    /* Messages */

    $.validator.messages.required = function(param, input) {
        var label = getLabelName(input);
        if($(input).is('select')) {
            return trans('required_select', trans(label))
        }
        return trans('required', trans(label));
    }
    $.validator.messages.minlength = function(param, input) {
        let label = getLabelName(input);
        return trans('minlength', trans(label), param);
    }
    $.validator.messages.maxlength = function(param, input) {
        let label = getLabelName(input);
        return trans('maxlength', trans(label), param);
    }
    $.validator.messages.alpha_numeric = function(param, input) {
        return trans('only_alphabets_numerals');
    }
    $.validator.messages.alpha_numeric_some_special = function(param, input) {
         let label = getLabelName(input);
        return trans('alpha_numeric_some_special', trans(label));
    }
    $.validator.messages.invalid = function(param, input) {
         let label = getLabelName(input);
        return trans('invalid', trans(label));
    }
    $.validator.messages.email = function() {
        return trans('valid_email');
    }
    $.validator.messages.alpha_city = function() {
        return trans('alpha_space', trans('city'));
    }
    $.validator.messages.digits = function() {
        return trans('digits');
    }
    $.validator.messages.number = function() {
        return trans('digits');
    }
    $.validator.messages.alpha_space = function() {
        return trans('alpha_space_only');
    }
    $.validator.messages.agree = function() {
        return trans('agree');
    }
    $.validator.messages.valid_position = function() {
        return trans('invalid', trans('position'));
    }
    $.validator.messages.valid_sponsor = function() {
        return trans('invalid', trans('sponsor_username'));
    }
    $.validator.messages.user_name_available = function() {
        return trans('username_not_available');
    }
    $.validator.messages.min = function(param) {
        return trans('greater_than', param.toString());
    }

}

/**
 * [getLabel name of input]
 * @param  {[element]} input
 * @return {[string]}
 */
function getLabelName(input) {
    let label = $(input).siblings('label').text();
    if(label == "") {
        label = $(input).attr('name');
    }
    return label;
}

function scrollTop() {
    $("html, body").animate({ scrollTop: 0 }, "slow");
}

function loadUserAutoList() {
    $('.user_autolist').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'admin/home/ajax_users_autolist',
                        data: { keyword: keyword },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });

    $('.agent_autolist').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'admin/home/ajax_agent_autolist',
                        data: { keyword: keyword },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });
    $('.agent_autolist_user').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'user/home/ajax_agent_autolist',
                        data: { keyword: keyword },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });
    $('.agent_autolist_new').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'admin/home/ajax_agent_autolist_new',
                        data: { keyword: keyword },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });
}

function changeDefaultLanguage(language_id) {
    var user_type = getUserType();
    if (user_type == 'admin' || user_type == 'user' || user_type == 'Unapproved') {
        $.ajax({
            url: base_url + getUserType() + '/home/change_default_language',
            data: { language: language_id },
            type: 'post',
            beforeSend: function() {

            },
            success: function(data) {
                location.replace(location.href);
            },
            error: function() {
            },
            complete: function() {
                $('#update_language_info').attr('disabled', false);
            }
        });

    }
}

/**
 * [trans validation language]
 * @param  {[string]} key     [language key]
 * @param  {[string]} replace [replace string]
 * @return {[string]}         [language]
 */
function trans(key = "", replace = "", replace2 = "") {
    var replace = replace || "";
    var replace2 = replace2 || "";
    if(window.translations) {
        translation = window.translations[key] || key;
        if(translation.includes('*')) {
            translation = translation.replace("*", "").toLowerCase();
        }
        if(translation) {
            translation = translation.replace("*", "").replace("%s", replace);
            translation = translation.replace("*", "").replace("%s", replace2);
            return translation;
        }
    }
    return key;
}

/**
 * [capitalizeFirstLetter capitalise first letter in a string]
 * @param  {[type]} string [mixed string]
 * @return {[type]} string [cpapitalised string]
 */
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

/**
 * [getFieldLang description]
 * @param  {[html]} input [input box]
 * @return {[string]}    [lang key]
 */
function getFieldLang(input) {
    if($(input).parent().hasClass('input-group')) {
        var lang = $(input).parent('.input-group').prevAll('label.control-label').text();
    }else{
        var lang = $(input).parents('.form-group').find('label.control-label').text();
    }

    if ($(input).is('[data-lang]')) {
        lang = $(input).data('lang');
    }
    lang = lang.replace(/\*/g, "");
    return lang.toLowerCase();
}

/**
 * [autocomplete_off description]
 * @param  {String} field_name [classname or id name]
 * @return {[void]} [nothing]
 */
function autocomplete_off(field_name = ".autocomplete-off") {
    $(field_name).attr('readonly', true)
    setTimeout(function() {
        $(field_name).attr('readonly', false);
    }, 1000);
}

/**
 * @param  {Number amount}
 * @return {[string currency formatted amount]}
 */
function format_currency(amount = 0) {
    var default_currency_value = $('#DEFAULT_CURRENCY_VALUE').val();
    var default_symbol_left = $('#DEFAULT_SYMBOL_LEFT').val();
    var default_symbol_right = $('#DEFAULT_SYMBOL_RIGHT').val();
    var default_precision = $('#DEFAULT_PRECISION').val();

    return `${default_symbol_left} ${parseFloat(amount * default_currency_value).toFixed(default_precision)} ${default_symbol_right}`;
}


function thousands_currency_format(value = 0) {
    var default_currency_value = $('#DEFAULT_CURRENCY_VALUE').val();
    var default_symbol_left = $('#DEFAULT_SYMBOL_LEFT').val();
    var default_symbol_right = $('#DEFAULT_SYMBOL_RIGHT').val();
    var default_precision = $('#DEFAULT_PRECISION').val();
    var newValue = value* default_currency_value;
    if (value >= 1000) {
        var suffixes = ["", "K", "M", "B","T"];
        var suffixNum = Math.floor( (""+value).length/3 );
        var shortValue = '';
        for (var precision = 2; precision >= 1; precision--) {
            shortValue = parseFloat( (suffixNum != 0 ? (value / Math.pow(1000,suffixNum) ) : value).toPrecision(precision));
            var dotLessShortValue = (shortValue + '').replace(/[^a-zA-Z 0-9]+/g,'');
            if (dotLessShortValue.length <= 2) { break; }
        }
        if (shortValue % 1 != 0)  shortValue = shortValue.toFixed(default_precision);
        newValue = shortValue+suffixes[suffixNum];
    }
    return `${default_symbol_left} ${newValue} ${default_symbol_right}`;
}

function loadUserDropdown() {
    $('.user-search-dropdown').select2({
        language: {
            searching: function() {
                return "";
            }
        },
        minimumInputLength: 1,
        multiple: true,
        placeholder: capitalizeFirstLetter(trans('user_name')),
        allowClear: true,
        closeOnSelect: false,
        width: 'auto',
        ajax: {
            url: $('#base_url').val() + "admin/user_search",
            dataType: 'json',
            delay: 250,
            processResults: function (response) {
                return {
                    results: response
                };
            }
        }
    });
}

function showErrorAlert(message, position = "top-right") {
    $.toast({
        heading: trans('error'),
        text: message,
        position: position,
        stack: false,
        icon: 'error',
        hideAfter: 3000
    });
}

function showSuccessAlert(message, position = "top-right") {
    $.toast({
        heading: trans('success'),
        text: message,
        position: position,
        stack: false,
        icon: 'success',
        hideAfter: 3000
    });
}

function closePopup(element) {
    $(element).find('.modal-content').scrollTop(0);
    $(element).find('form')[0].reset();
    $(element).fadeOut(3000,function() {
        $(element).modal('hide');
    });
}

function setValidationErrors(form, response) {
    for (input_name in response.validation_error) {
        var element = form.find('#' + input_name);
        var error_html = '<div class="text-danger">' + response.validation_error[input_name] + '</div>';
        if (element.parent().hasClass('input-group')) {
            element.parent().after(error_html);
        } else {
            element.after(error_html);
        }
    }
}

function loadDateRangePicker(element) {
    var ranges_language = JSON.parse($('#daterangepicker_ranges_language').val());
    var locale = JSON.parse($('#daterangepicker_language').val());
    var start = moment.utc($('#system_start_date').val(), "MMMM D, YYYY");
    var end = moment();
    var ranges = {};
    ranges[ranges_language['All']] = [start, moment()];
    ranges[ranges_language['Today']] = [moment(), moment()];
    ranges[ranges_language['ThisWeek']] = [moment().startOf('week'), moment().endOf('week')];
    ranges[ranges_language['ThisMonth']] = [moment().startOf('month'), moment().endOf('month')];
    ranges[ranges_language['ThisYear']] = [moment().startOf('year'), moment().endOf('year')];

    function dateRangePickerHtml(start, end) {
        if (start && end) {
            $(element).find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    }

    $(element).daterangepicker({
        autoUpdateInput: false,
        alwaysShowCalendars: true,
        startDate: start,
        endDate: end,
        locale: locale,
        ranges: ranges,
    }, dateRangePickerHtml);
    dateRangePickerHtml(start, end);
}

function reloadDateRangePicker(element) {
    var start = moment.utc($('#system_start_date').val(), "MMMM D, YYYY");
    var end = moment();

    function dateRangePickerHtml(start, end) {
        if (start && end) {
            $(element).find('span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        }
    }

    dateRangePickerHtml(start, end);
    $(element).data('daterangepicker').setStartDate(start);
    $(element).data('daterangepicker').setEndDate(end);
}

function newexportaction(e, dt, button, config) {
    var date_time = new Date().toLocaleString().replace(/[^a-zA-Z0-9 ]+/g,'-');
    config.filename = config.filename.replace(/[^a-zA-Z_ ]+/g,'').replace(/_+/g, '');
    config.filename += `(${date_time})`;
    config.filename = config.filename.replace(/\s\s+/g, ' ');
    var self = this;
    var oldStart = dt.settings()[0]._iDisplayStart;
    dt.one('preXhr', function (e, s, data) {
        // Just this once, load all data from the server...
        data.start = 0;
        data.length = 2147483647;
        data.total_row = true;
        dt.one('preDraw', function (e, settings) {
            
            // Call the original action function
            if (button[0].className.indexOf('buttons-copy') >= 0) {
                $.fn.dataTable.ext.buttons.copyHtml5.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-excel') >= 0) {
                $.fn.dataTable.ext.buttons.excelHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.excelHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.excelFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-csv') >= 0) {
                $.fn.dataTable.ext.buttons.csvHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.csvHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.csvFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-pdf') >= 0) {
                $.fn.dataTable.ext.buttons.pdfHtml5.available(dt, config) ?
                    $.fn.dataTable.ext.buttons.pdfHtml5.action.call(self, e, dt, button, config) :
                    $.fn.dataTable.ext.buttons.pdfFlash.action.call(self, e, dt, button, config);
            } else if (button[0].className.indexOf('buttons-print') >= 0) {
                $.fn.dataTable.ext.buttons.print.action(e, dt, button, config);
            }
            dt.one('preXhr', function (e, s, data) {
                // DataTables thinks the first item displayed is index 0, but we're not drawing that.
                // Set the property to what it was before exporting.
                settings._iDisplayStart = oldStart;
                data.start = oldStart;
            });
            // Reload the grid with the original page. Otherwise, API functions like table.cell(this) don't work properly.
            setTimeout(dt.ajax.reload, 0);
            // Prevent rendering of the full data to the DOM
            return false;
        });
    });
    // Requery the server with the new one-time export settings
    dt.ajax.reload();
};

function getUrlParameter(sParam) {
    var sPageURL = window.location.search.substring(1),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
        }
    }
    return false;
};

date = new Date().toLocaleString();

const EXPORT_OPTIONS = [
    {
       "extend": 'excelHtml5',
       'title': '',
       "text": `<i class="fa fa-file-excel-o" >  </i> ${trans('excel')}`,
       "titleAttr": trans('excel'), 
       "action": newexportaction,
       /*csustomize: function(xlsx) {
            var sheet = xlsx.xl.worksheets['sheet1.xml'];
       }*/
    }, {
       'title': '',
       "extend": 'csv',
       "text": `<i class="fa fa-file-text-o" >  </i>${trans('csv')}`,
       "titleAttr": trans('csv'),                               
       "action": newexportaction,
       'footer': true,
   }, 
   {
        "extend": 'print',
        "text": `<i class="fa fa-print" > </i> ${trans('print')}`,
        "titleAttr": trans('print'),
        "action": newexportaction,
        'footer': true, 
   }
];