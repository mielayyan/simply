{extends file=$BASE_TEMPLATE} 
{block name=$CONTENT_BLOCK}
{assign var="excel_url" value="{$BASE_URL}user/excel/create_excel_sales_report/{$product_type}?{http_build_query($ci->input->get())}"} 
{assign var="csv_url" value="{$BASE_URL}user/excel/create_csv_sales_report/{$product_type}?{http_build_query($ci->input->get())}"}
{assign var="report_name" value="{lang('sales_report')}"}{include file="user/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
{include file="user/report/header.tpl" name=""}
  <div class="panel panel-defaultng-scope">
  <div class="table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
      <tbody>
      <thead>
        <tr>
            <th>{lang('sl_no')}</th>
            <th>{lang('invoice_no')}</th>
            <th>{lang('prod_name')}</th>
            <th>{lang('user_name')}</th>
            <th>{lang('payment_method')}</th>
            <th>{lang('amount')}</th>
            <th>{lang('action')}</th>
        </tr>
        </thead>
        {assign var="i" value=0}
        {foreach from=$report_arr item=v}
        {$i=$i+1}
        <tr class="">
            <td>{counter}</td>
            <td>{$v.invoice_no}</td>
            <td>{$v.prod_id}</td>
            <td>{$v.user_id} {if $v.pending_id}<span>(pending)</span>{/if}</td>

            <td>{lang($v.payment_method)}</td>
            <td>{format_currency($v.amount)}</td>
            <td style="text-align: center;">
            <a href='#' onclick='getInvoice("{$v.invoice_no}")'><button tabindex="" type="button" name="" id="" title="{lang('view')}" value="" class="btn-link text-primary h4"><span class="fa fa-eye"></span></button></a></td>
        </tr>
        {/foreach}
      </tbody>
      {else}
            <h4 align="center">
                <font>{lang('no_data')}</font>
            </h4>
            {/if}
    </table>
  </div>
</div>
</div>
<div class="modal fade" id="myModal" role="dialog">
<div class="modal-dialog">

  <!-- Modal content-->
  <div class="modal-content">
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal">&times;</button>
      <h4 class="modal-title">{lang('Invoice')}</h4>
    </div>
    <div class="modal-body">

    
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
    </div>
  </div>
  
</div>
</div>
{/block}
{block name = "style"}
  <style>
  .modal-dialog {
      width: 786px !important;
  }
  </style>
  {/block}
{block name = "script"}
  {$smarty.block.parent}
  
   <script>
  
      function getInvoice(invoice_no){
           
           var url = '{$BASE_URL}' + "repurchase/SalesDetailsInvoice";
  
           $.ajax({
                  'url': url,
                  'type': "POST",
                  'data': {
                    invoice_no : invoice_no
                  },
                  'dataType': 'text',
                  'async': false,
                  success: function(data) {
  
                      //alert(data);
                      $('.modal-body').empty();
                      $('.modal-body').append(data);
                      // Display Modal
                      $('#myModal').modal('show');
                      
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
     
  
   </script>
  
  {/block} 