{extends file=$BASE_TEMPLATE} 

{block name=$CONTENT_BLOCK}
{assign var="report_name" value="{lang('activate_deactivate_report')}"} 
{assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_activate_deactivate_report_view/?from_date={$from_date}&&to_date={$to_date}"} 
{assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_activate_deactivate_report_view/?from_date={$from_date}&&to_date={$to_date}"} 

<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/activate_deactivate_report_view','role="form" class="" method="get" name="daily" id="daily" onsubmit="return validation()"')}
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
        <div class="padding_both_small col-sm-3">
            <div class="form-group credit_debit_button">
                <button class="btn btn-primary" name="dailydate" type="submit" value="{lang('submit')}">
                {lang('submit')} </button>
            </div>
        </div>
        {form_close()} 

    </div>
</div>

{include file="admin/report/report_nav.tpl" name=""}

<div id="print_area" class="img panel-body panel">
    {include file="admin/report/header.tpl" name=""}
    <h4 align="center"><b>{$dateRangeString}</b></h4>
    <div class="panel panel-default  ng-scope">
    <div class="table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
            <tbody>
                <thead>
                    <tr class="th">
                        <th>{lang('sl_no')}</th>
                        <th>{lang('member_name')}</th>
                        <th>{lang('status')}</th>
                        <th>{lang('activate_deactivate_date')}</th>
                    </tr>
                </thead>
                {assign var="i" value=0} {foreach from=$activate_deactive item=v} {$i=$i+1}
                <tr>
                    <td>{$ci->input->get('offset')+$i}</td>
                    <!-- <td>{$v.user_name}</td> -->
                    <td>{$v.full_name} ({$v.user_name})</td>
                    <td>{$v.status}</td>
                    <td>{$v.date}</td>
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