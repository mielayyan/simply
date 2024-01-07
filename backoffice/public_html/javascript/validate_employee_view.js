$(function()
{
   ValidateEmployeeView.init();
});
var ValidateEmployeeView = function() {
    var runValidatorweeklySelection = function() {
        var searchform = $('#edit_form');
        $.validator.addMethod("alpha_num", function(value, element) {
            return this.optional(element) || value == value.match(/^[A-Za-z0-9]+$/);
        });
        var errorHandler1 = $('.errorHandler', searchform);
        $('#edit_form').validate({
            errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function(error, element) { // render error placement for each input type
                error.insertAfter(element);
                 error.insertAfter($(element).closest('.input-group'));
                // for other inputs, just perform default behavior
            },
            ignore: ':hidden',
            rules: {
                first_name: {
                    required: true,
                    maxlength: 250,
                },
                last_name: {
                    required: true,
                    maxlength: 250,
                },
                email: {
                    required: true,
                    email: true
                },
                mobile: {
                    required: true,
                    minlength: 5,
                    maxlength: 50,
                    phone_number: true
                },
            },
             messages: {

                first_name: {

                     maxlength:trans('maxlength',trans('first_name'),"250")
                    
                },
                last_name: {

                     maxlength:trans('maxlength',trans('last_name'),"250")

                },
                mobile: {

                    minlength:trans('minlength',trans('mobile_no'),"5"),
                    maxlength:trans('maxlength',trans('mobile_no'),"50"),
                    phone_number:trans('phone_number')
                },
                email: {

                     email:trans('valid_email'),

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

        }
    };
}();
function showErrorSpanOnKeyup(element, message) {
    var span = "<span class='keyup_error' style='color:#b94a48';>" + message + "</span>";
    $(element).next('span.keyup_error').remove();
    $(element).after(span);
    $(element).next('span:first').fadeOut(2000, 0);
}


$('#check_all').click(function() {
        $(this).is(':checked') ? $('.delete_details').prop('checked', true) : $('.delete_details').prop('checked', false);
    });
 
 