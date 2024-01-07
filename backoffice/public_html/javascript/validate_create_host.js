$(function()
{
   ValidateCreatGuest.init();
   ValidateCreateHost.init();
   ValidateInviteGuest.init();
});
var ValidateCreateHost = function () {
    // function to initiate Validation Sample 1
    var msg1 = $("#validate_msg1").html();
    var msg2 = $("#validate_msg2").html();
    var msg3 = $("#validate_msg3").html();
    var msg4 = $("#validate_msg4").html();
    var msg5 = $("#validate_msg5").html();
    var msg6 = $("#validate_msg6").html();
    var msg7 = $("#validate_msg7").html();
    var msg8 = $("#validate_msg8").html();
    var msg9 = $("#validate_msg9").html();


    var runValidateCreateHost = function () {
        var searchform = $('#create_host');
        var errorHandler1 = $('.errorHandler', searchform);
        $('#create_host').validate({
            errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function (error, element) { // render error placement for each input type
                if ($(element).hasClass("date-picker")) {
                    error.insertAfter($(element).closest('.input-group'));
                }
                else
                {
                    error.insertAfter(element);
                }
                ;
                // for other inputs, just perform default behavior
            },
            ignore: ':hidden',
            rules: {
                firstname: {
                    maxlength:250, 
                    required: true
                },
                lastname: {
                    maxlength:250,
                    
                },
                address: {
                    minlength: 3,
                    maxlength:1000,
                    required: true
                },
                city: {
                    
                    required: true
                },
                country: {
                    
                    required: true
                },
                zip: {
                    
                    required: true,
                    number: true
                },
                phone: {
                    
                    required: true,
                    number: true,
                    maxlength:50,
                    minlength:5,
                },
                email: {
                    
                    required: true,
                    email: true
                }

            },
            messages: {
                firstname: {

                    maxlength:trans('maxlength',trans('first_name'),"250"),
                },
                lastname: {

                    maxlength:trans('maxlength',trans('last_name'),"250"),
                },
                address: {

                    maxlength:trans('maxlength',trans('address'),"250"),
                    minlength:trans('minlength',trans('address'),"3")
                },
                
                zip: {
                    number:trans('digits')
                },
                phone: {

                    number:trans('digits'),
                    minlength:trans('minlength_digit',trans('phone'),"3"),
                     maxlength:trans('maxlength_digit',trans('phone'),"50"),
                },
                email: {

                    email:trans('valid_email'),
                }
                
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
    };

    return {
        //main function to initiate template pages
        init: function () {
            runValidateCreateHost();

        }
    };
}();
var ValidateCreatGuest = function () {

    // function to initiate Validation Sample 1
    var msg1 = $("#validate_msg1").html();
    var msg2 = $("#validate_msg2").html();
    var msg3 = $("#validate_msg3").html();
    var msg4 = $("#validate_msg4").html();
    var msg5 = $("#validate_msg5").html();
    var msg6 = $("#validate_msg6").html();
    var msg7 = $("#validate_msg7").html();
    var msg8 = $("#validate_msg8").html();
    var msg9 = $("#validate_msg9").html();


    var runValidateCreateGuest = function () {
        var searchform = $('#create_guest');
        var errorHandler1 = $('.errorHandler', searchform);
        $('#create_guest').validate({
            errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function (error, element) { // render error placement for each input type
                if ($(element).hasClass("date-picker")) {
                    error.insertAfter($(element).closest('.input-group'));
                }
                else
                {
                    error.insertAfter(element);
                }
                ;
                // for other inputs, just perform default behavior
            },
            ignore: ':hidden',
            rules: {
                firstname: {
                    
                    required: true,
                    maxlength:250
                },
                lastname: {
                    
                    maxlength:250
                },
                address: {
                    minlength: 3,
                    required: true,
                    maxlength:1000
                },
                city: {
                    
                    required: true
                },
                country: {
                    
                    required: true
                },
                zip: {
                    
                    required: true,
                    number: true
                },
                phone: {
                    
                    required: true,
                    number: true
                },
                email: {
                    
                    required: true,
                    email: true
                }

            },
            messages: {
                firstname: {

                    maxlength:trans('maxlength',trans('first_name'),"250"),
                },
                lastname: {

                    maxlength:trans('maxlength',trans('last_name'),"250"),
                },
                address: {

                    maxlength:trans('maxlength',trans('address'),"250"),
                    minlength:trans('minlength',trans('address'),"3")
                },
                
                zip: {
                    number:trans('digits')
                },
                phone: {

                    number:trans('digits'),
                    minlength:trans('minlength_digit',trans('phone'),"3"),
                     maxlength:trans('maxlength_digit',trans('phone'),"50"),
                },
                email: {

                    email:trans('valid_email'),
                }
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
    };

    return {
        //main function to initiate template pages
        init: function () {
            runValidateCreateGuest();

        }
    };
}();


var ValidateInviteGuest = function () {

    // function to initiate Validation Sample 1
    var msg1 = $("#validate_msg1").html();

    var runvalidateInviteGuest = function () {
        var searchform = $('#guest');
        var errorHandler1 = $('.errorHandler', searchform);
        $('#guest').validate({
            errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function (error, element) { // render error placement for each input type
                if ($(element).hasClass("date-picker")) {
                    error.insertAfter($(element).closest('.input-group'));
                }
                else
                {
                    error.insertAfter(element);
                }
                ;
                // for other inputs, just perform default behavior
            },
            ignore: ':hidden',
            rules: {
                select_guest: {
                    minlength: 1,
                    required: true
                }

            },
            messages: {
                select_guest: msg1

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
    };

    return {
        //main function to initiate template pages
        init: function () {
            runvalidateInviteGuest();

        }
    };
}();

$(document).ready(function ()
{
    $("#firstname").keypress(function (e)
    {
        //if the letter is not digit then display error and don't type anything
        var msg1 = $("#validate_msg10").html();
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && (e.which < 65 || e.which > 90) && (e.which < 97 || e.which > 122))

        {
            //display error message            

            $("#errormsg1").html(msg1).show().fadeOut(1200, 0);

            return false;
        }

    });
    $("#lastname").keypress(function (e)
    {
        //if the letter is not digit then display error and don't type anything
        var msg1 = $("#validate_msg10").html();
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && (e.which < 65 || e.which > 90) && (e.which < 97 || e.which > 122))

        {
            //display error message            

            $("#errormsg2").html(msg1).show().fadeOut(1200, 0);

            return false;
        }

    });
    $("#address").keypress(function (e)
    {
        var msg1 = $("#validate_msg10").html();
        //if the letter is not digit then display error and don't type anything

        if (e.which != 44 && e.which != 46 && e.which != 32 &&  e.which != 13 && e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && (e.which < 65 || e.which > 90) && (e.which < 97 || e.which > 122))

        {
            //display error message            

            //$("#errormsg3").html("Alpha numeric values only").show().fadeOut(1200, 0);
            $("#errormsg3").html(msg1).show().fadeOut(1200, 0);
            return false;
        }

    });



//        $("#mobile").keypress(function(e)
//    {
//        var msg20 = $("#validate_msg37").html();
//
//        //if the letter is not digit then display error and don't type anything
//
//        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
//
//        {
//
//            //display error message
//
//            $("#errmsg5").html(msg20).show().fadeOut(1200, 0);
//
//            return false;
//
//        }
//
//    });



    $("#city").keypress(function (e)
    {
        //if the letter is not digit then display error and don't type anything
  var msg1 = $("#validate_msg10").html();
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && (e.which < 65 || e.which > 90) && (e.which < 97 || e.which > 122))

        {
            //display error message            

            $("#errormsg4").html(msg1).show().fadeOut(1200, 0);

            return false;
        }

    });



    $("#zip").keypress(function (e)
    {
          var msg2 = $("#validate_msg11").html();
          //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
        {
            //display error message
            $("#errmsg5").html(msg2).show().fadeOut(1200, 0);
            return false;
        }
        return true;
    });
    $("#phone").keypress(function (e)
    {
          var msg2 = $("#validate_msg11").html();
          //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
        {
            //display error message
            $("#errmsg6").html(msg2).show().fadeOut(1200, 0);
            return false;
        }
        return true;
    });
});

$('#check_all').click(function() {
        $(this).is(':checked') ? $('.delete_details').prop('checked', true) : $('.delete_details').prop('checked', false);
    });


$('#check_all1').click(function() {
        $(this).is(':checked') ? $('.guest_details').prop('checked', true) : $('.guest_details').prop('checked', false);
    });


$('#check_all2').click(function() {
        $(this).is(':checked') ? $('.delete_host_details').prop('checked', true) : $('.delete_host_details').prop('checked', false);
    });

$('#check_all3').click(function() {
        $(this).is(':checked') ? $('.delete_guest_details').prop('checked', true) : $('.delete_guest_details').prop('checked', false);
    });

