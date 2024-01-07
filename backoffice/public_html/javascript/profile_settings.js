var ValidateConfiguration = function () {
    var runValidateConfiguration = function () {
        var searchform = $('#profile_form');
        var errorHandler1 = $('.errorHandler', searchform);

        $('#profile_form').validate({
            errorElement: "span",
            errorClass: 'help-block',
            errorId: 'err_config',
            errorPlacement: function (error, element) {
                if ($(element).parent('.input-group').length === 0) {
                    error.insertAfter(element);
                } else {
                    error.insertAfter($(element).closest('.input-group'));
                }
            },
            ignore: ':hidden',
            rules: {
                age_limit: {
                    required: true,
                    digits: true,
                    min: 1
                },
                prefix: {
                    minlength: 1,
                    required: true
                },
                min_password_length: {
                    required: true,
                    number: true,
                    min: 6,
                    max: 50
                }
            },
            messages: {
                age_limit: {
                    required: trans('required', trans('age')),
                    digits: trans('non_zero'),
                    min: trans('non_zero')
                },
                min_password_length: {
                    required: trans("required", trans('min_password_length')),
                    number: trans('should_be_number'),
                    min: trans('between_6_and_50'),
                    max: trans('between_6_and_50')
                },
            },
            invalidHandler: function () {
                errorHandler1.show();
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
    };

    return {
        init: function () {
            runValidateConfiguration();
        }
    };
}();

$(document).ready(function() {
    ValidateConfiguration.init();
    // Username 
    $('select[name="user_name_type"]').on('change', function () {
        var user_name_type = this.value;
        if (user_name_type == 'static') {
            $('#length_div').hide();
            //$('#prefix_status_div').hide();
            $('#prefix_div').hide();
            $('#prefix_checkbox').hide();
        }
        if (user_name_type == 'dynamic') {
            $('#length_div').show();
            $('#prefix_status_div').show();
            $('#prefix_div').show();
            $('#prefix_checkbox').show();
            $('input[name="prefix_status"]').change();
        }
    });
    $('input[name="prefix_status"]').on('change', function () {
        var prefix_status = this.checked;
        if (prefix_status) {
            $('#prefix_div').show();
        } else {
            $('#prefix_div').hide();
        }
    });
    var userNameType = $("#user_name_type").val();
    if(userNameType == 'static'){
       $("#prefix_checkbox").hide(); 
    } 
   
    if ($('#Dynamic').attr('checked')) {
        $("#user_type_div").show();
        $("#user_type_div1").show();
    }
    
    $("#Dynamic").click(function () {
        $("#user_type_div").show();
        $("#user_type_div1").show();

        if ($('#yes').attr('checked')) {

            var prefix_val = $('#user_name_config').html();

            var html;
            html = "<td>Username Prefix:<font color='#ff0000'>*</font></strong></td><td><input type='text' class='form-control' name ='prefix' id ='prefix' value='' maxlength='19' tabindex='8' title='This is the prefix of user name. It should contain 3 to 15 characters.'><span id='errmsg1'></span></td>";
            document.getElementById('prefix_div').innerHTML = html;
            $('#prefix').val(prefix_val);
            $("#prefix_div").show();
        }
    });

    $("#Static").click(function () {
        $("#user_type_div").hide("fast");
        $("#user_type_div1").hide("fast");
        $("#prefix_div").hide("fast");
    });

    if ($('#yes').attr('checked')) {
        var prefix_val = $('#user_name_config').html();
        var html;
        html = "<td>Username Prefix:<font color='#ff0000'>*</font></strong></td><td><input type='text' class='form-control' name ='prefix' id ='prefix' value='' minlength='2' maxlength='5' tabindex='8' title='This is the prefix of user name. It should contain 2 to 5 characters.'><span id='errmsg1'></span></td>";
        document.getElementById('prefix_div').innerHTML = html;
        $('#prefix').val(prefix_val);
        $("#prefix_div").show();
    }

    $("#yes").click(function () {
        var prefix_val = $('#user_name_config').html();
        html = "<td>Username Prefix:<font color='#ff0000'>*</font></strong></td><td><input type='text' class='form-control' name ='prefix' id ='prefix' value='' minlength='2' maxlength='5' tabindex='8' title='This is the prefix of user name. It should contain 2 to 5 characters.'><span id='errmsg1'></span></td>";
        document.getElementById('prefix_div').innerHTML = html;
        $('#prefix').val(prefix_val);
        $("#prefix_div").show();

    });
    $("#no").click(function () {
        $("#prefix_div").hide("fast");

    });
    // Username
});
// Profile
$("#ex2").bootstrapSlider();
$('input[name="age_limit_status"]').on('change', function () {
    if (this.checked) {
        $('input[name="age_limit"]').closest('.form-group').show();
    } else {
        $('input[name="age_limit"]').closest('.form-group').hide();
    }
});

$('input[name="age_limit_status"]').change();

// Password Policy
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