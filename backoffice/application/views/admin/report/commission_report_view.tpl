{extends file=$BASE_TEMPLATE} 
{block name=$CONTENT_BLOCK}

{assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_commission_report/{$user_name}?from_date={$from_date}&&to_date={$to_date}&&type={$type}"} 
{assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_commission_report/{$user_name}?from_date={$from_date}&&to_date={$to_date}&&type={$type}"}
{assign var="report_name" value="{lang('commission_report')}"}
{$total=0}{$tot_pay=0}
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/commission_report','role="form" class="" method="get" name="commision_form" id="commision_form" onsubmit="return validation()"')}
        <div class="col-sm-3 padding_both">
        <div class="form-group">
            <label>{lang('user_name')}</label>
            <input type="text" class="form-control user_autolist" id="user_name" name="user_name" autocomplete="Off" value="{$user_name}">
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
        <div class="form-group">
            <label>{lang('commission_type')}</label>
            <select multiple name="amount_type[]" id="amount_type" class="form-control select2">
            {foreach from=$commission_types item=v}
                <option value="{$v}">{lang($v)}</option>
            {/foreach}
            </select>
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
{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
    {include file="admin/report/header.tpl" name=""}
    {if $filterUserString != ""}<h4 align="center"><b>{$filterUserString}</b></h4>{/if}
    {if $dateRangeString != ""}<h4 align="center"><b>{$dateRangeString}</b></h4>{/if}
    {if $typeFilterString != ""}<h4 align="center"><b>{$typeFilterString}</b></h4>{/if}
  <div class="panel panel-default  ng-scope">
  <div class=" table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
      <tbody>
      <thead>
        <tr>
            <th>{lang('sl_no')}</th>
            {if $filterUserString == ""}<th>{lang('member_name')}</th>{/if}
            <th>{lang('amount_type')}</th>
            <th>{lang('total_amount')}</th>
            {if $showTDS == "yes"}<th>{lang('tds')}</th>{/if}
            {if $showServiceCharge == "yes"}<th>{lang('service_charge')}</th>{/if}
            {if $showAmountPayable == "yes"}<th>{lang('amount_payable')}</th>{/if}
            <th style="width:20%">{lang('date')}</th>
        </tr>
        </thead>
        {assign var="i" value=1}
        {foreach from=$details item=v}
            <tr>
                <td>{$ci->input->get('offset')+$i}</td>
                {if $filterUserString == ""}
                    <td>
                        {if $v.delete_status == "active"}
                            {$v.full_name}({$v.user_name})
                        {else}
                            {$v.user_name}
                        {/if}
                    </td>
                {/if}
                <td>
                    {if $v.view_amt == "Board Commission"}
                        {if {$MODULE_STATUS['table_status']} eq 'yes' && {$MODULE_STATUS['mlm_plan']} eq 'Board'}
                            {lang('table_commission')}
                        {else}
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
            {assign var="colspan" value=3}
            {if $filterUserString != ""}{$colspan = $colspan - 1}{/if}

            <th colspan="{$colspan}" style="text-align:center;">{lang('total')}</th>
            <th>{format_currency($total_amount)}</th>
            {if $showTDS == "yes"}<th>{format_currency($total_tds)}</th>{/if}
            {if $showServiceCharge == "yes"}<th>{format_currency($total_service_charge)}</th>{/if}
            {if $showAmountPayable == "yes"}<th>{format_currency($total_amount_payable)}</th>{/if}
            <th></th>

        </tr>
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
