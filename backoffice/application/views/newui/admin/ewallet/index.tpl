{extends file='newui/layout/admin.tpl'}
{block name=$CONTENT_BLOCK}
<div class="main-content-new-dashboard">
    <div class="breadcrumb-header-new-dashboard justify-content-between">
        <div>
            <h4>{lang('ewallet')}</h4>
        </div>
        <div class="d-flex my-auto">
            
            {if str_contains($coming_from, 'profile_view')}
                <div class="back-btn" style="padding-right: 10px; text-align: right;">
                    <a href="{BASE_URL}/admin/profile_view?user_name={$ci->input->get('user_name')}" class="btn m-b-xs btn-sm btn-info btn-addon" style="height: 32px"><i class="fa fa-backward"></i> {lang('back')}</a>
                </div>
            {/if}

            <div class="new-dashboard-btn">
                <div class="btn-group dropdown">
                    <a href="#" data-toggle="modal" data-target="#fund_transfer_modal" style="float: left;">
                        <button class="btn m-b-xs btn-sm btn-primary add-btn"
                            aria-expanded="false">{lang('fund_transfer')}</button>
                    </a>
                    <button class="btn m-b-xs btn-sm btn-primary btn-addon" data-toggle="dropdown"
                        aria-expanded="false"><i class="fa fa-caret-down pull-right"></i></button>
                    <ul class="dropdown-menu">
                        <li><a href="#" data-toggle="modal" data-target="#fund_credit_modal">{lang('credit_fund')}</a>
                        </li>
                        <li><a href="#" data-toggle="modal" data-target="#fund_debit_modal">{lang('debit_fund')}</a>
                        </li>
                        <li><a href="#" data-toggle="modal" data-target="#fund_agent_credit_modal">{lang('credit_agent_fund')}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

{block name="style"}
<link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable_with_btn.min.css">
{/block}

<!--Tiles-->
<div class="tile-new-dashboard-top">
    <div class="new-dashboard-tile-ewallet-all justify-content-center">
        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left green-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/income-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total['credit']) }">
                    <h4 title="{lang('total_credited')}">
                        {lang('total_credited')}
                    </h4>
                    <span id="summary_credited"> {thousands_currency_format($total['credit']) }</span>
                </div>
            </div>
        </div>
        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left red-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/Bonus-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total['debit'])}">
                    <h4 title="{lang('total_debited')}">
                        {lang('total_debited')}
                    </h4>
                    <span id="summary_debited"> {thousands_currency_format($total['debit'])}</span>
                </div>
            </div>
        </div>
        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left blue-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/E-Wallet-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total['credit'] - $total['debit'])}">
                    <h4 title="{lang('total_ewallet_balance')}">{lang('total_ewallet_balance')}</h4>
                    <span id="summary_balance"> {thousands_currency_format($total['credit'] - $total['debit'])}</span>
                </div>
            </div>
        </div>

        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left blue-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/income-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($purchase_wallet_balance)}">
                    <h4 title="{lang('total_purchase_wallet_balance')}">{lang('purchase_wallet')}</h4>
                    <span id="summary_balance">
                        {thousands_currency_format($purchase_wallet_balance)}
                    </span>
                </div>
            </div>
        </div>

        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left blue-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/income-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($commission_earned)}">
                    <h4 class="text-capitalize" title="{lang('commission_earned')}">{lang('commission_earned')}</h4>
                    <span id="summary_balance">
                        {thousands_currency_format($commission_earned)}
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>
{block name="style"}
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable_with_btn.min.css">
    {/block}
<!-- Tabs -->
<div class="new-dashborad-summary pt-10 m-b-xxl">
    <div class="tabs pt-15">
        <!-- Ewallet Summary -->
        <input class="tabs__item-input" type="radio" name="tabs" id="tab_summary" {if $active_tab=="tab_summary"}checked="checked"{/if}>
        <label class="tabs__item-label" for="tab_summary">{lang('ewallet_summary')}</label>
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
            <div class="debit-credit-all">
                <div class="debit-credit">
                    <div class="list-group">
                        <div class="list-group-item list-group-item-header color-text credit">{lang('credit')}</div>
                        <div id="credited_items" class="summary-tile-grid">
                            {foreach from=$details item=item key="type"}
                            {if $item['type'] == 'credit'}
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
                        <div class="list-group-item list-group-item-header color-text debit">{lang('debit')}</div>
                        <div id="debited_items" class="summary-tile-grid">
                            {foreach from=$details item=item key="type"}
                            {if $item['type'] == 'debit'}
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
        <!-- Ewallet Transactions -->
        <input class="tabs__item-input" type="radio" name="tabs" id="tab_transactions" {if $active_tab=="tab_transactions"}checked="checked"{/if}>
        <label class="tabs__item-label" for="tab_transactions">{lang('ewallet_transactions')}</label>
        <div class="tabs__item-content">
            <div class="filter-new">
                <form id="transactions_form">
                    <div class="row">
                        <div class="form-group">
                            <select class="user-search-selectize user-search-dropdown" name="user_name"></select>
                        </div>
                        <div class="form-group">
                            <select name="transaction_type" id="transaction_type" class="form-control select2-category" multiple>
                                <option value="credit">{lang('credited')}</option>
                                <option value="debit">{lang('debited')}</option>
                            </select>
                        </div>
                        <div class="form-group select-check">
                            <select name="transaction_category" class="form-control select2-category" id="transaction_category" multiple>
                                {foreach $ewallet_categories as $category}
                                <option value="{$category}">{lang($category)}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="form-group">
                            <div id="transactions_daterangepicker"
                                class="date-range-picker">
                                <i class="fa fa-calendar"></i>&nbsp;
                                <span></span><i class="fa fa-caret-down"></i>
                            </div>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-primary search_filter" type="button">
                                {lang('search')}
                            </button>
                            <button class="btn btn-sm btn-info search_clear" type="button">
                                {lang('reset')}
                            </button>
                            <a href="http://localhost/WC/majeed/backoffice/admin/excel/create_excel_total_ewallet_transaction_report">
                              <button class="btn btn-sm btn-primary search_filter" type="button"><i class="fa fa-file-excel-o"></i>  Excel
                              </button>
                           </a>
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
        <!-- Ewallet Balance -->
        <input class="tabs__item-input" type="radio" name="tabs" id="tab_balance" {if $active_tab=="tab_balance"}checked="checked"{/if}>
        <label class="tabs__item-label" for="tab_balance">{lang('ewallet_balance')}</label>
        <div class="tabs__item-content">
            <div class="filter-new">
                <form id="balance_form">
                    <div class="row">
                        <div class="form-group">
                            <select class="user-search-selectize user-search-dropdown" name="user_name" multiple></select>
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-primary search_filter" type="button">
                                {lang('search')}
                            </button>
                            <button class="btn btn-sm btn-info search_clear" type="button">
                                {lang('reset')}
                            </button>
                            <a href="http://localhost/WC/majeed/backoffice/admin/excel/create_excel_total_ewallet_balance_report">
                              <button class="btn btn-sm btn-primary search_filter" type="button">
                                <i class="fa fa-file-excel-o"></i> Excel
                              </button>
                           </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-wallet">
                <div class="table-responsive">
                    <table id="balance_table" class="display">
                        <thead>
                            <tr>
                                <th>{lang('member_name')}</th>
                                <th>{lang('ewallet_balance')}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

        <!--Ewallet Statement -->
        <input class="tabs__item-input" type="radio" name="tabs" id="ewallet_statement_tab" {if $active_tab=="ewallet_statement_tab"}checked="checked"{/if}>
        <label class="tabs__item-label" for="ewallet_statement_tab">{lang('ewallet_statement')}</label>
        <div class="tabs__item-content">
            <div class="filter-new">
                <form id="ewallet_statement_form">
                    <div class="row">
                        <div class="form-group">
                            <input class="form-control user_autolist" type="text" name="user_name" value="{$active_user_name}" autocomplete="Off" data-value="{$active_user_name}">
                        </div>
                        <div class="form-group">
                            <button class="btn btn-sm btn-primary search_filter" type="button">
                                {lang('search')}
                            </button>

                            <button class="btn btn-sm btn-info search_clear" type="button">
                                {lang('reset')}
                            </button>
                            <a href="http://localhost/WC/majeed/backoffice/admin/excel/create_excel_total_ewallet_statement_report">
                              <button class="btn btn-sm btn-primary search_filter" type="button">
                                <i class="fa fa-file-excel-o"></i> Excel
                              </button>
                           </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-wallet">
                <div class="table-responsive">
                    <table id="ewallet_statement_table" class="display">
                        <thead>
                            <tr>
                                <th>{lang('description')}</th>
                                <th>{lang('amount')}</th>
                                <th>{lang('balance')}</th>
                                <th>{lang('transaction_date')}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        {* Ewallet Statement *}

        <!-- Purchase Wallet -->
        <input class="tabs__item-input" type="radio" name="tabs" id="purchase_wallet_tab" {if $active_tab=="purchase_wallet_tab"}checked="checked"{/if}>
        <label class="tabs__item-label" for="purchase_wallet_tab">{lang('purchase_wallet')}</label>
        <div class="tabs__item-content">
            <div class="filter-new">
                <form id="purchase_wallet_form">
                    <div class="row">
                        <div class="form-group">
                            <input class="form-control user_autolist" type="text" name="user_name" value="{$active_user_name}" autocomplete="Off" data-value="{$active_user_name}">
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
                    <table id="purchase_wallet_table" class="display">
                        <thead>
                            <tr>
                                <th>{lang('description')}</th>
                                <th>{lang('amount')}</th>
                                <th>{lang('balance')}</th>
                                <th>{lang('transaction_date')}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
        {* Purchase Wallet *}

        <!-- User Earnigs -->
        <input class="tabs__item-input" type="radio" name="tabs" id="user_earnigs_tab" {if $active_tab=="user_earnigs_tab"}checked="checked"{/if}>
        <label class="tabs__item-label" for="user_earnigs_tab">{lang('user_earnings')}</label>
        <div class="tabs__item-content">
            <div class="filter-new">
                <form id="user_earnings_form">
                    <div class="row">
                        <div class="form-group">
                            <input class="form-control user_autolist" type="text" name="user_name" value="{$active_user_name}" autocomplete="Off" data-value="{$active_user_name}">
                        </div>
                        <div class="form-group">
                            <select name="user_earnings_category" class="form-control select2-category" id="user_earnings_category">
                                <option value="" selected="selected" disabled="disabled"></option>
                                {foreach $user_earnigs_categories as $category}
                                    <option value="{$category}">{lang($category)}</option>
                                {/foreach}
                            </select>
                        </div>
                        <div class="form-group">
                            <div id="user_earnigs_daterangepicker"
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
                            <a href="http://localhost/WC/majeed/backoffice/admin/excel/create_excel_total_user_earnings_report">
                              <button class="btn btn-sm btn-primary search_filter" type="button">
                                <i class="fa fa-file-excel-o"></i> Excel
                              </button>
                           </a>
                        </div>
                    </div>
                </form>
            </div>
            <div class="table-wallet">
                <div class="table-responsive">
                    <table id="user_earnings_table" class="display">
                        <thead>
                            <tr>
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
        {* User Earnings *}
    </div>
{include file="newui/admin/ewallet/fund_transfer.tpl"}
{include file="newui/admin/ewallet/fund_credit.tpl"}
{include file="newui/admin/ewallet/fund_debit.tpl"}
{include file="newui/admin/ewallet/fund_credit_agent.tpl"}
</div>


<div class="no-display" id="template_credit_debit">
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
    <link rel="stylesheet" href="{$PUBLIC_URL}javascript/toastr/jquery.toast.min.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/ewallet.css">
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable.min.css">
{/block}

{block name=script}
    {$smarty.block.parent}
    <script type="text/javascript" src="{$PUBLIC_URL}javascript/toastr/jquery.toast.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/datatables.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/select2.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/moment.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/daterangepicker.min.js"></script>
    <script src="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/ewallet.js" charset="utf-8" type="text/javascript"></script>
{/block}