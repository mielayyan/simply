var ValidateUser = function() {

    // function to initiate Validation Sample 1
    var msg = $("#error_msg1").html();
    var msg1 = $("#error_msg2").html();
    var msg2 = $("#error_msg3").html();
    var msg3 = $("#error_msg4").html();
    var msg4 = $("#error_msg5").html();
    var msg5 = $("#error_msg6").html();
    var msg6 = $("#error_msg7").html();
    var msg7 = $("#error_msg8").html();
    var runValidateChangePasswordUser = function() {
        var searchform = $('#change_pass');
        var errorHandler1 = $('.errorHandler', searchform);
        $('#change_pass').validate({
            errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function(error, element) { // render error placement for each input type

                //error.insertAfter(element);
                error.insertAfter(element);

                // for other inputs, just perform default behavior
            },
            ignore: ':hidden',
            rules: {
                user_name: {
                    minlength: 1,
                    required: true,
                    valid_user: true
                },
                new_pwd: {
                    minlength: 6,
                    maxlength:100,
                    required: true
                },
                confirm_pwd: {
                    minlength: 6,
                    maxlength:100,
                    required: true,
                    equalTo:"#new_pwd"
                }
            },
            messages: {
                user_name: {

                    required:trans('required',trans('user_name')),
                     valid_user: trans('invalid',trans('user_name'))
                },
                new_pwd: {
                    required: trans('required',trans('password')),
                    minlength: trans('minlength',trans('password'),"6"),
                    maxlength:trans('maxlength',trans('password'),"100")
                },
                confirm_pwd: {
                    required: trans('required',trans('confirm_password')),
                    
                    equalTo: trans('password_mismatch')
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
        });

        $.validator.addMethod('valid_user', function(param, input) {
            var path_root = $('#base_url').val();
            var path = path_root + "admin/employee/validate_username";
            var flag = false;
           $.ajax({
                'url': path,
                'type': "POST",
                'data': {
                    username: $(input).val()
                },
                'dataType': 'text',
                'async': false,
                success: function(data) {
                   if(data == "yes") {
                    flag = true;
                   } else {
                    flag = false;
                   } 
                }
            });
           return flag;
    });
        
    };

    return {
        //main function to initiate template pages
        init: function() {
            runValidateChangePasswordUser();
        }
    };
}();

