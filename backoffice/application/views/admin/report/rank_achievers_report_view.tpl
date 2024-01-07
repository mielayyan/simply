{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}{assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_rank_achievers_report?from_date={$from_date}&&to_date={$to_date}&&ranks={$rank_data}"} {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_rank_achievers_report?from_date={$from_date}&&to_date={$to_date}&&ranks={$rank_data}"}
{assign var="report_name" value="{lang('rank_achieve_report')}"}{include file="admin/report/report_nav.tpl" name=""}
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/rank_achievers_report','role="form" class="" method="get" name="sales_report" id="weekly_payout" onsubmit="return validation()"')} 
        {include file="layout/error_box.tpl"}
        <div class="col-sm-2 padding_both">
            <div class="form-group">
                <label>{lang('select_rank')}</label>
                <select name="ranks[]" multiple class="form-control select2">
            {foreach from=$rank_arr item=rank}
            <option value="{$rank["rank_id"]}" 
                {if !empty($ci->input->get('ranks')) && in_array($rank['rank_id'], $ci->input->get('ranks'))} selected {/if} 
            >
              {$rank["rank_name"]}
            </option>
            {/foreach}
        </select> {if $error_count && isset($error_array['ranks[]'])}{$error_array['ranks[]']}{/if}
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
                <button class="btn btn-primary" name="weekdate" type="submit" value="{lang('submit')}">{lang('submit')}</button>
            </div>
        </div>
        {form_close()}
    </div>
</div>
<div id="print_area" class="img panel-body panel">
{include file="admin/report/header.tpl" name=""}
  <h4 align="center"><b>{$dateRangeString}</b></h4>
  <h4 align="center"><b>{$rankFilterString}</b></h4>
  <div class="panel panel-default  ng-scope">
  <div class="table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
      <tbody>
      <thead>
        <tr>
            <th>{lang('slno')}</th>
            <th>{lang('new_rank')}</th>
            <th>{lang('member_name')}</th>
            <th>{lang('rank_achieved_date')}</th>
        </tr>
      </thead>
      {assign var="i" value=0}
        {foreach from=$report_arr item=v}
        {$i=$i+1}
      <tr>
            <td>{$ci->input->get('offset')+$i}</td>
            <td>{$v.rank_name}</td>
            <td>{$v.user_detail_name}({$v.user_name})</td>
            <td>{date("d M Y - h:i:s A", strtotime($v.date))}</td>
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
  {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
</div>
{/block}