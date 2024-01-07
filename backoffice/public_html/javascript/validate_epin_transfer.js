
var ValidateUser = function() {

    // function to initiate Validation Sample 1
    var msg = $("#error_msg").html();
    var msg1 = $("#error_msg1").html();
    var msg2 = $("#error_msg2").html();
    var msg3 = $("#error_msg3").html();
     
    var runValidateUser = function() {

        var searchform = $('#epin_transfer_form');
        var errorHandler1 = $('.errorHandler', searchform);

        $('#epin_transfer_form').validate({
            errorElement: "span", // contain the error msg in a span tag
            errorClass: 'help-block',
            errorPlacement: function(error, element) { // render error placement for each input type

                error.insertAfter(element);
                error.insertAfter($(element).closest('.info_block'));
                // for other inputs, just perform default behavior
            },
            ignore: ':hidden',
            rules: {
                user_name: {
                    
                    required: true,
                    valid_user: true
                },
                from_user_name: {
                    required: true,
                    valid_user: true
                },
                epin: {
                    minlength: 1,
                    required: true,
                    check_dfault_option: true

                }

            },
            messages: {
                from_user_name: {
                    required:trans('required',trans('from_user_name')),
                    valid_user: trans('invalid_user')
                },
                user_name: {
                    required: trans('required',trans('to_user_name')),
                    valid_user: trans('invalid_user')
                },
                epin: trans('required_select',trans('epin'))

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


        jQuery.validator.addMethod('check_dfault_option', function(ToDate) {
            var option = $("#epin").val();

            if ($("#epin").val() == 'default') {
                return false;
            } else
                return true;
        }, "");

        $.validator.addMethod('valid_user', function(param, input) {
            var path_root = $('#base_url').val();
            var path ="epin/validate_username";
            var flag = false;
           $.ajax({
                'url': path,
                'type': "POST",
                'data': {
                    username: param
                },
                'dataType': 'text',
                'async': false,
                success: function(data) {
                   if(data == "yes") {
                    flag = true;
                   } else {
                    flag = false;
                   } 
                }
            });
           return flag;
    });
       
    };
    var flag = false;
    var epin_msg = $("#select_epin").html();
    
    // $("#from_user_name").click(function() {
    //     var data = "<option value='default'>" + epin_msg + "</option>";
    //     flag = true;
    //     $('#epin').html(data);
    // });

    $("#from_user_name").change(function() {
        var user = $('#from_user_name').val();
        var base_url = $("#base_url").val();
            $.ajax({
                url: base_url + "admin/epin/epin_dynamic_list/" + user + "/",
                type: 'POST',
                dataType: "JSON",
                success: function(data) {
                    $('#epin').html(data);
                }
            });
   });

    return {
        //main function to initiate template pages
        init: function() {
            runValidateUser();

        }
    };
}();

function copyEpinToClipboard(element) {
    var selection = window.getSelection(), //Get the window selection
        selectData = document.createRange(); //Create a range

    selection.removeAllRanges(); //Clear any currently selected text.
    selectData.selectNodeContents(element); //Add the desired element to the range you want to select.
    selection.addRange(selectData); //Highlight the element (this is the same as dragging your cursor over an element)
    var copyResult = document.execCommand("copy"); //Execute the copy.

    if (copyResult) //was the copy successful?
        selection.removeAllRanges(); //Clear the highlight.
    else
        alert("Your browser does not support clipboard commands, press ctrl+c");
}