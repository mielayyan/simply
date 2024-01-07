var searchform = $('#reset_password_form');
var errorHandler1 = $('.errorHandler', searchform);

var valid_password_msg = trans('minlength', trans('new_password'), 6);
if ($("#passwordPolicyJson").length) {
    var passwordPolicyObj = JSON.parse($("#passwordPolicyJson").val());
    valid_password_msg = trans('minlength', trans('new_password'), passwordPolicyObj.min_length);
    if (passwordPolicyObj.disableHelper != 1) {
        valid_password_msg = trans('field_no_min_requirement', trans('new_password'));
    }
}

$('#reset_password_form').validate({
    errorElement: "span", // contain the error msg in a span tag
    errorClass: 'help-block',
    errorPlacement: function (error, element) {
        error.insertAfter(element);
    },
    ignore: ':hidden',
    rules: {
        pass: {
            required: true,
            valid_password: true,
            maxlength: 50
        },
        confirm_pass: {
            required: true,
            equalTo: "#pass"
        },
        captcha: {
            required: true
        }
    },
    messages: {
        pass: {
            required: trans('required', trans('new_password')),
            valid_password: valid_password_msg,
            maxlength: trans('maxlength', trans('new_password'), "50")
        },
        confirm_pass: {
            required: trans('required', trans('confirm_password')),
            equalTo: trans('password_mismatch')
        },
    },
    invalidHandler: function (event, validator) { //display error alert on form submit
        errorHandler1.show();
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
    }
});