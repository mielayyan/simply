var ValidateUser = function() {
    // function to initiate Validation Sample 1
    var msg1 = $("#error_msg1").html();
    var msg2 = $("#error_msg2").html();
    var msg3 = $("#error_msg3").html();
    var msg4 = $("#error_msg4").html();
    var msg5 = $("#error_msg5").html();
    var msg6 = $("#error_msg6").html();
    var msg7 = $("#error_msg7").html();
    var msg8 = $("#error_msg8").html();
    var msg9 = $("#error_msg9").html();
    var msg10 = $("#error_msg10").html();

    var runValidatorweeklySelection = function() {
        var searchform = $('#lcp_form');
        var errorHandler1 = $('.errorHandler', searchform);
        $('#lcp_form').validate({
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
                    maxlength: 250
                },
                last_name: {
                    required: true,
                    maxlength: 250
                },
                email: {
                    required: true,
                    email: true
                },
                phone: {
                    required: true,
                    phone_number: true
                },
                skype_id: {
                    maxlength: 250
                },
                comment:{
                    maxlength: 1000
                }

            },
            messages: {
                first_name: {
                    required: trans('required', trans('first_name')),
                    maxlength: trans('maxlength', trans('first_name'), "250")
                },
                last_name: {
                    required: trans('required', trans('last_name')),
                    maxlength: trans('maxlength', trans('last_name'), "250")
                },
                email: {
                    required: trans('required', trans('email')),
                },
                phone: {
                    required: trans('required', trans('telephone_cell_number')),
                },
                skype_id: {
                    maxlength: trans('maxlength', trans('skype_id'), "250")
                },
                comment:{
                     maxlength: trans('maxlength', trans('comments'), "1000")
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
$(function() {
    setJQueryValidationDefaults();
    ValidateUser.init();
    $("#close_link").click(function() {
        $("#message_box").fadeOut(1000);
        $("#message_box").removeClass('ok');
    });
});

    function changeLCPDefaultLanguage(language_id) {
            var base_url = $("#base_url_id").val();
            $.ajax({
           
                type: 'GET',
                url: base_url + "lcp/change_lcp_default_lang/" + language_id + "/ajax",
             //  url: base_url + "lcp/change_lcp_default_lang",
             //  data:  JSON.stringify({language: language_id,inf_token: $('input[name="inf_token"]').val()}),
//                data: { 
//                    language: language_id,
//                    inf_token: $('input[name="inf_token"]').val()
//                },
               
                contentType: 'application/json',
                 dataType: 'JSON',
                
                success: function(data) {
                },
                complete: function() {
                  setTimeout(function () {
                            location.reload();
                            }, 500);
                }
            });
    }

/**
 * [trans validation language]
 * @param  {[string]} key     [language key]
 * @param  {[string]} replace [replace string]
 * @return {[string]}         [language]
 */
function trans(key = "", replace = "", replace2 = "") {
    var replace = replace || "";
    var replace2 = replace2 || "";
    if(window.translations) {
        translation = window.translations[key] || key;
        if(translation) {
            translation = translation.replace("*", "").replace("%s", replace).toLowerCase();
            translation = translation.replace("*", "").replace("%s", replace2).toLowerCase();
            return translation;
        }
    }
    return key.toLowerCase();
}

function setJQueryValidationDefaults() {
    jQuery.validator.setDefaults({
        errorPlacement: function(error, element) {
            if ($(element).hasClass('ckeditor')) {
                var element_id = $(element).attr('id');
                error.insertAfter($('#cke_' + element_id));
            } else if (element.parent('.input-group').length) {
                error.insertAfter(element.parent());
            } else {
                error.insertAfter(element);
            }
        }
    });

    /* Rules */
    // greaterThanEqual
    jQuery.validator.addMethod("greaterThanEqual",
        function(value, element, param) {
            var otherElementVal = $(param).val();
            if (!value || !otherElementVal) {
                return true;
            }
            return parseFloat(value) >= parseFloat(otherElementVal);
        }
    );

    // greaterThanNum
    jQuery.validator.addMethod('greaterThanNum', function (value, el, param) {
        return value > param;
    });

    // alpha_numeric
    $.validator.addMethod("alpha_numeric", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z0-9]+$/);
    }, trans('only_alphabets_numerals'));

    $.validator.addMethod("phone_number", function(value, element) {
        return this.optional(element) || value == value.match(/^[0-9+()-\s]+$/);
    }, trans('phone_number'));

    //  alpha_numeric_some_special
    $.validator.addMethod("alpha_numeric_some_special", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z0-9_.-]+$/);
    });

    // Alpha password
    $.validator.addMethod("alpha_password", function(value, element) {
        return this.optional(element) || value == value.match(/^[0-9a-zA-Z\s\r\n@!#\$\^%&*()+=\-\[\]\\\';,\.\/\{\}\|\":<>\?\_\`\~]+$/);
    }, trans('alpha_password'));

    $.validator.addMethod("pan_format", function(value, element) {
        return this.optional(element) || value == value.match(/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/);
    }, trans('please_follow_the_pan_format'));

    $.validator.addMethod("alpha_space", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z ]+$/);
    }, trans('only_characters_allowed'));

    $.validator.addMethod("alpha_dash", function(value, element) {
        return this.optional(element) || /^[a-z0-9A-Z$@$!%*#?& _~\-!@#\$%\^&\*\(\)?,.:|\\+\\[\]{}''"";`~=]*$/i.test(value);
    }, trans('special_characters_not_allowed'));

    $.validator.addMethod("alpha_city", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z0-9\s\.\,\-]+$/);
    }, trans('alpha_space', trans('city')));

    $.validator.addMethod("alpha_address", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z0-9\s\.\,]+$/);
    }, trans('alpha_space', trans('address')));

    $.validator.addMethod("cardExpiry", function() {
        if ($("#card_expiry_mm").val() != "" && $("#card_expiry_yyyy").val() != "") {
            return true;
        } else {
            return false;
        }
    }, trans('please_select_month_year'));

    $.validator.addMethod("valid_value", function() {
        var limit = $('#p_scents p').size();
        for (var i = 0; i <= limit; i++) {
            if ($('#epin' + i).val() != "") {
                return true;
            } else {
                return false;
            }
        }
    }, trans('valid_transaction_password'));

    $.validator.addMethod("alpha", function(value, element) {
        return this.optional(element) || value == value.match(/^[a-zA-Z]+$/);
    }, trans('only_characters_allowed'));

    /* Messages */
    //Required
    $.validator.messages.required = function(param, input) {
        var label = getLabelName(input);
        if($(input).is('select')) {
            return trans('required_select', trans(label))
        }
        return trans('required', trans(label));
    }
    $.validator.messages.minlength = function(param, input) {
        let label = getLabelName(input);
        return trans('minlength', trans(label), param);
    }
    $.validator.messages.maxlength = function(param, input) {
        let label = getLabelName(input);
        return trans('maxlength', trans(label), param);
    }
    $.validator.messages.alpha_numeric = function(param, input) {
        return trans('only_alphabets_numerals');
    }
    $.validator.messages.alpha_numeric_some_special = function(param, input) {
         let label = getLabelName(input);
        return trans('alpha_numeric_some_special', trans(label));
    }
    $.validator.messages.invalid = function(param, input) {
         let label = getLabelName(input);
        return trans('invalid', trans(label));
    }
    $.validator.messages.email = function() {
        return trans('valid_email');
    }
    $.validator.messages.alpha_city = function() {
        return trans('alpha_space', trans('city'));
    }
    $.validator.messages.digits = function() {
        return trans('digits');
    }
    $.validator.messages.number = function() {
        return trans('digits');
    }
    $.validator.messages.alpha_space = function() {
        return trans('alpha_space_only');
    }

    $.validator.messages.agree = function() {
        return trans('agree');
    }

    $.validator.messages.valid_position = function() {
        return trans('invalid', trans('position'));
    }

    $.validator.messages.valid_sponsor = function() {
        return trans('invalid', trans('sponsor_username'));
    }

    $.validator.messages.user_name_available = function() {
        return trans('username_not_available');
    }

}
/**
 * [getLabel name of input]
 * @param  {[element]} input
 * @return {[string]}
 */
function getLabelName(input) {
    let label = $(input).siblings('label').text();
    if(label == "") {
        label = $(input).attr('name');
    }
    return label
}