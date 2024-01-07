
var transactions_table = null;
var selected_tab = 'tab_summary';
var loaded_tabs = ['tab_summary'];

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
        loadBusinessSummary(this);
    });

    loadUserDropdown();

    $('#transaction_type').select2({
        width: 'element',
        multiple: true,
        placeholder: trans('type'),
        allowClear: true,
        closeOnSelect: false,

    });

    $('#transaction_category').select2({
        placeholder: trans('category'),
        allowClear: true,
        closeOnSelect: false,
        width: 'element'
    });

    loadDateRangePicker('#transactions_daterangepicker');

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
            url: $('#base_url').val() + "admin/business/transactions",
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
                    var content = $('#template_type').html();
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
    
});

function loadBusinessSummary(element) {
    var startDate = $(element).data('daterangepicker').startDate.format('YYYY-MM-DD');
    var endDate = $(element).data('daterangepicker').endDate.format('YYYY-MM-DD');
    $.ajax({
        url: $('#base_url').val() + "admin/business/summary",
        type: 'GET',
        data: {
            'from_date': startDate,
            'to_date': endDate,
        },
        dataType: 'json',
        success: function (response) {
            income_html = '', bonus_html = '', paid_html = '', pending_html = '';

            response.income.forEach(function (item) {
                income_html += "<div class='list-group-item'><div>" + item.type + "</div><span class='badge bg-success'>" + item.amount + "</span></div>";
            });
            $("#income_items").fadeOut(200, function () {
                $('#income_items').html(income_html);
            }).fadeIn(500);
            
            response.bonus.forEach(function (item) {
                bonus_html += "<div class='list-group-item'><div>" + item.type + "</div><span class='badge bg-success'>" + item.amount + "</span></div>";
            });
            $("#bonus_items").fadeOut(200, function () {
                $('#bonus_items').html(bonus_html);
            }).fadeIn(500);
            
            response.paid.forEach(function (item) {
                paid_html += "<div class='list-group-item'><div>" + item.type + "</div><span class='badge bg-success'>" + item.amount + "</span></div>";
            });
            $("#paid_items").fadeOut(200, function () {
                $('#paid_items').html(paid_html);
            }).fadeIn(500);
            
            response.pending.forEach(function (item) {
                pending_html += "<div class='list-group-item'><div>" + item.type + "</div><span class='badge bg-success'>" + item.amount + "</span></div>";
            });
            $("#pending_items").fadeOut(200, function () {
                $('#pending_items').html(pending_html);
            }).fadeIn(500);
            
        }
    });
}

function loadTabData(tab) {
    if (tab == 'tab_summary') {
        loadBusinessSummary('#summary_daterangepicker');
    }
    else if (tab == 'tab_transactions') {
        transactions_table.draw();
    }
}