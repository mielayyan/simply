
var transactions_table = balance_table = user_earnigs_table = purchase_wallet_table = ewallet_statement_table = null;
// var selected_tab = 'tab_summary';
var selected_tab = $('input[name="tabs"]:checked').attr('id');
var loaded_tabs = [selected_tab];

$(function () {
    $('input[name="tabs"]').on('click', function () {
        selected_tab = this.id;
        if (!loaded_tabs.includes(selected_tab)) {
            loaded_tabs.push(selected_tab);
            loadTabData(selected_tab);
        }
    });

    var data_table_language = JSON.parse($('#data_table_language').val());

    loadDateRangePicker('#summary_daterangepicker');

    $('#summary_daterangepicker').on('apply.daterangepicker', function (ev, picker) {
        loadEwalletSummary(this);
    });

    loadUserDropdown();

    $('#transaction_type').select2({
        width: 'element',
        multiple: true,
        placeholder: trans('type'),
        allowClear: true,
        closeOnSelect: false,

    });

  $('#wallet_type').select2({
        width: 'element',
        multiple: true,
        placeholder: trans('wallettype'),
        allowClear: true,
        closeOnSelect: false,

    });
    // $('#transaction_category').select2({
    //     placeholder: trans('category'),
    //     allowClear: true,
    //     closeOnSelect: false,
    //     width: 'element'
    // });

    $('#user_earnings_category').select2({
        placeholder: trans('category'),
        allowClear: true,
        closeOnSelect: false,
        width: 'element',
        multiple: false
    });

    loadDateRangePicker('#transactions_daterangepicker');
    loadDateRangePicker('#user_earnigs_daterangepicker');

    transactions_table = $('#transactions_table').DataTable({
        language: data_table_language,
        order: [[3, "desc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        ajax: {
            url: $('#base_url').val() + "admin/member/cwallet_transactions",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    'user_name': $('#transactions_form select[name="user_name"]').val(),
                    'category': $('#transaction_category').val(),
                    'type': $('#transaction_type').val(),
                    'start_date': $('#transactions_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    'end_date': $('#transactions_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                });
            }
        },
        columns: [
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_member_name').html();
                    content = content.replace('[profile_image]', row['profile_image']);
                    content = content.replace('[full_name]', row['full_name']);
                    content = content.replace('[user_name]', row['user_name']);
                    return content;
                }
            },
            { data: "amount_type", orderable: false },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_credit_debit').html();
                    content = content.replace('[type]', row['type']);
                    content = content.replace('[amount]', row['amount']);
                    return content;
                }
            },
            { data: "date_added" },
        ]
    });

    $('#transactions_form .search_filter').on('click', function () {
        transactions_table.draw();
    });

    $('#transactions_form .search_clear').on('click', function () {
        $('#transactions_form select[name="user_name"]').val('').trigger('change');
        $('#transaction_category').val('').trigger('change');
        $('#transaction_type').val('').trigger('change');
        reloadDateRangePicker('#transactions_daterangepicker');

        transactions_table.draw();
    });

    balance_table = $('#balance_table').DataTable({
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
            url: $('#base_url').val() + "admin/ewallet/balance",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    'user_name': $('#balance_form select[name="user_name"]').val(),
                });
            }
        },
        columns: [
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_member_name').html();
                    content = content.replace('[profile_image]', row['profile_image']);
                    content = content.replace('[full_name]', row['full_name']);
                    content = content.replace('[user_name]', row['user_name']);
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

    $('#balance_form .search_filter').on('click', function () {
        balance_table.draw();
    });

    $('#balance_form .search_clear').on('click', function () {
        $('#balance_form select[name="user_name"]').val('').trigger('change');

        balance_table.draw();
    });

    // User Earnigs table
    user_earnigs_table = $('#user_earnings_table').DataTable({
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
            url: $('#base_url').val() + "admin/ewallet/user_earnigs",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    'user_name': $('#user_earnings_form input[name="user_name"]').val(),
                    'category': $('#user_earnings_category').val(),
                    'start_date': $('#user_earnigs_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                    'end_date': $('#user_earnigs_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
                });
            }
        },
         columns: [
            { data: "category",  orderable: false },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_amount').html();
                    content = content.replace('[amount]', row['amount']);
                    return content;
                }
            },
            {data: 'transaction_date'}
        ]
    });

    $('#user_earnings_form .search_filter').on('click', function () {
        user_earnigs_table.draw();
    });

    $('#user_earnings_form .search_clear').on('click', function () {
        $('#user_earnings_form input[name="user_name"]').val($('#user_earnings_form input[name="user_name"]').data('value')).trigger('change');
        $('#user_earnings_category').val('').trigger('change');
        reloadDateRangePicker('#user_earnigs_daterangepicker');
        user_earnigs_table.draw();
    });
    
    // ./User Earnigs table
    
    // Purchase wallet table
    purchase_wallet_table = $('#purchase_wallet_table').DataTable({
        language: data_table_language,
        order: [[3, "desc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        ajax: {
            url: $('#base_url').val() + "admin/ewallet/purchase_wallet",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    'user_name': $('#purchase_wallet_form input[name="user_name"]').val(),
                });
            }
        },
        columns: [
            { data: "description",  orderable: false },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_credit_debit').html();
                    content = content.replace('[type]', row['type']);
                    content = content.replace('[amount]', row['amount']);
                    return content;
                },
                orderable: false
            },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_amount').html();
                    content = content.replace('[amount]', row['balance']);
                    return content;
                },
                orderable: false
            },
            {data: 'transaction_date', orderable: false}
        ] 
    });

    $('#purchase_wallet_form .search_filter').on('click', function () {
        purchase_wallet_table.draw();
    });

    $('#purchase_wallet_form .search_clear').on('click', function () {
        $('#purchase_wallet_form input[name="user_name"]').val($('#purchase_wallet_form input[name="user_name"]').data('value')).trigger('change');
        purchase_wallet_table.draw();
    });
    
    // ./Purchase Wallet table 
    
    
    // Ewallet Statemnt table
    ewallet_statement_table = $('#ewallet_statement_table').DataTable({
        language: data_table_language,
        order: [[3, "asc"]],
        dom: '<f<t><"#df"< i><p><l>>>',
        lengthChange: true,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        searching: false,
        processing: true,
        serverSide: true,
        autoWidth: false,
        deferLoading: 0,
        ajax: {
            url: $('#base_url').val() + "admin/ewallet/ewallet_statement",
            type: 'GET',
            data: function (d) {
                return $.extend({}, d, {
                    'user_name': $('#ewallet_statement_form input[name="user_name"]').val(),
                });
            }
        },
        columns: [
            { data: "description",  orderable: false },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_credit_debit').html();
                    content = content.replace('[type]', row['type']);
                    content = content.replace('[amount]', row['amount']);
                    return content;
                },
                orderable: false
            },
            {
                render: function (data, type, row, meta) {
                    var content = $('#template_amount').html();
                    content = content.replace('[amount]', row['balance']);
                    return content;
                },
                orderable: false
            },
            {data: 'transaction_date', orderable: false}
        ] 
    });

    $('#ewallet_statement_form .search_filter').on('click', function () {
        ewallet_statement_table.draw();
    });

    $('#ewallet_statement_form .search_clear').on('click', function () {
        $('#ewallet_statement_form input[name="user_name"]').val($('#ewallet_statement_form input[name="user_name"]').data('value')).trigger('change');
        ewallet_statement_table.draw();
    });
    
    // ./Ewallet Statemnt table 
    // loadURLTab();
    loadTabData(selected_tab);

    $("#fund_transfer_modal").on('shown.bs.modal', function () {
        var modal = $(this);
        $.ajax({
            url: $('#base_url').val() + "admin/ewallet/fund_transfer_fee",
            type: 'GET',
            dataType: 'text',
            success: function (response) {
                modal.find('#transaction_fee').val(response);
            }
        });
    });
    
    $("#fund_transfer_modal #user_name").on('blur', function () {
        getUserBalance($(this).val(), '#fund_transfer_modal #user_balance');
    });
    
    $("#fund_transfer_form").on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: $('#base_url').val() + "admin/ewallet/fund_transfer_post",
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
                } else {
                    if (response.validation_error) {
                        setValidationErrors(form, response);
                    }
                    showErrorAlert(response.message);
                }
            }
        });
    });

    $("#fund_credit_form").on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: $('#base_url').val() + "admin/ewallet/fund_credit_post",
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                form.find('.form-group .text-danger').remove();
            },
            success: function (response) {
                if (response.status) {
                    showSuccessAlert(response.message);
                    closePopup('#fund_credit_modal');
                    loadEwalletSummaryTotal();
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

    $("#fund_debit_form").on('submit', function (e) {
        e.preventDefault();
        var form = $(this);
        $.ajax({
            url: $('#base_url').val() + "admin/ewallet/fund_debit_post",
            type: 'POST',
            data: form.serialize(),
            dataType: 'json',
            beforeSend: function() {
                form.find('.form-group .text-danger').remove();
            },
            success: function (response) {
                if (response.status) {
                    showSuccessAlert(response.message);
                    closePopup('#fund_debit_modal');
                    loadEwalletSummaryTotal();
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
});

function loadEwalletSummary(element) {
    // var startDate = $(element).data('daterangepicker').startDate.format('YYYY-MM-DD');
    // var endDate = $(element).data('daterangepicker').endDate.format('YYYY-MM-DD');
    $.ajax({
        url: $('#base_url').val() + "admin/ewallet/summary",
        type: 'GET',
        // data: {
        //     'from_date': startDate,
        //     'to_date': endDate,
        // },
        dataType: 'json',
        success: function (response) {
            credited_html = '', debited_html = '';

            response.credited.forEach(function (item) {
                credited_html += "<div class='list-group-item'><div>" + item.type + "</div><span class='badge bg-success'>" + item.amount + "</span></div>";
            });
            $("#credited_items").fadeOut(200, function () {
                $('#credited_items').html(credited_html);
            }).fadeIn(500);

            response.debited.forEach(function (item) {
                debited_html += "<div class='list-group-item'><div>" + item.type + "</div><span class='badge bg-success'>" + item.amount + "</span></div>";
            });
            $("#debited_items").fadeOut(200, function () {
                $('#debited_items').html(debited_html);
            }).fadeIn(500);

        }
    });
}

function loadEwalletSummaryTotal() {
    $.ajax({
        url: $('#base_url').val() + "admin/ewallet/summary_total",
        type: 'GET',
        dataType: 'json',
        success: function (response) {
            $('#summary_credited').text(response.credit_formated);
            $('#summary_debited').text(response.debit_formated);
            $('#summary_balance').text(response.balance_formated);
        }
    });
}

function getUserBalance(user_name, target) {
    $(target).closest('.form-group').parent().hide();
    $.ajax({
        url: $('#base_url').val() + "admin/ewallet/user_balance",
        type: 'GET',
        data: {
            'user_name': user_name,
        },
        dataType: 'json',
        success: function (response) {
            if (response.status) {
                $(target).closest('.form-group').parent().show();
                $(target).val(response.data);
            }
        }
    });
}

function loadTabData(tab) {
    if (tab == 'tab_summary') {
        loadEwalletSummary('#summary_daterangepicker');
    } else if (tab == 'tab_transactions') {
        transactions_table.draw();
    } else if (tab == 'tab_balance') {
        balance_table.draw();
    } else if(tab == 'user_earnigs_tab') {
        user_earnigs_table.draw();
    } else if(tab == 'purchase_wallet_tab') {
        purchase_wallet_table.draw();
    } else if(tab == 'ewallet_statement_tab') {
        ewallet_statement_table.draw();
    }
}

/*function loadURLTab() {
    var user_name = getUrlParameter('user_name');
    var tab = getUrlParameter('tab');

    if(tab != '') {
        $(`#${tab}`).trigger('click');
        if (tab == 'tab_summary') {
            
        } else if (tab == 'tab_transactions') {
            
        } else if (tab == 'tab_balance') {
            
        } else if(tab == 'user_earnigs_tab') {
            if(user_name != '') {
                $(`#user_earnings_form input[name="user_name"]`).val(user_name);
            }
        } else if(tab == 'purchase_wallet_tab') {
            
        } else if(tab == 'ewallet_statement_tab') {
            
        }
        loadTabData(tab);
    }
}*/