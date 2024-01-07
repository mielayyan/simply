
var purchase_report_table= null;
loadDateRangePicker('#repurchase_daterangepicker');
  // purchase_report_table
  purchase_report_table =  $('#purchase_report_table').DataTable({
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
          url : $('#base_url').val()+"/user/report/repurchase_report",
          type : 'GET',
          "data": function ( d ) {
              return $.extend( {}, d, {
                'start_date': $('#repurchase_daterangepicker').data('daterangepicker').startDate.format('YYYY-MM-DD'),
                'end_date': $('#repurchase_daterangepicker').data('daterangepicker').endDate.format('YYYY-MM-DD'),
              });
          }
      },
      "columns": [
          { "data": "invoice_no" },
          { 
            render: function(data, type, row, meta) {
              return $('#template_amount').html().replace('[amount]', row['total_amount'])
            }
          },
          { "data": "payment_method" },
          { "data": "purchase_date" },
      ],
      // dom: 'Bfrtip',
      buttons: [
            {
               "extend": 'excelHtml5',
               'title'  : '',
               'filename': `Purchase Report`,
               "text": `<i class="fa fa-file-excel-o" >  </i> ${trans('excel')}`,
               "titleAttr": trans('excel'), 
               "action": newexportaction,
            }, {
               "extend": 'csv',
                'title': '',
               'filename': `Purchase Report`,
               "text": `<i class="fa fa-file-text-o" >  </i>${trans('csv')}`,
               "titleAttr": trans('csv'),                               
               "action": newexportaction,
               'footer': true,
           }, 
           {
                "extend": 'print',
                'title': `Purchase Report`,
                'message': $('#print_title').html(),
                'filename': `Purchase Report`,
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
  purchase_report_table.buttons().container().appendTo( $('#user_purchase_report_btn'));
  purchase_report_table.draw();

  $('#repurchase_report_filter_form').on('submit', function() {
    purchase_report_table.draw();
  });

  $('#repurchase_report_filter_form .search_clear').on('click', function () {
    reloadDateRangePicker('#repurchase_daterangepicker');
    purchase_report_table.draw();
  });

