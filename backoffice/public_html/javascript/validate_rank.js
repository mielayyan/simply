
$(function () {
    Validateconfig.init();
});

var Validateconfig = function() {

    // function to initiate Validation Sample 1
    var msg1 = $("#error_msg1").html();
    var msg2 = $("#error_msg2").html();
    var msg3 = $("#error_msg3").html();
    var msg4 = $("#error_msg5").html();
    var msg5 = $("#error_msg9").html();
    var msg6 = $("#error_msg8").html();
    var msg7 = $("#error_msg4").html();
    var msg8 = $("#error_msg10").html();
    var msg9 = $("#error_msg11").html();
    var msg10 = $("#error_msg12").html();
    var msg11 = $("#error_msg13").html();
    var msg12 = $("#error_msg14").html();
    var msg13 = $("#error_msg15").html();
    var msg14 = $("#error_msg16").html();
    var msg17 = $("#error_msg17").html();
    var msg18 = $("#error_msg18").html();
    var msg20 = $("#error_msg20").html();
    var msg21 = $("#error_msg21").html();
    var msg22 = $("#error_msg22").html();
    var msg23 = $("#error_msg23").html();

    var runValidatorweeklySelection = function() {
	var searchform = $('#rank_form');
        $.validator.addMethod("alpha_num", function(value, element) {
            return this.optional(element) || value == value.match(/^[A-Za-z0-9]+$/);
        }, msg7);
              
        jQuery.validator.addMethod("Regex", function(value, element) {
        return this.optional(element) || value == value.match(/^[0-9,]+$/);
        }, msg4);
        
        $.validator.addMethod("rankname_valid", function(value, element) {
                var path_root = $('#base_url').val();
                var rank_id = $('#rank_id').val();
                var flag2 = false;
                if (value != "/" && value != ".") {
                    $.ajax({
                        'url': path_root + "/admin/configuration/validate_rankname",
                        'type': "POST",
                        'data': {rankname: value,rank_id:rank_id},
                        'dataType': 'text',
                        'async': false,
                        'success': function (data) {
                            if (data == 'no') {
                                flag2 = true;
                            }
                            else if (data == 'yes') {
                                flag2 = false;
                            }
                        },
                        'error': function (error) {
                        },
                    });
                    return flag2;
                }
                else
                {
                    return true;
                }
        },msg14); 

	var errorHandler1 = $('.errorHandler', searchform);
	$('#rank_form').validate({
	    errorElement: "span", // contain the error msg in a span tag
	    errorClass: 'help-block',
	    errorId: 'err_rank',
	    errorPlacement: function(error, element) { // render error placement for each input type

		if($(element).parent('.input-group').length === 0) {
	        error.insertAfter(element);
	    }
	    else {
	        error.insertAfter($(element).closest('.input-group'));
	    }
	    },
	    ignore: ':hidden',
	    rules: {
		rank_name: {
		    minlength: 1,
            maxlength: 250,
            //alpha_num: true,
		    required: true,
		    //rankname_valid: true
		},
		ref_count: {
		    minlength: 1,
                    digits:true,
		    required: true,
		    maxlength: 10
		},
		ref_commission: {
		    minlength: 1,
            digits:true,
		    required: true,
		    maxlength: 10
		},
		rank_achievers_bonus: {
                    minlength: 1,
		    required: true,
		    maxlength: 10,
                    min: 0
		},
		personal_pv: {
		    minlength: 1,
            digits:true,
		    required: true,
		    maxlength: 5
		},
		gpv: {
		    minlength: 1,
                    digits:true,
		    required: true,
		    maxlength: 5
		},
		downline_member_count: {
		    minlength: 1,
            digits:true,
		    required: true,
		    maxlength: 5
        },
        // team_member_count: {
		//     minlength: 1,
        //     digits:true,
		//     required: true,
		//     maxlength: 5
		// },
		downline_count: {
		    minlength: 1,
            digits:true,
		    required: true,
		    maxlength: 5,
        },
        rank_color: {
            required: true
        }

	    },
	    messages: {
		rank_name: {
                   // required: msg8,
                    //alpha_num: msg7
                required: trans('required', trans('rank name')),
                minlength: trans('minlength', trans('rank name'), "1"),
                maxlength: trans('maxlength', trans('rank name'), "250"),
                //rankname_valid:trans('invalid',trans('rank_name'))
                },
		ref_count: {
		     digits:trans('digits'),
            maxlength: trans('maxlength', trans('referral count'), "10")
                },
        ref_commission: {
		   digits:trans('digits'),
            maxlength: trans('maxlength', trans('referral commission'), "10")
                },
		personal_pv: {
		    digits:trans('digits'),
            maxlength: trans('maxlength', trans('personal pv'), "5")
                },
		gpv: {
		    digits:trans('digits'),
            maxlength: trans('maxlength', trans('gpv'), "5")
                },
		downline_count: {
		    digits:trans('digits'),
            maxlength: trans('maxlength', trans('downline count'), "5"),
                },
		// team_member_count: {
		//     required: msg22,
        //     digits:msg4,
		//     maxlength: msg6,
		//     min: msg20
        // },
		rank_achievers_bonus: {
		     maxlength: trans('maxlength', trans('rank_achievers_bonus'), "10"),
             min: trans('greater_zero')
                },
		rank_color: {
		    
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

$("#rank_achievers_bonus").keypress(function(e) {
    if (e.which == 0 || e.which == 8) {
        return;
    }
    var regex = new RegExp("^[0-9.]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
    else {
        var msg = $("#error_msg5").html();
        $("#rank_achievers_bonus_err").html("<font color= '#b94a48'>"+msg+"</font>").show().fadeOut(1200, 0);
        return false;
    }
});

$("#ref_count").keypress(function(e) {
    if (e.which == 0 || e.which == 8) {
        return;
    }
    var regex = new RegExp("^[0-9]+$");
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
    else {
        var msg = $("#error_msg5").html();
        $("#errmsg1").html("<font color= '#b94a48'>"+msg+"</font>").show().fadeOut(1200, 0);
        return false;
    }
});

$(document).ready(function () {
    var msg19 = $("#error_msg19").html();
    var msg4 = $("#error_msg5").html();
    var msg6 = $("#error_msg8").html();
    $('.pkg_count').each(function () {
        $(this).rules("add", {
            required: true,
            minlength: 1,
            digits:true,
            min:1,
            maxlength: 5,
            messages: {
            digits:msg4,
		    maxlength: msg6,
		    required: $(this).data('lang')
            }
        });
    });
    $('.downline_rank_count').each(function () {
        $(this).rules("add", {
            required: true,
            minlength: 1,
            digits:true,
            min:1,
            maxlength: 5,
            messages: {
            digits:msg4,
		    maxlength: msg6,
		    required: $(this).data('lang')
            }
        });
    });
});
