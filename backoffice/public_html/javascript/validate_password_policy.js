var searchform = $('#password_policy_form');
var errorHandler1 = $('.errorHandler', searchform);
$('#password_policy_form').validate({
    errorElement: "span", // contain the error msg in a span tag
    errorClass: 'help-block',
    errorPlacement: function(error, element) { 
        error.insertAfter(element);
    },
    ignore: ':hidden',
    rules: {
        min_password_length: {
            required: true,
            number: true,
            min: 6,
            max: 50
        }
    },
    messages: {
        min_password_length: {
            required: trans("required", trans('min_password_length')),
            number: trans('should_be_number'),
            min: trans('between_6_and_50'),
            max: trans('between_6_and_50')
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

$("#enable_password_policy").on("change", function () {
    if($("#enable_password_policy").is(":checked")) {
        $("#passwordPolicyDiv").show();
    } else {
        $("#passwordPolicyDiv").hide();
    }
});

$("#min_password_length").on("keypress", function (event) {
    var charCode = (event.which) ? event.which : event.keyCode;

    if((charCode >= 48) && (charCode <= 57))
        return true;
    return false;
});