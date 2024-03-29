{extends file=$BASE_TEMPLATE} {block name=script} {$smarty.block.parent}
<script>
    jQuery(document).ready(function() {
        ValidateUser.init();
    });
</script>
{/block} {block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display:none;">
    <span id="error_msg">{lang('You_must_select_from_date')}</span>
    <span id="error_msg1">{lang('You_must_select_to_date')}</span>
    <span id="errmsg4">{lang('to_date_greater_than_from_date')}</span>
    <span id="error_msg2">{lang('You_must_enter_user_name')}</span>
</div>

{* <div class="panel panel-default">
    <div class="panel-body">
    <legend><span class="fieldset-legend">{lang('full_epin_report')}</span></legend>
        {form_open('admin/epin_report_view','role="form" class="" method="post" name="daily" id="daily" target="_blank"')}
        <div class="form-group">
            <label>{lang('full_epin_report')}</label>
            <button class="btn btn-primary" name="full_epin" type="submit" value="{lang('view')}"> {lang('view')} </button>
        </div>
        {form_close()}
    </div>
</div> *}
 
<div class="panel panel-default">
    <div class="panel-body">
    <legend><span class="fieldset-legend">{lang('epin_transfer_report')}</span></legend>
        {form_open('admin/epin_transfer_report_view', 'role="form" class="" method="get" name="epin_report" id="epin_report" target="_blank"')} {include file="layout/error_box.tpl"}
        <div class="col-sm-3 padding_both">
            <div class="form-group">
                <label>{lang('from_user')}</label>
                <input type="text" class="form-control user_autolist" autocomplete="Off" id="user_name" name="user_name">
            </div>
        </div>
        <div class="col-sm-3 padding_both_small">
            <div class="form-group">
                <label>{lang('to_user')}</label>
                <input type="text" class="form-control user_autolist" autocomplete="Off" id="to_user_name" name="to_user_name">
            </div>
        </div>
        <!--<div class="col-sm-3 padding_both_small">
            <div class="form-group">
                <label>{lang('from_date')}</label>
                <input autocomplete="off" class="form-control date-picker" name="week_date1" id="week_date1" type="text" size="20" maxlength="10" value=""> {if isset($error_array['week_date1'])}{$error_array['week_date1']}{/if}
            </div>
        </div>
        <div class="col-sm-3 padding_both_small">
            <div class="form-group">
                <label>{lang('to_date')}</label>
                <input autocomplete="off" class="form-control date-picker" name="week_date2" id="week_date2" type="text" size="20" maxlength="10" value=""> {if isset($error_array['week_date2'])}{$error_array['week_date2']}{/if}
            </div>
        </div>-->
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
        <div class="col-sm-2 padding_both_small">
            <div class="form-group credit_debit_button">
                <button class="btn btn-primary" name="submit" id="submit" type="submit" value="{lang('submit')}"> {lang('submit')} </button>
            </div>
        </div>    
        {form_close()}
    </div>
</div>
{/block}