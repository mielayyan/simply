var selected_tab = 'ewallet_statement';
var loaded_tabs = ['ewallet_statement'];
var ewallet_statement_table = transfer_history_table = purchase_wallet_table = user_earnings_table = null;
var data_table_language = $('#data_table_language').val();

$('#transaction_type').select2({
    width: 'element',
    placeholder: trans('type'),

});

$('#purchase_wallet_transaction_type').select2({
    width: 'element',
    multiple: true,
    placeholder: trans('type'),
    allowClear: true,
    closeOnSelect: false,

});


$('#user_earnings_categories').select2({
    width: 'element',
    multiple: true,
    placeholder: trans('categories'),
    allowClear: true,
    closeOnSelect: false,

});

$('input[name="tabs"]').on('click', function () {
    selected_tab = this.id;
    if (!loaded_tabs.includes(selected_tab)) {
        loaded_tabs.push(selected_tab);
        loadTabData(selected_tab);
    }
});

$(function () {
    loadDateRangePicker('#ewallet_transfer_history_daterangepicker');
    loadDateRangePicker('#user_earnings_daterangepicker');

    ewallet_statement_table = $('#ewallet_statement_table').DataTable({
        language: data_table_language,
        ordering: false,
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        orderable: false,
        deferLoading: 0,
        ajax: {
            url: $('#base_url').val() + "user/ewallet/statement",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    /*'user_names': $('#epin_list_filter_form .user-search-select2').val(),
                    'epins': $('.epin-search-select2').val(),
                    'amounts': $('.amount-search-select2').val(),
                    'status': $('#epin_status').val(),*/
                });
            }
        },
        columns: [
            {data: 'description'},
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_credit_debit').html();
                    content = content.replace('[type]', row['type']);
                    content = content.replace('[amount]', row['amount']);
                    return content;
                }
            },
            {data: 'transaction_date'},
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_amount').html();
                    content = content.replace('[amount]', row['balance']);
                    return content;
                }
            },
        ]
    });

    transfer_history_table = $('#transfer_history_table').DataTable({
        language: data_table_language,
        order: [[1, "desc"]],
        // ordering: false,
        // bSort: false,
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        ajax: {
            url: $('#base_url').val() + "user/ewallet/transfer_history",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    /*'user_names': $('#epin_list_filter_form .user-search-select2').val(),
                    'epins': $('.epin-search-select2').val(),
                    'amounts': $('.amount-search-select2').val(),
                    'status': $('#epin_status').val(),*/
                    'start_date': $('#ewallet_transfer_history_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    'end_date': $('#ewallet_transfer_history_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                    'type': $('#transaction_type').val(),
                });
            }
        },
        columns: [
            {data: 'description', 'orderable': false},
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_credit_debit').html();
                    if(row['type'] == "debit") {
                        content = content.replace('[type]', 'debit');
                    } else {
                        content = content.replace('[type]', 'credit');
                    }
                    content = content.replace('[amount]', row['amount']);
                    return content;
                }
            },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_credit_debit').html();
                    if(row['type'] != "debit") {
                        return row['transaction_fee'];
                    }
                    content = content.replace('[type]', 'debit');
                    content = content.replace('[amount]', row['transaction_fee']);
                    return content;
                }  
            },
            // {data: 'transaction_fee'},
            // {data: 'transfer_type'},
            {data: 'date'}
        ]
    });

    purchase_wallet_table = $('#purchase_wallet_table').DataTable({
        language: data_table_language,
        order: [[1, "desc"]],
        ordering: false,
        // ordering: false,
        // bSort: false,
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        ajax: {
            url: $('#base_url').val() + "user/ewallet/purchase_wallet_table",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    /*'user_names': $('#epin_list_filter_form .user-search-select2').val(),
                    'epins': $('.epin-search-select2').val(),
                    'amounts': $('.amount-search-select2').val(),
                    'status': $('#epin_status').val(),*/
                });
            }
        },
        columns: [
            {data: 'description'},
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_credit_debit').html();
                    content = content.replace('[type]', row['type']);
                    content = content.replace('[amount]', row['amount']);
                    return content;
                }
            },
            {data: 'date'},
            // {data: 'balance'}
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_balance').html();
                    content = content.replace('[balance]', row['balance']);
                    return content;
                }
            }
        ]
    });
    var date_time = new Date().toLocaleString().replace(/[^a-zA-Z0-9]+/g,'_');
    // var file_name = `user_earnigs_`;
    user_earnings_table = $('#user_earnings_table').DataTable({
        language: data_table_language,
        order: [[1, "desc"]],
        ordering: false,
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        // autoWidth: false,
        deferLoading: 0,
        ajax: {
            url: $('#base_url').val() + "user/ewallet/user_earnings_table",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    'start_date': $('#user_earnings_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    'end_date': $('#user_earnings_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                    'categories': $('#user_earnings_categories').val(),
                });
            }
        },
        columns: [
            {data: 'category', 'orderable': false},
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_credit_debit').html();
                    content = content.replace('[type]', 'credit');
                    content = content.replace('[amount]', format_currency(row['total_amount']));
                    return content;
                }
            },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_amount').html();
                    content = content.replace('[type]', 'credit');
                    content = content.replace('[amount]', format_currency(row['tax']));
                    return content;
                }
            },
            
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_amount').html();
                    content = content.replace('[type]', 'credit');
                    content = content.replace('[amount]', format_currency(row['service_charge']));
                    return content;
                }
            },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_amount').html();
                    content = content.replace('[type]', 'credit');
                    content = content.replace('[amount]', format_currency(row['amount_payable']));
                    return content;
                }
            },
            {data: 'transaction_date'},
        ],
    
        footerCallback: function () {},
        dom: '<f<t><"#df"< i><p><l>>>',
        // dom: 'Bfrtip',
        buttons: [
            {
               "extend": 'excelHtml5',
               'title'  : '',
               'filename': `User Earnigs Report `,
               "text": `<i class="fa fa-file-excel-o" >  </i> ${trans('excel')}`,
               "titleAttr": trans('excel'), 
               "action": newexportaction,
            }, {
               "extend": 'csv',
                'title': '',
               'filename': `User Earnigs Report `,
               "text": `<i class="fa fa-file-text-o" >  </i>${trans('csv')}`,
               "titleAttr": trans('csv'),                               
               "action": newexportaction,
               'footer': true,
           }, 
           {
                "extend": 'print',
                'title': `User Earnigs Report`,
                'message': $('#print_title').html(),
                'filename': `User Earnigs Report `,
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
        ]
    });
    user_earnings_table.buttons().container().appendTo( $('#user_earnings_report_btn'));

    loadTabData(selected_tab);
 });

$('#ewallet_transfer_history_filter_form .search_filter').on('click', function () {
    transfer_history_table.draw();
});

$('#ewallet_transfer_history_filter_form .search_clear').on('click', function () {
    $('#ewallet_transfer_history_filter_form select[name="transaction_type"]').val('').trigger('change');
    reloadDateRangePicker('#ewallet_transfer_history_daterangepicker');
    transfer_history_table.draw();
});

$('#user_earnings_filter_form .search_filter').on('click', function () {
        user_earnings_table.draw();
});

$('#user_earnings_filter_form .search_clear').on('click', function () {
    $('#user_earnings_filter_form select[name="user_earnings_categories"]').val('').trigger('change');
    reloadDateRangePicker('#user_earnings_daterangepicker');
    user_earnings_table.draw();
});

$("#fund_transfer_form").on('submit', function (e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: $('#base_url').val() + "user/ewallet/fund_transfer_post",
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        beforeSend: function() {
            form.find('.form-group .text-danger').remove();
        },
        success: function (response) {
            if (response.status) {
                showSuccessAlert(response.message);
                closePopup('#fund_transfer_modal');
                loadEwalletSummaryTotal();
                loaded_tabs = [selected_tab];
                loadTabData(selected_tab);
                ewallet_statement_table.draw();
                transfer_history_table.draw();
                purchase_wallet_table.draw();
                user_earnings_table.draw();
            } else {
                if (response.validation_error) {
                    setValidationErrors(form, response);
                }
                showErrorAlert(response.message);
            }
        }
    });
});

$("#fund_transfer_modal").on('show.bs.modal', function(){
    loadEwalletSummaryTotal();
});

function loadTabData(tab) {
    purchase_wallet_table.draw();
    if (tab == 'ewallet_statement') {
        ewallet_statement_table.draw();
    } else if (tab == 'transfer_history') {
        transfer_history_table.draw();
    } else if (tab == 'purchase_wallet') {
        purchase_wallet_table.draw();
    } else if (tab == 'user_earnings') {
        user_earnings_table.draw();
    }
   

}


function loadEwalletSummaryTotal() {
    $.ajax({
        url: $('#base_url').val() + "user/ewallet/summary_total",
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            $('#summary_credited').text(response.credited);
            $('#summary_debited').text(response.debited);
            $('#summary_balance').text(response.balance);
            $('#commission_earned').text(response.commission_earned);
            $('#purchase_wallet_tile').text(response.purchase_wallet);
            $('#trans_fee').val(response.trans_fee);
            $('#avb_amount').val(response.balamount);
            $('#bal').val(response.balamount);
        }
    });
}
function loadEwalletSummaryTotal1() {
    $.ajax({
        url: $('#base_url').val() + "user/ewallet/summary_total",
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            $('#summary_credited').text(response.credited);
            $('#summary_debited').text(response.debited);
            $('#summary_balance').text(response.balance);
            $('#commission_earned').text(response.commission_earned);
            $('#purchase_wallet_tile').text(response.purchase_wallet);
            $('#trans_fee').val(response.trans_fee);
            $('#avb_amount').val(response.balamount);
            $('#bal').val(response.balamount);
        }
    });
}

$("#fund_transfer_form1").on('submit', function (e) {
    e.preventDefault();
    var form = $(this);
    $.ajax({
        url: $('#base_url').val() + "user/ewallet/fund_transfer_to_agent_post",
        type: 'POST',
        data: form.serialize(),
        dataType: 'json',
        beforeSend: function() {
            form.find('.form-group .text-danger').remove();
        },
        success: function (response) {
            if (response.status) {
                showSuccessAlert(response.message);
                closePopup('#fund_transfer_modal1');
                loadEwalletSummaryTotal1();
                loaded_tabs = [selected_tab];
                loadTabData(selected_tab);
                ewallet_statement_table.draw();
                transfer_history_table.draw();
                purchase_wallet_table.draw();
                user_earnings_table.draw();
            } else {
                if (response.validation_error) {
                    setValidationErrors(form, response);
                }
                showErrorAlert(response.message);
            }
        }
    });
});

$("#fund_transfer_modal1").on('show.bs.modal', function(){
    loadEwalletSummaryTotal1();
});


