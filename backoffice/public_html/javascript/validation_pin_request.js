var ValidateUser = function() {

    // function to initiate Validation Sample 1
    var msg = $("#error_msg").html();
    var msg1 = $("#error_msg1").html();
    var msg2 = $("#error_msg2").html();
    var msg3 = $("#error_msg3").html();
    var msg4 = $("#error_msg4").html();
    var msg5 = $("#error_msg5").html();
    var msg6 = $("#error_msg6").html();

    var runValidatorweeklySelection = function() {
	var searchform = $('#upload');
	var errorHandler1 = $('.errorHandler', searchform);
	jQuery.validator.addMethod('todate_greaterthan_fromdate', function(ExpireDate1) {
        var ExpireDate = new Date(ExpireDate1);
        var CurrentDate = new Date();
        CurrentDate.setHours(0, 0, 0, 0);
        ExpireDate.setHours(0, 0, 0, 0);
        return (ExpireDate >= CurrentDate);
    }, "");
	$('#upload').validate({
	    errorElement: "span", // contain the error msg in a span tag
	    errorClass: 'help-block',
	    errorPlacement: function(error, element) { // render error placement for each input type

		error.insertAfter(element);
		// for other inputs, just perform default behavior
	    },
	    ignore: ':hidden',
	    rules: {
		product: {
		    minlength: 1,
		    required: true
		},
		count: {
		    minlength: 1,
		    digits: true,
		    maxlength: 2,
		    required: true
		},
		amount1: {
		    minlength: 1,
		    required: true
		},
		date: {
            required: true,
            date: true,
            todate_greaterthan_fromdate: true,
        },


	    },
	    messages: {
		product: msg,
		amount1: msg2,
		count: {required: msg1,
		    digits: msg3,
                    maxlength:msg6
		},
		date: {
            required: trans('required_select', trans('date')),
            todate_greaterthan_fromdate: trans('valid_date'),
            date: trans('select_valid_date')
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
$("#count").keypress(function (e)
{
//    var msg = $("#error_msg4").html();
//    var value = document.getElementById('count').value;
//    if (value.length >= 5 && e.which != 8) {
//        $("#errmsg").html(msg).show().fadeOut(1200, 0);
//        return false;
//    }
    var msg1= $("#error_msg3").html();
    //if the letter is not digit then display error and don't type anything
    if (e.which != 8 && e.which != 0 && (e.which < 48 || e.which > 57))
    {
	//display error message
	$("#errmsg").html("<font color= '#b94a48'>"+msg1+"</font>").show().fadeOut(1200, 0);
	return false;
    }
});