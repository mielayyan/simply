var payout_release_table  = process_payment_table = payout_status_pending_requests_table = payout_status_approved_pending_table = payout_status_approved_paid_table = payout_status_rejected_table = null;
var balance_table = null;
var selected_tab = 'tab_payout_status';
var loaded_tabs = ['tab_payout_status'];

$(document).ready(function() {
    $('input[name="tabs"]').on('click', function () {
        unsetchekbox();
        selected_tab = this.id;
        if (!loaded_tabs.includes(selected_tab)) {
            loaded_tabs.push(selected_tab);
            loadTabData(selected_tab);
        }
    });

    $('.user-search-select2').select2({
        minimumInputLength: 1,
        multiple: true,
        placeholder : `${capitalizeFirstLetter(trans("user_name"))}`,
        allowClear: true,
        closeOnSelect : false,
        width: 'auto',
        ajax: {
            url: $('#base_url').val()+"admin/user_search",
            dataType: 'json',
            delay: 250,
            processResults: function (response) {
               return {
                  results: response
               };
            }
        }
    });
    
    $('#payment_method').select2({
         width: 'auto',
        placeholder: `${trans('payment_method')}`
    });
    
    payout_release_table = $('#payout_release_table').DataTable({
        language: JSON.parse($('#data_table_language').val()),
        order: [[1, "asc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        "ajax": {
            url : $('#base_url').val()+"admin/payout/payout_release",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names'         : $('#payout_manual_filter_form .user-search-select2').val(),
                'payment_method'     : $('#payment_method').val(),
                'payout_release_type': $('#payout_release_type').val(),
                'kyc_status'         : $('#kyc_status').val()
              });
            }
        },
        "columns": [
            {
                orderable: false,
                render: function(data, type, row, meta) {
                    return $('#template_payout_release_checkbox').html()
                        .replace('[request_id]', row['request_id'])
                        .replace('[user_name]', row['user_name']);
                }
            },
            {
                render: function(data, type, row, meta) {
                    return $('#template_payout_release_name').html()
                        .replace('[full_name]', row['full_name'])
                        .replace('[profile_image]', row['profile_image'])
                        .replace('[user_name]', row['user_name']);
                }
            },
            {
                orderable: false,
                render: function(data, type, row, meta) {
                    if(row['payout_request_type'] == "admin") {
                        return $('#template_payout_release_payout_amount_admin').html()
                            .replace('[payout_amount]', row['payout_amount']);
                    }
                    return $('#template_payout_release_payout_amount_user').html()
                    .replace('[payout_amount]', format_currency(row['payout_amount']));
                }
            },
            { "data": "payout_method", orderable: false },
            { "data": "payout_type", orderable: false },
            {
                orderable: $('#payout_release_type').val() === "admin",
                render: function(data, type, row, meta) {
                    return $('#template_payout_release_ewallet_balance').html()
                        .replace('[ewallet_balance_amount]', format_currency(row['ewallet_balance']));
                }
             }
        ]
    }).draw();

    let url_params = new URLSearchParams(location.search);
    url_params.get('tab');
    if(url_params.get('tab') == "requests") {
        $('#tab_payout_release_manual').trigger('click');
    }
    
    $('#payout_manual_filter_form').on('submit', function(e) {
       e.preventDefault();
       unsetchekbox();
       loadPayoutReleaseTable();
    });
    
    $('.payout-manual-release-all').click(function () {
        $(this).is(':checked') ? $('#payout_release_table .payout-manual-release-single').prop('checked', true) : $('#payout_release_table .payout-manual-release-single').prop('checked', false);
        showPayoutManualActionPopup();
    });
    
    $('body').on('click', '.payout-manual-release-single', function() {
        $(this).is(':checked') ? $(this).prop('checked', true) : $('#payout_release_table .payout-manual-release-all').prop('checked', false);
        
       showPayoutManualActionPopup();
    });
    
    $('#payout_release_manual_btn').on('click', function() {
        let selected_payouts = [];
        $('.payout-manual-release-single:checked').each(function(){ 
            selected_payouts.push({
                'user_name': $(this).data('user_name'),
                'amount': $(this).closest('tr').find('.payout_amount').val()
            });
        });
        $.ajax({
           'method': 'POST',
           'url': $('#base_url').val()+"/admin/payout/payout_release_action",
           'data': {
               'payouts': selected_payouts,
               'payout_type': 'admin',
               'payment_method': $('#payment_method').val(),
           }, 
           success: function(response) {
               response = JSON.parse(response);
                if(response.status == "failed") {
                    if(response.error_type == "validation") {
                        showErrorAlert(response.message);
                    } else if(response.error_type == "unknown") {
                        showErrorAlert(response.message)
                    }
                } else if(response.status == "success") {
                    loadPayoutReleaseTable();
                    process_payment_table.draw();
                    showSuccessAlert(response.message)
                    $('#payout_manual_release_popup').addClass('hidden')
                }
           }, 
           beforeSend: function() {
                $('#payout_release_manual_btn').button('loading');
                $('#payout_release_manual_btn').button('loading');
          },
          complete: function() {
                loadPayoutSummaryTotal();
                $('#payout_release_manual_btn').button('reset');
                $('#payout_release_manual_btn').button('reset');
          }
        }); 
    });
    
    $('#payout_requests_payment_method').select2();
    
    $('.payout-request-release-all').click(function () {
        $(this).is(':checked') ? $('.payout-requests-release-single').prop('checked', true) : $('.payout-requests-release-single').prop('checked', false);
        showPayoutRequestsActionPopup();
    });
    
    $('body').on('click', '.payout-requests-release-single', function() {
       showPayoutRequestsActionPopup();
    });
    
    $('#payout_release_requests_btn').on('click', function() {
        let selected_payouts = [];
        $('.payout-manual-release-single:checked').each(function(){   
            selected_payouts.push({
                'user_name': $(this).val(),
                'amount': $(this).closest('tr').find('.payout_amount').val()
            });
        });
        $.ajax({
           'method': 'POST',
           'url': $('#base_url').val()+"/admin/payout/payout_requests_release_action",
           'data': {
               'payouts': selected_payouts,
               'payout_type': 'user',
               'payment_method': $('#payment_method').val(),
           },
           success: function(response) {
               response = JSON.parse(response);
                if(response.status == "failed") {
                    if(response.error_type == "validation") {
                        showErrorAlert(response.message);
                    } else if(response.error_type == "unknown") {
                        showErrorAlert(response.message)
                    }
                } else if(response.status == "success") {
                    showSuccessAlert(response.message);
                    loadPayoutSummaryTotal();
                    loadPayoutReleaseTable();
                    $('#payout_requests_release_popup').addClass('hidden')
                }
           },
           beforeSend: function() {
                $('#payout_release_requests_btn').button('loading');
                $('#payout_delete_requests_btn').button('loading');
          },
          complete: function() {
                $('#payout_release_requests_btn').button('reset');
                $('#payout_delete_requests_btn').button('reset');
          }
        }); 
    });
    
    $('#payout_delete_requests_btn').on('click', function() {
        let selected_payouts = [];
        $('.payout-manual-release-single:checked').each(function(){   
            selected_payouts.push($(this).val());
        });
        $.ajax({
           'method': 'POST',
           'url': $('#base_url').val()+"/admin/payout/payout_requests_delete_action",
           'data': {
               'payouts': selected_payouts,
               'payout_type': 'user',
               'payment_method': $('#payment_method').val(),
           },
           success: function(response) {
               response = JSON.parse(response);
                if(response.status == "failed") {
                    if(response.error_type == "validation") {
                        showErrorAlert(response.message);
                    } else if(response.error_type == "unknown") {
                        showErrorAlert(response.message)
                    }
                } else if(response.status == "success") {
                    // payout_requests_table.draw()
                    loadPayoutReleaseTable();
                    showSuccessAlert(response.message)
                    $('#payout_requests_release_popup').addClass('hidden')
                }
           }, 
           beforeSend: function() {
                $('#payout_release_requests_btn').button('loading');
                $('#payout_delete_requests_btn').button('loading');
          },
          complete: function() {
                $('#payout_release_requests_btn').button('reset');
                $('#payout_delete_requests_btn').button('reset');
          }
        }); 
    });
    
    
    // Process Payment 
    process_payment_table =  $('#process_payment_table').DataTable({
        language: JSON.parse($('#data_table_language').val()),
        order: [[3, "desc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/process_payment_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('#process_payment_filter_users').val(),
              });
            }
        },
        "columns": [
            { 
                'orderable': false,
                render: function(data, type, row, meta) {
                    return $('#template_process_payment_table_checkbox').html()
                        .replace('[paid_id]', row['paid_id']);
                }
            },
            {
                render: function(data, type, row, meta)  {
                    return $('#template_process_payment_table_member').html()
                        .replace('[user_photo]', row['user_photo'])
                        .replace('[full_name]', row['full_name'])
                        .replace('[user_name]', row['user_name']);
                }
            },
            { 
                render: function(data, type, row, meta) {
                    return $('#template_amount').html()
                        .replace('[amount]', row['paid_amount'])
                },
            },
            { "data": "approved_date" }
        ]
    });
    
    $('#process_payment_filter_form').on('submit', function(e) {
        e.preventDefault();
        $('.payout-process-all').prop('checked', false);
        process_payment_table.draw();
    })
    
    $('.payout-process-all').click(function () {
        $(this).is(':checked') ? $('#process_payment_table .payout-process-single').prop('checked', true) : $('#process_payment_table .payout-process-single').prop('checked', false);
        showPayoutProcessActionPopup();
    });
    
    $('body').on('click', '.payout-process-single', function() {
        $(this).is(':checked') ? $(this).prop('checked', true) : $('#process_payment_table .payout-process-all').prop('checked', false);
        showPayoutProcessActionPopup();
    });
    
    
    $('#payout_process_btn').on('click', function() {
        let selected_payouts = [];
        $('.payout-process-single:checked').each(function(){    
            selected_payouts.push($(this).val());
        });
        $.ajax({
           'method': 'POST',
           'url': $('#base_url').val()+"/admin/payout/process_payout_action",
           'data': {
               'payouts': selected_payouts,
           },
           success: function(response) {
               response = JSON.parse(response);
                if(response.status == "failed") {
                    if(response.error_type == "validation") {
                        showErrorAlert(response.message);
                    } else if(response.error_type == "unknown") {
                        showErrorAlert(response.message)
                    }
                } else if(response.status == "success") {
                    process_payment_table.draw()
                    showSuccessAlert(response.message)
                    $('#payout_process_popup').addClass('hidden')
                }
           }, 
           beforeSend: function() {
                $('#payout_process_btn').button('loading');
          },
          complete: function() {
                loadPayoutSummaryTotal();
                $('#payout_process_btn').button('reset');
          }
        }); 
    });
    
    // Payout staus
    payout_status_pending_requests_table =  $('#payout_status_pending_requests_table').DataTable({
        language: JSON.parse($('#data_table_language').val()),
        order: [[3, "desc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/payout_status_pending_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('#payout_status_user_name').val()
              });
            }
        },
        "columns": [
            {
                render: function(data, type, row, meta) {
                    return $('#template_payout_status_pending_requests_table_member').html()
                        .replace('[user_photo]', row['user_photo'])
                        .replace('[full_name]', row['full_name'])
                        .replace('[user_name]', row['user_name']);
                }
            },
            { 
                render: function(data, type, row, meta) {
                    return $('#template_payout_status_pending_requests_table_payout_amount').html()
                    .replace('[payout_amount]', row['payout_amount']);
                },
            },
            { 
                render: function(data, type, row, meta) {
                    return $('#template_ewallet_balance').html()
                        .replace('[balance_amount]', row['ewallet_balance']);
                },
                'orderable': false 
            },
            { "data": "requested_date" }
        ]
    });
    
    payout_status_approved_pending_table =  $('#payout_status_approved_pending_table').DataTable({
        language: JSON.parse($('#data_table_language').val()),
        order: [[3, "desc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/payout_status_approved_pending_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('#payout_status_user_name').val()
              });
            }
        },
        "columns": [
            { 
                render: function(data, type, row, meta) {
                    return $('#template_payout_member').html()
                    .replace('[user_photo]', row['user_photo'])
                    .replace('[full_name]', row['full_name'])
                    .replace('[user_name]', row['user_name']);
                },
            },
            { 
                render: function(data, type, row, meta) {
                    return $('#template_amount').html()
                        .replace('[amount]', row['amount']);
                }
            },
            { "data": "payout_method" },
            { "data": "approved_date" }
        ]
    });
    
    payout_status_approved_paid_table =  $('#payout_status_approved_paid_table').DataTable({
        language: JSON.parse($('#data_table_language').val()),
        order: [[3, "desc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        deferLoading: 0,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/payout_status_approved_paid_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('#payout_status_user_name').val()
              });
            }
        },
        "columns": [
            { 
                render: function(data, type, row, meta) {
                    return $('#template_payout_member').html()
                        .replace('[user_photo]', row['user_photo'])
                        .replace('[full_name]', row['full_name'])
                        .replace('[user_name]', row['user_name']);
                }
            },
            {
                render: function(data, type, row) {
                    return `<a href="#" onclick="getInvoice(${row['invoice_no']})">PR000${row['invoice_no']}</a>`;
                }
            },
            // {'data': 'invoice_no'},
            { 
                render: function(data, type, row, meta) {
                    return $('#template_amount').html()
                        .replace('[amount]', row['amount']);
                }
            },
            {"data": "payout_method"},
            { "data": "paid_date" }
        ]
    });
    
    payout_status_rejected_table =  $('#payout_status_rejected_table').DataTable({
        language: JSON.parse($('#data_table_language').val()),
        order: [[3, "desc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/payout_status_rejected_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('#payout_status_user_name').val()
              });
            }
        },
        "columns": [
            { 
                render: function(data, type, row, meta) {
                    return $('#template_payout_member').html()
                        .replace('[user_photo]', row['user_photo'])
                        .replace('[full_name]', row['full_name'])
                        .replace('[user_name]', row['user_name'])
                }
            },
            { 
                render: function(data, type, row, meta) {
                    return $('#template_amount').html()
                        .replace('[amount]', row['amount']);

                }
            },
            { "data": "requested_date" },
            { "data": "rejected_date" }
        ]
    });
    $('#payout_manual_filter_form .search_clear').on('click', function () {
        $('#payout_manual_filter_form .user-search-select2').val('').trigger('change');
        $('#payout_manual_filter_form #payment_method').val('Bank Transfer').trigger('change');
        $('#payout_manual_filter_form #payout_release_type').val('admin').trigger('change');
        $('#payout_manual_filter_form #kyc_status').val('active').trigger('change');
        unsetchekbox();
        loadPayoutReleaseTable();
    });
     $('#process_payment_filter_form .search_clear').on('click', function () {
        $('#process_payment_filter_form .user-search-select2').val('').trigger('change');
        unsetchekbox();
        process_payment_table.draw();
    });
     $('#payout_status_filter_form .search_clear').on('click', function () {
        $('#payout_status_filter_form .user-search-select2').val('').trigger('change');
        $('#payout_status_filter_form #payout_status_filter').val('paid').trigger('change');
        
        unsetchekbox();
        loadTabData('tab_payout_status');
    });
    
    $('#payout_status_filter_form').on('submit', function(e) {
        e.preventDefault();
        $('.payout-manual-release-all').prop('checked', false);
        $('.payout-process-all').prop('checked', false);
        loadTabData('tab_payout_status');
    });
    
    $('.tabs__item-input').on('change', function() {
        $('.popup-btn-area').addClass('hidden');
    })
    loadTabData(selected_tab);
    $('.payout-manual-release-all').prop('checked', false);
    
    $('.close-popup').click(function(e) {
        e.preventDefault();
        $(this).closest('.popup-btn-area').addClass('hidden');
        $('.payout-checkbox').prop('checked', false);
        $('.payout-process-all').prop('checked', false);
        $('.payout-manual-release-all').prop('checked', false);
    });
  
    $('#payout_release_type').select2({
    'placeholder': `${trans('release_type')}`
    });
      
    $('#kyc_status').select2({
    'placeholder': `${trans('KYC Status')}`,
    width:'auto'
    });
    
    $('#payout_status_filter').select2({
    'placeholder': `${trans('payout_status')}`
    });
});

function showPayoutProcessActionPopup() {
    if ($(".payout-process-single:checked").length > 0) { // any one is checked
        let items_selected = $(".payout-process-single:checked").length;
        $('#payout_process_popup_span').text(items_selected);
        $('#payout_process_popup').removeClass('hidden');
    } else { // none is checked
        $('.payout-process-single').prop('checked', false);
        $('#payout_process_popup').addClass('hidden');
        $('.payout-process-all').prop('checked', false);
    }
}

function showPayoutManualActionPopup() {
    if($('#payout_release_type').val() == "admin") {
        if ($(".payout-manual-release-single:checked").length) { // any one is checked
            let items_selected = $(".payout-manual-release-single:checked").length;
            $('#payout_manual_release_popup_span').text(items_selected);
            $('#payout_manual_release_popup').removeClass('hidden');
        } else { // none is checked
            $('.payout-manual-release-single').prop('checked', false);
            $('#payout_manual_release_popup').addClass('hidden');
            $('.payout-manual-release-all').prop('checked', false);
            $('.payout-process-all').prop('checked', false);
        }

    } else if($('#payout_release_type').val() == "user") {
        if ($(".payout-manual-release-single:checked").length > 0) { // any one is checked
            let items_selected = $(".payout-manual-release-single:checked").length;
            $('#payout_requests_release_popup_span').text(items_selected);
            $('#payout_requests_release_popup').removeClass('hidden');
        } else { // none is checked
            $('.payout-manual-release-single').prop('checked', false);
            $('#payout_requests_release_popup').addClass('hidden');
            $('.payout-manual-release-all').prop('checked', false);
             $('.payout-process-all').prop('checked', false);
        }
    }
}

function showPayoutRequestsActionPopup() {
    if ($(".payout-requests-release-single:checked").length > 0) { // any one is checked
        let items_selected = $(".payout-requests-release-single:checked").length;
        $('#payout_requests_release_popup_span').text(items_selected);
        $('#payout_requests_release_popup').removeClass('hidden');
    } else { // none is checked
        $('.payout-requests-release-single').prop('checked', false);
        $('#payout_requests_release_popup').addClass('hidden');
        $('.payout-manual-release-all').prop('checked', false);
        $('.payout-process-all').prop('checked', false);
    }
}

function loadTabData(tab) {
    if (tab == 'tab_payout_release_manual') {
        loadPayoutReleaseTable();
    } else if (tab == 'tab_payout_status') {
         $('.table-payout-summary').addClass('hidden');
        //  alert($('#payout_status_filter').val());
        if($('#payout_status_filter').val() == "pending") {
            $('.table-payout-summary-pending').removeClass('hidden');
            payout_status_pending_requests_table.draw();
            payout_status_approved_pending_table.draw();
        } else if($('#payout_status_filter').val() == "approved") {
            $('.table-payout-summary-approved-pending').removeClass('hidden');
            payout_status_approved_pending_table.draw();
        } else if($('#payout_status_filter').val() == "paid") {
            $('.table-payout-summary-paid').removeClass('hidden');
            payout_status_approved_paid_table.draw();
        } else if($('#payout_status_filter').val() == "rejected") {
            $('.table-payout-summary-rejected').removeClass('hidden');
            payout_status_rejected_table.draw();
        }
    } else if(tab == 'tab_process_payment') {
        process_payment_table.draw();
    }
}
function unsetchekbox()
{
       $('#payout_manual_release_popup').addClass('hidden');
       $('#payout_requests_release_popup').addClass('hidden');
       $('#payout_process_popup').addClass('hidden');
       $('.payout-manual-release-all').prop('checked', false);
       $('.payout-process-all').prop('checked', false);
       $('.payout-manual-release-single').prop('checked', false);
       $('.payout-requests-release-single').prop('checked', false);
       $('.payout-process-single').prop('checked', false);
       
}

function loadPayoutReleaseTable() {
    payout_release_table.draw();
    if($('#kyc_status').val()) {
        payout_release_table.column( 0 ).visible($('#kyc_status').val() === "active");
    }
}
function loadPayoutSummaryTotal() {
    $.ajax({
        url: $('#base_url').val() + "admin/payout/summary_total",
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            $('#total_amount_active_request').text(response.total_amount_active_request);
            $('#total_amount_waiting_requests').text(response.total_amount_waiting_requests);
            $('#total_amount_paid_request').text(response.total_amount_paid_request);
            $('#total_amount_rejected_requests').text(response.total_amount_rejected_requests);
        }
    });
}

function getInvoice(invoice_id){
    var url = $('#base_url').val() + "repurchase/getPayoutInvoiceDetails";
    $.ajax({
        'url': url,
        'type': "POST",
        'data': {
            invoice_id : invoice_id
        },
        'dataType': 'text',
        'async': false,
        success: function(data) {
            $('.invoice_shopping').empty();
            $('.invoice_shopping').append(data);
            $('#invoice_modal').modal('show');
        },
        error: function(error) {
          console.log(error);
        }
     });
}

function print_invoice_report() {
    var myPrintContent = document.getElementById('print_invoice_area');
    var myPrintWindow = window.open("", "Print Report", 'left=300,top=100,width=700,height=500', '_blank');
    myPrintWindow.document.write(myPrintContent.innerHTML);
    myPrintWindow.document.close();
    myPrintWindow.focus();
    myPrintWindow.print();
    myPrintWindow.close();
    return false;
}   