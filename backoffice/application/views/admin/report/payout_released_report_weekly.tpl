{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}{assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_payout_released_report_weekly?from_date={$from_date}&&to_date={$to_date}"} {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_payout_released_report_weekly?from_date={$from_date}&&to_date={$to_date}"}
{assign var="report_name" value="{lang('payout_release_report')}"}
<div class="panel panel-default">
  <div class="panel-body">
  {* <legend><span class="fieldset-legend">{lang('payout_release_reports')}</span></legend> *}
    {form_open('admin/payout_release_report','role="form" class="" method="get" name="searchform2" id="searchform2" onsubmit="return validation()"')}
      <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label class="" for="status">{lang('status')}</label>
                <select name="status" id="status" class="form-control">
                    <option value="released">{lang('released')}</option>
                    <option value="pending">{lang('pending')}</option>
                </select>
            </div>
      </div>
      <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label class="" for="daterange">{lang('daterange')}</label>
                <select name="daterange" id="daterange" class="form-control">
                    <option value="all" {if $daterange == 'all'} selected {/if}>{lang('overall')}</option>
                    <option value="today" {if $daterange == 'today'} selected {/if}>{lang('today')}</option>
                    <option value="month" {if $daterange == 'month'} selected {/if}>{lang('this_month')}</option>
                    <option value="year" {if $daterange == 'year'} selected {/if}>{lang('this_year')}</option>
                    <option value="custom" {if $daterange == 'custom'} selected {/if}>{lang('custom')}</option>
                </select>
            </div>
        </div>
        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label>{lang('from_date')}</label>
                <input autocomplete="off" class="form-control date-picker custom-date" name="from_date" id="from_date" type="text" value="{$from_date}">
            </div>
        </div>
        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label>{lang('to_date')}</label>
                <input autocomplete="off" class="form-control date-picker custom-date" name="to_date" id="to_date" type="text" value="{$to_date}">
            </div>
        </div>
        <div class="col-sm-3 padding_both_small">
        <div class="form-group credit_debit_button">
            <button class="btn btn-primary" name="commision" type="submit" value="{lang('submit')}">
                {lang('submit')}</button>
        </div>
        </div>
    {form_close()}
  </div>
</div>

{* <div class="panel panel-default">
  <div class="panel-body">
  <legend><span class="fieldset-legend">{lang('payout_pending_report')}</span></legend>
    {form_open('admin/payout_pending_report_weekly','role="form" class="" method="get" name="searchform1" id="searchform1"  ')}
      <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label class="" for="daterange">{lang('daterange')}</label>
                <select name="daterange" id="daterange" class="form-control">
                    <option value="all">{lang('overall')}</option>
                    <option value="today">{lang('today')}</option>
                    <option value="month">{lang('this_month')}</option>
                    <option value="year">{lang('this_year')}</option>
                    <option value="custom">{lang('custom')}</option>
                </select>
            </div>
        </div>
        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label>{lang('from_date')}</label>
                <input autocomplete="off" class="form-control date-picker custom-date" name="from_date" id="from_date" type="text" value="">
            </div>
        </div>
        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label>{lang('to_date')}</label>
                <input autocomplete="off" class="form-control date-picker custom-date" name="to_date" id="to_date" type="text" value="">
            </div>
        </div>
        <div class="col-sm-3 padding_both_small">
        <div class="form-group credit_debit_button">
            <button class="btn btn-primary" name="commision" type="submit" value="{lang('submit')}">
                {lang('submit')}</button>
        </div>
        </div>
    {form_close()}
  </div>
</div> *}
{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
{include file="admin/report/header.tpl" name=""}
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
            <th>{lang('member_name')}</th>
            <th>{lang('total_amount')}</th>
            {if $payoutFeeDisplay == "yes"}<th>{lang('payout_fee')}</th>{/if}
            <th>{lang('Date')}</th>
                <!-- mark as paid -->
            <th>{lang('status')}</th>
                <!-- ends -->
        </tr>
      </thead>
      {foreach from=$binary_details item=v}
            {$j=$j+1}
            <tr >
                <td> {$ci->input->get('offset')+$j} </td>
                <td>
                  <!--<a href="./payout/payout_invoice/{$v.paid_id}" >{"PR000"}{$v.paid_id}
                  </a>-->
                  <a href='#' onclick='getInvoice("{$v.paid_id}")'>{"PR000"}{$v.paid_id}</a> 

                </td>
                <td>
                    {if $v.delete_status == "active"}
                      {$v.full_name} ({$v.paid_user_name})
                    {else}
                      {$v.paid_user_name}
                    {/if}
                </td>
                <td>{format_currency($v.paid_amount)}</td>
                {if $payoutFeeDisplay == "yes"}<td>{format_currency($v.payout_fee)}</td>{/if}
                <td>{date("d M Y - h:i:s A", strtotime($v.paid_date))}</td>
                    <!-- mark as paid -->
                    <td>{if $v.paid_status == 'yes'}{lang('paid')}{else}{lang('not_paid')}{/if}</td>
                    <!-- ends -->
            </tr>
        {/foreach}
        <tr>
            <td colspan="3" class="text-right"> <b>{lang('total_amount')}</b></td></th>
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
