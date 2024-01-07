{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}
{assign var="excel_url" value="{$BASE_URL}user/excel/create_excel_repurchase_report?from_date={$from_date}&&to_date={$to_date}"}
{assign var="csv_url" value="{$BASE_URL}user/excel/create_csv_repurchase_report?from_date={$from_date}&&to_date={$to_date}"}
{assign var="report_name" value="{lang('repurchase_report')}"}
{include file="user/report/report_nav.tpl"
name=""}
<div id="print_area" class="img panel-body panel">
    {include file="user/report/header.tpl" name=""}
  {if $dateRangeString != ""}<h4 align="center"><b>{$dateRangeString}</b></h4>{/if}
    <div class="panel panel-default table-responsive ng-scope">
        <table st-table="rowCollectionBasic" class="table table-striped">{if $count >=1}
            <tbody>
                <thead>
                <tr class="th" align="center">
                    <th>{lang('slno')}</th>
                    <th>{lang('invoice_no')}</th>
                    <th>{lang('total_amount')}</th>
                    <th>{lang("payment_method")}</th>
                    <th>{lang('purchase_date')}</th>
                </tr>
          </thead>
                {assign var="i" value=0} {assign var="total_quantity" value=0} {assign var="total_amount" value=0} {foreach from=$purcahse_details item=v}
                <tr>
                    <td>{counter}</td>
                    <td>
                        <!--<a href="../repurchase/repurchase_invoice/{$v.encrypt_order_id}" target="_blank">
                            {$v.invoice_no}
                        </a>-->
    
                        <a href='#' onclick='getInvoice("{$v.encrypt_order_id}")'>{$v.invoice_no}</a>
                    </td>
                    <td style="text-align: center;">{format_currency($v.amount)}</td>
                    <td>{lang($v.payment_method)}</td>
                    <td>{date("d M Y - h:i:s A", strtotime($v.order_date))}</td>
                    {$total_amount = $total_amount + $v.amount}
                </tr>
                {/foreach}
                <tr>
                    <td colspan="2" class="text-right"> <b>{lang('total_amount')}</b></td></th>
                    <td style="text-align: center;"><b>{format_currency($total_amount)}</b></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
            {else}
            <h4>
                <center>{lang('no_data')}</center>
            </h4>
            {/if}
        </table>
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

 <script>

    function getInvoice(invoice_id){
         
         var url = '{$BASE_URL}' + "repurchase/getRepurchaseInvoiceDetails";

         $.ajax({
                'url': url,
                'type': "POST",
                'data': {
                  invoice_id : invoice_id
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