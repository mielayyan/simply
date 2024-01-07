
$(function () {

    runValidateConfiguration();

});

var runValidateConfiguration = function () {
    var override_commission = $("#override_required").html();
    var purchase_income_perc = $("#purchase_income_perc_required").html();

    $.validator.addMethod("valid_taxes", function (value, element) {
        var tds = parseInt($('#tds').val());
        var service_charge = parseInt($('#service').val());
        var total_tax = tds + service_charge;
        if (total_tax == 0) {
            return true;
        }
        var res = checktaxes(tds, service_charge);
        if (res == true) {
            return true;
        } else {
            return false;
        }
    });

    var searchform = $('#form_general_setting');
    var errorHandler = $('.errorHandler', searchform);

    $('#form_general_setting').validate({
        errorElement: "span",
        errorClass: 'help-block',
        errorId: 'err_config',
        errorPlacement: function (error, element) {
            if ($(element).parent('.input-group').length === 0) {
                error.insertAfter(element);
            }
            else {
                error.insertAfter($(element).closest('.input-group'));
            }
        },
        ignore: ':hidden',
        rules: {
            reg_amount: {
                 minlength: 1,
                 number: true,
                 min: 0,
                 maxlength:10,
                 required: true
            },
            purchase_income_perc: {
                 minlength: 1,
                 number: true,
                 min: 0,
                 max:100,
                 required: true
            },
            service_charge: {
                 minlength: 1,
                 number: true,
                 required: true,
                 min: 0,
                 max: 100
            },
            trans_fee: {
                 minlength: 1,
                 number: true,
                 required: true,
                 maxlength: 10,
            },
            tds: {
                 minlength: 1,
                 number: true,
                 required: true,
                 min: 0,
                 max: 100,
                 valid_taxes: true
            },
        },
        messages: {
            service_charge: {
                required: trans('required', trans('service_charge')),
                min: trans('between_0_100',trans('service_charge')),
                max: trans('between_0_100',trans('service_charge')),
            },
            tds: {
                required: trans('required',trans('tds')),
                min:trans('between_0_100'), 
                max: trans('between_0_100'),
                valid_taxes: trans('sum_of_tds'),
            },
            reg_amount:{
                required: trans('required', trans('registration_amount')),
                maxlength:trans('maxlength_digits',trans('registration_amount'),'10')

        },
            trans_fee:{ 
               required: trans('required',trans('trans_fee')),
               maxlength:trans('maxlength_digits',trans('trans_fee'),'10')

        },
            override_commission: {
                required: trans('required', trans('override_commission')),
                min: trans('between_0_100', trans('override_commission')),
                max: trans('between_0_100', trans('override_commission')),
            },
            purchase_income_perc: {
                minlength: trans("digits"),
                min: trans('between_0_100',trans('purchase_inc')),
                max:trans('between_0_100',trans('purchase_inc')),
                required:trans("required",trans("purchase_inc")),
            }
        },
        invalidHandler: function (event, validator) {
            errorHandler.show();
        },
        highlight: function (element) {
            $(element).closest('.help-block').removeClass('valid');

            $(element).closest('.form-group').removeClass('has-success').addClass('has-error').find('.symbol').removeClass('ok').addClass('required');

        },
        unhighlight: function (element) {
            $(element).closest('.form-group').removeClass('has-error');
        },
        success: function (label, element) {
            label.addClass('help-block valid');
            $(element).closest('.form-group').removeClass('has-error').addClass('ok');
        },
        submitHandler: function (form) {
            form.submit();
        }
    });
}

$(document).ready(function ()
{

    var msg2 = $("#validate_msg13").html();
    var msg3 = $("#validate_msg12").html();
    var msg4 = $("#validate_msg14").html();

    $("#reg_amount").keypress(function (e)
    {
        var flag = 0;
        if (e.which == 46) {
            if ($(this).val().indexOf('.') != -1) {
                flag = 1;
            }
        }
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46)
        {
            flag = 1;
        }
        if (flag == 1) {   //display error message
            $("#errmsg3").html("<font color= '#b94a48'>" + msg2 + "</font>").show().fadeOut(1200, 0);
            return false;
        }
    });

    $("#referal_amount").keypress(function (e)
    {
        var flag = 0;
        if (e.which == 46) {
            if ($(this).val().indexOf('.') != -1) {
                flag = 1;
            }
        }
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46)
        {
            flag = 1;
        }
        if (flag == 1) {
            //display error message
            $("#errmsg6").html("<font color= '#b94a48'>" + msg2 + "</font>").show().fadeOut(1200, 0);
            return false;
        }

    });

    $("#purchase_income_perc").keypress(function (e)
    {
        var flag = 0;
        if (e.which == 46) {
            if ($(this).val().indexOf('.') != -1) {
                flag = 1;
            }
        }
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46)
        {
            flag = 1;
        }
        if (flag == 1) {   //display error message
            $("#errmsg3").html("<font color= '#b94a48'>" + msg2 + "</font>").show().fadeOut(1200, 0);
            return false;
        }
    });

    $("#trans_fee").keypress(function (e)
    {
        var flag = 0;
        if (e.which == 46) {
            if ($(this).val().indexOf('.') != -1) {
                flag = 1;
            }
        }
        //if the letter is not digit then display error and don't type anything
        if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57) && e.which != 46)
        {
            flag = 1;
        }
        if (flag == 1) {
            //display error message
            $("#errmsg7").html("<font color= '#b94a48'>" + msg2 + "</font>").show().fadeOut(1200, 0);
            return false;
        }
    });
});

function checktaxes(tds, service_charge) {
    return ((tds + service_charge) <= 100);
}