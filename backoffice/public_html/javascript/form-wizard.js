var FormWizard = (function() {
    $.validator.addMethod("valid_age", function(value, element) {
        var dob = value
        var d = dob.split('-');
        var year = parseInt(d[0]);
        var month = parseInt(d[1]);
        var day = parseInt(d[2]);
        var age_limit = parseInt($('#age_limit').val());
        if (age_limit == 0) {
            return true;
        }

        // age validation based on year, month and day
        // var res = isAgeMoreThanOrEqual(day, month, year, age_limit);
        // age validation based on year
        var res = isAgeMoreThanOrEqualYear(year, age_limit);
        return res;
    });

    var regValidate = function() {
        var valid_password_msg = trans('minlength', trans('password'), 6);
        if ($("#passwordPolicyJson").length) {
            var passwordPolicyObj = JSON.parse($("#passwordPolicyJson").val());
            valid_password_msg = trans('minlength', trans('password'), passwordPolicyObj.min_length);
            if (passwordPolicyObj.disableHelper != 1) {
                valid_password_msg = trans('field_no_min_requirement', trans('password'));
            }
        }

        var usernameMin = 0;
        var usernameMax = 0;
        var path_root = $('#path_root').val();
        var url = path_root + "register/getUsernameRange";
        $.ajax({
                'url': url,
                'type': "GET",
                'data': {
                },
                'dataType': 'text',
                'async': false,
                success: function(data) {

                    data = JSON.parse(data);
                    usernameMin = data.min;
                    usernameMax = data.max;
                    
                }
         });
         
        var pro = $('#p_type').val();
        if(pro == 'founder_pack'){
        $('#msform').validate({
            errorElement: "span",
            errorId: "register",
            errorClass: 'help-block',
            errorPlacement: function(error, element) {
                element.siblings('.help-block-2').remove();
                if (element.prop("type") == "checkbox") {
                    error.insertAfter(element.parent("label"));
                } else {
                    if ($(element).next('span').hasClass('combodate')) {
                        error.insertAfter($(element).parent('.form-date-group'));
                    } else if ($(element).attr('name') == 'day' || $(element).attr('name') == 'month' || $(element).attr('name') == 'year') {
                        error.insertAfter($(element).closest('.form-date-group'));
                    } else if ($(element).attr('name') == 'email' || $(element).attr('name') == 'otp') {
                        error.insertAfter($(element).closest('.input-group'));
                    }else {
                        error.insertAfter(element);
                    }
                }
            },
            ignore: ':hidden',
            rules: {
                placement_user_name: {
                    required: true
                },
                sponsor_user_name: {
                    required: true,
                    maxlength: 50,
                    valid_sponsor:true
                },
                position: {
                    required: true,
                    valid_position: true,
                },
                product_id: {
                    required: true
                },
                user_name_entry: {
                    required: true,
                    alpha_numeric_some_special: true,
                    minlength: usernameMin,
                    maxlength: usernameMax,
                    user_name_available: true,
                },
                pswd: {
                    required: true,
                    valid_password: true,
                    maxlength: 50
                },
                cpswd: {
                    required: true,
                    equalTo: "#pswd"
                },
                first_name: {
                    maxlength: 250,
                },
                last_name: {
                    maxlength: 250
                },
                gender: {
                },
                date_of_birth: {
                    required: true,
                    valid_age: true
                },
                adress_line1: {
                    maxlength: 1000,
                },
                adress_line2: {
                    maxlength: 1000,
                },
                otp: {
                    otp_check:true,
                },
                pin: {
                    maxlength: 50,
                },
                country: {
                },
                state: {
                },
                email: {
                    required: true,
                    maxlength: 250,
                    email: true
                },
                city: {
                    maxlength: 250
                },
                land_line: {
                    maxlength: 50,
                    phone_number: true,
                },
                mobile: {
                    required: true,
                    maxlength: 50,
                    phone_number: true,
                },
                agree: {
                    required: true
                },
            },
            messages: {
                pswd: {
                    required: trans('required', trans('password')),
                    valid_password: valid_password_msg,
                    maxlength: trans('maxlength', trans('pswd'), "50")
                },
                cpswd: {
                    equalTo: trans('password_mismatch'),
                },
                date_of_birth: {
                    required: trans('required_select', trans('date_of_birth')),
                    valid_age: trans('valid_age', trans($('#age_limit').val())),
                },
                agree: trans('agree'),
                otp:{
                    otp_check :" OTP Verification Failed",
                },
            },
            onkeyup: false,
            onfocusout: function(element) {
                $(element).valid();
            },
            highlight: function(element) {
                if ($(element).closest('.combodate').length) {
                    $(element).closest('div').addClass('has-error');
                } else {
                    $(element).closest('.form-group').addClass('has-error');
                    $(element).next('span.keyup_error').remove();
                }
            },
            unhighlight: function(element) {
                if ($(element).closest('.combodate').length) {
                    $(element).closest('div').removeClass('has-error');
                } else {
                    $(element).closest('.form-group').removeClass('has-error');
                }
            },
            success: function(label) {
                if ($(label).attr('for') == 'date_of_birth') {
                    $(label).closest('.has-error').removeClass('has-error');
                }
            }

        });
        
        }else{
        
        $('#msform').validate({
            errorElement: "span",
            errorId: "register",
            errorClass: 'help-block',
            errorPlacement: function(error, element) {
                element.siblings('.help-block-2').remove();
                if (element.prop("type") == "checkbox") {
                    error.insertAfter(element.parent("label"));
                } else {
                    if ($(element).next('span').hasClass('combodate')) {
                        error.insertAfter($(element).parent('.form-date-group'));
                    } else if ($(element).attr('name') == 'day' || $(element).attr('name') == 'month' || $(element).attr('name') == 'year') {
                        error.insertAfter($(element).closest('.form-date-group'));
                    } else if ($(element).attr('name') == 'email' || $(element).attr('name') == 'otp') {
                        error.insertAfter($(element).closest('.input-group'));
                    } else {
                        error.insertAfter(element);
                    }
                }
            },
            ignore: ':hidden',
            rules: {
                placement_user_name: {
                    required: true
                },
                sponsor_user_name: {
                    required: true,
                    maxlength: 50,
                    valid_sponsor:true
                },
                position: {
                    required: true,
                    valid_position: true,
                },
                product_id: {
                    required: true
                },
                user_name_entry: {
                    required: true,
                    alpha_numeric_some_special: true,
                    minlength: usernameMin,
                    maxlength: usernameMax,
                    user_name_available: true,
                },
                user_name_child1: {
                    required: true,
                    alpha_numeric_some_special: true,
                    minlength: usernameMin,
                    maxlength: usernameMax,
                    user_name_available1: true,
                },
                user_name_child2: {
                    required: true,
                    alpha_numeric_some_special: true,
                    minlength: usernameMin,
                    maxlength: usernameMax,
                    user_name_available2: true,
                },
                pswd: {
                    required: true,
                    valid_password: true,
                    maxlength: 50
                },
                cpswd: {
                    required: true,
                    equalTo: "#pswd"
                },
                pswd_child1: {
                    required: true,
                    valid_password: true,
                    maxlength: 50
                },
                cpswd_child1: {
                    required: true,
                    equalTo: "#pswd_child1"
                },
                pswd_child2: {
                    required: true,
                    valid_password: true,
                    maxlength: 50
                },
                cpswd_child2: {
                    required: true,
                    equalTo: "#pswd_child2"
                },
                first_name: {
                    maxlength: 250,
                },
                last_name: {
                    maxlength: 250
                },
                gender: {
                },
                date_of_birth: {
                    required: true,
                    valid_age: true
                },
                adress_line1: {
                    maxlength: 1000,
                },
                adress_line2: {
                    maxlength: 1000,
                },
                pin: {
                    maxlength: 50,
                },
                country: {
                },
                state: {
                },
                email: {
                    required: true,
                    maxlength: 250,
                    email: true
                },
                city: {
                    maxlength: 250
                },
                land_line: {
                    maxlength: 50,
                    phone_number: true,
                },
                mobile: {
                    required: true,
                    maxlength: 50,
                    phone_number: true,
                },
                otp: {
                    otp_check:true,
                },
                agree: {
                    required: true
                },
            },
            messages: {
                user_name_child1: {
                    user_name_available1: trans('username_unavailable'),
                },
                user_name_child2: {
                    user_name_available2: trans('username_unavailable'),
                },
                pswd: {
                    required: trans('required', trans('password')),
                    valid_password: valid_password_msg,
                    maxlength: trans('maxlength', trans('pswd'), "50")
                },
                cpswd: {
                    equalTo: trans('password_mismatch'),
                },
                date_of_birth: {
                    required: trans('required_select', trans('date_of_birth')),
                    valid_age: trans('valid_age', trans($('#age_limit').val())),
                },
                agree: trans('agree'),
                otp:{
                    otp_check :" OTP Verification Failed",
                },
            },
            onkeyup: false,
            onfocusout: function(element) {
                $(element).valid();
            },
            highlight: function(element) {
                if ($(element).closest('.combodate').length) {
                    $(element).closest('div').addClass('has-error');
                } else {
                    $(element).closest('.form-group').addClass('has-error');
                    $(element).next('span.keyup_error').remove();
                }
            },
            unhighlight: function(element) {
                if ($(element).closest('.combodate').length) {
                    $(element).closest('div').removeClass('has-error');
                } else {
                    $(element).closest('.form-group').removeClass('has-error');
                }
            },
            success: function(label) {
                if ($(label).attr('for') == 'date_of_birth') {
                    $(label).closest('.has-error').removeClass('has-error');
                }
            }

        });
        
        }
        
        $.validator.addMethod('valid_sponsor', function() {
            var path_root = $('#path_root').val();
            var ref_user_availability = path_root + "register/validate_username";
            var flag = false;
           $.ajax({
                'url': ref_user_availability,
                'type': "POST",
                'data': {
                    username: $('#sponsor_user_name').val()
                },
                'dataType': 'text',
                'async': false,
                success: function(data) {
                    if (data == 'no') { //if username not avaiable
                        flag =  false;
                        hideSpnsorFullName();
                    } else {
                        flag = true;
                        showSponsorFullName($('#sponsor_user_name').val());
                        if (!$('#position').is(':hidden')) {
                            $('#position').valid();
                        }
                        $('#product_id').trigger('change');
                    }
                }
            });
           return flag;
        });
        $.validator.addMethod('valid_position', function() {
            var path_root = $('#path_root').val();
            var leg_availability = path_root + "register/check_leg_availability";
            $.ajax({
                'url': leg_availability,
                'type': "POST",
                'data': {
                    sponsor_leg: $('#position').val(),
                    sponsor_user_name: $('#sponsor_user_name').val(),
                    placement_user_name: $('#reg_from_tree').val() == 1 ? $('#placement_user_name').val() :  ''
                },
                'dataType': 'text',
                'async': false,
                success: function(data) {
                    if (data == 'no') { //if username not avaiable
                        flag =  false;
                    } else {
                        flag = true;
                    }
                }
            });
           return flag;
        });
        
        $.validator.addMethod('user_name_available', function() {
            var user_name = $('#user_name_entry').val();
            var user_name_child1 = $('#user_name_child1').val();
            var user_name_child2 = $('#user_name_child2').val();
            var path_root = $('#path_root').val();
            var user_name_availability = path_root + "register/ajax_is_username_available"
            $.ajax({
                'url': user_name_availability,
                'type': "POST",
                'data': {
                    user_name: $('#user_name_entry').val(),
                    old_user_name: $('#user_name_entry').data('value')
                },
                'dataType': 'text',
                'async': false,
                success: function(data) {
                    if (data == 'no') { //if username not avaiable
                        flag =  false;
                    } else {
                        $('#user_name_entry').data('value', $('#user_name_entry').val());
                        flag = true;
                    }
                }
            });
            
        if(flag){
            
        if(user_name == user_name_child1 || user_name == user_name_child2){
            return false;
        }else{
            return true;
        }
            
        }else{
            return flag;
        }
        
        });
        
        
        
        $.validator.addMethod('user_name_available1', function() {
            var user_name = $('#user_name_entry').val();
            var user_name_child1 = $('#user_name_child1').val();
            var user_name_child2 = $('#user_name_child2').val();
            var path_root = $('#path_root').val();
            var user_name_availability = path_root + "register/ajax_is_username_available"
            $.ajax({
                'url': user_name_availability,
                'type': "POST",
                'data': {
                    user_name: $('#user_name_child1').val(),
                    old_user_name: $('#user_name_child1').data('value')
                },
                'dataType': 'text',
                'async': false,
                success: function(data) {
                    if (data == 'no') { //if username not avaiable
                        flag =  false;
                    } else {
                        $('#user_name_child1').data('value', $('#user_name_child1').val());
                        flag = true;
                    }
                }
            });
            
        if(flag){
            
        if(user_name == user_name_child1 || user_name == user_name_child2 || user_name_child1 == user_name_child2){
            return false;
        }else{
            return true;
        }
            
        }else{
            return flag;
        }
        
        });
        
        
        
        $.validator.addMethod('user_name_available2', function() {
            var user_name = $('#user_name_entry').val();
            var user_name_child1 = $('#user_name_child1').val();
            var user_name_child2 = $('#user_name_child2').val();
            var path_root = $('#path_root').val();
            var user_name_availability = path_root + "register/ajax_is_username_available"
            $.ajax({
                'url': user_name_availability,
                'type': "POST",
                'data': {
                    user_name: $('#user_name_child2').val(),
                    old_user_name: $('#user_name_child2').data('value')
                },
                'dataType': 'text',
                'async': false,
                success: function(data) {
                    if (data == 'no') { //if username not avaiable
                        flag =  false;
                    } else {
                        $('#user_name_child2').data('value', $('#user_name_child2').val());
                        flag = true;
                    }
                }
            });
            
        if(flag){
            
        if(user_name == user_name_child1 || user_name == user_name_child2 || user_name_child1 == user_name_child2){
            return false;
        }else{
            return true;
        }
            
        }else{
            return flag;
        }
        
        });
        $.validator.addMethod('otp_check', function() {
            var path_root = $('#path_root').val();
            var email_availability = path_root + "register/check_otp"
            $.ajax({
                'url': email_availability,
                'type': "POST",
                'data': {
                    otp: $('#otp').val(),
                    email: $('#email').val()
                },
                'dataType': 'text',
                'async': false,
                success: function(data) {
                    if (data == 'no') { //if username not avaiable
                        flag =  false;
                    } else {
                        flag = true;
                    }
                }
            });
           return flag;
        });
        
        
        
    };
    regValidate();
}());

function isAgeMoreThanOrEqual(day, month, year, age) {
    return new Date(year + age, month - 1, day) <= new Date();
}

function isAgeMoreThanOrEqualYear(year, age_limit) {
    var d = new Date();
    var current_year = d.getFullYear();
    return (current_year - year) >= age_limit;
}

$(function() {
    var today = new Date();
    var dd = String(today.getDate()).padStart(2, '0');
    var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
    var yyyy = today.getFullYear();
    today = yyyy + '-' + mm + '-' + dd;
    var datepicker_options = {
        format: 'Y-m-d',
        direction: [false, today],
        readonly_element: true,
        default_position: 'below',
        view: 'years',
        icon_position: 'left',
        offset: [-28, 28],
        onSelect: function() {
            $(this).change();
        }
    };
    $('.date-picker-dob').Zebra_DatePicker(datepicker_options);
    $('#form').on('keyup keypress', function(e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    if($("#default_country").val() !='')
    {
        if($("#state").length != 0) {
            getAllStates($("#default_country").val());
        }
    }
});

function showSponsorFullName(referral_name) {
    var html;
    var msg = trans('sponsor_fullname');
    var get_referral_name_url = $('#path_root').val() + "register/get_sponsor_full_name";
    $.post(get_referral_name_url, { sponsor_user_name: referral_name, inf_token: $('input[name="inf_token"]').val()}, function(data) {
        data = trim(data);
        html = "<div class='form-group'>  <label class='control-label' for='sponsor_full_name'>" + msg + "</label> <input type='text' name='sponsor_full_name' id='sponsor_full_name' autocomplete='Off' value='" + data + "' readonly='true' class='form-control'/><span id='errorsponsor'></span></div>";
        $('#referal_div').html(html);
        $('#referal_div').show();
    });
}
function hideSpnsorFullName() {
    $('#referal_div').html('');
}
showSponsorFullName($('#sponsor_user_name').val());

$('#product_id').on('change', function() {

    var product_value= $('#product_id').val();
    $("#pro_id").val(product_value);
    $('#board_downline').empty()
    $('#board_div').hide();
    
    // if(product_value == 2){
    //     $("#log1").show();
    //     $('#down_user_div').show();
    // }else{
    //     $('#down_user_div').hide();
    //     $("#log1").hide();
    // }
    
    var path_root = $('#path_root').val();
    url = path_root + 'register/get_product_amount';
     $.ajax({
        'url': url,
        'type': "POST",
        'data': {
            product_id: $('#product_id').val(),
            sponsor_user_name: $('#sponsor_user_name').val()
        },
        'dataType': 'json',
        success: function(data) {
            var product_amount = data.product_amount * 1;
            var reg_fee= $('#total_reg_amount').val() * 1;
            var registration_fee = product_amount + reg_fee;
            $("#total_product_amount").text(format_currency(registration_fee));
            var board_status = data.board_status;
            var prod_type = data.pck_type;
            $("#p_type").val(prod_type);
            
            if(prod_type == 'founder_pack'){
            $("#log1").show();
            $('#down_user_div').show();
            }else{
            $('#down_user_div').hide();
            $("#log1").hide();
            }
            if(board_status == 'yes'){
                $('#board_downline').append(data.downlines); 
                $('#board_div').show();
            }else{
                $('#board_div').hide();
            }
            
        }
    });



});
