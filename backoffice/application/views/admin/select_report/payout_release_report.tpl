{extends file=$BASE_TEMPLATE}
{block name=script} {$smarty.block.parent}
<script>
    jQuery(document).ready(function() {
        ValidateUser.init();
    });
</script>
{/block}
{block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display:none;">
    <span id="error_msg">{lang('You_must_select_from_date')}</span>
    <span id="error_msg1">{lang('You_must_select_a_date')}</span>
    <span id="error_msg2">{lang('You_must_select_a_date')}</span>
    <span id="error_msg4">{lang('to_date_greater_than_from_date')}</span>
    <span id ="error_msg5">{lang('digits_only')}</span>
</div>

{* <div class="panel panel-default">
  <div class="panel-body">
  <legend><span class="fieldset-legend">{lang('payout_release_reports')}</span></legend>
    {form_open('admin/payout_released_report_daily','role="form" class="" method="post" name="searchform" id="searchform"  target="_blank"')}
      {include file="layout/error_box.tpl"}
      <div class="col-sm-3 padding_both">
      <div class="form-group">
        <label class="required" for="week_date1">{lang('released_date')}</label>
        <input class="form-control date-picker" name="week_date1" id="week_date1" onchange="myFunction()" type="text" value="">
        {if $error_count && isset($error_array['week_date1'])}{$error_array['week_date1']}{/if}
      </div>
      </div>
      <div class="col-sm-3 padding_both_small">
      <div class="form-group credit_debit_button">
      <button class="btn btn-primary" id="payout_released" name="payout_released" type="submit" value="{lang('view')}">{lang('view')}</button>
      </div>
      </div>
    {form_close()}
  </div>
</div> *}

<div class="panel panel-default">
  <div class="panel-body">
  <legend><span class="fieldset-legend">{lang('payout_release_reports')}</span></legend>
    {form_open('admin/payout_released_report_weekly','role="form" class="" method="get" name="searchform2" id="searchform2"  target="_blank"')}
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

<div class="panel panel-default">
  <div class="panel-body">
  <legend><span class="fieldset-legend">{lang('payout_pending_report')}</span></legend>
    {form_open('admin/payout_pending_report_weekly','role="form" class="" method="get" name="searchform1" id="searchform1"  target="_blank"')}
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
{/block}