$(function(){
    var dtToday = new Date();
    
    var month = dtToday.getMonth() + 1;
    var day = dtToday.getDate();
    var year = dtToday.getFullYear();
    if(month < 10)
        month = '0' + month.toString();
    if(day < 10)
        day = '0' + day.toString();
    
    var maxDate = year + '-' + month + '-' + day;
    $('#user_dob').attr('max', maxDate);
});

var valid_password_msg = trans('minlength', trans('new_password'), 6);
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
            // pop over helper
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

// user login password change
if ($("#passwordPolicyJson").length) {
    var passwordPolicyObj = JSON.parse($("#passwordPolicyJson").val());
    valid_password_msg = trans('minlength', trans('new_password'), passwordPolicyObj.min_length);
    if (passwordPolicyObj.disableHelper != 1) {
        valid_password_msg = trans('field_no_min_requirement', trans('new_password'));
    }
}
$('#confirm_pwd_admin').on('keyup', function () {
  if ($('#new_pwd_admin').val() == $('#confirm_pwd_admin').val()) {
    $('#message').html('Matching').css('color', 'green');
  } else 
    $('#message').html(trans('password_mismatch')).css('color', 'red');
});
$('#change_login_user_pass').submit(function(e) {
    e.preventDefault();
    console.log($('#change_login_user_pass').valid());
});

// cancel button clear input feild
$('#cancel_change_login_passcod').on('click', function() {
        $('#confirm_pwd_user').val("");
        $('#new_pwd_user').val("");
        $('#current_pwd_user').val("");
        $('.help-block').remove();
        $('body').find('.has-error').removeClass('has-error');
});

// user login password ajax
$('#change_login_user_pass').validate({
    event:"submit",
    errorElement: "span", // contain the error msg in a span tag
    errorClass: 'help-block',
    errorId: 'err_change',
    errorPlacement: function (error, element) { // render error placement for each input type
        error.insertAfter(element);
    },
    ignore: ':hidden',
    rules: {
        current_pwd_user: {
            required: true,
            minlength: 6,
            maxlength: 100,
        },
        new_pwd_user: {
            required: true,
            valid_password: true,
            maxlength: 50,
        },
        confirm_pwd_user: {
            minlength: 6,
            maxlength: 32,
            required: true,
            equalTo: "#new_pwd_user",
            // alpha_password: true
        }
    },
    messages: {
        current_pwd_user: {
            required: trans('required',trans('current_passwd')),
            minlength: trans("minlength",trans('current_passwd'),"6"),
            maxlength:trans('maxlength',trans('current_passwd'),"100"),
        },
        new_pwd_user: {
            required: trans('required',trans('new_passwd')),
            valid_password: valid_password_msg,
            maxlength: trans('maxlength',trans('new_passwd'),"50"),
        },
        confirm_pwd_user: {
            required: trans('required',trans('cpasswd')),
            minlength: trans('minlength',trans('cpasswd'),"6"),
            maxlength:trans('maxlength',trans('cpasswd'),"100"),
            equalTo: trans('password_mismatch') ,
            //alpha_password: msg6
        }

    },
    invalidHandler: function (event, validator) { //display error alert on form submit
        // errorHandler1.show();
    },
    highlight: function (element) {
        $(element).closest('.help-block').removeClass('valid');
        // display OK icon
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
        // add the Bootstrap error class to the control group
    },
    unhighlight: function (element) { // revert the change done by hightlight
        $(element).closest('.form-group').removeClass('has-error');
        // set error class to the control group
    },
    success: function (label, element) {
        label.addClass('help-block valid');
        // mark the current input as valid and display OK icon
        //$(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
        $(element).closest('.form-group').removeClass('has-error').addClass('ok');
    },
    submitHandler: function(form) {
        var form_data = new FormData();
        form_data.append('inf_token', $('input[name="inf_token"]').val());
        form_data.append('current_pwd_user', $('input[name="current_pwd_user"]').val());
        form_data.append('new_pwd_user', $('input[name="new_pwd_user"]').val());
        form_data.append('confirm_pwd_user', $('input[name="confirm_pwd_user"]').val());
        form_data.append('user_name', $('input[name="user_name"]').val());
        var user_type =getUserType();
        $.ajax({

            url: base_url + user_type + '/password/change_user_login_password',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            beforeSend: function() {
                // 
            },
            success: function(response) {
                console.log(response);
                if (response.status) {
                    if(response.status == "failed") {
                        if (response.validation_error) {
                            setValidationErrors(form, response);
                        }
                        showErrorAlert(response.message);
                    } else if(response.status == "success"){
                        showSuccessAlert(response.message);
                        $('#change_login_user_pass').find("input[type=password]").val("")
                        $('.change_user_password').modal('toggle');
                        
                        // prevent modal close on out side click
                        // $('.user_password_success_msg').modal({
                        // backdrop: 'static',
                        // keyboard: false
                        // })
                        // 
                        // $('.user_password_success_msg').modal('toggle');
                    }
                } else {
                    if (response.validation_error) {
                        setValidationErrors(form, response);
                    }
                    showErrorAlert(response.message);
                }
            },
            error: function() {

            },
            complete: function() {
               
            }

        });
    }
});

// transactio password ajax 
$('#change_user_trans_pass').submit(function(e) {
    e.preventDefault();
    console.log($('#change_user_trans_pass').valid());
});

// cancel button clear input feild
$('#cancel_change_trans_passcod').on('click', function() {
        
        $('#confirm_tarns_pwd_user').val("");
        $('#new_tarns_pwd_user').val("");
        $('#current_tarns_pwd_user').val("");
        $('.help-block').remove();
        $('body').find('.has-error').removeClass('has-error');
});

$('#change_user_trans_pass').validate({
    event:"submit",
    errorElement: "span", // contain the error msg in a span tag
    errorClass: 'help-block',
    errorId: 'err_change',
    errorPlacement: function (error, element) { // render error placement for each input type
        error.insertAfter(element);
    },
    ignore: ':hidden',
    rules: {
        current_tarns_pwd_user: {
            required: true,
            minlength: 8,
            maxlength: 100,
        },
        new_tarns_pwd_user: {
            required: true,
            minlength: 8,
            maxlength: 50,
            valid_password: true,
        },
        confirm_tarns_pwd_user: {
            minlength: 8,
            maxlength: 50,
            required: true,
            equalTo: "#new_tarns_pwd_user",
            // alpha_password: true
        }
    },
    messages: {
        current_tarns_pwd_user: {
            required: trans('required',trans('current_passwd')),
            minlength: trans("minlength",trans('current_passwd'),"8"),
            maxlength:trans('maxlength',trans('current_passwd'),"100"),
        },
        new_tarns_pwd_user: {
            required: trans('required',trans('new_passwd')),
            minlength: trans("minlength",trans('current_passwd'),"8"),
            maxlength: trans('maxlength',trans('new_passwd'),"50"),
            valid_password: valid_password_msg,
        },
        confirm_tarns_pwd_user: {
            required: trans('required',trans('cpasswd')),
            minlength: trans('minlength',trans('cpasswd'),"8"),
            maxlength:trans('maxlength',trans('cpasswd'),"50"),
            equalTo: trans('password_mismatch') ,
            //alpha_password: msg6
        }

    },
    invalidHandler: function (event, validator) { //display error alert on form submit
        // errorHandler1.show();
    },
    highlight: function (element) {
        $(element).closest('.help-block').removeClass('valid');
        // display OK icon
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
        // add the Bootstrap error class to the control group
    },
    unhighlight: function (element) { // revert the change done by hightlight
        $(element).closest('.form-group').removeClass('has-error');
        // set error class to the control group
    },
    success: function (label, element) {
        label.addClass('help-block valid');
        // mark the current input as valid and display OK icon
        //$(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
        $(element).closest('.form-group').removeClass('has-error').addClass('ok');
    },
    submitHandler: function(form) {
        var form = $(this);
        var form_data = new FormData();
        form_data.append('inf_token', $('input[name="inf_token"]').val());
        form_data.append('old_passcode', $('input[name="current_tarns_pwd_user"]').val());
        form_data.append('new_passcode', $('input[name="new_tarns_pwd_user"]').val());
        form_data.append('re_new_passcode', $('input[name="confirm_tarns_pwd_user"]').val());
        form_data.append('user_name', $('input[name="user_name"]').val());
        var user_type =getUserType();
        console.log(user_type);
        $.ajax({

            url: base_url + user_type + '/tran_pass/transaction_password_change',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            beforeSend: function() {
                // 
            },
            success: function(response) {
                console.log(response);
                if (response.status) {
                    if(response.status == "failed") {
                        if (response.validation_error) {
                            setValidationErrors(form, response);
                        }
                        showErrorAlert(response.message);
                    } else if(response.status == "success"){
                        showSuccessAlert(response.message);
                        $('#change_user_trans_pass').find("input[type=password]").val("")
                        $('.change_transaction_password').modal('toggle');
                        
                        // prevent modal close on out side click
                        // $('.user_password_success_msg').modal({
                        // backdrop: 'static',
                        // keyboard: false
                        // })
                        // 
                        // $('.user_password_success_msg').modal('toggle');
                    }
                } else {
                    if (response.validation_error) {
                        setValidationErrors(form, response);
                    }
                    showErrorAlert(response.message);
                }
            },
            error: function() {

            },
            complete: function() {
               
            }

        });
    }
});

// forget transaction password 
$('#forgot_trans_password').submit(function(e) {
    e.preventDefault();
    console.log($('#change_user_trans_pass').valid());
});
$(document).on('show.bs.modal', '.modal', function () {
     $("body").css("padding-right","0");
});

$(document).on('hide.bs.modal', '.modal', function () {
     $("body").css("padding-right","0");
});
// cancel button clear input feild
$('#cancel_forgot_passcod').on('click', function() {
        
        $('#captcha_form').val("");
        $('#confirm_tarns_pwd_user').val("");
        $('#new_tarns_pwd_user').val("");
        $('#current_tarns_pwd_user').val("");
        $('.help-block').remove();
        $('body').find('.has-error').removeClass('has-error');
});
// 

$('#forgot_trans_password').validate({
    event:"submit",
    errorElement: "span", // contain the error msg in a span tag
    errorClass: 'help-block',
    errorId: 'err_change',
    errorPlacement: function (error, element) { // render error placement for each input type
        error.insertAfter(element);
    },
    ignore: ':hidden',
    rules: {
        captcha_form: {
            required: true,
        },
    },
    messages: {
        captcha_form: {
            required: trans('required',trans('current_passwd')),
        },

    },
    invalidHandler: function (event, validator) { //display error alert on form submit
        // errorHandler1.show();
    },
    highlight: function (element) {
        $(element).closest('.help-block').removeClass('valid');
        // display OK icon
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
        // add the Bootstrap error class to the control group
    },
    unhighlight: function (element) { // revert the change done by hightlight
        $(element).closest('.form-group').removeClass('has-error');
        // set error class to the control group
    },
    success: function (label, element) {
        label.addClass('help-block valid');
        // mark the current input as valid and display OK icon
        //$(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
        $(element).closest('.form-group').removeClass('has-error').addClass('ok');
    },
    submitHandler: function(form) {
        var form = $(this);
        var form_data = new FormData();
        form_data.append('inf_token', $('input[name="inf_token"]').val());
        form_data.append('captcha', $('input[name="captcha_form"]').val());
        form_data.append('user_name', $('input[name="user_name"]').val());
        var user_type =getUserType();
        console.log(user_type);
        $.ajax({

            url: base_url + user_type + '/tran_pass/forget_transaction_password',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            beforeSend: function() {
                // 
            },
            success: function(response) {
                console.log(response);
                if (response.status) {
                    if(response.status == "failed") {
                        if (response.validation_error) {
                            setValidationErrors(form, response);
                        }
                        showErrorAlert(response.message);
                    } else if(response.status == "success"){
                        showSuccessAlert(response.message);
                        $('#change_user_trans_pass').find("input[type=text]").val("")
                        $('.forget_transaction_pass').modal('toggle');
                        
                        // prevent modal close on out side click
                        // $('.user_password_success_msg').modal({
                        // backdrop: 'static',
                        // keyboard: false
                        // })
                        // 
                        // $('.user_password_success_msg').modal('toggle');
                    }
                } else {
                    if (response.validation_error) {
                        setValidationErrors(form, response);
                    }
                    showErrorAlert(response.message);
                }
            },
            error: function() {

            },
            complete: function() {
               
            }

        });
    }
});forgot_trans_password


 function showErrorAlert(message, position = "top-center") {
    $.toast({
        heading: trans('error'),
        text: message,
        position: position,
        stack: false,
        icon: 'error',
        hideAfter: 8000
    });
}

function showSuccessAlert(message, position = 'top-center') {
    $.toast({
        heading: trans('success'),
        text: message,
        position: position,
        stack: false,
        icon: 'success',
        hideAfter: 8000
    });
}
//button click jquery validation of form feild
/*$('#user_login_pass_change').click(function(){
    console$('#change_login_user_pass').validate();
})*/
// end of login password change

function isAgeMoreThanOrEqual(day, month, year, age) {
    return new Date(year + age, month - 1, day) <= new Date();
}

function isAgeMoreThanOrEqualYear(year, age_limit) {
    var d = new Date();
    var current_year = d.getFullYear();
    return (current_year - year) >= age_limit;
}

function showErrorSpanOnKeyup(element, message) {
    var span = "<span id='err_keyup_" + element.name + "'  class='keyup_error' style='color:#b94a48;'>" + message + "</span>";
    $(element).next('span.keyup_error').remove();
    $(element).after(span);
    $(element).next('span:first').fadeOut(2000, 0);
}


var otp_stat = false;
$(function() {

    var user_type = getUserType();
    if (user_type == 'admin') {
        // otp_stat = getOtpStat();
    }
    ValidateSearchMember.init();
    var base_url = $('#base_url').val();
    var site_url = $('#site_url').val();
    // INITIAL STATE
    $('#upload_profile_image').hide();
    $('#personal_info_div').find('input').prop( "disabled", true );
    $('#personal_info_div').find('select').prop( "disabled", true );
    $('#update_personal_info').hide();
    $('#cancel_personal_info').hide();
    $('#contact_info_div').find('input').prop( "disabled", true );
    $('#contact_info_div').find('select').prop( "disabled", true );

    $('#settings_details_div').find('select').prop( "disabled", true );
    $('#settings_details_div').find('input').prop( "disabled", true );
    
    $('#update_settings_details').hide();
    $('#cancel_settings_details').hide();
    $('#update_contact_info').hide();
    $('#cancel_contact_info').hide();
    $('#bank_info_div').find('input').prop( "disabled", true );
    $('#update_bank_info').hide();
    $('#cancel_bank_info').hide();
    $('#social_info_div').find('input').hide();
    $('#social_info_div').find('input').next('font').hide();
    $('#update_social_info').hide();
    $('#cancel_social_info').hide();
    $('#payment_details_div').find('input').prop( "disabled", true );
    $('#payment_details_div').find('select').prop( "disabled", true );
    $('#update_payment_details').hide();
    $('#cancel_payment_details').hide();
    $('#language_info_div').find('select').hide();
    $('#update_language_info').hide();
    $('#cancel_language_info').hide();
    $('#custom_details_div').find('input').prop('disabled',true);
    $('#update_custom_details').hide();
    $('#cancel_custom_details').hide()

    // curency settings
    if($('#prev_currency').val()=='NA')
    {
        $('#currency').val($('#DEFAULT_CURRENCY_VALUE').val());
        $('#currency').data('value', $('#DEFAULT_CURRENCY_VALUE').val());
    }

    $('form').find('input,select').each(function(i, elem) {
        var input = $(elem);
        input.data('initialState', input.val());
    });

    // DATE OF BIRTH VALIDATION FIX
    $('#dob').on('change', function() {
        if (this.value) {
            $(this).valid();
        } else {
            $('span[for="dob"]').closest('.form-group').removeClass('has-error');
            $('span[for="dob"]').remove();
        }
    });

    // PROFILE IMAGE
    $('#edit_profile_image').on('click', function() {
        $('#profile_image_div').closest('.alert').remove();
        $('#profile_image').hide();
        $('#upload_profile_image').show();
        $('#cancel_personal_info,#cancel_contact_info,#cancel_bank_info,#cancel_social_info,#cancel_payment_details,#cancel_settings_details').click();
    });

    $('#cancel_profile_image').on('click', function() {
        $('#profile_image_div').find('.alert').remove();
        $('#profile_image').show();
        $('#upload_profile_image').hide();
        $('#upload_profile_image').find('img').attr('src', $('#profile_image').find('img').attr('src'));
    });

    $('#update_profile_image').on('click', function(i, k) {
        $(this).closest('.alert').remove();
        $("#profile_image_div").find(".alert").remove();
        var file_data = $('#userfile').prop('files')[0];
        if (file_data) {
            var res = loadModal(this.id, k);
            if (!res) {
                return;
            }
        }
        var form_data = new FormData();
        form_data.append('file', file_data);
        form_data.append('inf_token', $('input[name="inf_token"]').val());
        form_data.append('user_name', $('input[name="profile_user"]').val());
        form_data.append('otp', $('input[name="otp"]').val());
        $.ajax({
            url: base_url + getUserType() + '/profile/update_profile_image',
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            data: form_data,
            type: 'post',
            beforeSend: function() {
                $('#update_profile_image').attr('disabled', true);
            },
            success: function(data) {
                if (data['error']) {
                    $('#alert_div').contents().clone().addClass('alert-danger').append(data['message']).prependTo('#profile_image_div');
                } else if (data['success']) {
                    var new_image_url = site_url + '/uploads/images/profile_picture/' + data['file_name'];
                    $('#profile_image').find('img').attr('src', new_image_url);
                    $('#upload_profile_image').find('img').attr('src', new_image_url);
                    $('#alert_div').contents().clone().addClass('alert-success').append(data['message']).prependTo('#profile_image_div');
                    $('#profile_image').show();
                    $('#upload_profile_image').hide();
                }
            },
            error: function() {

            },
            complete: function() {
                $('#update_profile_image').attr('disabled', false);
            }
        });
    });

    // PERSONAL INFO
    $('#edit_personal_info').on('click', function() {
        $('#personal_info_div').find('.alert').remove();
        $(this).hide();
        $('#cancel_profile_image,#cancel_contact_info,#cancel_bank_info,#cancel_social_info,#cancel_payment_details,#cancel_settings_details').click();
        $('#personal_info_div').find('.form-control-static').hide();
        $('#personal_info_div').find('input,select').prop( "disabled", false );
        $('#update_personal_info,#cancel_personal_info').show();

        $('#personal_info_div').find('input,select').each(function(i, elem) {
            var input = $(elem);
            input.val(input.data('initialState'));
            if (input.val() == 'NA') {
                input.val('');
            }
        });

        $('#dob').combodate({
            format: 'YYYY-MM-DD',
            template: 'YYYY MM DD',
            smartDays: true,
            minYear: 1900,
            maxYear: (new Date()).getFullYear(),
            yearDescending: false,
            firstItem: 'name',
            customClass: 'form-control',
            errorClass: 'none'
        });
        
        let dob_attr = $('#dob').attr('required')
        if (typeof dob_attr !== typeof undefined && dob_attr !== false) {
            $('.year.form-control').attr('required', true);
            $('.month.form-control').attr('required', true);
            $('.day.form-control').attr('required', true);
        }
        $('.year.form-control').wrap('<div></div>');
        $('.year.form-control').attr('name', 'year');
        $('.year.form-control').attr('id', 'year');
        $('.month.form-control').wrap('<div></div>');
        $('.month.form-control').attr('name', 'month');
        $('.month.form-control').attr('id', 'month');
        $('.day.form-control').wrap('<div></div>');
        $('.day.form-control').attr('name', 'day');
        $('.day.form-control').attr('id', 'day');

    });

    $('#cancel_personal_info').on('click', function() {
        $('#first_name').val($('#first_name').data('value'));
        $('#last_name').val($('#last_name').data('value'));
        $('#gender').val($('#gender').data('value'));
        $('#dob').val($('#dob').data('value'));

        $('#personal_info_div').find('.alert').remove();
        $('#dob').combodate('destroy');
        $('#personal_info_div').find('.form-control-static').show();
        $('#personal_info_div').find('input,select').prop( "disabled", true );
        $('#edit_personal_info').show();
        $('#update_personal_info,#cancel_personal_info').hide();
        $('.help-block').remove();
        $('.error-block').remove();
        $('body').find('.has-error').removeClass('has-error');
    });

    $('#update_personal_info').on('click', function(i, k) {
        $('#personal_info_div').find('.alert').remove();
        $('body').find('.error-block').remove();
        
        var res = loadModal(this.id, k);
        if (!res) {
            return;
        }
        $.ajax({
            url: base_url + getUserType() + '/profile/update_personal_info',
            dataType: 'json',
            data: $('#personal_info_div input, #personal_info_div select, input[type="hidden"]'),
            type: 'post',
            beforeSend: function() {
                $('#update_personal_info').attr('disabled', true);
                $('#personal_info .field-error').remove();
            },
            success: function(data) {
                if (data['error']) {
                    showErrorAlert(data['message']);
                    // $('#alert_div').contents().clone().addClass('alert-danger').append(data['message']).prependTo('#personal_info_div');
                    if (data['form_error']) {
                        $.each(data['form_error'], function(i, v) {
                            if (v) {
                                i = i == 'dob' ? 'user_dob' : i;    
                                var error_span = '<span class="error-block" style="color: red;" for="' + i + '">' + v + '</span>';
                                $('#' + i).after(error_span);
                            }
                        });
                    }
                } else if (data['success']) {
                    showSuccessAlert(data['message']);
                    // $('#alert_div').contents().clone().addClass('alert-success').append(data['message']).prependTo('#personal_info_div');
                    $('#dob').data('value', $('#dob').val());
                    $('#gender').data('value', $('#gender').val());    
                    $('#last_name').data('value', $('#last_name').val());
                    $('#first_name').data('value', $('#first_name').val());
                    $('#dob').combodate('destroy');
                    $('#personal_info_div').find('.form-control-static').show();
                    $('#edit_personal_info').show();
                    $('#personal_info_div').find('input,select').prop( "disabled", true );
                    $('#update_personal_info,#cancel_personal_info').hide();
                    $('#profile_full_name').html($('#first_name').val()+' '+$('#last_name').val());
                    $("#profile_full_name").attr("title",$('#first_name').val()+' '+$('#last_name').val());
                    $('#personal_info_div').find('input,select').each(function(i, elem) {
                        var input = $(elem);
                        input.val(input.val());
                        input.prev('.form-control-static').text(input.val());
                        if (input.val() == '') {
                            input.prev('.form-control-static').text('NA');
                        }
                        input.data('initialState', input.val());
                    });
                    $('#gender').prev('.form-control-static').text($('#gender option:selected').text());
                }
            },
            error: function() {

            },
            complete: function() {
                $('#update_personal_info').attr('disabled', false);
            }
        });
        
    });

    // CONTACT INFO
    $('#edit_contact_info').on('click', function() {
        $('#contact_info_div').find('.alert').remove();
        $(this).hide();
        $('#cancel_profile_image,#cancel_personal_info,#cancel_bank_info,#cancel_social_info,#cancel_payment_details,#cancel_settings_details').click();
        $('#contact_info_div').find('.form-control-static').hide();
        $('#contact_info_div').find('input,select').prop( "disabled", false );
        $('#contact_info_div').find('#prof_state_div > select').prop( "disabled", false );
        $('#update_contact_info,#cancel_contact_info').show();

        $('#contact_info_div').find('input,select').each(function(i, elem) {
            var input = $(elem);
            input.val(input.data('initialState'));
            if (input.val() == 'NA') {
                input.val('');
            }
        });

        $('#contact_info_div').find('input[value="NA"]').val('');
        if ($('#pincode').val() == '0') {
            $('#pincode').val('');
        }
        $('#mobile_code,#mobile').show();
        $('#contact_info_div').find('.input-group').show();
        // $("#prof_state_div").load(" #prof_state_div > *");
        $('#mcode').text($('#mobile_code').val());

    });

    $('#cancel_contact_info').on('click', function() {
        $('#address').val($('#address').data('value'));
        $('#address2').val($('#address2').data('value'));
        $('#country').val($('#country').data('value'));
        // $('#state').val($('#state').data('value'));
        $('#city').val($('#city').data('value'));
        $('#pincode').val($('#pincode').data('value'));
        $('#email').val($('#email').data('value'));
        $('#mobile').val($('#mobile').data('value'));
        $('#mobile_code').val($('#mobile_code').data('value'));
        $('#land_line').val($('#land_line').data('value'));
        $('#contact_info_div').find('.alert').remove();
        $('#contact_info_div').find('.form-control-static').show();
        $('#contact_info_div').find('input,select').prop('disabled',true);
        // $('#contact_info_div').find('#prof_state_div > select').hide();
        $('#edit_contact_info').show();
        // $('#mobile_code,#mobile').hide();
        // $('#contact_info_div').find('.input-group').hide();
        $('#update_contact_info,#cancel_contact_info').hide();
        $('.help-block').remove();
        $('body').find('.has-error').removeClass('has-error');
    });

    $('#update_contact_info').on('click', function(i, k) {

        $('#contact_info_div').find('.alert').remove();
        $('body').find('.error-block').remove();
        $('body').find('.help-block').remove();

            var res = loadModal(this.id, k);
            if (!res) {
                return;
            }
            $.ajax({
                url: base_url + getUserType() + '/profile/update_contact_info',
                dataType: 'json',
                data: $('#contact_info_div input, #contact_info_div select, input[type="hidden"]'),
                type: 'post',
                beforeSend: function() {
                    $('#update_contact_info').attr('disabled', true);
                },
                success: function(data) {
                    if (data['error']) {
                        showErrorAlert(data['message']);
                        // $('#alert_div').contents().clone().addClass('alert-danger').append(data['message']).prependTo('#contact_info_div');
                        if (data['form_error']) {
                            $.each(data['form_error'], function(i, v) {
                                if (v) {
                                    var error_span = '<span class="help-block" style="color: red;" for="' + i + '">' + v + '</span>';
                                    $('#' + i).after(error_span);
                                }
                            });
                        }
                    } else if (data['success']) {
                        $('body').find('.error-block').remove();
                        $('#edit_user_profile').find('.help-block').remove();
                        showSuccessAlert(data['message']);
                        $('#address').data('value', $('#address').val());
                        $('#address2').data('value', $('#address2').val());
                        $('#country').data('value', $('#country').val());
                        $('#state').data('value', $('#state').val());
                        $('#city').data('value', $('#city').val());
                        $('#pincode').data('value', $('#pincode').val());
                        $('#email').data('value', $('#email').val());
                        $('#mobile').data('value', $('#mobile').val());
                        $('#mobile_code').data('value', $('#mobile_code').val());
                        $('#land_line').data('value', $('#land_line').val());
                        // $('#alert_div').contents().clone().addClass('alert-success').append(data['message']).prependTo('#contact_info_div');
                        $('#contact_info_div').find('.form-control-static').show();
                        $('#contact_info_div').find('input,select').prop( "disabled", true );
                        $('#profile-email').html($('#email').val());
                        $('#edit_contact_info').show();
                        $('#update_contact_info,#cancel_contact_info').hide();
                        $('#update_contact_info,#cancel_contact_info').hide();
                        $('#contact_info_div').find('input,select').each(function(i, elem) {
                            var input = $(elem);
                            input.val(input.val());
                            input.prev('.form-control-static').text(input.val());
                            if (input.val() == '') {
                                input.prev('.form-control-static').text('NA');
                            }
                            input.data('initialState', input.val());
                        });
                        $('#country').prev('.form-control-static').text($('#country option:selected').text());
                        $('#mobile_code').prev('.form-control-static').text($('#mobile_code').val() + $('#mobile').val());
                        $('#prof_state_div').prev('.form-control-static').text($('#state option:selected').text());
                        if ($('#state').val() == '' || $('#state').val() == 0) {
                            $('#prof_state_div').prev('.form-control-static').text('NA');
                        }
                    }
                },
                error: function() {

                },
                complete: function() {
                    $('#update_contact_info').attr('disabled', false);
                }
            });
    });

    // BANK INFO
    $('#edit_bank_info').on('click', function() {
        
        $('#bank_info_div').find('.alert').remove();
        $(this).hide();
        $('#cancel_profile_image,#cancel_personal_info,#cancel_contact_info,#cancel_social_info,#cancel_payment_details,#cancel_settings_details').click();
        $('#bank_info_div').find('.form-control-static').hide();
        $('#bank_info_div').find('input').prop( "disabled", false );
        $('#update_bank_info,#cancel_bank_info').show();

        $('#bank_info_div').find('input').each(function(i, elem) {
            var input = $(elem);
            input.val(input.data('initialState'));
            if (input.val() == 'NA') {
                input.val('');
            }
        });

    });

    $('#cancel_bank_info').on('click', function() {
        $('#bank_name').val($('#bank_name').data('value'));
        $('#branch_name').val($('#branch_name').data('value'));
        $('#account_holder').val($('#account_holder').data('value'));
        $('#account_no').val($('#account_no').data('value'));
        $('#ifsc').val($('#ifsc').data('value'));
        $('#pan').val($('#pan').data('value'));
        $('#bank_info_div').find('.alert').remove();
        $('#bank_info_div').find('.form-control-static').show();
        $('#bank_info_div').find('input').prop('disabled',true);
        $('#edit_bank_info').show();
        $('#update_bank_info,#cancel_bank_info').hide();
        $('.help-block').remove();
        $('body').find('.has-error').removeClass('has-error');
    });

    $('#update_bank_info').on('click', function(i, k) {
        $('#bank_info_div').find('.alert').remove();
        $('#edit_user_profile').validate();

            var res = loadModal(this.id, k);
            if (!res) {
                return;
            }
            $.ajax({
                url: base_url + getUserType() + '/profile/update_bank_info',
                dataType: 'json',
                data: $('#bank_info_div input, input[type="hidden"]'),
                type: 'post',
                beforeSend: function() {
                    $('#update_bank_info').attr('disabled', true);
                },
                success: function(data) {
                    if (data['error']) {
                        showErrorAlert(data['message']);
                        // $('#alert_div').contents().clone().addClass('alert-danger').append(data['message']).prependTo('#bank_info_div');
                        if (data['form_error']) {
                            $.each(data['form_error'], function(i, v) {
                                if (v) {
                                    var error_span = '<span class="help-block" style="color: red;" for="' + i + '">' + v + '</span>';
                                    $('#' + i).after(error_span);
                                }
                            });
                        }
                    } else if (data['success']) {
                        $('#bank_name').data('value', $('#bank_name').val());
                        $('#branch_name').data('value', $('#branch_name').val());
                        $('#account_holder').data('value', $('#account_holder').val());
                        $('#account_no').data('value', $('#account_no').val());
                        $('#ifsc').data('value', $('#ifsc').val());
                        $('#pan').data('value', $('#pan').val());
                        showSuccessAlert(data['message']);
                        // $('#alert_div').contents().clone().addClass('alert-success').append(data['message']).prependTo('#bank_info_div');
                        $('#bank_info_div').find('.form-control-static').show();
                        $('#bank_info_div').find('input').prop( "disabled", true );
                        $('#edit_bank_info').show();
                        $('#update_bank_info,#cancel_bank_info').hide();

                        $('#bank_info_div').find('input').each(function(i, elem) {
                            var input = $(elem);
                            input.val(input.val());
                            input.prev('.form-control-static').text(trim(input.val()));
                            if (input.val() == '') {
                                input.prev('.form-control-static').text('NA');
                            }
                            input.data('initialState', trim(input.val()));
                        });
                    }
                },
                error: function() {

                },
                complete: function() {
                    $('#update_bank_info').attr('disabled', false);
                }
            });
    });

    // SOCIAL PROFILE
    $('#edit_social_info').on('click', function() {
        $('#social_info_div').find('.alert').remove();
        $(this).hide();
        $('#cancel_profile_image,#cancel_personal_info,#cancel_contact_info,#cancel_bank_info,#cancel_payment_details,#cancel_settings_details').click();
        $('#social_info_div').find('.form-control-static').hide();
        $('#social_info_div').find('.form-control-static').next('input').show();
        $('#update_social_info,#cancel_social_info').show();

        $('#social_info_div').find('input').each(function(i, elem) {
            var input = $(elem);
            input.val(input.data('initialState'));
            if (input.val() == 'NA') {
                input.val('');
            }
        });

    });

    $('#cancel_social_info').on('click', function() {
        $('#social_info_div').find('.alert').remove();
        $('#social_info_div').find('.form-control-static').show();
        $('#social_info_div').find('.form-control-static').next('input').hide();
        $('#edit_social_info').show();
        $('#update_social_info,#cancel_social_info').hide();
        $('.help-block').remove();
        $('body').find('.has-error').removeClass('has-error');
    });

    $('#update_social_info').on('click', function(i, k) {
        $('#social_info_div').find('.alert').remove();
        $('#edit_user_profile').validate();

        if ($('#facebook,#twitter').valid()) {
            var res = loadModal(this.id, k);
            if (!res) {
                return;
            }
            $.ajax({
                url: base_url + getUserType() + '/profile/update_social_info',
                dataType: 'json',
                data: $('#social_info_div input, input[type="hidden"]'),
                type: 'post',
                beforeSend: function() {
                    $('#update_social_info').attr('disabled', true);
                },
                success: function(data) {
                    if (data['error']) {
                        showErrorAlert(data['message']);
                        // $('#alert_div').contents().clone().addClass('alert-danger').append(data['message']).prependTo('#social_info_div');
                        if (data['form_error']) {
                            $.each(data['form_error'], function(i, v) {
                                if (v) {
                                    var error_span = '<span class="help-block" style="color: red;" for="' + i + '">' + v + '</span>';
                                    $('#' + i).after(error_span);
                                }
                            });
                        }
                    } else if (data['success']) {
                        showSuccessAlert(data['message']);
                        // $('#alert_div').contents().clone().addClass('alert-success').append(data['message']).prependTo('#social_info_div');
                        $('#social_info_div').find('.form-control-static').show();
                        $('#social_info_div').find('.form-control-static').next('input').hide();
                        $('#edit_social_info').show();
                        $('#update_social_info,#cancel_social_info').hide();

                        $('#social_info_div').find('input').each(function(i, elem) {
                            var input = $(elem);
                            input.val(input.val());
                            input.prev('.form-control-static').text(input.val());
                            if (input.val() == '') {
                                input.prev('.form-control-static').text('NA');
                            }
                            input.data('initialState', input.val());
                        });
                    }
                },
                error: function() {

                },
                complete: function() {
                    $('#update_social_info').attr('disabled', false);
                }
            });
        }
    });


    // PAYMENT DETAILS
    $('#edit_payment_details').on('click', function() {
        $('#payment_details_div').find('.alert').remove();
        $(this).hide();
        $('#cancel_profile_image,#cancel_personal_info,#cancel_contact_info,#cancel_bank_info,#cancel_social_info,#cancel_settings_details').click();
        $('#payment_details_div').find('.form-control-static').hide();
        $('#payment_details_div').find('input').prop( "disabled", false );
        $('#payment_details_div').find('select').prop( "disabled", false );
        $('#update_payment_details,#cancel_payment_details').show();

        $('#payment_details_div').find('input').each(function(i, elem) {
            var input = $(elem);
            input.val(input.data('initialState'));
            if (input.val() == 'NA') {
                input.val('');
            }
        });

    });

    $('#cancel_payment_details').on('click', function() {
        $('#paypal_account').val($('#paypal_account').data('value'));
        $('#blocktrail_account').val($('#blocktrail_account').data('value'));
        $('#blockchain_account').val($('#blockchain_account').data('value'));
        $('#bitgo_accounta').val($('#bitgo_accounta').data('value'));
        $('#payment_method').val($('#payment_method').data('value'));
        $('#payment_details_div').find('.alert').remove();
        $('#payment_details_div').find('.form-control-static').show();
        $('#payment_details_div').find('input').prop('disabled',true);
        $('#payment_details_div').find('select').prop('disabled',true);
        $('#edit_payment_details').show();
        $('#update_payment_details,#cancel_payment_details').hide();
        $('.help-block').remove();
        $('body').find('.has-error').removeClass('has-error');
    });

    $('#update_payment_details').on('click', function(i, k) {
        $('#payment_details_div').find('.field-error').remove();
        $('#payment_details_div').find('.alert').remove()
        $('#edit_user_profile').validate();

        if ($('#paypal_account,#blockchain_account,#bitgo_account,#blocktrail_account,#payment_method').valid()) {
            var res = loadModal(this.id, k);
            if (!res) {
                return;
            }
            $.ajax({
                url: base_url + getUserType() + '/profile/update_payment_details',
                dataType: 'json',
                data: $('#payment_details_div input, #payment_details_div select, input[type="hidden"]'),
                type: 'post',
                beforeSend: function() {
                    $('#update_payment_details').attr('disabled', true);
                },
                success: function(data) {
                    if (data['error']) {
                        showErrorAlert(data['message']);
                        // $('#alert_div').contents().clone().addClass('alert-danger').append(data['message']).prependTo('#payment_details_div');
                        if (data['form_error']) {
                            $.each(data['form_error'], function(i, v) {
                                if (v) {
                                    var error_span = '<span class="help-block" style="color: red;" for="' + i + '">' + v + '</span>';
                                    $('#' + i).after(error_span);
                                }
                            });
                        }
                    } else if (data['success']) {
                        $('#paypal_account').data('value', $('#paypal_account').val());
                        $('#blocktrail_account').data('value', $('#blocktrail_account').val());
                        $('#blockchain_account').data('value', $('#blockchain_account').val());
                        $('#bitgo_accounta').data('value', $('#bitgo_accounta').val());
                        $('#payment_method').data('value', $('#payment_method').val());
                        showSuccessAlert(data['message']);
                        // $('#alert_div').contents().clone().addClass('alert-success').append(data['message']).prependTo('#payment_details_div');
                        $('#payment_details_div').find('.form-control-static').show();
                        $('#payment_details_div').find('input').prop( "disabled", true );
                        $('#payment_details_div').find('select').prop( "disabled", true );
                        $('#edit_payment_details').show();
                        $('#update_payment_details,#cancel_payment_details').hide();

                        $('#payment_details_div').find('input,select').each(function(i, elem) {
                            var input = $(elem);
                            input.val(input.val());
                            input.prev('.form-control-static').text(input.val());
                            if (input.val() == '') {
                                input.prev('.form-control-static').text('NA');
                            }
                            input.data('initialState', input.val());
                        });
                        $('#payment_method').prev('.form-control-static').text($('#payment_method option:selected').text());
                    }
                },
                error: function() {

                },
                complete: function() {
                    $('#update_payment_details').attr('disabled', false);
                }
            });
        }
    });

    // setting 
    $('#edit_settings_details').on('click', function() {
        $('#settings_details_div').find('.alert').remove();
        $(this).hide();
        $('#cancel_profile_image,#cancel_personal_info,#cancel_contact_info,#cancel_bank_info,#cancel_social_info,#cancel_payment_details').click();
        $('#settings_details_div').find('.form-control-static').hide();
        $('#settings_details_div').find('input,select').prop( "disabled", false );
        $('#update_settings_details,#cancel_settings_details').show();

        $('#language_info_div').find('input,select').each(function(i, elem) {
            var input = $(elem);
            input.val(input.data('initialState'));
            if (input.val() == 'NA') {
                input.val('');
            }
        });

        $('#settings_details_div').find('input[value="NA"]').val('');

    });

    $('#cancel_settings_details').on('click', function() {
        $('#language').val($('#language').data('value'));
        $('#binary_leg').val($('#binary_leg').data('value'));
        $('#currency').val($('#currency').data('value'));
        $('#google_auth_status').val($('#google_auth_status').data('value'));
        $('#settings_details_div').find('.alert').remove();
        $('#settings_details_div').find('input,select').prop( "disabled", true );
        $('#edit_settings_details').show();
        $('#update_settings_details,#cancel_settings_details').hide();
        $('.help-block').remove();
        $('body').find('.has-error').removeClass('has-error');
    });

    $('#update_settings_details').on('click', function(i, k) {
        $('#settings_details_div').find('.alert').remove();
        $('#edit_user_profile').validate();

        if ($('#google_auth_status,#currency,#binary_leg,#language').valid()) {
            var res = loadModal(this.id, k);
            if (!res) {
                return;
            }
            $.ajax({
                url: base_url + getUserType() + '/profile/update_default_settings',
                 // url: base_url + getUserType() + '/profile/update_default_language',
                dataType: 'json',
                data: $('#settings_details_div input, #settings_details_div select, input[type="hidden"]'),
                type: 'post',
                beforeSend: function() {
                    // $('#update_settings_details').attr('disabled', true);
                },
                success: function(data) {
                    if (data['error']) {
                        showErrorAlert(data['message']);
                        // $('#alert_div').contents().clone().addClass('alert-danger').append(data['message']).prependTo('#update_settings_details');
                        if (data['form_error']) {
                            $.each(data['form_error'], function(i, v) {
                                if (v) {
                                    var error_span = '<span class="help-block" style="color: red;" for="' + i + '">' + v + '</span>';
                                    $('#' + i).after(error_span);
                                }
                            });
                        }
                    } else if (data['success']) {
                        $('#language').data('value', $('#language').val());
                        $('#binary_leg').data('value', $('#binary_leg').val());
                        $('#currency').data('value', $('#currency').val());
                        $('#google_auth_status').data('value', $('#google_auth_status').val());
                        showSuccessAlert(data['message']);
                        // $('#alert_div').contents().clone().addClass('alert-success').append(data['message']).prependTo('#language_info_div');
                        $('#settings_details_div').find('.form-control-static').show();
                        $('#settings_details_div').find('input,select').prop('disabled',true);
                        $('#edit_settings_details').show();
                        $('#update_settings_details,#cancel_settings_details').hide();

                        $('#settings_details_div').find('input,select').each(function(i, elem) {
                            var input = $(elem);
                            input.val(input.val());
                            input.prev('.form-control-static').text(input.val());
                            if (input.val() == '') {
                                input.prev('.form-control-static').text('NA');
                            }
                            input.data('initialState', input.val());
                        });
                        $('#language').prev('.form-control-static').text($('#language option:selected').text());
                        if (getUserType() == 'user') {
                            if($('#language').val()!= $('#prev_language').val())
                            {

                             window.location = base_url + $('#current_url_full').val();
                            
                            }
                            if($('#currency').val()!= $('#prev_currency').val())
                            {

                             window.location = base_url + $('#current_url_full').val();
                            
                            }
                        }
                    }
                },
                error: function() {

                },
                complete: function() {
                    $('#update_language_info').attr('disabled', false);
                }
            });
        }
    });
    // CUSTOM DETAILS
    $('#edit_custom_details').on('click', function() {
        $('#custom_details_div').find('.alert').remove();
        $(this).hide();
        $('#cancel_profile_image,#cancel_personal_info,#cancel_contact_info,#cancel_bank_info,#cancel_social_info,#cancel_payment_details,#cancel_settings_details').click();
        $('#custom_details_div').find('.form-control-static').hide();
        $('#custom_details_div').find('input').prop('disabled',false);
        $('#update_custom_details,#cancel_custom_details').show();

        $('#custom_details_div').find('input').each(function(i, elem) {
            var input = $(elem);
            input.val(input.data('initialState'));
            if (input.val() == 'NA') {
                input.val('');
            }
        });
    });

    $('#cancel_custom_details').on('click', function() {
        $('#custom_details_div').find('.alert').remove();
        $('#custom_details_div').find('.form-control-static').show();
        $('#custom_details_div').find('input,select').prop('disabled',true);
        $('#edit_custom_details').show();
        $('#update_custom_details,#cancel_custom_details').hide();
        $('.help-block').remove();
        $('body').find('.has-error').removeClass('has-error');
    });

    $('#update_custom_details').on('click', function(i, k) {
        $('#custom_details_div').find('.alert').remove();
        $('#edit_user_profile').validate();

        if ($('.custom_field').valid()) {
            var res = loadModal(this.id, k);
            if (!res) {
                return;
            }
            $.ajax({
                url: base_url + getUserType() + '/profile/update_custom_field',
                dataType: 'json',
                data: $('#custom_details_div input, #custom_details_div select, input[type="hidden"]'),
                type: 'post',
                beforeSend: function() {
                    $('#update_custom_details').attr('disabled', true);
                },
                success: function(data) {
                    if (data['error']) {
                        showErrorAlert(data['message']);
                        // $('#alert_div').contents().clone().addClass('alert-danger').append(data['message']).prependTo('#custom_details_div');
                        if (data['form_error']) {
                            $.each(data['form_error'], function(i, v) {
                                if (v) {
                                    var error_span = '<span class="help-block" style="color: red;" for="' + i + '">' + v + '</span>';
                                    $('#' + i).after(error_span);
                                }
                            });
                        }
                    } else if (data['success']) {
                        showSuccessAlert(data['message']);
                        // $('#alert_div').contents().clone().addClass('alert-success').append(data['message']).prependTo('#custom_details_div');
                        $('#custom_details_div').find('.form-control-static').show();
                        $('#custom_details_div').find('input,select').prop('disabled',true);
                        $('#edit_custom_details').show();
                        $('#update_custom_details,#cancel_custom_details').hide();

                        $('#custom_details_div').find('input,select').each(function(i, elem) {
                            var input = $(elem);
                            input.val(input.val());
                            input.prev('.form-control-static').text(input.val());
                            if (input.val() == '') {
                                input.prev('.form-control-static').text('NA');
                            }
                            input.data('initialState', input.val());
                        });
                    }
                },
                error: function() {

                },
                complete: function() {
                    $('#update_custom_details').attr('disabled', false);
                }
            });
        }
    });

});

$('#tab1').on('click', function() {
    $('#cancel_profile_image,#cancel_contact_info,#cancel_bank_info,#cancel_social_info,#cancel_payment_details').click();
});
$('#tab2').on('click', function() {
    $('#cancel_profile_image,#cancel_personal_info,#cancel_bank_info,#cancel_social_info,#cancel_payment_details').click();
});
$('#tab3').on('click', function() {
    $('#cancel_profile_image,#cancel_contact_info,#cancel_bank_info,#cancel_personal_info,#cancel_payment_details').click();
});
$('#tab4').on('click', function() {
    $('#cancel_profile_image,#cancel_contact_info,#cancel_personal_info,#cancel_social_info,#cancel_payment_details').click();
});
$('#tab5').on('click', function() {
    $('#cancel_profile_image,#cancel_contact_info,#cancel_bank_info,#cancel_social_info,#cancel_personal_info').click();
});

function trim(a) {
    return a.replace(/^\s+|\s+$/, '');
}
var load_otp = function(form) {
    var url = $("input[name='base_url']").val();
    $.ajax({
        type: 'POST',
        url: url + 'admin/profile/profileOtpModal',
        success: function(msg) {
            $("input[name='submit_form']").text(form);
            $("#otp-modal").modal({
                backdrop: 'static',
                keyboard: false
            });
            $('#otp-modal').on('show.bs.modal', function(e) {
                $('#one_time_password').focus();
            });
            $('#otp-modal').modal('show');
            ValidateOtp.init();
        },
        error: function(msg) {
            alert("Error Occured!");
        }
    });
};
var ValidateOtp = function() {
    // function to initiate Validation Sample 1
    var msg1 = $("#otp_err1").html();
    var msg2 = $("#otp_err2").html();
    ///////----for 'CHANGE USER PASSWORD' Tab - Begin/////////
    var runValidateOtpForm = function() {

        var searchform = $('#otp_form');
        var errorHandler1 = $('.errorHandler', searchform);
        $('#otp_form').validate({
            errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function(error, element) { // render error placement for each input type

                error.insertAfter(element);
                error.insertAfter($(element).closest('.input-group'));
                // for other inputs, just perform default behavior
            },
            ignore: ':hidden',
            rules: {
                one_time_password: {
                    required: true,
                    number: true,
                }
            },
            messages: {
                one_time_password: {
                    required: msg1,
                    number: msg2
                }
            },
            invalidHandler: function(event, validator) { //display error alert on form submit
                errorHandler1.show();
            },
            highlight: function(element) {
                $(element).closest('.help-block').removeClass('valid');
                // display OK icon
                $(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
                // add the Bootstrap error class to the control group
            },
            unhighlight: function(element) { // revert the change done by hightlight
                $(element).closest('.form-group').removeClass('has-error');
                // set error class to the control group
            },
            success: function(label, element) {
                label.addClass('help-block valid');
                // mark the current input as valid and display OK icon
                //$(element).closest('.form-group').removeClass('has-error').addClass('has-success').find('.symbol').removeClass('required').addClass('ok');
                $(element).closest('.form-group').removeClass('has-error').addClass('ok');
            },
            submitHandler: function(form, event) {
                event.preventDefault();
                var release = $("input[name='submit_form']").text();
                if ($('#' + release).valid()) {
                    $('#' + release).trigger("click", ["from_modal"]);
                }
            }
        });
    };
    return {
        //main function to initiate template pages
        init: function() {
            runValidateOtpForm();
        }
    };
}();
var checkAdmin = function(i) {
    var url = $("input[name='base_url']").val();
    var v = "no";
    $.ajax({
        async: false,
        type: 'POST',
        data: { user_name: i },
        url: url + 'admin/profile/checkAdmin',
        success: function(msg) {
            v = msg;
        },
        error: function(msg) {
            alert("Error Occured!");
        }
    });
    return v;
};
var loadModal = function(i, k) {
    if (!otp_stat) return true;
    var mod_status = checkAdmin($("#profile_user").val());
    if (mod_status == "yes") {
        if (k == "from_modal") {
            $('#otp').val($('#one_time_password').val());
            $('#otp-modal').modal('hide');
        } else {
            load_otp(i);
            return false;
        }
    }
    return true;
};
$('#resend').click(function() {
    var resend = $(this).find('span');
    resend.addClass('fa-spin');
    setTimeout(function() {
        var url = $("input[name='base_url']").val();
        $.ajax({
            type: 'POST',
            url: url + 'admin/profile/profileOtpModal',
            success: function(msg) {},
            error: function(msg) {
                $(this).find('span').removeClass('fa-spin');
                alert("Error Occured!");
            }
        });
        resend.removeClass('fa-spin');
    }, 3000);
});

function loadProfile() {
    var alert_margin_top = 0;
    var user_type =getUserType();
    if(user_type == 'admin')
    {
    var alert_margin_top = 35;
    }   
    
    var flag = false;
    var url = $("input[name='base_url']").val();
    $.ajax({
        type: 'POST',
        dataType: 'json',
        data: { user_name: $('input[name="user_name"]').val() },
        url: url + user_type+'/profile/getUserPhoto',
        success: function(data) {
            if (data['success']) {
                $('#header_pro_pic').attr('src',data['photo']);
            }
        },
        error: function() {
            alert("Error Occured!");
        }
    });
    return flag;
};
$('#user_active_block').click(function() {
    $('#contact_info_div').find('.alert').remove();
        var form_data = new FormData();
        form_data.append('inf_token', $('input[name="inf_token"]').val());
                $.ajax({
                url: base_url + getUserType() + '/activate_block_member_ajax',
                dataType: 'json',
                data: {'user_name': $('input[name="user_name"]').val(),'action': $('#user_active_block').val()},
                type: 'post',
                beforeSend: function() {
                    
                },
                success: function(data) {
                    if (data['error']) {
                        showErrorAlert(data['message']);
                        // $('.user-activation-switch').trigger('click');
                        if($('#user_active_block').prop("checked") == true){
                            $('#user_active_block').prop("checked",false);
                        }
                        else if($('#user_active_block').prop("checked") == false){
                            $('#user_active_block').prop("checked",true);
                        }

                        
                    } else if (data['success']) {
                        showSuccessAlert(data['message']);
                        if(data['status'] == 'block')
                        {
                            
                            $('#user_active_inactive_label').html(trans('blocked')+' '+'<span class="fa fa-ban ml-1" style="color:#CB2323" data-fa-transform="shrink-2"></span>').css({'color':'#CB2323','border-color':'#CB2323'});
                            $('#user_active_block').val('activate_member');
                        }
                        else
                        {
                            $('#user_active_block').val('block_member');
                            $('#user_active_inactive_label').html(trans('active')+' '+'<span class="fa fa-check ml-1" style="color:#27c24c"  data-fa-transform="shrink-2"></span>').css({'color':'#27c24c','border-color':'#27c24c'});
                        }
                        
                        
                    }
                },
                error: function() {

                },
                complete: function() {
                    $('#update_contact_info').attr('disabled', false);
                }
            });
        
});


// add personal pv
$('#add_pv').click(function() {
    $('#contact_info_div').find('.alert').remove();
        var form_data = new FormData();
        form_data.append('inf_token', $('input[name="inf_token"]').val());
                $.ajax({
                url: base_url + getUserType() + '/member/add_pv',
                dataType: 'json',
                data: {'user_name': $('input[name="user_name"]').val(),'new_pv': $('#new_pv').val()},
                type: 'post',
                beforeSend: function() {
                    
                },
                success: function(data) {
                    if (data['error']) {
                        showErrorAlert(data['message']);
                        $('#new_pv').val('');
                        $('.update_pv_profile').modal('toggle');


                        
                    } else if (data['success']) {
                        showSuccessAlert(data['message']);
                        $('#new_pv').val('');
                        $('#extra_data_personal_pv').html(data['personal_pv']);
                        $('#extra_data_group_pv').html(data['group_pv']);
                        $('.update_pv_profile').modal('toggle');
                        
                        
                    }
                },
                error: function() {

                },
                complete: function() {
                    
                }
            });
        
});

//  deduct personal pv
$('#deduct_pv').click(function() {
    $('#contact_info_div').find('.alert').remove();
        var form_data = new FormData();
        form_data.append('inf_token', $('input[name="inf_token"]').val());
                $.ajax({
                url: base_url + getUserType() + '/member/deduct_pv',
                dataType: 'json',
                data: {'user_name': $('input[name="user_name"]').val(),'new_pv': $('#new_pv').val()},
                type: 'post',
                beforeSend: function() {
                    
                },
                success: function(data) {
                    if (data['error']) {
                        showErrorAlert(data['message']);
                        $('#new_pv').val('');
                        $('.update_pv_profile').modal('toggle');


                        
                    } else if (data['success']) {
                        showSuccessAlert(data['message']);
                        $('#new_pv').val('');
                        $('#extra_data_personal_pv').html(data['personal_pv']);
                        $('#extra_data_group_pv').html(data['group_pv']);
                        $('.update_pv_profile').modal('toggle');
                        
                        
                    }
                },
                error: function() {

                },
                complete: function() {
                    
                }
            });
        
});
