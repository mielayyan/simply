{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
<ul class="list-group list-group-overview b-a">
    <li class="list-group-item">
        <span class="text-md">{lang('total_credited')}</span>
        <span class="block text-md text-success">{format_currency($total.credit)}</span>
    </li>
    <li class="list-group-item">
        <span class="text-md">{lang('total_debited')}</span>
        <span class="block text-md text-danger">{format_currency($total.debit)}</span>
    </li>
    <li class="list-group-item">
        <span class="text-md">{lang('total_ewallet_balance')}</span>
        <span class="block text-md text-primary">{format_currency(($total.credit - $total.debit))}</span>
    </li>
</ul>
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/business_wallet','role="form" method="get" class="" name="sform" id="sform"')}
        <div class="col-sm-2 padding_both">
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
                <button class="btn btn-primary"  id="submit" type="submit" value="{lang('search')}">
                    {lang('search')} </button>
                <a class="btn btn-info" href="{$BASE_URL}admin/business_wallet">
                    {lang('reset')} </a>
            </div>
        </div>
        {form_close()}
    </div>
</div>
<div class="panel panel-default table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">
        <thead>
            <tr>
                <th>{lang('sl_no')}</th>
                <th>{lang('category')}</th>
                <th>{lang('amount')}</th>
            </tr>
        </thead>
        <tbody>
            {$i = 1}
            {foreach from=$details item=v key=amount_type}
                {if $amount_type == 'board_commission' && $MLM_PLAN == 'Board' && $MODULE_STATUS['table_status']
                == 'yes'}
                    {$category = "bs_table_commission"}
                {else}
                    {$category = "bs_`$amount_type`"}
                {/if}
                {if $v.type == 'credit'}
                    {$amount_class = 'text-success-dker'}
                    {$amount_font_class = 'fa-plus'}
                    {$cat_font_class = 'fa-long-arrow-right text-success'}
                {elseif $v.type == 'debit'}
                    {$amount_class = 'text-danger-dker'}
                    {$amount_font_class = 'fa-minus'}
                    {$cat_font_class = 'fa-long-arrow-left text-danger'}
                {/if}
                <tr>
                    <td>{$i}</td>
                    <td><i class="fa {$cat_font_class}"></i> {lang($category)}</td>
                    <td class="{$amount_class}">
                        <i class="currency-symbol fa {$amount_font_class}"></i> 
                        {format_currency($v.amount)}
                    </td>
                    <td></td>
                </tr>
                {$i = $i + 1}
            {/foreach}
        </tbody>
    </table>
</div>
{/block}
