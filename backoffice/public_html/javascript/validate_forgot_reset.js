function onloadCaptcha() {
    document.getElementById('captcha-form').focus();
}


var ValidateUser = function() {


    var runValidatorweeklySelection = function() {

        //alert('dddd');
        var msg1 = $("#error_msg1").html();
        var msg2 = $("#error_msg2").html();
        var msg3 = $("#error_msg3").html();
        var searchform = $('#forgot_password');
        var errorHandler1 = $('.errorHandler', searchform);

        $('#forgot_password').validate({
            errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function(error, element) {

                // render error placement for each input type
                error.insertAfter($(element).closest('.input-group'));
                error.insertAfter(element);
                // for other inputs, just perform default behavior
            },
            ignore: ':hidden',
            rules: {
                user_name: {
                    required: true,
                    valid_user: true
                },
                e_mail: {
                    required: true,
                    email: true,
                    valid_user_email: true,
                },
                captcha: {
                    required: true,
                }
            },
            messages: {
                user_name: {
                    required: trans('required', trans('user_name')),
                    valid_user: trans('not_exists', trans('user_name'))
                },
                e_mail: {
                    required: trans('required', trans('email')),
                    email: trans('valid_email'),
                    valid_user_email: trans('invalid_email')
                },
                captcha: {
                    required: trans('required', trans('captcha'))
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
        
        $.validator.addMethod('valid_user', function(param, input) {
            var flag = false;
            $.ajax({
                url: $('#base_url').val()+'login/valid_user',
                method: "POST",
                data: {
                    user_name: param
                },
                dataType: "text",
                async: false,
                success: function(data) {
                   flag = data == "no" ? false : true;
                }
            });
            return flag;
        });

        $.validator.addMethod('valid_user_email', function(param, input) {
            var flag = false;
            $.ajax({
                url: $('#base_url').val()+'login/valid_user_email',
                method: "POST",
                data: {
                    user_name : $('#user_name').val(),
                    e_mail: param
                },
                dataType: "text",
                async: false,
                success: function(data) {
                    flag = data == "yes" ? true : false;
                }
            });
            return flag;
        });
    };

    return {
        //main function to initiate template pages
        init: function() {
            runValidatorweeklySelection();

        }
    };
}();

$(function() {
    ValidateUser.init();
});