function validate_epin(total) {
    var path_temp = document.form.path_temp.value;
    var path_root = document.form.path_root.value;

    var pin_array = new Array();
    var epin_empty_flag = false;
    var epin_duplicate_flag = false;
    var append= true;
    $('#p_scents > tbody > tr > td:nth-child(2)').find('input').each(function () {
        var epin_field_id = $(this).attr('id');
        var i = epin_field_id.substring(4);
        var epin_value = $(this).val();
        if (epin_value == "") {
            epin_empty_flag = false;
            append= false;
            //epin_value = 'epin';
            document.getElementById("pin_amount" + i).value = 0;
            document.getElementById("balance_amount" + i).value = 0;
            document.getElementById("remaining_amount" + i).value = 0;

            $("#pin_box_" + i).fadeTo(500, 0.1, function () {
                //var msg36 = $("#validate_msg71").html();
                var msg3=trans('required',trans('epin'));
                $(this).removeClass();
                $(this).addClass('messageboxerror');
                $(this).html('<img align="absmiddle" src="' + path_temp + 'images/Error.png" />' + msg3).show().fadeTo(1900, 1);
                document.getElementById("pin_amount" + i).value = 0;
                document.getElementById("balance_amount" + i).value = 0;
                document.getElementById("remaining_amount" + i).value = 0;
            });
            
            //removeRaw(i);
            if ($("#p_scents > tbody > tr").length == 0){
                $("#p_scents").load(" #p_scents > *");
            }
        } else {
            if (isExistInEPinQue(pin_array, epin_value)) {
                var pass_str = {
                    'pin': epin_value,
                    'amount': 0,
                    'i' : i
                };
                pin_array.push(pass_str);
            } else {
                epin_duplicate_flag = true;
                $("#pin_box_" + i).fadeTo(2000, 0.1, function () {
                    //var msg36 = $("#validate_msg71").html();
                    var msg3=trans('repeated',trans('epin'));
                    $(this).removeClass();
                    $(this).addClass('messageboxerror');
                    $(this).html('<img align="absmiddle" src="' + path_temp + 'images/Error.png" />' + msg3).show().fadeTo(1900, 1);
                    document.getElementById("pin_amount" + i).value = 0;
                    document.getElementById("balance_amount" + i).value = 0;
                    document.getElementById("remaining_amount" + i).value = 0;
                });
            }
        }
    });

    if (!epin_empty_flag) {
        var epin_available = path_root + "register/check_epin_validity/";
        var JSON_data = JSON.stringify(pin_array);

        var epin = "";
        var amount = 0;
        var epin_used_amount = 0;
        var balance_amount = 0;
        var reg_balance_amount = 0;
        var total_epin_amount = 0;
        var i = 1;
        var j = 1;
        var product_id = $("#product_id").val();
        var sponsor_name = $("#sponsor_user_name").val();

             var epinuse=$('#epin1').val();
    if(epinuse=="")
    {
        $('#'+$('#epin1').parent('p').next().attr('id')).fadeTo(2000, 0.1, function () {
                    //var msg36 = $("#validate_msg71").html();
                    var msg99=trans('required',trans('epin'));
                    $(this).removeClass();
                    $(this).addClass('messageboxerror');
                    $(this).html('<img align="absmiddle" src="' + path_temp + 'images/Error.png" />' + msg99).show().fadeTo(1900, 1);
                
                });
    }  else{

        $.ajax({
            url: epin_available,
            type: 'POST',
            data:{
                pin_array: pin_array,
                product_id: product_id,
                sponsor_name: sponsor_name,
                inf_token: $('input[name="inf_token"]').val()
            },
            dataType: "json",
            // contentType: "application/json",
            success: function (data) {
                var total_sum = 0;
                $.each(data, function (k, v) {
                    i = v.i;
                    epin = v.pin;
                   
                    amount = v.amount;
                    balance_amount = v.balance_amount;
                    reg_balance_amount = v.reg_balance_amount;
                    epin_used_amount = v.epin_used_amount;
                    product_amount = v.product_amount;

                    var reg_amount = $('#total_reg_amount1').val();

                    total_sum = Number(reg_amount) + Number(product_amount);
                    console.log(total_sum);
                    if (epin == "nopin") {
                        if (total_sum == total_epin_amount) {
                            removeRaw(i);
                        } else {
                            $("#pin_box_" + j++).fadeTo(2000, 0.1, function () {
                                // var msg36 = $("#validate_msg9").html();
                                var msg = trans('invalid', trans('epin'));
                                $(this).removeClass();
                                $(this).addClass('messageboxerror');
                                $(this).html('<img align="absmiddle" src="' + path_temp + 'images/Error.png" />' + msg).show().fadeTo(1900, 1);
                            });
                        }
                        
                    } else {

                        if (total_sum == total_epin_amount) {
                            removeRaw(i);
                        } else {
                            document.getElementById("pin_count").value = i;
                            document.getElementById("epin_count").value = parseFloat(i) + parseFloat(1);
                            document.getElementById("pin_amount" + i).value = amount;
                            document.getElementById("balance_amount" + i).value = reg_balance_amount;
                            document.getElementById("remaining_amount" + i).value = balance_amount;
                            total_epin_amount = parseFloat(total_epin_amount) + parseFloat(epin_used_amount);

                            $("#pin_box_" + j++).fadeTo(2000, 0.1, function () {
                                //var msg37 = $("#validate_msg10").html();
                                var msg1=trans('valid',trans('epin'));
                                $(this).removeClass();
                                $(this).addClass('messageboxok');
                                $(this).html('<img align="absmiddle" src="' + path_temp + 'images/accepted.png" />' + msg1).show().fadeTo(1900, 1);

                            });
                        }

                    }
                });


                document.getElementById("epin_total_amount").value = total_epin_amount;
                if (epin != 'nopin' && !epin_empty_flag && !epin_duplicate_flag) {
                    if (total_sum > total_epin_amount) {
                        if (append)
                        {
                            addNewraw();
                        }
                    } else {
                        document.getElementById("pin_btn").disabled = true;
                        // document.getElementById("epin_submit").disabled = false;
                        $('.sw-btn-finish').prop('disabled', false);
                    }
                }
            }
        });
    }
    }

    function isExistInEPinQue(pass_arr, epin_value) {
        var i = 0;
        var j = 1;
        var arr_len = pass_arr.length;
        if (arr_len == 0) {
            j = 1;
        }
        if (arr_len > 0) {
            j = 0;
        }

        for (var i = j; i < arr_len; i++) {
            if ((pass_arr[i].pin).toLowerCase() === epin_value.toLowerCase()) {
                return false;
            }
        }
        return true;
    }

    var epinuse=$('#epin1').val();
    if(epinuse=="")
    {
        $("#pin_box_").fadeTo(2000, 0.1, function () {
                    //var msg36 = $("#validate_msg71").html();
                    var msg99=trans('required',trans('epin'));
                    $(this).removeClass();
                    $(this).addClass('messageboxerror');
                    $(this).html('<img align="absmiddle" src="' + path_temp + 'images/Error.png" />' + msg99).show().fadeTo(1900, 1);
                
                });
    }

}