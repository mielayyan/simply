{extends file=$BASE_TEMPLATE} {block name=script} {$smarty.block.parent}
<script>
    jQuery(document).ready(function() {
        ValidateUser.init();
    });
</script>
{/block}{block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display:none;">
    <span id="error_msg">{lang('you_must_select_from_date')}</span>
    <span id="error_msg1">{lang('you_must_select_to_date')}</span>
    <span id="errmsg4">{lang('you_must_select_from_to_date_correctly')}</span>
    <span id="error_msg4">{lang('you_must_select_a_to_date_greater_than_from_date')}</span>
    <span id="error_msg5">{lang('digits_only')}</span>
    <span id="error_msg6">{lang('select_rank')}</span>
</div>
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/rank_achievers_report_view','role="form" class="" method="get" name="sales_report" id="weekly_payout" target="_blank" onsubmit = "return dateValidation()"')} {include file="layout/error_box.tpl"}
        <div class="col-sm-2 padding_both">
            <div class="form-group">
                <label>{lang('select_rank')}</label>
                <select name="ranks[]" multiple class="form-control select2">
            {foreach from=$rank_arr item=rank}
                <option value="">{dump(in_array($rank['rank_id'], $ci->input->get('ranks')))}</option>
                <option value="{$rank["rank_id"]}" {if in_array($rank['rank_id'], $ci->input->get('ranks'))} selected {/if}>{$rank["rank_name"]}</option>
            {/foreach}
        </select> {if $error_count && isset($error_array['ranks[]'])}{$error_array['ranks[]']}{/if}
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
                <button class="btn btn-primary" name="weekdate" type="submit" value="{lang('submit')}">{lang('submit')}</button>
            </div>
        </div>
        {form_close()}
    </div>
</div>

{/block}