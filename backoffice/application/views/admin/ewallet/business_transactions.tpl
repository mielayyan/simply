{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<ul class="list-group list-group-overview b-a">
    <li class="list-group-item">
        <span class="text-md">{lang('total_income')}</span>
        <span class="block text-md text-success">{format_currency($total.income)}</span>
    </li>
    <li class="list-group-item">
        <span class="text-md">{lang('bonus_generated')}</span>
        <span class="block text-md text-info">{format_currency($total.commission)}</span>
    </li>
    <li class="list-group-item">
        <span class="text-md">{lang('paid_amount')}</span>
        <span class="block text-md text-danger">{format_currency($total.payout)}</span>
    </li>
    <li class="list-group-item">
        <span class="text-md">{lang('pending_payment')}</span>
        <span class="block text-md text-warning-dker">{format_currency($total.payout_pending)}</span>
    </li>
    <li class="list-group-item">
        <span class="text-md">{lang('profit')}</span>
        <span class="block text-md text-primary">{format_currency(($total.income - $total.payout))}</span>
    </li>
</ul>
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/business_transactions','role="form" method="get" class="" name="sform" id="sform"')}
        <div class="col-sm-2 padding_both">
            <div class="form-group">
                <label class="" for="user_name">{lang('user_name')}</label>
                <input class="form-control user_autolist" type="text" id="user_name" name="user_name" value="{$user_name}" autocomplete="Off">
            </div>
        </div>
        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label class="" for="type">{lang('type')}</label>
                <select name="cat_type" id="cat_type" class="form-control">
                    <option value="all" {if $cat_type=="all"} selected {/if}>{lang('any')}</option>
                    <option value="income" {if $cat_type=="income"} selected {/if}>{lang('bs_income')}</option>
                    <option value="commission" {if $cat_type=="commission"} selected {/if}>{lang('bs_commission')}</option>
                    <option value="paid" {if $cat_type=="paid"} selected {/if}>{lang('bs_payout')}</option>
                    <option value="pending" {if $cat_type=="pending"} selected {/if}>{lang('bs_payout_pending')}</option>
                </select>
            </div>
        </div>
        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label class="" for="category">{lang('category')}</label>
                <select name="category" id="category" class="form-control {$category}">
                    <option value="all" {if $category=="all"} selected {/if}>{lang('any')}</option>
                    {foreach from=$categories item=c}
                        <option value="{$c}" {if $category==$c} selected {/if}>{lang("bs_`$c`")}</option>
                    {/foreach}
                </select>
            </div>
        </div>
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
                <a class="btn btn-info" href="{$BASE_URL}admin/business_transactions">
                    {lang('reset')} </a>
            </div>
        </div>
        {form_close()}
    </div>
</div>

<div class="panel panel-default">
    <div class="table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">
        <thead class="">
            <tr class="th">
                <th>{lang('sl_no')}</th>
                <th colspan="2">{lang('category')}</th>
                <th>{lang('member_name')}</th>
                <th>{lang('amount')}</th>
                <th>{lang('transaction_date')}</th>
            </tr>
        </thead>
        {if count($all_transaction) > 0}
            <tbody>
                {assign var=i value="0"}
                {foreach from=$all_transaction item=v}
                    {$i = $i + 1}
                    {$type_class = ''}
                    {if $v.cat_type == 'income'}
                        {$type_class = 'label-success'}
                    {elseif $v.cat_type == 'commission'}
                        {$type_class = 'label-info'}
                    {elseif $v.cat_type == 'payout'}
                        {$type_class = 'label-danger'}
                    {elseif $v.cat_type == 'pending'}
                        {$type_class = 'label-warning'}
                    {/if}
                    {if $v.category == 'board_commission' && $MLM_PLAN == 'Board' && $MODULE_STATUS['table_status'] == 'yes'}
                        {$category = "bs_table_commission"}
                    {else}
                        {$category = "bs_`$v.category`"}
                    {/if}
                    <tr>
                        <td>{$i + $page_id}</td>
                        <td class="td-label"><span class="label {$type_class}">{lang("bs_`$v.cat_type`")}</span></td>
                        <td>{lang($category)}</td>
                        <td>
                            {if $v.delete_status == "active"}
                                {user_with_name($v.user_name, "`$v.user_detail_name` `$v.user_detail_second_name`", true, null)}
                            {else}
                                {$v.user_name}
                            {/if}
                        </td>
                        <td>
                            {format_currency($v.amount)}
                        </td>
                        <td>{$v.transaction_date|date_format:"d M Y - h:i:s A"}</td>
                    </tr>
                {/foreach}
            </tbody>
        {else}
            <tbody>
                <tr>
                    <td colspan="6">
                        <h4 class="text-center">{lang('No_Details_Found')}</h4>
                    </td>
                </tr>
            </tbody>
        {/if}
    </table>
    </div>
    {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
</div>


{/block}

