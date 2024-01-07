{extends file='newui/layout/admin.tpl'}
{block name=$CONTENT_BLOCK}

<div class="main-content-new-dashboard">
    <div class="breadcrumb-header-new-dashboard justify-content-between">
        <div>
            <h4>{lang('business')}</h4>
        </div>
    </div>
</div>

<!--Tiles-->
<div class="tile-new-dashboard-top">
    <div class="new-dashboard-tile-business-all justify-content-center">
        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left green-bg-new">
                    <img src="{$PUBLIC_URL}/images/newui/total_debited.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total['income']) }">
                    <h4>{lang('total_income')}</h4>
                    <span id="summary_income"> {thousands_currency_format($total['income']) }</span>
                </div>
            </div>
        </div>
        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left pink-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/Bonus-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total['bonus']) }">
                    <h4>{lang('bonus_generated')}</h4>
                    <span id="summary_bonus"> {thousands_currency_format($total['bonus']) }</span>
                </div>
            </div>
        </div>
        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left red-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/Paid-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total['paid'])}">
                    <h4>{lang('paid_amount')}</h4>
                    <span id="summary_paid"> {thousands_currency_format($total['paid'])}</span>
                </div>
            </div>
        </div>
        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left yellow-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/Pending-w.png">
                </div>
                <div class="e-wallet-content-right " title="{format_currency($total['pending'])}">
                    <h4>{lang('pending_payment')}</h4>
                    <span id="summary_pending"> {thousands_currency_format($total['pending'])}</span>
                </div>
            </div>
        </div>
        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left blue-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/income-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total['income'] - $total['paid'])}">
                    <h4>{lang('profit')}</h4>
                    <span id="summary_profit"> {thousands_currency_format($total['income'] - $total['paid'])}</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="new-dashborad-summary pt-10 m-b-xxl">
    <div class="tabs pt-15">
        <!-- Business Summary -->
        <input class="tabs__item-input" type="radio" name="tabs" id="tab_summary" checked="checked">
        <label class="tabs__item-label" for="tab_summary">{lang('business_summary')}</label>
        <div class="tabs__item-content">
            <div class="filter-new">
                <div class="row">
                    <div class="form-group">
                        <div id="summary_daterangepicker"
                            class="date-range-picker">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="debit-credit-all grid-2">
                <div class="debit-credit">
                    <div class="list-group">
                        <div class="list-group-item list-group-item-header color-text debit">{lang('paid')}</div>
                        <div id="paid_items" class="summary-tile-grid">
                            {foreach from=$details item=item key="type"}
                            {if $item['type'] == 'paid'}
                            <div class="list-group-item">
                                <div>{lang($type)}</div>
                                <span class="badge bg-success">{format_currency($item['amount'])}</span>
                            </div>
                            {/if}
                            {/foreach}
                        </div>
                    </div>
                </div>
                <div class="debit-credit">
                    <div class="list-group">
                        <div class="list-group-item list-group-item-header color-text debit">{lang('pending')}</div>
                        <div id="pending_items" class="summary-tile-grid">
                            {foreach from=$details item=item key="type"}
                            {if $item['type'] == 'pending'}
                            <div class="list-group-item">
                                <div>{lang($type)}</div>
                                <span class="badge bg-success">{format_currency($item['amount'])}</span>
                            </div>
                            {/if}
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>
            <div class="debit-credit-all">
                <div class="debit-credit">
                    <div class="list-group">
                        <div class="list-group-item list-group-item-header color-text credit">{lang('income')}</div>
                        <div id="income_items" class="summary-tile-grid">
                            {foreach from=$details item=item key="type"}
                            {if $item['type'] == 'income'}
                            <div class="list-group-item">
                                <div>{lang($type)}</div>
                                <span class="badge bg-success">{format_currency($item['amount'])}</span>
                            </div>
                            {/if}
                            {/foreach}
                        </div>
                    </div>
                </div>
                <div class="debit-credit">
                    <div class="list-group">
                        <div class="list-group-item list-group-item-header color-text debit">{lang('bonus')}</div>
                        <div id="bonus_items" class="summary-tile-grid">
                            {foreach from=$details item=item key="type"}
                            {if $item['type'] == 'bonus'}
                            <div class="list-group-item">
                                <div>{lang($type)}</div>
                                <span class="badge bg-success">{format_currency($item['amount'])}</span>
                            </div>
                            {/if}
                            {/foreach}
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- Business Transactions -->
        <input class="tabs__item-input" type="radio" name="tabs" id="tab_transactions">
        <label class="tabs__item-label" for="tab_transactions">{lang('business_transactions')}</label>
        <div class="tabs__item-content">
            <div class="filter-new">
                <form id="transactions_form">
                    <div class="row">
                        <div class="form-group">
                            <select class="user-search-selectize user-search-dropdown" name="user_name"></select>
                        </div>
                        <div class="form-group">
                            <select name="transaction_type" id="transaction_type" class="form-control select2-category" multiple>
                                <option value="income">{lang('income')}</option>
                                <option value="bonus">{lang('bonus')}</option>
                                <option value="paid">{lang('paid')}</option>
                                <option value="pending">{lang('pending')}</option>
                            </select>
                        </div>
                        <div class="form-group select-check">
                            <select name="transaction_category" class="form-control select2-category" id="transaction_category" multiple>
                                {foreach $business_categories as $category}
                                <option value="{$category}">{lang($category)}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="form-group">
                            <div id="transactions_daterangepicker"
                                class="date-range-picker">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span> <i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-primary search_filter" type="button">
                                {lang('search')}
                            </button>
                            <button class="btn btn-sm btn-info search_clear" type="button">
                                {lang('reset')}
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-wallet">
                <div class="table-responsive">
                    <table id="transactions_table" class="display">
                        <thead>
                            <tr>
                                <th>{lang('member_name')}</th>
                                <th>{lang('category')}</th>
                                <th>{lang('amount')}</th>
                                <th>{lang('transaction_date')}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


<div class="no-display" id="template_type">
    <span class="badge bg-[type]">[amount]</span>
</div>
<div class="no-display" id="template_amount">
    <span class="badge bg-amount">[amount]</span>
</div>
<div class="no-display" id="template_member_name">
    <div class="d-flex">
        <img src="[profile_image]" alt="img" class="ht-30 wd-30 mr-2"> 
        <div class="margin-wallet-img">
            <h5>[full_name]</h5>
            <span class="sub-text">[user_name]</span>
        </div>
    </div>
</div>

{/block}

{block name="style"}
    {$smarty.block.parent}
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/datepicker.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.css">
    <link href="{$PUBLIC_URL}theme/newui/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/ewallet.css">
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable.min.css">
{/block}

{block name=script}
    {$smarty.block.parent}
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/datatables.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/select2.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/moment.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/daterangepicker.min.js"></script>
    <script src="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/business.js" charset="utf-8" type="text/javascript"></script>
{/block}