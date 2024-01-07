{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}{assign var="report_name" value="{lang('user_payout_report')}"} {assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_weekly_payout_report/?from_date={$from_date}&&to_date={$to_date}&&user_name={$user_name}"} {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_weekly_payout_report/?from_date={$from_date}&&to_date={$to_date}&&user_name={$user_name}"}
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/total_payout_report','role="form" class="" method="get" name="weekly_payout" id="weekly_payout" onsubmit="return validation()"')}

        <div class="col-sm-2 padding_both">
            <div class="form-group">
                <label>{lang('user_name')}</label>
                <input type="text" class="form-control user_autolist" id="user_name" name="user_name" autocomplete="Off">
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
                <button class="btn btn-primary" name="weekdate" type="submit" value="{lang('submit')}">
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
    <div class="panel panel-default  ng-scope">
    <div class=" table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
            <tbody>
                <thead>
                    <tr>
                        <th>{lang('sl_no')}</th>
                        {if $filterUserString == ""}<th>{lang('member_name')}</th>{/if}
                        {* <th>{lang('address')}</th>
                        <th>{lang('bank')}</th>
                        <th>{lang('account_number')}</th> *}
                        <th>{lang('total_amount')}</th>
                        {if $showTDS == "yes"}<th>{lang('tds')}</th>{/if}
                        {if $showServiceCharge == "yes"}<th>{lang('service_charge')}</th>{/if}
                        {if $showAmountPayable == "yes"}<th>{lang('amount_payable')}</th>{/if}
                    </tr>
                </thead>
                {assign var="i" value=1} {foreach from=$weekly_payout item=v} 
                  
                <tr>
                    <td>{$ci->input->get('offset')+$i}</td>
                    {if $filterUserString == ""}<td>{$v.full_name} ({$v.user_name})</td>{/if}
                    {* <td>{$v.user_address}</td>
                    <td>{$v.user_bank}</td>
                    <td>{$v.acc_number}</td> *}
                    <td>{format_currency($v.total_amount)}</td>
                    {if $showTDS == "yes"}<td>{format_currency($v.tds)}</td>{/if}
                    {if $showServiceCharge == "yes"}<td>{format_currency($v.service_charge)}</td>{/if}
                    {if $showAmountPayable == "yes"}<td>{format_currency($v.amount_payable)}</td>{/if}
                </tr>
                {$i = $i+1}
                {/foreach}
                <tr>
                    {assign var="colspan" value=2}
                    {if $filterUserString != ""}{$colspan = $colspan - 1}{/if}

                    <th colspan="{$colspan}" style="text-align:center;">{lang('total')}</th>
                    <th>{format_currency($total_amount)}</th>
                    {if $showTDS == "yes"}<th>{format_currency($total_tds)}</th>{/if}
                    {if $showServiceCharge == "yes"}<th>{format_currency($total_service_charge)}</th>{/if}
                    {if $showAmountPayable == "yes"}<th>{format_currency($total_amount_payable)}</th>{/if}

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