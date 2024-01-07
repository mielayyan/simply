$(document).ready(function() {
    var msg = "";
    var msg1 = $("#error_msg17").html();
    var msg2 = $("#error_msg18").html();
    var msg3 = $("#error_msg19").html();
});

function trim(a) {
    return a.replace(/^\s+|\s+$/, '');
}

function disable() {
    document.user_register.register.disabled = true;
}

function enable() {

    document.user_register.register.disabled = false;
}

function check_username_availability(username) {
    var error = 0;
    var path_temp = document.user_register.path_temp.value;
    var path_root = document.user_register.path_root.value;

    if (username == "" || username.length < 6)

    {
        var msg = "";
        $("#username_box").removeClass();

        $("#username_box").addClass('messagebox');

        msg = $("#error_msg12").html();

        $("#username_box").html('<img align="absmiddle" src="' + path_temp + 'images/Error.png" />' + '<font color="#a94442">' + msg + '</font>').show().fadeTo(1900, 1);

        error = 1;

        disable();

    }
    if (username == "" || username.length > 12) {
        error = 1;

        disable();
    }

    if (error != 1)

    {
        var msg = "";
        disable();

        var username_available = path_root + "admin/employee/employee_username_availability";
        //remove all the class add the messagebox classes and start fading

        $("#username_box").removeClass();

        $("#username_box").addClass('messagebox');

        msg = $("#error_msg13").html();
        
        $("#username_box").html('<img align="absmiddle" src="' + path_temp + 'images/loader.gif" /> ' + msg).show().fadeTo(1900, 1);

        //check the username exists or not from ajax
        $.post(username_available, {
            user_name: username
        }, function(data) {
            if (trim(data) == 'yes') //if username not avaiable
            {
                $("#username_box").fadeTo(200, 0.1, function()  //start fading the messagebox
                {
                    //add message and change the class of the box and start fading
                    $(this).removeClass();
                    $(this).addClass('messageboxok');
                    msg = $("#error_msg14").html();
                    $(this).html('<img align="absmiddle" src="' + path_temp + 'images/accepted.png" /> ' + msg).show().fadeTo(1900, 1);
                    enable();
                });
            }
            else
            {
                $("#username_box").fadeTo(200, 0.1, function() //start fading the messagebox
                {
                    //add message and change the class of the box and start fading
                    $(this).removeClass();
                    $(this).addClass('messageboxerror');
                    msg = $("#error_msg12").html();
                    $(this).html('<img align="absmiddle" src="' + path_temp + 'images/Error.png" /> ' + '<font color="#a94442">' + msg + '</font>').show().fadeTo(1900, 1);
                    disable();
                });
            }
        });
    }
}

//************************************************edited by amrutha
var ValidateUser = function() {
    // function to initiate Validation Sample 1
    var msg = $("#error_msg").html();
    var msg1 = $("#error_msg1").html();
    var msg2 = $("#error_msg2").html();
    var msg3 = $("#error_msg3").html();
    var msg4 = $("#error_msg4").html();
    var msg5 = $("#error_msg5").html();
    var msg6 = $("#error_msg6").html();
    var msg7 = $("#error_msg7").html();
    var msg8 = $("#error_msg8").html();
    var msg9 = $("#error_msg16").html();
    var msg10 = $("#error_msg19").html();
    var msg11 = $("#error_msg20").html();
    var msg12 = $("#error_msg21").html();
    var msg13 = $("#error_msg23").html();
    var msg14 = $("#error_msg24").html();

    var valid_password_msg = trans('minlength', trans('password'), 6);
    if ($("#passwordPolicyJson").length) {
        var passwordPolicyObj = JSON.parse($("#passwordPolicyJson").val());
        valid_password_msg = trans('minlength', trans('password'), passwordPolicyObj.min_length);
        if (passwordPolicyObj.disableHelper != 1) {
            valid_password_msg = trans('field_no_min_requirement', trans('password'));
        }
    }
    
    var runValidatorweeklySelection = function() {
        var searchform = $('#user_register');
        $.validator.addMethod('valid_username', function() {
            var path_root = document.user_register.path_root.value;
            var ref_user_availability = path_root + "admin/employee/employee_username_availability";
            var flag = false;
           $.ajax({
                'url': ref_user_availability,
                'type': "POST",
                'data': {
                    user_name: $('#ref_username').val()
                },
                'dataType': 'text',
                'async': false,
                success: function(data) {
                    if (data == 'no') { //if username not avaiable
                        flag =  false;
                    } else {
                        flag = true;
                    }
                }
            });
           return flag;
        });
        var errorHandler1 = $('.errorHandler', searchform);
        $('#user_register').validate({
            errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function(error, element) { // render error placement for each input type
                error.insertAfter(element);
                error.insertAfter($(element).closest('.input-group'));
                // for other inputs, just perform default behavior
            },
            ignore: ':hidden',
            rules: {
                ref_username: {
                    required: true,
                    minlength: 6,
                    maxlength: 20,
                    valid_username: true,
                    alpha_numeric_some_special: true,

                },
                first_name: {
                    required: true,
                    maxlength: 250,
                },
                last_name: {
                    required: true,
                    maxlength: 250,
                },
                mobile_no: {
                    required: true,
                    minlength: 5,
                    maxlength:20,
                    phone_number: true,
                },
                pswd: {
                    required: true,
                    valid_password: true,
                    maxlength: 50,
                },
                cpswd: {
                    required: true,
                    equalTo: "#pswd"
                },
                email: {
                    required: true,
                    email: true
                }
            },
            messages: {
                ref_username: {
                    valid_username: trans('invalid_username')
                },
                pswd:{ 
                    required: trans('required', trans('password')),
                    valid_password: valid_password_msg,
                    maxlength: trans('maxlength', trans('pswd'), "50"),
                },
                cpswd:{ 
                    equalTo: trans('password_mismatch')
                },
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
            }
        });
    };

    return {
        //main function to initiate template pages
        init: function() {
            runValidatorweeklySelection();

        }
    };
}();