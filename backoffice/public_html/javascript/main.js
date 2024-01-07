// global variables
var base_url = $("#base_url").val();

BASE_URL = base_url;
var exclsub = {
    'admin/my_referal': 'admin/user_account',
    'admin/configuration/my_referal': 'admin/user_account',
    'admin/view_leg_count': 'admin/user_account',
    'admin/configuration/view_leg_count': 'admin/user_account',
    'admin/my_income': 'admin/user_account',
    'admin/payout/my_income': 'admin/user_account',
    'admin/business_volume': 'admin/user_account',
    'admin/profile/business_volume': 'admin/user_account',
    'admin/add_new_epin': 'admin/epin_management',
    'admin/epin/add_new_epin': 'admin/epin_management',
    'admin/edit_profile': 'admin/profile_view',
    'admin/profile/edit_profile': 'admin/profile_view',
    'product_details': 'repurchase_product',
    'repurchase/product_details': 'repurchase_product',
    'checkout_product': 'repurchase_product',
    'repurchase/checkout_product': 'repurchase_product',
    'admin/add_membership_package': 'admin/membership_package',
    'admin/product/add_membership_package': 'admin/membership_package',
    'admin/edit_membership_package': 'admin/membership_package',
    'admin/product/edit_membership_package': 'admin/membership_package',
    'admin/add_repurchase_package': 'admin/repurchase_package',
    'admin/product/add_repurchase_package': 'admin/repurchase_package',
    'admin/edit_repurchase_package': 'admin/repurchase_package',
    'admin/product/edit_repurchase_package': 'admin/repurchase_package',
    'admin/add_repurchase_category': 'admin/repurchase_category',
    'admin/product/add_repurchase_category': 'admin/repurchase_category',
    'admin/edit_repurchase_category': 'admin/repurchase_category',
    'admin/product/edit_repurchase_category': 'admin/repurchase_category',
    'admin/add_text_invite': 'admin/text_invite_configuration',
    'admin/member/add_text_invite': 'admin/text_invite_configuration',
    'admin/edit_invite_text': 'admin/text_invite_configuration',
    'admin/member/edit_invite_text': 'admin/text_invite_configuration',
    'admin/add_banner_invite': 'admin/invite_banner_config',
    'admin/member/add_banner_invite': 'admin/invite_banner_config',
    'admin/edit_invite_wallpost': 'admin/invite_wallpost_config',
    'admin/member/edit_invite_wallpost': 'admin/invite_wallpost_config',
    'admin/add_email_invite': 'admin/invite_wallpost_config',
    'admin/member/add_email_invite': 'admin/invite_wallpost_config',
    'admin/add_facebook_invite': 'admin/invite_wallpost_config',
    'admin/member/add_facebook_invite': 'admin/invite_wallpost_config',
    'admin/add_social_invite': 'admin/invite_wallpost_config',
    'admin/member/add_social_invite': 'admin/invite_wallpost_config',
    'admin/auto_responder_settings': 'admin/auto_responder_details',
    'admin/auto_responder/auto_responder_settings': 'admin/auto_responder_details',
    'admin/upload_new_material': 'admin/upload_materials',
    'admin/news/upload_new_material': 'admin/upload_materials',
    'admin/add_new_news': 'admin/add_news',
    'admin/news/add_new_news': 'admin/add_news',
    'admin/dashboard_config': 'admin/set_employee_permission',
    'admin/employee/dashboard_config': 'admin/set_employee_permission',
    'admin/compose_mail': 'admin/mail_management',
    'admin/mail/compose_mail': 'admin/mail_management',
    'admin/mail_sent': 'admin/mail_management',
    'admin/mail/mail_sent': 'admin/mail_management',
    'admin/compensation_settings': 'admin/general_setting',
    'admin/configuration/compensation_settings': 'admin/general_setting',
    'admin/level_commissions': 'admin/general_setting',
    'admin/configuration/level_commissions': 'admin/general_setting',
    'admin/binary_bonus_config': 'admin/general_setting',
    'admin/configuration/binary_bonus': 'admin/general_setting',
    'admin/rank_configuration': 'admin/general_setting',
    'admin/configuration/rank_configuration': 'admin/general_setting',
    'admin/add_new_rank': 'admin/general_setting',
    'admin/configuration/add_new_rank': 'admin/general_setting',
    'admin/referal_commissions': 'admin/general_setting',
    'admin/configuration/referal_commissions': 'admin/general_setting',
    'admin/matching_bonus': 'admin/general_setting',
    'admin/configuration/matching_bonus': 'admin/general_setting',
    'admin/pool_bonus_config': 'admin/general_setting',
    'admin/configuration/pool_bonus': 'admin/general_setting',
    'admin/fast_start_bonus_config': 'admin/general_setting',
    'admin/configuration/fast_start_bonus': 'admin/general_setting',
    'admin/performance_bonus': 'admin/general_setting',
    'admin/configuration/performance_bonus': 'admin/general_setting',
    'admin/sales_commission': 'admin/general_setting',
    'admin/configuration/sales_commission': 'admin/general_setting',
    'admin/plan_settings': 'admin/general_setting',
    'admin/configuration/plan_settings': 'admin/general_setting',
    'admin/board_bonus_config': 'admin/general_setting',
    'admin/configuration/board_bonus': 'admin/general_setting',
    'admin/stairstep_configuration': 'admin/general_setting',
    'admin/configuration/stairstep_configuration': 'admin/general_setting',
    'admin/donation_configuration': 'admin/general_setting',
    'admin/configuration/donation_configuration': 'admin/general_setting',
    'admin/roi_commission': 'admin/general_setting',
    'admin/configuration/roi_commission': 'admin/general_setting',
};

$(function(obj) {
    $.each(obj, function(key, value) {
        if (window.location.pathname.indexOf(key) > -1) {
            $('#aside').find("a[href*='" + value + "']").closest('li').addClass('active');
        }
    });
}(exclsub));

var ValidateSearchMember = function() {
    var error_message = $('#search_member_error').val();
    var error_message2 = $('#search_member_error2').val();
    var initValidator = function() {
        $.validator.addMethod("username_check", function(value, element) {
            var path_root = $('#base_url').val();
            var flag2 = false;
            if (value != "/" && value != ".") {
                $.ajax({
                    'url': path_root + getUserType() + "/profile/validate_username",
                    'type': "POST",
                    'data': {
                        username: value
                    },
                    'dataType': 'text',
                    'async': false,
                    'success': function(data) {
                        if (data == 'no') {
                            flag2 = false;
                        } else if (data == 'yes') {
                            flag2 = true;
                        }
                    },
                    'error': function(error) {},
                });
                return flag2;
            } else {
                return true;
            }
        }, error_message2);
        var searchform = $('#search_member');
        var errorHandler = $('.errorHandler', searchform);
        $(searchform).validate({
            errorElement: 'span',
            errorClass: 'help-block error',
            errorId: 'err_search',
            errorPlacement: function(error, element) {
                error.insertAfter(element);
                //                error.insertAfter($(element).parent('.form-group').next('.form-group'));
            },
            ignore: ':hidden',
            rules: {
                // user_name: {
                //     required: true,
                //     username_check: true
                //}
            },
            messages: {
                user_name: {
                    required: error_message,
                    username_check: error_message2,
                },
            },
            onkeyup: false,
            onfocusout: function(element) {
                $(element).valid();
            },
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
    };

    return {
        init: function() {
            initValidator();
        }
    };
}();

function getUserType() {
    var userType = false;
    $.ajax({
        type: 'GET',
        url: $('#base_url').val() + 'login/get_user_type',
        dataType: 'text',
        async: false,
        success: function(data) {
            userType = (data == '') ? false : data;
        },
        error: function() {
            userType = false;
        }
    });
    return userType;
}
var ValidateForget = function() {
    var error_message = $('#search_member_error').val();
    var error_message2 = $('#search_member_error2').val();
    var initValidator = function() {
        $.validator.addMethod("username_check", function(value, element) {
            var path_root = $('#base_url').val();
            var flag2 = false;
            if (value != "/" && value != ".") {
                $.ajax({
                    'url': path_root + getUserType() + "/ewallet/validate_username",
                    'type': "POST",
                    'data': {
                        username: value
                    },
                    'dataType': 'text',
                    'async': false,
                    'success': function(data) {
                        if (data == 'no') {
                            flag2 = false;
                        } else if (data == 'yes') {
                            flag2 = true;
                        }
                    },
                    'error': function(error) {},
                });
                return flag2;
            } else {
                return true;
            }
        }, error_message2);
    };

    return {
        init: function() {
            initValidator();
        }
    };
}();


$(function() {
    $('ul.nav.nav-sub > li.active').parent('ul').parent('li').addClass('active');

    $("form").attr('autocomplete', 'off');

    $(window).on('shown.bs.modal', function() {
        $('input').attr('autocomplete', 'off');
    });

    setJQueryValidationDefaults();

    setJQueryInputFilter();

    loadDatePicker();
    loadTimePicker();
    loadDateTimePicker();

    loadUserAutoList();
    loadUserAutoListExceptAdmin();
    loadUserDownlineAutoList();
    loadEpinAutoList();
    loadEmployeeAutoList();
    customDateRangeAction();

    $('select#daterange').change();

    $('.select-checkbox-all').click(function () {
        $(this).is(':checked') ? $('.select-checkbox-single').prop('checked', true) : $('.select-checkbox-single').prop('checked', false);
    });

    $(".date-picker").keypress(function(e) {
        if (e.which == 0 || e.which == 8) {
            return;
        }
        var regex = new RegExp("^[0-9\-]+$");
        var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
        if (regex.test(str)) {
            return true;
        } else {
            return false;
        }
    });
    $(document).ready(function() {
        $('.select2').select2();
    });
});

function setJQueryInputFilter() {
    $.fn.inputFilter = function (inputFilter) {
        return this.on("input keydown keyup mousedown mouseup select contextmenu drop", function () {
            if (inputFilter(this.value)) {
                this.oldValue = this.value;
                this.oldSelectionStart = this.selectionStart;
                this.oldSelectionEnd = this.selectionEnd;
            } else if (this.hasOwnProperty("oldValue")) {
                this.value = this.oldValue;
                this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
            }
        });
    };
}

function setJQueryValidationDefaults() {
    jQuery.validator.setDefaults({
        errorPlacement: function(error, element) {
            element.next('.help-block-2').remove();
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

     $.validator.addMethod("valid_time", function(value, element) {
        return this.optional(element) || value == value.match(/^(0?[1-9]|1[012])(:[0-5]\d) [APap][mM]$/);
    }, trans('enter_valid_time'));

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

    // Not Equal to
    $.validator.addMethod("notEqual", function(value, element, param) {
     return this.optional(element) || value != $(param).val();
    }, "This has to be different...");

    // password policy

    if ($("#passwordPolicyJson").length) {
        var passwordPolicyObj = JSON.parse($("#passwordPolicyJson").val());
        if(passwordPolicyObj.disableHelper != 1) {
            // form validator
            $.validator.addMethod("valid_password", function (value, element) {
                var lowerCaseStatus = true;
                if (passwordPolicyObj.lowercase > 0) {
                    var regexString = '[a-z]';
                    for (var i = 0; i < (passwordPolicyObj.lowercase - 1); i++) {
                        regexString = regexString + '.*[a-z]';
                    }
                    var lowercaseRegExp = new RegExp(regexString);
                    lowerCaseStatus = lowercaseRegExp.test(value);
                }

                var upperCaseStatus = true;
                if (passwordPolicyObj.uppercase > 0) {
                    var regexString = '[A-Z]';
                    for (var i = 0; i < (passwordPolicyObj.uppercase - 1); i++) {
                        regexString = regexString + '.*[A-Z]';
                    }
                    var uppercaseRegExp = new RegExp(regexString);
                    upperCaseStatus = uppercaseRegExp.test(value);
                }

                var numberStatus = true;
                if (passwordPolicyObj.number > 0) {
                    var regexString = '[0-9]';
                    for (var i = 0; i < (passwordPolicyObj.number - 1); i++) {
                        regexString = regexString + '.*[0-9]';
                    }
                    var numberRegExp = new RegExp(regexString);
                    numberStatus = numberRegExp.test(value);
                }

                var spCharStatus = true;
                if (passwordPolicyObj.sp_char > 0) {
                    var regexString = '[\\W|_]';
                    for (var i = 0; i < (passwordPolicyObj.sp_char - 1); i++) {
                        regexString = regexString + '.*[\\W|_]';
                    }
                    var spCharRegExp = new RegExp(regexString);
                    spCharStatus = spCharRegExp.test(value);
                }

                var minLengthStatus = (value.length >= passwordPolicyObj.min_length);

                return (lowerCaseStatus && upperCaseStatus && numberStatus && spCharStatus && minLengthStatus);
            });
            // pop over helper
            var checkedView = '<i class="fa fa-check-circle" style="color: green;"></i>';
            var unCheckedView = '<i class="fa fa-times-circle" style="color: #f05050;"></i>';

            var lowerCaseStatus = false;
            if (passwordPolicyObj.lowercase > 0) {
                var regexString = '[a-z]';
                for (var i = 0; i < (passwordPolicyObj.lowercase - 1); i++) {
                    regexString = regexString + '.*[a-z]';
                }
                var lowercaseRegExp = new RegExp(regexString);
                lowerCaseStatus = true;
            }

            var upperCaseStatus = false;
            if (passwordPolicyObj.uppercase > 0) {
                var regexString = '[A-Z]';
                for (var i = 0; i < (passwordPolicyObj.uppercase - 1); i++) {
                    regexString = regexString + '.*[A-Z]';
                }
                var uppercaseRegExp = new RegExp(regexString);
                upperCaseStatus = true;
            }

            var numberStatus = false;
            if (passwordPolicyObj.number > 0) {
                var regexString = '[0-9]';
                for (var i = 0; i < (passwordPolicyObj.number - 1); i++) {
                    regexString = regexString + '.*[0-9]';
                }
                var numberRegExp = new RegExp(regexString);
                numberStatus = true;
            }

            var spCharStatus = false;
            if (passwordPolicyObj.sp_char > 0) {
                var regexString = '[\\W|_]';
                for (var i = 0; i < (passwordPolicyObj.sp_char - 1); i++) {
                    regexString = regexString + '.*[\\W|_]';
                }
                var spCharRegExp = new RegExp(regexString);
                spCharStatus = true;
            }

            $(".act-pswd-popover").popover({
                placement: 'bottom',
                html: true,
                title: '<b>' + trans('your_password_must') + '</b>',
                trigger: "focus",
                content: function () {
                    var value = $(this).val();

                    var lowerCaseCheck = unCheckedView;
                    var upperCaseCheck = unCheckedView;
                    var numberCheck = unCheckedView;
                    var spCharCheck = unCheckedView;
                    var minLengthCheck = unCheckedView;
                    
                    if (lowerCaseStatus && lowercaseRegExp.test(value))
                        lowerCaseCheck = checkedView;
                    if (upperCaseStatus && uppercaseRegExp.test(value))
                        upperCaseCheck = checkedView;
                    if (numberStatus && numberRegExp.test(value))
                        numberCheck = checkedView;
                    if (spCharStatus && spCharRegExp.test(value))
                        spCharCheck = checkedView;
                    if (value.length >= passwordPolicyObj.min_length)
                        minLengthCheck = checkedView;

                    var htmlContent = '<div id="pswdPopOverContent" width="150px">';

                    if (lowerCaseStatus)
                        htmlContent = htmlContent + '<p id="lowerCase">' + lowerCaseCheck + ' ' + trans('atleast_lowercase_letter', passwordPolicyObj.lowercase) +'</p>';

                    if (upperCaseStatus)
                        htmlContent = htmlContent + '<p id="upperCase">' + upperCaseCheck + ' ' + trans('atleast_uppercase_letter', passwordPolicyObj.uppercase) +'</p>';

                    if (numberStatus)
                        htmlContent = htmlContent + '<p id="number">' + numberCheck + ' ' + trans('atleast_number', passwordPolicyObj.number) +'</p>';

                    if (spCharStatus)
                        htmlContent = htmlContent + '<p id="sp_char">' + spCharCheck + ' ' + trans('atleast_sp_char', passwordPolicyObj.sp_char) +'</p>';

                    htmlContent = htmlContent + '</div><p id="minlength">' + minLengthCheck + ' ' + trans('atleast_length', passwordPolicyObj.min_length) +'</p></div>';

                    return htmlContent;
                }
            });
            $(".act-pswd-popover").on("keyup", function (e) {
                var value = $(this).val();

                var lowerCaseCheck = unCheckedView;
                var upperCaseCheck = unCheckedView;
                var numberCheck = unCheckedView;
                var spCharCheck = unCheckedView;
                var minLengthCheck = unCheckedView;

                if (lowerCaseStatus && lowercaseRegExp.test(value))
                    lowerCaseCheck = checkedView;
                if (upperCaseStatus && uppercaseRegExp.test(value))
                    upperCaseCheck = checkedView;
                if (numberStatus && numberRegExp.test(value))
                    numberCheck = checkedView;
                if (spCharStatus && spCharRegExp.test(value))
                    spCharCheck = checkedView;
                if (value.length >= passwordPolicyObj.min_length)
                    minLengthCheck = checkedView;

                if (lowerCaseStatus >= 0)
                    $("#lowerCase").html(lowerCaseCheck + ' ' + trans('atleast_lowercase_letter', passwordPolicyObj.lowercase));
                if (upperCaseStatus >= 0)
                    $("#upperCase").html(upperCaseCheck + ' ' + trans('atleast_uppercase_letter', passwordPolicyObj.uppercase));
                if (numberStatus >= 0)
                    $("#number").html(numberCheck + ' ' + trans('atleast_number', passwordPolicyObj.number));
                if (spCharStatus >= 0)
                    $("#sp_char").html(spCharCheck + ' ' + trans('atleast_sp_char', passwordPolicyObj.sp_char));
                $("#minlength").html(minLengthCheck + ' ' + trans('atleast_length', passwordPolicyObj.min_length));
            });
        } else {
            $.validator.addMethod("valid_password", function (value, element) {
                return (value.length >= passwordPolicyObj.min_length);
            });
        }
        
    } else {
        $.validator.addMethod("valid_password", function (value, element) {
            return (value.length >= 6);
        });
    }
    
    // end::password policy

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
    $.validator.messages.min = function(param) {
        return trans('greater_than', param.toString());
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

function dateValidation() {
    if ($("#week_date1").val() && $("#week_date2").val()) {
        var FromDate = $("#week_date1").val();
        var ToDate = $("#week_date2").val();
        if (ToDate < FromDate) {
            vis = "block";
            document.getElementById("error").style.display = vis;
            return false;
        }
    }

    if ($("#ip_address").val()) {
        value = $("#ip_address").val();

        var split = value.split('.');

        if (split.length != 4) {
            vis = "block";
            document.getElementById("ip_err").style.display = vis;
            return false;
        }

        for (var i = 0; i < split.length; i++) {
            var s = split[i];
            if (s.length == 0 || isNaN(s) || s < 0 || s > 255) {
                vis = "block";
                document.getElementById("ip_err").style.display = vis;
                return false;
            }
        }
    }
}

function print_report() {
    var myPrintContent = document.getElementById('print_area');
    var myPrintWindow = window.open("", "Print Report", 'left=300,top=100,width=700,height=500', '_blank');
    myPrintWindow.document.write(myPrintContent.innerHTML);
    myPrintWindow.document.close();
    myPrintWindow.focus();
    myPrintWindow.print();
    myPrintWindow.close();
    return false;
}

function scrollTop() {
    $("html, body").animate({ scrollTop: 0 }, "slow");
}

function loadDatePicker() {
    var datepicker_options = {
        format: 'Y-m-d',
        readonly_element: true,
        default_position: 'below',
        icon_position: 'left',
        offset: [-28, 28],
        onSelect: function() {
            $(this).change();
        }
    };
    $('.date-picker').Zebra_DatePicker(datepicker_options);
}

function loadTimePicker() {
    var timepicker_options = {
        format: 'H:i A',
        readonly_element: true,
        default_position: 'below',
        icon_position: 'left',
        offset: [-28, 28],
        onSelect: function() {
            $(this).change();
        },
        onOpen: function() {
            $('.Zebra_DatePicker').not('.dp_hidden').css('width', '200px');
            $('.Zebra_DatePicker').not('.dp_hidden').find('.dp_timepicker.dp_body').css('height', '150px');
        }
    };
    $('.time-picker').Zebra_DatePicker(timepicker_options);
}

function loadDateTimePicker() {
    var datetimepicker_options = {
        format: 'Y-m-d H:i',
        readonly_element: true,
        default_position: 'below',
        icon_position: 'left',
        offset: [-28, 28],
        onSelect: function() {
            $(this).change();
        }
    };
    $('.datetime-picker').Zebra_DatePicker(datetimepicker_options);
}

function loadUserAutoList() {
    $('.user_autolist').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'admin/home/ajax_users_autolist',
                        data: { keyword: keyword },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });

    $('.agent_autolist').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'admin/home/ajax_agent_autolist',
                        data: { keyword: keyword },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });
    $('.agent_autolist_new').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'admin/home/ajax_agent_autolist_new',
                        data: { keyword: keyword },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });
    // console.log($('#country').val());
    // $('.countrywise_user_autolist').autocomplete({
    //     minLength: 1,
    //     appendMethod: 'replace',
    //     highlight: false,
    //     showHint: false,
    //     visibleLimit: 10,
    //     filter: function(items, query, source) {
    //         var results = [],
    //             value = '';
    //         for (var i in items) {
    //             value = items[i][this.valueKey];
    //             results.push(items[i]);
    //         }
    //         return results;
    //     },
    //     source: [
    //         function(q, add) {
    //             if (q == '/' || q == '.') {
    //                 var keyword = null;
    //             } else {
    //                 var keyword = q;
    //             }
    //             if (q == '' || q == null) {
    //                 add([]);
    //             } else {
    //                 $.ajax({
    //                     method: "POST",
    //                     url: base_url + 'admin/home/ajax_countrywise_user_autolist',
    //                     data: { keyword: keyword, country : $('#country').val() },
    //                     dataType: 'json',
    //                     success: function(data) {
    //                         add(data);
    //                     }
    //                 });
    //             }
    //         }
    //     ]
    // });
}

function loadUserAutoListExceptAdmin() {
    $('.autolist_except_admin').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'admin/home/ajax_except_admin_autolist',
                        data: { keyword: keyword },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });
}

function loadUserDownlineAutoList() {
    $('.user_downline_autolist').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'user/home/ajax_user_downline_autolist',
                        data: { keyword: keyword },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });
}

function loadEpinAutoList() {
    $('.epin_autolist').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'admin/epin/ajax_epin_autolist',
                        data: { keyword: keyword },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });
}

function loadEmployeeAutoList() {
    $('.employee_autolist').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'admin/employee/ajax_employee_autolist',
                        data: { keyword: keyword },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });
}

function changeDefaultLanguage(language_id) {
    var user_type = getUserType();
    if (user_type == 'admin' || user_type == 'user' || user_type == 'Unapproved') {
        $.ajax({
            url: base_url + getUserType() + '/home/change_default_language',
            data: { language: language_id },
            type: 'post',
            beforeSend: function() {

            },
            success: function(data) {
                location.replace(location.href);
                window.location.reload();
            },
            error: function() {
            },
            complete: function() {
                $('#update_language_info').attr('disabled', false);
            }
        });

    }
}

function confirmAction(message) {
    return confirm($('#' + message).text());
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
        if(translation.includes('*')) {
            translation = translation.replace("*", "").toLowerCase();
        }
        if(translation) {
            translation = translation.replace("*", "").replace("%s", replace);
            translation = translation.replace("*", "").replace("%s", replace2);
            return translation;
        }
    }
    return key;
}

/**
 * [capitalizeFirstLetter capitalise first letter in a string]
 * @param  {[type]} string [mixed string]
 * @return {[type]} string [cpapitalised string]
 */
function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

/**
 * [getFieldLang description]
 * @param  {[html]} input [input box]
 * @return {[string]}    [lang key]
 */
function getFieldLang(input) {
    if($(input).parent().hasClass('input-group')) {
        var lang = $(input).parent('.input-group').prevAll('label.control-label').text();
    }else{
        var lang = $(input).parents('.form-group').find('label.control-label').text();
    }

    if ($(input).is('[data-lang]')) {
        lang = $(input).data('lang');
    }
    lang = lang.replace(/\*/g, "");
    return lang.toLowerCase();
}

/**
 * [autocomplete_off description]
 * @param  {String} field_name [classname or id name]
 * @return {[void]} [nothing]
 */
function autocomplete_off(field_name = ".autocomplete-off") {
    $(field_name).attr('readonly', true)
    setTimeout(function() {
        $(field_name).attr('readonly', false);
    }, 1000);
}

function customDateRangeAction() {
    $('select#daterange').on('change', function () {
        var date_inputs = $(this).closest('form').find('.custom-date');
        var date_inputs_parent = $(date_inputs).closest('.form-group').parent('div');
        if (this.value == 'custom') {
            $(date_inputs).attr('disabled', false);
            $(date_inputs_parent).show();
        }
        else {
            $(date_inputs).attr('disabled', true);
            $(date_inputs_parent).hide();
        }
    });
}

/**
 * @param  {Number amount}
 * @return {[string currency formatted amount]}
 */
function format_currency(amount = 0) {
    var default_currency_value = $('#DEFAULT_CURRENCY_VALUE').val();
    var default_symbol_left = $('#DEFAULT_SYMBOL_LEFT').val();
    var default_symbol_right = $('#DEFAULT_SYMBOL_RIGHT').val();
    var default_precision = $('#DEFAULT_PRECISION').val();

    return `${default_symbol_left} ${parseFloat(amount * default_currency_value).toFixed(default_precision)} ${default_symbol_right}`;
}

function format_currency(amount = 0) {
    var default_currency_value = $('#DEFAULT_CURRENCY_VALUE').val();
    var default_symbol_left = $('#DEFAULT_SYMBOL_LEFT').val();
    var default_symbol_right = $('#DEFAULT_SYMBOL_RIGHT').val();
    var default_precision = $('#DEFAULT_PRECISION').val();

    return `${default_symbol_left} ${parseFloat(amount * default_currency_value).toFixed(default_precision)} ${default_symbol_right}`;
}
function thousands_currency_format(value = 0) {
    console.log(value);
    var default_currency_value = $('#DEFAULT_CURRENCY_VALUE').val();
    var default_symbol_left = $('#DEFAULT_SYMBOL_LEFT').val();
    var default_symbol_right = $('#DEFAULT_SYMBOL_RIGHT').val();
    var default_precision = $('#DEFAULT_PRECISION').val();
    var newValue = value* default_currency_value;
    if (value >= 1000) {
        var suffixes = ["", "k", "m", "b","t"];
        var suffixNum = Math.floor( (""+value).length/3 );
        var shortValue = '';
        for (var precision = 2; precision >= 1; precision--) {
            shortValue = parseFloat( (suffixNum != 0 ? (value / Math.pow(1000,suffixNum) ) : value).toPrecision(precision));
            var dotLessShortValue = (shortValue + '').replace(/[^a-zA-Z 0-9]+/g,'');
            if (dotLessShortValue.length <= 2) { break; }
        }
        if (shortValue % 1 != 0)  shortValue = shortValue.toFixed(default_precision);
        newValue = default_symbol_left+shortValue+suffixes[suffixNum];
    }
    return newValue;
}

(function ($) {

    $.fn.widthChanged = function (handleFunction) {
        var element = this;
        var lastWidth = element.width();
        // var lastHeight = element.height();

        setInterval(function () {
            if (lastWidth === element.width()/*  && lastHeight === element.height() */)
                return;
            if (typeof (handleFunction) == 'function') {
                handleFunction({ width: lastWidth/* , height: lastHeight */ },
                    { width: element.width()/* , height: element.height() */ });
                lastWidth = element.width();
                // lastHeight = element.height();
            }
        }, 100);


        return element;
    };

}(jQuery));

function validation() {
    if($("#daterange").val() == 'custom') {
        var FromDate = $("#from_date").val();
        var ToDate = $("#to_date").val();
        if (!ToDate && !FromDate) {
            alert("Please enter date limit");
            return false;
        }
        if (ToDate && FromDate && (ToDate <= FromDate)) {
            alert("From date greater than to date");
            return false;
        }
    }
}
