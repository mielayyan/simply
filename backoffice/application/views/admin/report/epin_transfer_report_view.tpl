{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}{assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_epin_transfer_report?from_date={$from_date}&&to_date={$to_date}&&from_user={$user_name}&&to_user={$to_user_name}"} {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_epin_transfer_report?from_date={$from_date}&&to_date={$to_date}&&from_user={$user_name}&&to_user={$to_user_name}"}
{assign var="report_name" value="{lang('epin_transfer_report')}"}
<div class="panel panel-default">
    <div class="panel-body">
    <legend><span class="fieldset-legend">{lang('epin_transfer_report')}</span></legend>
        {form_open('admin/epin_report', 'role="form" class="" method="get" name="epin_report" id="epin_report"  onsubmit="return validation()"')} {include file="layout/error_box.tpl"}
        <div class="col-sm-3 padding_both">
            <div class="form-group">
                <label>{lang('from_user')}</label>
                <input type="text" class="form-control user_autolist" autocomplete="Off" id="user_name" name="user_name" value="{$ci->input->get('user_name')}">
            </div>
        </div>
        <div class="col-sm-3 padding_both_small">
            <div class="form-group">
                <label>{lang('to_user')}</label>
                <input type="text" class="form-control user_autolist" autocomplete="Off" id="to_user_name" name="to_user_name" value="{$ci->input->get('to_user_name')}">
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
        <div class="col-sm-2 padding_both_small">
            <div class="form-group credit_debit_button">
                <button class="btn btn-primary" name="submit" id="submit" type="submit" value="{lang('submit')}"> {lang('submit')} </button>
                <button class="btn btn-sm btn-info search_clear" type="button">{lang('reset')}</button>
            </div>
        </div>    
        {form_close()}
    </div>
</div>
{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
{include file="admin/report/header.tpl" name=""}
  {if $fromUserString != ""}<h4 align="center"><b>{$fromUserString}</b></h4>{/if}
  {if $toUserString != ""}<h4 align="center"><b>{$toUserString}</b></h4>{/if}
  {if $dateRangeString != ""}<h4 align="center"><b>{$dateRangeString}</b></h4>{/if}
  <div class="panel panel-default ng-scope">
  <div class="table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
      <tbody>
      <thead>
        <tr>
          <th>{lang('sl_no')}</th>
            <th>{lang('from_user')}</th>
            <th>{lang('to_user')}</th>
            <th>{lang('epin')}</th>
            <th>{lang('transfer_date')}</th>
        </tr>
        </thead>
        {assign var="i" value=0}
        {foreach from=$transfer_details item=v}
            {$i = $i+1}
            <tr >
                <td>{$ci->input->get('offset')+$i}</td>
                <td>
                  {if $v.from_user_delete_status == "active"}
                    {$v.from_full_name}({$v.from_user_name})
                  {else}
                    {$v.from_user_name}
                  {/if}
                </td>
                <td>
                  {if $v.to_user_delete_status == "active"}
                    {$v.to_full_name}({$v.to_user_name})
                  {else}
                    {$v.to_user_name}
                  {/if}
                </td>
                <td>{$v.epin}</td>
                <td>{date("d M Y - h:i:s A", strtotime($v.transfer_date))}</td>
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

{block name=script}
  {$smarty.block.parent}
  <script>
    $('.search_clear').on('click', function () {
      $('#user_name').val('');
      $('#to_user_name').val('');
      $('#daterange').val('all').trigger('change');
    });
  </script>
{/block}
