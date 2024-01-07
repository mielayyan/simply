{extends file='newui/layout/admin.tpl'}
{block name=$CONTENT_BLOCK}
<div class="main-content-new-dashboard">
    <div class="breadcrumb-header-new-dashboard justify-content-between">
        <div>
            <h4>{lang('custom_wallet')}</h4>
        </div>
        <div class="d-flex my-auto">
            
            {if str_contains($coming_from, 'profile_view')}
                <div class="back-btn" style="padding-right: 10px; text-align: right;">
                    <a href="{BASE_URL}/admin/profile_view?user_name={$ci->input->get('user_name')}" class="btn m-b-xs btn-sm btn-info btn-addon" style="height: 32px"><i class="fa fa-backward"></i> {lang('back')}</a>
                </div>
            {/if}

            {* <div class="new-dashboard-btn" style="padding-left: 20px;">
                <div class="btn-group dropdown">
                    <a href="#" data-toggle="modal" data-target="#wallet_transfer_modal" style="float: left;">
                        <button class="btn m-b-xs btn-sm btn-primary add-btn"
                            aria-expanded="false">{lang('wallet_transfer_agent')}</button>
                    </a>
                </div>
            </div> *}

            
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
                
                <div class="e-wallet-content-right arab" title="{format_currency($total['credit_anm']) }">
                    <h4 title="{lang('total_credited')}"><span style="font-weight:500px;color:black;">{lang('ANM')}</span>
                        {lang('total_credited')}
                    </h4>
                    <span id="summary_credited_anb"> {thousands_currency_format($total['credit_anm']) }</span>
                </div>
            </div>
        </div>
        <div class="new-dashboard-tile-ewallet panda">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left blue-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/income-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total['credit_panda']) }">
                    <h4 title="{lang('total_credited')}"><span style="font-weight:500px;color:black;">{lang('panda')}</span>
                        {lang('total_credited')}
                    </h4>
                    <span id="summary_credited"> {thousands_currency_format($total['credit_panda']) }</span>
                </div>
            </div>
        </div>
        <div class="new-dashboard-tile-ewallet hajar">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left orange-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/income-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total['credit_hajar']) }">
                    <h4 title="{lang('total_credited')}"><span style="font-weight:500px;color:black;">{lang('hajar')}</span>
                        {lang('total_credited')}
                    </h4>
                    <span id="summary_credited"> {thousands_currency_format($total['credit_hajar']) }</span>
                </div>
            </div>
        </div> 
        <div class="new-dashboard-tile-ewallet raed" >
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left purple-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/income-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total['credit_raed']) }">
                    <h4 title="{lang('total_credited')}"><span style="font-weight:500px;color:black;">{lang('raed')}</span>
                        {lang('total_credited')}
                    </h4>
                    <span id="summary_credited"> {thousands_currency_format($total['credit_raed']) }</span>
                </div>
            </div>
        </div>
        <div class="new-dashboard-tile-ewallet agent">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left brown-bg-new">
                    <img src="{$SITE_URL}/uploads/images/logos/income-w.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total['credit_agent']) }">
                    <h4 title="{lang('total_credited')}"><span style="font-weight:500px;color:black;">{lang('agent_wallet')}</span>
                        {lang('total_credited')}
                    </h4>
                    <span id="summary_credited"> {thousands_currency_format($total['credit_agent']) }</span>
                </div>
            </div>
        </div>
        </div>
        <div>
                {assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_commission_report"} 
        {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_commission_report"}
        {assign var="report_name" value="{lang('custom_wallet')}"}
        
        <div class="panel panel-default">
            <div class="panel-body">
                {form_open('admin/custom_wallet','role="form" class="" method="get" name="custom_wallet" id="custom_wallet" onsubmit="return validation()"')}
                <div class="col-sm-3 padding_both">
                <div class="form-group">
                    <label>{lang('user_name')}</label>
                    <input type="text" class="form-control user_autolist" id="user_name" name="user_name" autocomplete="Off" value="{{$user_name}}">
                </div>
                </div>
                <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label class="" for="daterange">{lang('daterange')}</label>
                    <select name="daterange" id="daterange" class="form-control">
                        <option value="all" {if $daterange == 'all'} selected {/if}>{lang('overall')}</option>
                        <option value="today" {if $daterange == 'today'} selected {/if}>{lang('today')}</option>
                        <option value="month" {if $daterange == 'month'} selected {/if}>{lang('this_month')}</option>
                        <option value="year" {if $daterange == 'year'} selected {/if}>{lang('this_year')}</option>
                        <option value="custom" {if $daterange == 'custom'} selected {/if}>{lang('custom')}</option>
                    </select>
                </div>
            </div>
            <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label>{lang('from_date')}</label>
                    <input autocomplete="off" class="form-control date-picker custom-date" name="from_date" id="from_date" type="date" value="{$from_date}">
                </div>
            </div>
            <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label>{lang('to_date')}</label>
                    <input autocomplete="off" class="form-control date-picker custom-date" name="to_date" id="to_date" type="date" value="{$to_date}">
                </div>
            </div>
                <div class="col-sm-2 padding_both_small">   
                <div class="form-group select-check">
                <label>{lang('wallet_type')}</label>
                            <select name="wallet_type[]" class="form-control select2-wallet_type" id="wallet_type" multiple>
                                {foreach $wallet_types as $category}
                                <option value="{$category}">{lang($category)}</option>
                                {/foreach}
                            </select>
                        </div>
                </div>
                <div class="form-group">
                <div class="col-sm-3 padding_both_small">
                <div class="form-group credit_debit_button">
                    <button class="btn btn-primary" name="commision" type="submit" value="{lang('submit')}">
                        {lang('submit')}</button>
                </div>
                </div>
              {*  <a href="http://localhost/wc/majeed_demo/backoffice/admin/excel/create_excel_custom_wallet_report">
                  <button class="btn btn-sm btn-primary search_filter" type="button"><i class="fa fa-file-excel-o"></i>  Excel
                  </button>
               </a> *}
            </div>
                {form_close()}
            </div>
        </div>
            <div class="panel panel-default" style="padding-left: 10px;">
                <div class="panel-body">
                    {* {var_dump($cwallet_anm)} *}
                        {* <div class="panel panel-default table-responsive"> *}
                            <table st-table="rowCollectionBasic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{lang('sl_no')}</th>
                                        <th>{lang('name')}</th>
                                        
                                        <th>{lang('from_name')}</th>
                                        <th>{lang('amount')}</th>
                                        <th>{lang('wallet_amount')}</th>
                                        <th>{lang('wallet_type')}</th>
                                        <th>{lang('date')}</th>
                                        
                                       
                                    </tr>
                                </thead>
                                {if count($cwallet_anm) > 0}

                                <tbody>
                                {assign var="path" value="{$BASE_URL}admin/"}
                                {assign var="i" value=1}
                                {foreach from=$cwallet_anm item=v}
                                        <tr>
                                            <td>{$i++}</td>
                                            <td>{$v.user_name}</td>
                                            <td>{$v.from_name}</td>
                                            <td>{$v.amount}</td>
                                            <td>{$v.wallet_amount}</td>
                                            <td>{$v.wallet_type}</td>
                                            <td>{date('d M Y - h:i:s A', strtotime($v.date_added))}</td>
                                            
                                        </tr>
                                {/foreach}
                                </tbody>
                                {else}
                                <tbody>
                                    <tr id="tr-empty"><td align="center"><h4 align="center">{lang('no_product_found')}</h4></td></tr>
                                </tbody>
                                {/if}
                            </table>
                        {* </div> *}
                        {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
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
        
        
        

       

        

        {* <!-- User Earnigs -->
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
{include file="newui/admin/custom_wallet/wallet_transfer.tpl"}
{include file="newui/admin/ewallet/fund_credit.tpl"}
{include file="newui/admin/ewallet/fund_debit.tpl"}
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
      <script type="text/javascript" >
    customDateRangeAction();

    $('select#daterange').change();
    function customDateRangeAction() {
        $('select#daterange').on('change', function () {
            var date_inputs = $(this).closest('form').find('.custom-date');
            var date_inputs_parent = $(date_inputs).closest('.form-group').parent('div');
            if (this.value == 'custom') {
                $(date_inputs).attr('disabled', false);
                $(date_inputs_parent).show();
            }
            else {
                $(date_inputs).attr('disabled', true);
                $(date_inputs_parent).hide();
            }
        });
    }
</script>
    <script type="text/javascript" src="{$PUBLIC_URL}javascript/toastr/jquery.toast.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/datatables.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/select2.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/moment.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/daterangepicker.min.js"></script>
    <script src="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/cwallet.js" charset="utf-8" type="text/javascript"></script>
{/block}

{block name=style}
    <style type="text/css">
        .panda{
            
        }
        .hajar{

        }

      .orange-bg-new {
            background: linear-gradient(to top, #ebd442 0%, #deca49 100%);
            padding: 10px;
            margin-right: 10px;
            border-radius: 100%;
            width: 55px;
            height: 55px;
            display: table;
            align-items: center;
        }
        .purple-bg-new {
            background: linear-gradient(to top, #937196 0%, #b89bba 100%);
            padding: 10px;
            margin-right: 10px;
            border-radius: 100%;
            width: 55px;
            height: 55px;
            display: table;
            align-items: center;
        }
        .brown-bg-new {
            background: linear-gradient(to top, #baa39b 0%, #ed815c 100%);
            padding: 10px;
            margin-right: 10px;
            border-radius: 100%;
            width: 55px;
            height: 55px;
            display: table;
            align-items: center;
        }

  
        
    </style>
{/block}