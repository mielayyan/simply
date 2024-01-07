{extends file=$BASE_TEMPLATE} 
{block name=$CONTENT_BLOCK}
{* {http_build_query($ci->input->get())} *}
{assign var="excel_url" value="{$BASE_URL}user/excel/create_excel_commission_report/{$user_name}?from_date={$from_date}&&to_date={$to_date}&&type={$type}"}
{assign var="csv_url" value="{$BASE_URL}user/excel/create_csv_commission_report/{$user_name}?from_date={$from_date}&&to_date={$to_date}&&type={$type}"}
{assign var="report_name" value="{lang('commission_report')}"}{$total=0}{$tot_pay=0}{include file="user/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
{include file="user/report/header.tpl" name=""}
    {if $dateRangeString != ""}<h4 align="center"><b>{$dateRangeString}</b></h4>{/if}
    {if $typeFilterString != ""}<h4 align="center"><b>{$typeFilterString}</b></h4>{/if}
  <div class="panel panel-default  ng-scope">
  <div class=" table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
      <tbody>
      <thead>
        <tr>
          <th>{lang('sl_no')}</th>
            <th>{lang('amount_type')}</th>
            <th>{lang('total_amount')}</th>
            {if $showTDS == "yes"}<th>{lang('tds')}</th>{/if}
            {if $showServiceCharge == "yes"}<th>{lang('service_charge')}</th>{/if}
            {if $showAmountPayable == "yes"}<th>{lang('amount_payable')}</th>{/if}
            <th>{lang('date')}</th>
        </tr>
        </thead>
        {assign var="i" value=1}
        {foreach from=$details item=v}
            <tr>
                <td>{$ci->input->get('offset')+$i}</td>
                <td>
                    {if $v.view_amt == "Board Commission"}
                        {if {$MODULE_STATUS['table_status']} eq 'yes' && {$MODULE_STATUS['mlm_plan']} eq 'Board'}
                            {lang('table_commission')}
                        {else}
                            {* {$v.view_amt} *}
                            {lang($v.amount_type)}
                        {/if}
                        {elseif $v.amount_type == 'daily_investment'}
                            {lang('daily_investment')}
                    {elseif $v.amount_type == 'purchase_donation'}
                        {lang('purchase_donation')}
                    {else}
                        {lang($v.amount_type)}
                    {/if}
                </td>
                <td>{format_currency($v.total_amount)}{$total=$total+$v.total_amount}</td>
                {if $showTDS == "yes"}<td>{format_currency($v.tds)}</td>{/if}
                {if $showServiceCharge == "yes"}<td>{format_currency($v.service_charge)}</td>{/if}
                {if $showAmountPayable == "yes"}<td>{format_currency($v.amount_payable)}{$tot_pay=$tot_pay+$v.amount_payable}</td>{/if}
                <td>{date('d M Y - h:i:s A', strtotime($v.date))}</td>
            </tr>
            {$i=$i+1}
        {/foreach}
          <tr>
            {assign var="colspan" value=2}
            <th colspan="{$colspan}" style="text-align:center;">{lang('total')}</th>
            <th>{format_currency($total_amount)}</th>
            {if $showTDS == "yes"}<th>{format_currency($total_tds)}</th>{/if}
            {if $showServiceCharge == "yes"}<th>{format_currency($total_service_charge)}</th>{/if}
            {if $showAmountPayable == "yes"}<th>{format_currency($total_amount_payable)}</th>{/if}
            <th></th>
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
