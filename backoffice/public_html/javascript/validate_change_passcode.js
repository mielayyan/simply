var ValidateUser = function() {

    // function to initiate Validation Sample 1
    var msg = $("#error_msg1").html();
    var msg1 = $("#error_msg2").html();
    var msg2 = $("#error_msg3").html();
    var msg3 = $("#error_msg4").html();
    var msg4 = $("#error_msg5").html();
	var msg5 = $("#error_msg6").html();
	
	var valid_password_msg = trans('minlength', trans('new_password'), 6);
	if ($("#passwordPolicyJson").length) {
		var passwordPolicyObj = JSON.parse($("#passwordPolicyJson").val());
		valid_password_msg = trans('minlength', trans('new_password'), passwordPolicyObj.min_length);
		if (passwordPolicyObj.disableHelper != 1) {
			valid_password_msg = trans('field_no_min_requirement', trans('new_password'));
		}
	}

    var runValidatorweeklySelection = function() {
	var searchform = $('#change_pass');
	var errorHandler1 = $('.errorHandler', searchform);
	$('#change_pass').validate({
	    errorElement: "span", // contain the error msg in a span tag
	    errorClass: 'help-block',
	    errorId: 'admin_tp',
	    errorPlacement: function(error, element) { // render error placement for each input type

		error.insertAfter(element);
		// for other inputs, just perform default behavior
	    },
	    ignore: ':hidden',
	    rules: {
		old_passcode: {
		    minlength: 6,
		    maxlength:100,
		    required: true
		},
		new_passcode: {
		    valid_password: true,
            maxlength:50,
		    required: true
		},
		re_new_passcode: {
		    minlength: 6,
		    maxlength:100,
		    required: true,
            equalTo: "#new_passcode"
		}
	    },
	    messages: {
		old_passcode: {
		    minlength: trans('minlength',trans('old_passcode'),"6"),
            maxlength:trans('maxlength',trans('old_passcode'),"100"),
                },
		new_passcode: {
			valid_password: valid_password_msg,
		    required: trans('required',trans('new_password')),
            maxlength:trans('maxlength',trans('new_password'),"50"),
                },
		re_new_passcode: {
			required:trans('required',trans('re_new_passcode')),
		    minlength: trans('minlength',trans('re_new_passcode'),"6"),
            maxlength:trans('maxlength',trans('re_new_passcode'),"100"),
                    equalTo: trans('password_mismatch'),
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
	    }
	});
    $.validator.addMethod('valid_user', function(param, input) {
        var path_root = $('#base_url').val();
        var path = path_root + "admin/password/validate_username";
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
    var runValidatordailySelection = function() {
	var searchform = $('#change_pass_user');
	var errorHandler1 = $('.errorHandler', searchform);
	$('#change_pass_user').validate({
	    errorElement: "span", // contain the error msg in a span tag
	    errorClass: 'help-block',
	    errorId: 'user_tp',
	    errorPlacement: function(error, element) { // render error placement for each input type

		error.insertAfter(element);
		// for other inputs, just perform default behavior
	    },
	    ignore: ':hidden',
	    rules: {
		user_name: {
		    valid_user:true,
		    required: true
		},
		new_passcode_user: {
			maxlength:50,
		    valid_password: trans('field_no_min_requirement', trans('new_passcode_user')),
		    required: true
		},
		re_new_passcode_user: {
			maxlength:100,
		    minlength: 6,
		    required: true,
                    equalTo: "#new_passcode_user"
		}
	    },
	    messages: {
		user_name: {
           valid_user:trans('invalid',trans('user_name')),
              

		},
		new_passcode_user: {
			required: trans('required', trans('new_password')),
			valid_password: valid_password_msg, 
		    maxlength:trans('maxlength',trans('new_password'),"50"),
                   
                },
		re_new_passcode_user: {
		    minlength:trans('minlength',trans('re_new_passcode'),"6"), 
		    maxlength:trans('maxlength',trans('re_new_passcode'),"100"),
                    
                    equalTo:trans('password_mismatch'),
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
	    }
	});
    };
    return {
	//main function to initiate template pages
	init: function() {
	    runValidatorweeklySelection();
	    runValidatordailySelection();
	}
    };
}();
$(function(){
	autocomplete_off();
    var base_url = $("#base_url").val();
    $('#user_name_1').on('blur', function () {
        $('#e_mail_1').val('');
        $('#e_mail').val('');
        var user_name = $('#user_name_1').val();
        $.ajax({
            url: base_url + 'admin/tran_pass/get_email',
            type: 'POST',
            data: {
                user_name: user_name,
            },
            dataType: 'text',
            success: function (data) {
                if (data != 'no') {
                    $('#err_usr_name').text('');
                    $('#e_mail_1').val(data);
                    $('#e_mail').val(data);
                }
            }});
	});
	
    var error_message = $('#search_member_error').val();
    var error_message2 = $('#search_member_error2').val();
    var error_message3 = $('#error_msg8').html();

	var searchform = $('#forgot_trans_password');
	var errorHandler = $('.errorHandler', searchform);
	$(searchform).validate({
		errorElement: 'span',
		errorClass: 'help-block',
		errorId: 'forgot_tp',
		errorPlacement: function(error, element) {
			error.insertAfter(element);
		},
		ignore: ':hidden',
		rules: {
			user_name: {
				required: true,
				username_check: true
			},
			captcha: {
				required: true,
			}
		},
		messages: {
			user_name: {
				
				username_check:trans('invalid',trans('user_name')),
			},
			captcha: {
				
			}
		},
		// onkeyup: false,
		// onfocusout: function(element) {
		// 	$(element).valid();
		// },
		invalidHandler: function(event, validator) {
			errorHandler.show();
		},
		highlight: function(element) {
			$(element).closest('.help-block').removeClass('valid');
			$(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');
		},
		unhighlight: function(element) {
			$(element).closest('.form-group').removeClass('has-error');
		},
		success: function(label, element) {
			label.addClass('help-block valid');
			$(element).closest('.form-group').removeClass('has-error').addClass('ok');
		}
	});

});