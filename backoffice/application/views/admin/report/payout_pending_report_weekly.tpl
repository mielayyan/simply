{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}{assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_payout_pending_report?from_date={$from_date}&&to_date={$to_date}"} {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_payout_pending_report?from_date={$from_date}&&to_date={$to_date}"}
{assign var="report_name" value="{lang('member_wise_payout_report')}"}
<div class="panel panel-default">
  <div class="panel-body">
    {assign var="status" value=$ci->input->get('status')}
  {* <legend><span class="fieldset-legend">{lang('payout_release_reports')}</span></legend> *}
    {form_open('admin/payout_release_report','role="form" class="" method="get" name="searchform2" id="searchform2"')}
      <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label class="" for="status">{lang('status')}</label>
                <select name="status" id="status" class="form-control">
                    <option value="released">{lang('released')}</option>
                    <option value="pending" {if $ci->input->get('status') == "pending"} selected {/if}>{lang('pending')}</option>
                </select>
            </div>
      </div>
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
</div>
{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
{include file="admin/report/header.tpl" name=""}
  <h4 align="center"><b>{$dateRangeString}</b></h4>
  <div class="panel panel-default ng-scope">
  <div class=" table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{assign var="j" value="0"}
      {if $count >=1}
      <tbody>
      <thead>
        <tr>
          <th>{lang('sl_no')}</th>
            <th>{lang('member_name')}</th>
            <th>{lang('total_amount')}</th>
            {if $payoutFeeDisplay == "yes"}<th>{lang('payout_fee')}</th>{/if}
            <th>{lang('Date')}</th>
        </tr>
      </thead>
      {foreach from=$binary_details item=v}
        {$j=$j+1}
        <tr>
            <td> {$ci->input->get('offset')+$j} </td>
            <td>{$v.full_name} ({$v.paid_user_id})</td>
            <td>{format_currency($v.paid_amount)}</td>
            {if $payoutFeeDisplay == "yes"}<td>{format_currency($v.payout_fee)}</td>{/if}
            <td>{date("d M Y - h:i:s A", strtotime($v.paid_date))}</td>
        </tr>
        {/foreach}
        <tr>
            <td colspan="2" class="text-right"> <b>{lang('total_amount')}</b></td></th>
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