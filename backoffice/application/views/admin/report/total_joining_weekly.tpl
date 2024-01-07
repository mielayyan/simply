{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK} {assign var="report_name" value="{lang('user_joining_report')}"} {assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_joining_report_weekly/?from_date={$from_date}&&to_date={$to_date}"} {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_joining_report_weekly/?from_date={$from_date}&&to_date={$to_date}"}

<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/total_joining_weekly','role="form" class="" method="get" name="daily" id="daily" target="__blank"  onsubmit="return validation()"')} {include file="layout/error_box.tpl"}
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
                <button class="btn btn-primary" name="dailydate" type="submit" value="{lang('submit')}"> {lang('submit')} </button>
            </div>
        </div>
        {form_close()}
    </div>
</div>
{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
{include file="admin/report/header.tpl" name=""}
  <h4 align="center"><b>{$dateRangeString}</b></h4>
  <div class="panel panel-default ng-scope">
  <div class="table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
      <tbody>
      <thead>
        <tr class="th">
            <th>{lang('sl_no')}</th>
            <th>{lang('member_name')}</th>
            <th>{lang('Country')}</th>
            <th>{lang('sponsor')}</th>
            <th>{lang('package')}</th>
            {if $showRegFee == 'yes'}<th>{lang('registration_fee')}</th>{/if}
            <th>{lang('payment_method')}</th>
            <th>{lang('enrollment_date')}</th>
        </tr>
      </thead>
      {assign var="i" value=0}
      {foreach from=$week_join item=v}
      {$i=$i+1}
      {* {if $v.active=="yes"}
            {assign var="stat" value=lang('active')}
        {else}
            {assign var="stat" value=lang('blocked')}
       {/if} *}
        <tr>
            <td>{$ci->input->get('offset')+$i}</td>
            <td>
                  {if $v.delete_status == "active"}
                    {$v.user_full_name} ({$v.user_name})
                  {else}
                    {$v.user_name}
                  {/if}
            </td>
            <td>{$v.country}</td>
            <td>{if $v.sponsor_name}{$v.sponsor_name}{else}{lang('na')}{/if}</td>
            <td>{$v.package_name}{if $v.package_name != lang('na')}({format_currency($v.package_amount)}){/if}</td>
            {if $showRegFee == 'yes'}<td>{format_currency($v.reg_amount)}</td>{/if}
            <td>{$v.paymode}</td>
            <td>{date('d M Y - h:i:s A', strtotime($v.date_of_joining))}</td>
        </tr>
    {/foreach}
        </tbody>
        {else}
        <h4 align="center">{lang('no_data')}</h4>
    {/if}
    </table>
    </div>
    
  </div>
  {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
</div>
{/block}
