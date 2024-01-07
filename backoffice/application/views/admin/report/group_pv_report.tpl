{extends file=$BASE_TEMPLATE} 
{block name=$CONTENT_BLOCK}

{assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_group_pv_report/?username={$user_name}&&date_range={$daterange}&&from_date={$from_date}&&to_date={$to_date}"} 
{assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_group_pv_report/?username={$user_name}&&date_range={$daterange}&&from_date={$from_date}&&to_date={$to_date}"}
{assign var="report_name" value="{lang('group_pv_report')}"}

<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/report/group_pv_report','role="form" class="" method="get" name="commision_form" id="commision_form" onsubmit="return validation()"')}
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
  <div class="panel panel-default  ng-scope">
  <div class=" table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
      <tbody>
      <thead>
        <tr>
            <th>{lang('sl_no')}</th>
            {if $filterUserString == ""}<th>{lang('user_name')}</th>{/if}
            <th>{lang('total_gpv')}</th>
            <th>{lang('from_user')}</th> 
            <th style="width:20%">{lang('date')}</th>
        </tr>
        </thead>
        {assign var="i" value=1}
        {foreach from=$details item=v}
            <tr>
                <td>{$ci->input->get('offset')+$i}</td>
                {if $filterUserString == ""}
                    <td>
                            {$v.user_name}
                    </td>
                {/if}
                <td>  
                 {$v.group_pv}
                </td>
                <td>   
                    {$v.from_user_name}
               </td>
               {if $v.date != ''}
                <td>{date('d M Y - h:i:s A', strtotime($v.date))}</td>
                {else}
                <td>{{$v.date_range}}</td>
                {/if}

            </tr>

            {$i=$i+1}
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
