{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}{assign var="excel_url" value="{$BASE_URL}user/excel/create_excel_payout_pending_report?from_date={$from_date}&&to_date={$to_date}"} {assign var="csv_url" value="{$BASE_URL}user/excel/create_csv_payout_pending_report?from_date={$from_date}&&to_date={$to_date}"}
{assign var="report_name" value="{lang('member_wise_payout_report')}"}{include file="user/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
{include file="user/report/header.tpl" name=""}
  <h4 align="center"><b>{$dateRangeString}</b></h4>
  <div class="panel panel-default ng-scope">
  <div class=" table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{assign var="j" value=0}
      {if $count >=1}
      <tbody>
      <thead>
        <tr>
            <th>{lang('sl_no')}</th>
            <th>{lang('total_amount')}</th>
            {if $payoutFeeDisplay == "yes"}<th>{lang('payout_fee')}</th>{/if}
            <th>{lang('Date')}</th>
        </tr>
      </thead>
      {assign var="totalPayout" value=0}
      {assign var="totalPayoutFee" value=0}
      
      {foreach from=$binary_details item=v}
        {$j=$j+1}
        <tr>
            <td> {$ci->input->get('offset')+$j}</td>
            <td>{format_currency($v.paid_amount)}</td>
            {if $payoutFeeDisplay == "yes"}<td>{format_currency($v.payout_fee)}</td>{$totalPayoutFee = $totalPayoutFee + $v.payout_fee}{/if}
            <td>{date("d M Y - h:i:s A", strtotime($v.paid_date))}</td>
            {$totalPayout = $totalPayout + $v.paid_amount}
        </tr>
        {/foreach}
        
        <tr>
            <td> <b>{lang('total_amount')}</b></td>
            <td><b>{format_currency($totalPayout)}</b></td>
            {if $payoutFeeDisplay == "yes"}<th>{format_currency($totalPayoutFee)}</th>{/if}
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
{/block}