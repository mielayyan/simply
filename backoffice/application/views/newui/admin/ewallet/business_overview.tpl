{extends file='newui/layout/admin.tpl'}
    {block name=$CONTENT_BLOCK}
        <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/datepicker.css">
        <link rel="stylesheet" href="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.css">
        <link href="{$PUBLIC_URL}theme/newui/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="{$PUBLIC_URL}theme/newui/css/toastr.min.css">
        <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/ewallet.css">
        <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable.min.css">
        
        <div class="main-content-new-dashboard">
           <div class="breadcrumb-header-new-dashboard justify-content-between">
                <div>
                    <h4>{lang('Business')}</h4>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb-new-dashboard">
                           <li class="breadcrumb-item-new-dashboard"><a href="#">{lang('dashboard')}</a> <i class="fa fa-angle-double-right"></i></li>
                           <li class="breadcrumb-item-new-dashboard active" aria-current="page">{lang('business_summary')}</li>
                        </ol>
                    </nav>
                </div>
            </div>
        </div>

        <!--Tiles-->
        <div class="tile-new-dashboard-top">
            <div class="new-dashboard-tile-ewallet-all justify-content-center">
                
                <div class="new-dashboard-tile-ewallet">
                    <div class="tile-new-dashboard-w-wallet d-flex">
                        <div class="e-wallet-image-left pink-bg-new">
                            <img src="{$PUBLIC_URL}/images/newui/total_credited.png">
                        </div>
                        <div class="e-wallet-content-right">
                            <h4>{lang('total_income')}<span> {format_currency($total.income) }</span></h4>
                        </div>
                    </div>
                </div>

              
                <div class="new-dashboard-tile-ewallet">
                    <div class="tile-new-dashboard-w-wallet d-flex">
                        <div class="e-wallet-image-left blue-bg-new">
                            <img src="{$PUBLIC_URL}/images/newui/total_debited.png">
                        </div>
                        <div class="e-wallet-content-right">
                            <h4>{lang('bonus_generated')} <span> {format_currency($total.commission) }</span></h4>
                        </div>
                    </div>
                </div>
           
                <div class="new-dashboard-tile-ewallet">
                    <div class="tile-new-dashboard-w-wallet d-flex">
                        <div class="e-wallet-image-left green-bg-new">
                            <img src="{$PUBLIC_URL}/images/newui/ewallet_balance.png">
                        </div>
                 
                        <div class="e-wallet-content-right">
                            <h4>{lang('paid_amount')} <span> {format_currency($total.payout) }</span></h4>
                        </div>
                    </div>
                </div>

                <div class="new-dashboard-tile-ewallet">
                    <div class="tile-new-dashboard-w-wallet d-flex">
                        <div class="e-wallet-image-left blue-bg-new">
                            <img src="{$PUBLIC_URL}/images/newui/total_debited.png">
                        </div>
                        <div class="e-wallet-content-right">
                            <h4>{lang('pending_payment')} <span> {format_currency($total.payout_pending) }</span></h4>
                        </div>
                    </div>
                </div>


                <div class="new-dashboard-tile-ewallet">
                    <div class="tile-new-dashboard-w-wallet d-flex">
                        <div class="e-wallet-image-left blue-bg-new">
                            <img src="{$PUBLIC_URL}/images/newui/total_debited.png">
                        </div>
                        <div class="e-wallet-content-right">
                            <h4>{lang('profit')} <span>{format_currency(($total.income - $total.payout))}</span></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="new-dashborad-summary m-b-xxl">
            <div class="tabs">

                <input class="tabs__item-input" type="radio" name="tabs" id="tabone" checked="checked">
                <label class="tabs__item-label" for="tabone">{lang('business_summary')}</label>
                    <div class="tabs__item-content">
                        <div class="filter-new">
                            <form action=""  id="business_summary_form">
                                <div class="row">
                                    <div class="col-lg-2 col-sm-6 padding_both">
                                        <div class="form-group">
                                            <div id="date_range_ewallet_summary" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                                <i class="fa fa-calendar"></i>&nbsp;
                                                <span></span> <i class="fa fa-caret-down"></i>
                                            </div> 
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-4 padding_both_small">
                                        <div class="form-group">
                                            <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">
                                                {lang('search')}
                                            </button>
                                            <a class="btn btn-sm btn-info" href="">{lang('reset')} </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="panel panel-default table-responsive">
                                <table st-table="rowCollectionBasic" class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>{lang('sl_no')}</th>
                                            <th colspan="2">{lang('category')}</th>
                                            <th>{lang('amount')}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="credited_items">
                                        {$i = 1}
                                        {foreach from=$business_summary item=v key=amount_type}
                                            {if $amount_type == 'board_commission' && $MLM_PLAN == 'Board' && $MODULE_STATUS['table_status']
                                            == 'yes'}
                                                {$category = "bs_table_commission"}
                                            {else}
                                                {$category = "bs_`$amount_type`"}
                                            {/if}
                                            {if $v.type == 'income'}
                                                {$type_class = 'label-success'}
                                            {elseif $v.type == 'commission'}
                                                {$type_class = 'label-info'}
                                            {elseif $v.type == 'payout'}
                                                {$type_class = 'label-danger'}
                                            {elseif $v.type == 'payout_pending'}
                                                {$type_class = 'label-warning'}
                                            {elseif $v.type == 'payout_fee'}
                                                {$type_class = 'label-success'}
                                            {/if}
                                            <tr>
                                                <td>{$i}</td>
                                                <td class="td-label"><span class="label {$type_class}">{lang("bs_`$v.type`")}</span></td>
                                                <td>{lang($category)}</td>
                                                <td>{format_currency($v.amount)}
                                                </td>
                                            </tr>
                                            {$i = $i + 1}
                                        {/foreach}
                                    </tbody>
                                </table>
                         </div>
                        
                    </div>
                    <input class="tabs__item-input" type="radio" name="tabs" id="tabtwo">
                    <label class="tabs__item-label" for="tabtwo">{lang('business_transactions')}</label>
                    <div class="tabs__item-content">
                            <div class="filter-new">
                                <form action="" id="business_transaction_filter_form">
                                    <div class="row">
                                        <div class="col-lg-2 col-sm-6 col-xs-12 padding_both">
                                            <div class="form-group">
                                                <select class="user-search-selectize user-search-select2"></select>
                                            </div>
                                        </div>
                                        <div class="col-lg-2 col-sm-6 col-xs-12 padding_both_small">
                                            <div class="form-group">
                                                <select name="cat_type" id="cat_type" class="form-control">
                                                    <option value="all">{lang('type')}</option>
                                                    <option value="income">{lang('bs_income')}</option>
                                                    <option value="commission">{lang('bs_commission')}</option>
                                                    <option value="paid">{lang('bs_payout')}</option>
                                                    <option value="pending">{lang('bs_payout_pending')}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="">
                                            <div class="form-group select-check">
                                                <select name="category" class="form-control select2-category" id="transaction_category" multiple>
                                                <option></option>
                                                {foreach $ewallet_categories as $category}
                                                    <option value="{$category}">{lang($category)}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-lg-3 col-sm-6 col-xs-12 padding_both">
                                        <div class="form-group">
                                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                                <i class="fa fa-calendar"></i>&nbsp;
                                                <span></span> <i class="fa fa-caret-down"></i>
                                            </div>    
                                        </div>
                                    </div>
                 
                                    <div class="col-lg-3 col-sm-4 padding_both">
                                        <div class="form-group">
                                            <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">
                                                {lang('search')}
                                            </button>
                                            <a class="btn btn-sm btn-info" href="">{lang('reset')} </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="table-wallet">
                            <div class="table-responsive">
                                <table id="table_id_business" class="display">
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

{/block}
{block name="style"}
{$smarty.block.parent}
{/block}
{block name=script}

{$smarty.block.parent}
    <script type="text/javascript" charset="utf8" src="{$PUBLIC_URL}theme/newui/js/datatables.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/select2.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/moment.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/daterangepicker.min.js"></script>
    <script src="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/toastr.min.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/ewallet.js"></script>
{/block}
{block name = "style"}
<style type="text/css">
    @media (min-width: 1400px) {
    .new-dashboard-tile-ewallet-all{
        
            grid-template-columns: repeat(5, 1fr) !important;
    }
    }
    .tile-new-dashboard-w-wallet .e-wallet-content-right span {
           float: left !important;
    }    
</style>
{/block}