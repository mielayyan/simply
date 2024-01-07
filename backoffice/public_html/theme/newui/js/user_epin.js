var selected_tab = 'epin_list';
var loaded_tabs = ['epin_list'];
var epin_list_table = epin_transfer_report_table = null;
var data_table_language = $('#data_table_language').val();
$(function () {


    $('input[name="tabs"]').on('click', function () {
        selected_tab = this.id;
        if (!loaded_tabs.includes(selected_tab)) {
            loaded_tabs.push(selected_tab);
            loadTabData(selected_tab);
        }
    });
    $('.amount-search-select2').select2({
        width: 'auto',
        multiple: true,
        placeholder: trans('amount'),
        allowClear: true,
        closeOnSelect: false,

    });
    $('.epin-search-select2').select2({
            minimumInputLength: 1,
            multiple: true,
            placeholder : trans("E-pin"),
            allowClear: true,
            closeOnSelect : false,
            width: 'auto',
            ajax: {
                url: $('#base_url').val()+"user/epin/epin_search",
                dataType: 'json',
                delay: 250,
                processResults: function (response) {
                   return {
                      results: response
                   };
                }
            }
        });
    $('.status-search-select2').select2({
        multiple: false,
        placeholder: trans('Status'),
        allowClear: true,
        closeOnSelect: true,
        width: 'auto'
    });

    loadDateRangePicker('#epin_transfer_history_daterangepicker');

    epin_requests_table =$('#epin_requests_table').DataTable({
        language: data_table_language,
        order: [[1, "desc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        ajax: {
            url: $('#base_url').val() + "/user/epin/epin_requests",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    'user_names': $('#epin_requests_filter_form .user-search-select2').val(),
                });
            }
        },
        columns: [

            { data: "requested_date" },
            { data: "expiry_date" },
           
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_balance_amount').html();
                    content = content.replace('[balance_amount]', row['requested_pin_count']);
                    return content;
                }
            },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_amount').html();
                    content = content.replace('[amount]', row['amount']);
                    return content;
                }
            },
        ]
    });
    $('#epin_requests_filter_form .search_filter').on('click', function () {
        epin_requests_table.draw();
    });

    $('#epin_requests_filter_form .search_clear').on('click', function () {
        $('#epin_requests_filter_form .user-search-dropdown').val('').trigger('change');

        epin_requests_table.draw();
    });

    epin_list_table = $('#epin_list_table').DataTable({
        language: data_table_language,
        order: [[1, "desc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        // deferLoading: 0,
        ajax: {
            url: $('#base_url').val() + "user/epin/epin_list",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    'user_names': $('#epin_list_filter_form .user-search-select2').val(),
                    'epins': $('.epin-search-select2').val(),
                    'amounts': $('.amount-search-select2').val(),
                    'status': $('#epin_status').val(),
                });
            }
        },
        columns: [

            { data: "pin_number", orderable: false },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_amount').html();
                    content = content.replace('[amount]', row['amount']);
                    return content;
                }
            },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_balance_amount').html();
                    content = content.replace('[balance_amount]', row['balance_amount']);
                    return content;
                }
            },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_status').html();
                    content = content.replace('[status]', row['status']);
                    content = content.replace('[type]', row['status']);
                    return content;
                }
            },
            { data: "expiry_date" },
            // { data: "refund" },
            {
                render: function(data, type, row, meta) {
                    if(row['refund'] == "NA") {
                        return row['refund'];
                    } else {
                        return `<a onclick="javascript:refund_pin(${row['epin_id']})" class="text-danger" >
                                    ${row['refund']} 
                                    <i class="fa fa-undo" aria-hidden="true"></i>
                                </a>
                        `;
                    }
                }
            }
        ]
    });

    epin_transfer_report_table = $('#epin_transfer_report_table').DataTable({
        language: data_table_language,
        // dom: '<f<t><"#df"< i><p><l>>>',
        dom: 'Bfrtip',
        buttons: [
            {
               "extend": 'excelHtml5',
               'title'  : '',
               'filename': `Epin Transfer Report `,
               "text": `<i class="fa fa-file-excel-o" >  </i> ${trans('excel')}`,
               "titleAttr": trans('excel'), 
               "action": newexportaction,
            }, {
               "extend": 'csv',
                'title': '',
               'filename': `Epin Transfer Report `,
               "text": `<i class="fa fa-file-text-o" >  </i>${trans('csv')}`,
               "titleAttr": trans('csv'),                               
               "action": newexportaction,
               'footer': true,
           }, 
           {
                "extend": 'print',
                'title': `Epin Transfer Report`,
                'message': $('#print_title').html(),
                'filename': `Epin Transfer Report `,
                "text": `<i class="fa fa-print" > </i> ${trans('print')}`,
                "titleAttr": trans('print'),
                "action": newexportaction,
                'footer': true, 
                customize: function ( win ) {
                    $(win.document.body).css( 'font-size', '12px' );
                    $(win.document.body).find( 'table' ).css( 'font-size', '12px' );
                    $(win.document.body).children("h1:first").remove();
                }
           }
        ],
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        // pageLength: 1,
        ajax: {
            url: $('#base_url').val() + "user/epin/epin_transfer_report",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    'start_date': $('#epin_transfer_history_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    'end_date': $('#epin_transfer_history_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                    /*'user_names': $('#epin_list_filter_form .user-search-select2').val(),
                    'epins': $('.epin-search-select2').val(),
                    'amounts': $('.amount-search-select2').val(),
                    'status': $('#epin_status').val(),*/
                });
            }
        },
        columns: [
            { data: 'member_name', },
            { data: 'epin', 'orderable' :false},
            { data: 'amount'},
            { data: 'transferred_date'},
            { data: 'type', 'orderable' : false},
        ]
    });
    epin_transfer_report_table.buttons().container().appendTo( $('#user_transfer_report_btn'));
    $('#epin_transfer_history_filter_form .search_filter').on('click', function () {
        epin_transfer_report_table.draw();
    });

    $('#epin_transfer_history_filter_form .search_clear').on('click', function () {
        reloadDateRangePicker('#epin_transfer_history_daterangepicker');
        epin_transfer_report_table.draw();
    });

    let url_params = new URLSearchParams(location.search);
    url_params.get('tab');
    if(url_params.get('tab') == "requests") {
        $('#epin_requests').trigger('click');
    }

    if(url_params.get('tab') == "list") {
        $('#epin_list').trigger('click');
    }


    
    $('#status_search_epin').select2({
        width: 'auto',
      'placeholder': `${trans('epin_status')}`
    });
    $("#create_epin_form").on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: $('#base_url').val() + "user/epin/reuest_epin_post",
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                form.find('.form-group .text-danger').remove();
            },
            success: function (response) {
                if (response.status) {
                    if(response.status == "failed") {
                        if (response.validation_error) {
                            setValidationErrors(form, response);
                        }
                        showErrorAlert(response.message);
                    } else if(response.status == "success"){
                        showSuccessAlert(response.message);
                        closePopup('#request_epin_modal');
                        loadEpinSummaryTotal();
                        loaded_tabs = [selected_tab];
                        loadTabData(selected_tab);
                    }
                } else {
                    if (response.validation_error) {
                        setValidationErrors(form, response);
                    }
                    showErrorAlert(response.message);
                }
            }
        });
    });
    $("#epin_transfer_form").on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: $('#base_url').val() + "user/epin/epin_transfer_post",
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                form.find('.form-group .text-danger').remove();
            },
            success: function (response) {
                if (response.status) {
                    showSuccessAlert(response.message);
                    closePopup('#epin_transfer_modal');
                    loadEpinSummaryTotal();
                    loaded_tabs = [selected_tab];
                    loadTabData(selected_tab);
                } else {
                    if (response.validation_error) {
                        setValidationErrors(form, response);
                    }
                    showErrorAlert(response.message);
                }
            }
        });
    });
    $("#epin_purchase_form").on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: $('#base_url').val() + "user/epin/epin_purchase_post",
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                form.find('.form-group .text-danger').remove();
            },
            success: function (response) {
                if (response.status) {
                    if(response.status == "failed") {
                        if (response.validation_error) {
                            setValidationErrors(form, response);
                        }
                        showErrorAlert(response.message);
                    } else if(response.status == "success"){
                        showSuccessAlert(response.message);
                        closePopup('#epin_purchase_modal');
                        loadEpinSummaryTotal();
                        loaded_tabs = [selected_tab];
                        loadTabData(selected_tab);
                    }
                } else {
                    if (response.validation_error) {
                        setValidationErrors(form, response);
                    }
                    showErrorAlert(response.message);
                }
            }
        });
    });
    $('#epin_list_filter_form .search_filter').on('click', function () {

       epin_list_table.draw();
       hideActionColumnTableEpinListTable();
       $('#epin_active_list_action_popup').addClass('hidden');
       $('#epin_blocked_list_action_popup').addClass('hidden');
    });
    $('#epin_list_filter_form .search_clear').on('click', function () {
        $('#epin_list_filter_form .user-search-dropdown').val('').trigger('change');
        $('.epin-search-select2').val('').trigger('change');
        $('.amount-search-select2').val('').trigger('change');
        $('#epin_status').val('active').trigger('change');
        epin_list_table.draw();
        hideActionColumnTableEpinListTable(); 
    });

    $("#epin_transfer_form #from_user_name").change(function() {
        var user = $('#from_user_name').val();
        var base_url = $("#base_url").val();
        var form = $('#epin_transfer_form');
        $.ajax({
            url: base_url + "admin/epin/user_epin_list",
            type: 'POST',
            data: {
                'from_user_name': user
            },
            dataType: "JSON",
            success: function(response) {
                html = '';
                if(response.status == "success") {
                    html = `<option value="" disabled="disabled" selected="selected">${trans('select_epin')}</option>`;
                    response.data.forEach(function(epin) {
                        html += `<option value="${epin.pin_id}">${epin.pin_numbers}</option>`;
                    });
                    $('#epin_transfer_form #epin').html(html);
                } else {
                    if (response.validation_error) {
                        setValidationErrors(form, response);
                    }
                    showErrorAlert(response.message);
                }
            }
        });
   });

    $('#epin_transfer_form input').on('input', function(e) {
        $(this).closest('.form-group').find('.text-danger').remove();
    })

    $('body').on('click', '.epin-list-select-checkbox-single', function() {
           showEpinActiveActionPopup();
        });

        $('body').on('click', '.epin-request-list-select-checkbox-single', function() {
           showEpinRequestsActionPopup();
        });        
        $('#block_epin_btn').on('click', block_unblock);
        $('#unblock_epin_btn').on('click', block_unblock);

        function block_unblock() {
            /*if(!confirm(`${trans('are_you_sure_want_block_epin')}`)) {
                return false;
            }*/
            let block_status = $('#epin_status').val();
            let selected_epins = [];
            $('.epin-list-select-checkbox-single:checked').each(function(){ 
                selected_epins.push($(this).val());
            });
            console.log(selected_epins)
            $.ajax({
               'method': 'POST',
               'url': $('#base_url').val()+"/admin/epin/block_epin",
               'data': {
                   epins: selected_epins ,
                   status : block_status
               }, 
               success: function(response) {
                   response = JSON.parse(response);
                    if(response.status == "failed") {
                        if(response.error_type == "validation") {
                          // showSuccessAlert(response.message);
                          showErrorAlert(response.message);
                            // toastr.error(response.message);
                        } else if(response.error_type == "unknown") {
                          // showSuccessAlert(response.message);
                          showErrorAlert(response.message);
                            // toastr.error(response.message)
                        }
                    } else if(response.status == "success") {
                        showSuccessAlert(response.message);
                        epin_list_table.draw();
                        hideActionColumnTableEpinListTable(); 
                        loadEpinSummaryTotal();
                        // toastr.success(response.message)
                    }
               }, 
               beforeSend: function() {
                    $('#block_epin_btn').button('loading');
                    $('#delete_epin_btn').button('loading');
              },
              complete: function() {
                    $('#block_epin_btn').button('reset');
                    $('#delete_epin_btn').button('reset');
                    $('#epin_active_list_action_popup').addClass('hidden')
              }
            });
        }

        $('.popup-delete-epin-button').on('click', function() {
            // if(!confirm(`${trans('are_you_sure_want_delete_epin')}`)) {
            //     return false;
            // }
            let selected_epins = [];
            $('.epin-list-select-checkbox-single:checked').each(function(){ 
                selected_epins.push($(this).val());
            });
            $.ajax({
               'method': 'POST',
               'url': $('#base_url').val()+"admin/epin/delete_epin",
               'data': {
                   epins: selected_epins 
               }, 
               success: function(response) {
                   response = JSON.parse(response);
                    if(response.status == "failed") {
                        if(response.error_type == "validation") {
                          showErrorAlert(response.message);
                        } else if(response.error_type == "unknown") {
                          showErrorAlert(response.message);
                        }
                    } else if(response.status == "success") {
                        showSuccessAlert(response.message);
                        epin_list_table.draw()
                        hideActionColumnTableEpinListTable();
                        loadEpinSummaryTotal();
                      }
               }, 
               beforeSend: function() {
                    $('#block_epin_btn').button('loading');
                    $('.popup-delete-epin-button').button('loading');
              },
              complete: function() {
                    //$('#activate_epin_btn').button('reset');
                    $('#block_epin_btn').button('reset');
                    $('.popup-delete-epin-button').button('reset');
                    $('#epin_active_list_action_popup').addClass('hidden')
                    $('#epin_requested_list_action_popup').addClass('hidden')
              },
              error: function() {
                showErrorAlert(trans('something_went_wrong'));
                epin_list_table.draw()
                hideActionColumnTableEpinListTable();
              }
            });
                
        });

        $('#allocate_epin_btn').on('click', function() {
            /*if(!confirm(`${trans('are_you_sure_want_activate_epin')}`)) {
                return false;
            }
*/            let selected_epins = [];
            $('.epin-request-list-select-checkbox-single:checked').each(function(){ 
                selected_epins.push({
                  'pin_id' : $(this).val(),
                  'count'  : $(this).closest('tr').find('input[name="count[]"]').val(),
                });
            });
            $.ajax({
               'method': 'POST',
               'url': $('#base_url').val()+"/admin/epin/allocate_epin",
               'data': {
                   epins: selected_epins 
               }, 
               success: function(response) {
                   response = JSON.parse(response);
                    if(response.status == "failed") {
                        if(response.error_type == "validation") {
                            showErrorAlert(response.message);
                        } else if(response.error_type == "unknown") {
                            showErrorAlert(response.message);
                        }
                    } else if(response.status == "success") {
                        epin_requests_table.draw()
                        showSuccessAlert(response.message);
                        loadEpinSummaryTotal();
                    }
               }, 
               beforeSend: function() {
                    $('#allocate_epin_btn').button('loading');
                    $('.popup-delete-epin-button').button('loading');
              },
              complete: function() {
                    $('#allocate_epin_btn').button('reset');
                    $('.popup-delete-epin-button').button('reset');
                    $('#epin_requested_list_action_popup').addClass('hidden');
                    $('#epin_active_list_action_popup').addClass('hidden')
                    $('#epin_blocked_list_action_popup').addClass('hidden')
              }
            });
                
        });

        $('#delete_epin_requests_btn').on('click', function() {
            /*if(!confirm(`${trans('are_you_sure_want_delete_epin')}`)) {
                return false;
            }*/
            let selected_epins = [];
            $('.epin-request-list-select-checkbox-single:checked').each(function(){ 
                selected_epins.push($(this).val());
            });
            $.ajax({
               'method': 'POST',
               'url': $('#base_url').val()+"/admin/epin/delete_epin_requests",
               'data': {
                   epins: selected_epins 
               }, 
               success: function(response) {
                   response = JSON.parse(response);
                    if(response.status == "failed") {
                        if(response.error_type == "validation") {
                          showErrorAlert(response.message);
                        } else if(response.error_type == "unknown") {
                          showErrorAlert(response.message);
                        }
                    } else if(response.status == "success") {
                        showSuccessAlert(response.message);
                        epin_list_table.draw();
                        epin_requests_table.draw();
                        hideActionColumnTableEpinListTable();
                        loadEpinSummaryTotal();
                      }
               }, 
               beforeSend: function() {
                    $('#allocate_epin_btn').button('loading');
                    $('#delete_epin_requests_btn').button('loading');
              },
              complete: function() {
                    //$('#activate_epin_btn').button('reset');
                    $('#epin_requested_list_action_popup').addClass('hidden');
                    $('#allocate_epin_btn').button('reset');
                    $('#delete_epin_requests_btn').button('reset');
                    $('#epin_active_list_action_popup').addClass('hidden')
                    
              }
            });
        });

        $('#epin_list_filter_form').submit(function(e) {
          e.preventDefault();
          epin_list_table.draw();
          hideActionColumnTableEpinListTable(); 
        });

        $('.close-popup').click(function(e) {
          e.preventDefault();
          $(this).closest('.popup-btn-area').addClass('hidden');
          $('.payout-checkbox').prop('checked', false);
        });

        $('.epin-list-select-checkbox-all').click(function () {
          $(this).is(':checked') ? $('#epin_list_table .epin-list-select-checkbox-single').prop('checked', true) : $('#epin_list_table .epin-list-select-checkbox-single').prop('checked', false);
          showEpinActiveActionPopup();
        });

        $('.epin-request-select-checkbox-all').click(function () {
          $(this).is(':checked') ? $('#epin_requests_table .epin-request-list-select-checkbox-single').prop('checked', true) : $('#epin_requests_table .epin-request-list-select-checkbox-single').prop('checked', false);
            showEpinRequestsActionPopup();
        });

        $('.tabs__item-input').on('change', function() {
          $('.popup-btn-area').addClass('hidden');
          if(this.id == "epin_requests") {
            epin_requests_table.draw();
            $('.epin-request-select-checkbox-all').prop('checked', false);
          }
          if(this.id == "epin_list") {
            epin_list_table.draw();
            hideActionColumnTableEpinListTable(); 
            $('.epin-list-select-checkbox-all').prop('checked', false);
          }
        })


        function showEpinRequestsActionPopup() {
          if ($(".epin-request-list-select-checkbox-single:checked").length > 0) { // any one is checked
            let items_selected = $(".epin-request-list-select-checkbox-single:checked").length;
            $('#requested_items_selected_epin_span').text(items_selected);
            $('#epin_requested_list_action_popup').removeClass('hidden');
          } else { // none is checked
            $('.epin-request-list-select-checkbox-single').prop('checked', false);
            $('#epin_requests_table .epin-request-select-checkbox-all').prop('checked', false);
            $('#epin_requested_list_action_popup').addClass('hidden');
          }   
        }


        function showEpinActiveActionPopup() {
            if ($(".epin-list-select-checkbox-single:checked").length > 0) { // any one is checked
                
                let items_selected = $(".epin-list-select-checkbox-single:checked").length;
                $('#active_items_selected_epin_span').text(items_selected);
                $('#epin_active_list_action_popup').removeClass('hidden');
                if ($('#epin_status').val() == 'blocked') {
                    $('#block_epin_btn').hide();
                    $('#unbloack_li').show();
                }else if($('#epin_status').val() == 'active'){
                    $('#block_epin_btn').show();
                    $('#unbloack_li').hide();
                }else{
                    $('#epin_active_list_action_popup').addClass('hidden');
                }
            } else { // none is checked
                $('.epin-list-select-checkbox-single').prop('checked', false);
                $('.epin-list-select-checkbox-all').prop('checked', false);
                $('#epin_active_list_action_popup').addClass('hidden');
            }
        }

    /*$('.epin-list-select-checkbox-all').on('click', function() {

    })*/


 });
function loadEpinSummaryTotal() {
    $.ajax({
        url: $('#base_url').val() + "user/epin/summary_total",
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            $('#summary_active_epins').text(response.active_epins);
            $('#summary_epin_requests').text(response.epin_requests_total_amount);
            $('#summary_active_epins_count').text(response.active_epins_count);
            }
    });
}
function loadTabData(tab) {
    if (tab == 'epin_list') {
        epin_list_table.draw();
        hideActionColumnTableEpinListTable(); 
    }
    else if (tab == 'epin_requests') {
        epin_requests_table.draw();
    }
}

function hideActionColumnTableEpinListTable() {
    if($('#epin_status').val() == "used_expired" || $('#epin_status').val() == "deleted") {
       epin_list_table.column( 0 ).visible( false );
        return true;
    } 
    epin_list_table.column( 0 ).visible( true );
}
function refund_pin(id) {
    // document.location.href = $('#base_url').val() + 'user/epin/refund_epin/refund/' + id;
    $.ajax({
            url: $('#base_url').val() + 'user/epin/refund_epin/refund/' + id,
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (response.status) {
                    showSuccessAlert(response.message);
                    loadEpinSummaryTotal();
                    epin_list_table.draw(false);
                } else {
                    if (response.validation_error) {
                        setValidationErrors(form, response);
                    }
                    showErrorAlert(response.message);
                }
            }
        });
}


