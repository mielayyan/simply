{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}{assign var="excel_url" value="{$BASE_URL}user/excel/create_excel_payout_released_report_weekly?from_date={$from_date}&&to_date={$to_date}"} {assign var="csv_url" value="{$BASE_URL}user/excel/create_csv_payout_released_report_weekly?from_date={$from_date}&&to_date={$to_date}"}
{assign var="report_name" value="{lang('payout_release_report')}"}{include file="user/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
{include file="user/report/header.tpl" name=""}
  <h4 align="center"><b>{$dateRangeString}</b></h4>
  <div class="panel panel-default ng-scope">
  <div class=" table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{assign var="j" value="0"}
      {if $count >=1}
      <tbody>
      <tbody>
      <thead>
        <tr>
            <th>{lang('sl_no')}</th>
            <th>{lang('invoice_no')}</th>
            <th>{lang('total_amount')}</th>
            {if $payoutFeeDisplay == "yes"}<th>{lang('payout_fee')}</th>{/if}
            <th>{lang('Date')}</th>
            <th>{lang('status')}</th>
        </tr>
      </thead>
      {assign var="totalPayout" value=0}
      {assign var="totalPayoutFee" value=0}
      {foreach from=$binary_details item=v}
            {$j=$j+1}
            <tr >
                <td> {$ci->input->get('offset')+$j} </td>
                <td>
                  <!--<a href="./payout/payout_invoice/{$v.paid_id}" target="_blank">{"PR000"}{$v.paid_id}
                  </a>-->
                  <a href='#' onclick='getInvoice("{$v.paid_id}")'>{"PR000"}{$v.paid_id}</a> 

                </td>
                <td>{format_currency($v.paid_amount)}</td>
                {if $payoutFeeDisplay == "yes"}<td>{format_currency($v.payout_fee)}</td>{$totalPayoutFee=$totalPayoutFee+$v.payout_fee}{/if}
                <td>{date("d M Y - h:i:s A", strtotime($v.paid_date))}</td>
                <td>{if $v.paid_status == 'yes'}{lang('paid')}{else}{lang('not_paid')}{/if}</td>
            </tr>
            {$totalPayout=$totalPayout+$v.paid_amount}
        {/foreach}
        <tr>
            <td colspan="2" class="text-right"> <b>{lang('total_amount')}</b></td></th>
            <td><b>{format_currency($totalPayout)}</b></td>
            {if $payoutFeeDisplay == "yes"}<th>{format_currency($totalPayoutFee)}</th>{/if}
            <td></td>
            <td></td>
        </tr>
        </tbody>
      {else}
        <h4 align="center">
            <font>{lang('no_data')}</font>
        </h4>
        {/if}
    </table>
    </div>
  </div>
  {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
</div>
<div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{lang('Invoice')}</h4>
        </div>
        <div class="modal-body invoice_shopping">

        
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

 {block name=script}
  {$smarty.block.parent}
    <script src="{$PUBLIC_URL}/javascript/pending_repurchase.js"></script>
    <script>

    function getInvoice(invoice_id){
         
         var url = '{$BASE_URL}' + "repurchase/getPayoutInvoiceDetails";

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
                    $('.invoice_shopping').empty();
                    $('.invoice_shopping').append(data);
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