{extends file=$BASE_TEMPLATE}{block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display:none;">
    <span id="error_msg">{lang('You_must_select_from_date')}</span>
    <span id="error_msg1">{lang('You_must_select_to_date')}</span>
    <span id="error_msg2">{lang('You_must_Select_From_To_Date_Correctly')}</span>
    <span id="error_msg3">{lang('You_must_select_a_date')}</span>
    <span id="error_msg4">{lang('you_must_select_a_to_date_greater_than_from_date')}</span>
    <span id="error_msg5">{lang('digits_only')}</span>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/total_joining_weekly','role="form" class="" method="get" name="daily" id="daily" target="__blank" onsubmit="return validation()"')} {include file="layout/error_box.tpl"}
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
                <button class="btn btn-primary" name="dailydate" type="submit" value="{lang('submit')}"> {lang('submit')} </button>
            </div>
        </div>
        {form_close()}
    </div>
</div>
{/block}