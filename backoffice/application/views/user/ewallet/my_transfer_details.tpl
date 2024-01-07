{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display:none;">
    <span id="error_msg1">{lang('you_must_select_user')}</span>
    <span id="error_msg2">{lang('You_must_select_a_date')}</span>
    <span id="error_msg3">{lang('invalid_period')}</span>
    <span id="error_msg4">{lang('You_must_select_a_Todate_greaterThan_Fromdate')}</span>
    <span id="error_msg5">{lang('digits_only')}</span>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        {form_open('','role="form" method="get"  name="transfer_history_filter"')} 
            {include file="layout/error_box.tpl"}
            <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label class="" for="daterange">{lang('daterange')}</label>
                    <select name="daterange" id="daterange" class="form-control">
                        <option value="all" {if $daterange=="all"} selected {/if}>{lang('overall')}</option>
                        <option value="today" {if $daterange=="today"} selected {/if}>{lang('today')}</option>
                        <option value="month" {if $daterange=="month"} selected {/if}>{lang('this_month')}</option>
                        <option value="year" {if $daterange=="year"} selected {/if}>{lang('this_year')}</option>
                        <option value="custom" {if $daterange=="custom"} selected {/if}>{lang('custom')}</option>
                    </select>
                </div>
            </div>

             <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label>{lang('from_date')}</label>
                    <input autocomplete="off" class="form-control date-picker custom-date" name="from_date" id="from_date" type="text" value="{$ci->input->get('from_date')}">
                </div>
            </div>
            <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label>{lang('to_date')}</label>
                    <input autocomplete="off" class="form-control date-picker custom-date" name="to_date" id="to_date" type="text" value="{$ci->input->get('to_date')}">
                </div>
            </div>
            
            <div class="col-sm-2 padding_both_small">
                <div class="form-group credit_debit_button">
                    <button class="btn btn-primary" id="submit" type="submit" value="Search">{lang('search')}</button>
                    <a class="btn btn-info" href="{base_url()}user/my_transfer_details">{lang('reset')}</a>
                </div>
            </div>
        {form_close()}
    </div>
</div>

<div class="panel panel-default">
    <div class="table-responsive">
        <table class="table table-striped m-b-none">
            <thead>
                <tr class="th">
                    <th>#</th>
                    <th>{lang('transaction_id')}</th>
                    <th>{lang('amount')}</th>
                    <th>{lang('transaction_fee')}</th>
                    <th>{lang('transfer_type')}</th>
                    <th>{lang('date')}</th>

                </tr>
            </thead>
            <tbody>
                {if !empty($fund_transfer_details)}
                    {foreach from=$fund_transfer_details item=item key=key}
                        <tr>
                            <td>{$ci->input->get('offset')+$key+1}</td>
                            <td>{$item.transaction_id}</td>
                            <td>{format_currency($item.total_amount)}</td>
                            <td>{format_currency($item.trans_fee)}</td>
                            <td>{lang($item.amount_type)}</td>
                            <td>{$item.date|date_format:"d M Y - h:i:s A"}</td>
                        </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td colspan="7">
                            <h4 class="text-center">{lang('no_records_found')}</h4>
                        </td>
                    </tr>
                {/if}
            </tbody>
        </table>
        {if !empty($fund_transfer_details)}
            {$ci->pagination->create_links('<div class="panel-footer">', '</div>')}
        {/if}
    </div>
</div>
{/block} {block name=script}{$smarty.block.parent}
<script>
    jQuery(document).ready(function() {
        ValidateUser.init();
    });
</script>
{/block}