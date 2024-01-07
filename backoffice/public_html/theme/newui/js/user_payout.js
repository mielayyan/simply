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
    
    $("#payout_request_form").on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: $('#base_url').val() + "user/payout/post_payout_release_request_ajax",
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                form.find('.form-group .text-danger').remove();
                $('#payout_request_submit').button('loading');
            },
             complete: function() {
                $('#payout_request_submit').button('reset');
             },
            success: function (response) {
                if (response.status) {
                    showSuccessAlert(response.message);
                    closePopup('#payout_request_modal');
                    loadPayoutSummaryTotal();
                    loaded_tabs = [selected_tab];
                    loadTabData(selected_tab);
                    payout_status_pending_requests_table.draw();
                    payout_status_approved_pending_table.draw();
                    payout_status_approved_paid_table.draw();
                    payout_status_rejected_table.draw();
                } else {
                    if (response.validation_error) {
                        setValidationErrors(form, response);
                    }
                    showErrorAlert(response.message);
                }
            }
        });
    });
    
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
        $(this).is(':checked') ? $('#payout_status_pending_requests_table .payout-manual-release-single').prop('checked', true) : $('#payout_status_pending_requests_table .payout-manual-release-single').prop('checked', false);
        showPayoutManualActionPopup();
    });
    
    $('body').on('click', '.payout-manual-release-single', function() {
         $(this).is(':checked') ? $(this).prop('checked', true) : $('#payout_status_pending_requests_table .payout-manual-release-all').prop('checked', false);
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
               'payment_method': 'bank'
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
               'payment_method': 'bank'
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
                    showSuccessAlert(response.message)
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
           'url': $('#base_url').val()+"/user/payout/payout_requests_delete_action",
           'data': {
               'payouts': selected_payouts,
               'payout_type': 'user',
               'payment_method': 'bank'
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
                    loadPayoutSummaryTotal();
                    loaded_tabs = [selected_tab];
                    loadTabData(selected_tab);
                    payout_status_pending_requests_table.draw();
                    payout_status_approved_pending_table.draw();
                    payout_status_approved_paid_table.draw();
                    payout_status_rejected_table.draw();
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
        language: $('#data_table_language').val(),
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
        language: $('#data_table_language').val(),
        order: [[2, "desc"]],
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        "ajax": {
            url : $('#base_url').val()+"/user/payout/payout_status_pending_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
              });
            }
        },
        "columns": [
            {
                orderable: false,
                render: function(data, type, row, meta) {
                  if(row['request_id'] != '<span class="text-lg">Total</span>') {
                    return $('#template_payout_cancel_checkbox').html()
                        .replace('[request_id]', row['request_id'])
                        .replace('[user_name]', row['user_name']);
                  } 
                  return row['request_id'];
                }
            },
            { "data": "requested_date" },
            { 
                render: function(data, type, row, meta) {
                    return $('#template_payout_status_pending_requests_table_payout_amount').html()
                    .replace('[payout_amount]', row['payout_amount']);
                },
            },
            { 
                render: function(data, type, row, meta) {
                  if(row['request_id'] != '<span class="text-lg">Total</span>') {
                    return $('#template_ewallet_balance').html()
                        .replace('[balance_amount]', row['ewallet_balance']);
                  } else {
                    return ''
                  }
                },
                'orderable': false 
            },
        ],
        // dom: 'Bfrtip',
        dom: '<f<t><"#df"< i><p><l>>>',
        buttons: [
            {
               "extend": 'excelHtml5',
               'title'  : '',
               'filename': `Payout Pending `,
               "text": `<i class="fa fa-file-excel-o" >  </i> ${trans('excel')}`,
               "titleAttr": trans('excel'), 
               "action": newexportaction,
            }, {
               "extend": 'csv',
                'title': '',
               'filename': `Payout Pending `,
               "text": `<i class="fa fa-file-text-o" >  </i>${trans('csv')}`,
               "titleAttr": trans('csv'),                               
               "action": newexportaction,
               'footer': true,
           }, 
           {
                "extend": 'print',
                'title': `Payout Pending`,
                'message': $('#print_title').html(),
                'filename': `Payout Pending `,
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
    });
    
    payout_status_approved_pending_table =  $('#payout_status_approved_pending_table').DataTable({
        language: $('#data_table_language').val(),
        order: [[2, "desc"]],
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        "ajax": {
            url : $('#base_url').val()+"/user/payout/payout_status_approved_pending_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
              });
            }
        },
        "columns": [
            { "data": "approved_date" },
            { 
                render: function(data, type, row, meta) {
                    return $('#template_amount').html()
                        .replace('[amount]', row['amount']);
                }
            },
            { "data": "payout_method" },
        ],
        dom: '<f<t><"#df"< i><p><l>>>',
        buttons:  [
            {
               "extend": 'excelHtml5',
               'title'  : '',
               'filename': `Payout Approved Pending `,
               "text": `<i class="fa fa-file-excel-o" >  </i> ${trans('excel')}`,
               "titleAttr": trans('excel'), 
               "action": newexportaction,
            }, {
               "extend": 'csv',
                'title': '',
               'filename': `Payout Approved Pending `,
               "text": `<i class="fa fa-file-text-o" >  </i>${trans('csv')}`,
               "titleAttr": trans('csv'),                               
               "action": newexportaction,
               'footer': true,
           }, 
           {
                "extend": 'print',
                'title': `Payout Approved Pending`,
                'message': $('#print_title').html(),
                'filename': `Payout Approved Pending `,
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
    });

    payout_status_approved_paid_table =  $('#payout_status_approved_paid_table').DataTable({
        language: $('#data_table_language').val(),
        order: [[2, "desc"]],
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        deferLoading: 0,
        "ajax": {
            url : $('#base_url').val()+"/user/payout/payout_status_approved_paid_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
              });
            }
        },
        "columns": [
            { "data": "paid_date" },
            { 
                render: function(data, type, row, meta) {
                    return $('#template_amount').html()
                        .replace('[amount]', row['amount']);
                }
            },
            {"data": "payout_method"},
        ],
        dom: '<f<t><"#df"< i><p><l>>>',
        buttons: [
            {
               "extend": 'excelHtml5',
               'title'  : '',
               'filename': `Payout Approved Paid `,
               "text": `<i class="fa fa-file-excel-o" >  </i> ${trans('excel')}`,
               "titleAttr": trans('excel'), 
               "action": newexportaction,
            }, {
               "extend": 'csv',
                'title': '',
               'filename': `Payout Approved Paid `,
               "text": `<i class="fa fa-file-text-o" >  </i>${trans('csv')}`,
               "titleAttr": trans('csv'),                               
               "action": newexportaction,
               'footer': true,
           }, 
           {
                "extend": 'print',
                'title': `Payout Approved Paid`,
                'message': $('#print_title').html(),
                'filename': `Payout Approved Paid`,
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
    });

    $(document).on( 'draw.dt', function ( e, settings ) {
      var api = new $.fn.dataTable.Api( settings );

      console.log( 'New DataTable created:', api.table().buttons().container() );
      // api.table().buttons().container().html( $('#table_export_btns') );
        // $('#table_export_btns').html(api.table().buttons().container());
        // $('#table_export_btns').html($('.dt-buttons').html());
        // $(".dataTables_wrapper > .dt-buttons").appendTo("div.panel-heading");
    });

    /*payout_status_approved_paid_table.on( 'preDraw', function () {
        $('#table_export_btns').html(payout_status_approved_paid_table.buttons().container());
    });*/
    // payout_status_approved_paid_table.buttons().container().appendTo( $('#table_export_btns'));
    
    payout_status_rejected_table =  $('#payout_status_rejected_table').DataTable({
        language: $('#data_table_language').val(),
        order: [[2, "desc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        "ajax": {
            url : $('#base_url').val()+"/user/payout/payout_status_rejected_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
              });
            }
        },
        "columns": [
            
            { "data": "rejected_date" },
            { 
                render: function(data, type, row, meta) {
                    return $('#template_amount').html()
                        .replace('[amount]', row['amount']);

                }
            },
            { "data": "requested_date" },
        ],
        dom: '<f<t><"#df"< i><p><l>>>',
        buttons: [
            {
               "extend": 'excelHtml5',
               'title'  : '',
               'filename': `Payout Rejected `,
               "text": `<i class="fa fa-file-excel-o" >  </i> ${trans('excel')}`,
               "titleAttr": trans('excel'), 
               "action": newexportaction,
            }, {
               "extend": 'csv',
                'title': '',
               'filename': `Payout Rejected `,
               "text": `<i class="fa fa-file-text-o" >  </i>${trans('csv')}`,
               "titleAttr": trans('csv'),                               
               "action": newexportaction,
               'footer': true,
           }, 
           {
                "extend": 'print',
                'title': `Payout Rejected`,
                'message': $('#print_title').html(),
                'filename': `Payout Rejected `,
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
    });
    $('#payout_manual_filter_form .search_clear').on('click', function () {
        $('#payout_manual_filter_form .user-search-select2').val('').trigger('change');
        $('#payout_manual_filter_form #payment_method').val('bank').trigger('change');
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
    payout_status_pending_requests_table.buttons().container().addClass('hidden');
    payout_status_approved_pending_table.buttons().container().addClass('hidden');
    payout_status_approved_paid_table.buttons().container().addClass('hidden');
    payout_status_rejected_table.buttons().container().addClass('hidden');
    if (tab == 'tab_payout_release_manual') {
        loadPayoutReleaseTable();
    } else if (tab == 'tab_payout_status') {
         $('.table-payout-summary').addClass('hidden');
        if($('#payout_status_filter').val() == "pending") {
            $('.table-payout-summary-pending').removeClass('hidden');
            payout_status_pending_requests_table.draw();
            payout_status_pending_requests_table.buttons().container().toggleClass('hidden');
            payout_status_pending_requests_table.buttons().container().appendTo( $('#table_export_btns'));
        } else if($('#payout_status_filter').val() == "approved") {
            $('.table-payout-summary-approved-pending').removeClass('hidden');
            payout_status_approved_pending_table.draw();
            payout_status_approved_pending_table.buttons().container().toggleClass('hidden');
            payout_status_approved_pending_table.buttons().container().appendTo( $('#table_export_btns'));
        } else if($('#payout_status_filter').val() == "paid") {
            $('.table-payout-summary-paid').removeClass('hidden');
            payout_status_approved_paid_table.draw();
            payout_status_approved_paid_table.buttons().container().toggleClass('hidden');
            payout_status_approved_paid_table.buttons().container().appendTo( $('#table_export_btns'));
        } else if($('#payout_status_filter').val() == "rejected") {
            $('.table-payout-summary-rejected').removeClass('hidden');
            payout_status_rejected_table.draw();
            payout_status_rejected_table.buttons().container().toggleClass('hidden');
            payout_status_rejected_table.buttons().container().appendTo( $('#table_export_btns'));
        }
    } else if(tab == 'tab_process_payment') {
        process_payment_table.draw();
        process_payment_table.buttons().container().appendTo( $('#table_export_btns'));
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
        url: $('#base_url').val() + "user/payout/summary_total",
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            $('#total_amount_active_request').text(response.total_amount_active_request);
            $('#total_amount_waiting_requests').text(response.total_amount_waiting_requests);
            $('#total_amount_paid_request').text(response.total_amount_paid_request);
            $('#total_amount_rejected_requests').text(response.total_amount_rejected_requests);

            $('#minimum_payout_amount').text(response.minimum_payout_amount);
            $('#maximum_payout_amount').text(response.maximum_payout_amount);
            $('#balance_amount').text(response.balance_amount);
            $('#req_amount').text(response.req_amount);
             $("#payout_amount").val(response.available_max_payout.toFixed(2));

        }
    });
}
