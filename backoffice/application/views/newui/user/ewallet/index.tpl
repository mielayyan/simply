{extends file='newui/layout/user.tpl'}
{block name=$CONTENT_BLOCK}
  <style>
      .error {
          color: #f05050;
      }
      select::-ms-expand {
       display: none;
      }
      .status-search{

      -webkit-appearance: none;
      -moz-appearance: none;
      height: 36px !important;
      border: 1px solid #d8d5d5 !important;
      border-color: #cfdadd !important;
      }

      table.dataTable thead .sorting {
          background: url('../public_html/images/sort_both.png') no-repeat center right;
      }
      table.dataTable thead .sorting_asc {
          background: url('../public_html/images/sort_asc.png') no-repeat center right;
      }
      table.dataTable thead .sorting_desc {
          background: url('../public_html/images/sort_desc.png') no-repeat center right;
      }
      table.dataTable thead .sorting_asc_disabled {
          background: url('../public_html/images/sort_asc_disabled.png') no-repeat center right;
      }
      table.dataTable thead .sorting_desc_disabled {
          background: url('../public_html/images/sort_desc_disabled.png') no-repeat center right;
      }

      table.dataTable tfoot th, table.dataTable tfoot td {
          padding: 10px 18px 6px 10px;
          /*border-top: 1px solid #111;*/
      }
      /*table#user_earnings_table > tbody > tr:last-child > td {
          border-top: 1px solid #000 !important;
          border-bottom: 1px solid #000 !important;
      }
      table#payout_status_approved_paid_table > tbody > tr:first-child > td {
          border-top: 1px solid #ddd !important;
          border-bottom: 1px solid #ddd !important;
      }*/

      {if $MODULE_STATUS['purchase_wallet'] == "yes"}
      .new-dashboard-tile-ewallet-all {
          display: grid;
          grid-template-columns: repeat(5, 1fr);
          grid-gap: 9px;
          margin-bottom: 20px;
          margin-top: 3px;
          padding: 11px 12px;
          margin: 0 auto 0px auto;
          max-width: 100%;
      }
      .tile-new-dashboard-w-wallet {
            min-height: 131px;
        }
        .new-dashboard-btn .dropdown-menu > li > a {
           width: auto;
        }
      {/if}
  </style>
  
  <div class="main-content-new-dashboard">
    <div class="breadcrumb-header-new-dashboard justify-content-between">
      
      <div>
        <h4>{lang('ewallet')}</h4>
      </div>
      
      <div class="d-flex my-auto">
      <div class="new-dashboard-btn">
      <div class="btn-group dropdown">

          <a href="#" data-toggle="modal" data-target="#fund_transfer_modal1" style="float: left;">
              <button class="btn m-b-xs btn-sm btn-primary add-btn"
                  aria-expanded="false">{lang('fund_transfer_to_agent')}</button>
          </a>
      </div>

  </div>
  &nbsp;
        <div class="new-dashboard-btn">
          <div class="btn-group dropdown">
            <a href="#" data-toggle="modal" data-target="#fund_transfer_modal" style="float: left;">
                <button class="btn m-b-xs btn-sm btn-primary add-btn" aria-expanded="false">
                  {lang('ewallet_fund_transfer')}
                </button>
            </a>
           
            
            <button class="btn m-b-xs btn-sm btn-primary btn-addon" data-toggle="dropdown" aria-expanded="false">
              
              <i class="fa fa-caret-down pull-right"></i>
             
            </button>
            
            
            <ul class="dropdown-menu">
            {if $MODULE_STATUS['purchase_wallet'] == 'yes' }
                <li>
                  <a href="#" data-toggle="modal" data-target="#add_fund_modal">{lang('add_purchase_wallet_fund')}</a>
                </li>
              {/if}
              {* <li>
                  <a href="#" data-toggle="modal" data-target="#fund_transfer_modal1">{lang('fund_transfer_to_agent')}</a>
                </li> *}
            </ul> 

            
          </div>
        </div>
      </div>
    </div>
  </div>

  {* Tiles *}
  <div class="tile-new-dashboard-top">
    <div class="new-dashboard-tile-ewallet-all justify-content-center">
      
      <div class="tile-new-dashboard-w-wallet d-flex">
        <div class="e-wallet-image-left green-bg-new">
          <img src="{$SITE_URL}/uploads/images/logos/income-w.png">
        </div>
        <div class="e-wallet-content-right" title="{format_currency($total['credit']) }">
            <h4>{lang('total_credited')}</h4>
            <span id="summary_credited"> {thousands_currency_format($total['credit']) }</span>
        </div>
      </div>

      <div class="new-dashboard-tile-ewallet">
          <div class="tile-new-dashboard-w-wallet d-flex">
              <div class="e-wallet-image-left red-bg-new">
                  <img src="{$SITE_URL}/uploads/images/logos/Bonus-w.png">
              </div>
              <div class="e-wallet-content-right" title="{format_currency($debit)}">
                  <h4>{lang('total_debited')}</h4>
                  <span id="summary_debited"> {thousands_currency_format($debit)}</span>
              </div>
          </div>
      </div>

      <div class="new-dashboard-tile-ewallet">
        <div class="tile-new-dashboard-w-wallet d-flex">
            <div class="e-wallet-image-left blue-bg-new">
                <img src="{$SITE_URL}/uploads/images/logos/E-Wallet-w.png">
            </div>
            <div class="e-wallet-content-right" title="{format_currency($balamount)}">
                <h4>{lang('total_ewallet_balance')}</h4>
                <span id="summary_balance"> {thousands_currency_format($balamount)}</span>
            </div>
        </div>
      </div>

      {if $MODULE_STATUS['purchase_wallet'] == "yes"}
        <div class="new-dashboard-tile-ewallet">
          <div class="tile-new-dashboard-w-wallet d-flex">
              <div class="e-wallet-image-left blue-bg-new">
                  <img src="{$SITE_URL}/uploads/images/logos/income-w.png">
              </div>
              <div class="e-wallet-content-right" title="{format_currency($purchase_wallet)}">
                  <h4>{lang('purchase_wallet')}</h4>
                  <span id="purchase_wallet_tile"> {thousands_currency_format($purchase_wallet)}</span>
              </div>
          </div>
        </div>
      {/if}

      <div class="new-dashboard-tile-ewallet">
          <div class="tile-new-dashboard-w-wallet d-flex">
              <div class="e-wallet-image-left blue-bg-new">
                  <img src="{$SITE_URL}/uploads/images/logos/income-w.png">
              </div>
              <div class="e-wallet-content-right" title="{format_currency($commission_earned)}">
                  <h4>{lang('commission_earned')}</h4>
                  <span id="commission_earned"> {thousands_currency_format($commission_earned)}</span>
              </div>
          </div>
        </div>

    </div>
    {* ./ Tiles end *}
    <div class="new-dashborad-summary pt-10 m-b-xxl">
      <div class="tabs pt-15">
        <input class="tabs__item-input" type="radio" name="tabs" id="ewallet_statement" checked="checked">
        <label class="tabs__item-label" for="ewallet_statement">{lang('statement')}</label>
        <div class="tabs__item-content">
          
          <div class="filter-new">
            <form  id="epin_list_filter_form">
              <div class="row">
                <div class="col-lg-3 col-sm-4 padding_both_small"></div>
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
                          <th>{lang('transaction_date')}</th>
                          <th>{lang('balance')}</th>
                       </tr>
                    </thead>
                    <tbody>
                    </tbody>
                 </table>
            </div>
          </div>
        </div>

        <input class="tabs__item-input" type="radio" name="tabs" id="transfer_history">
        <label class="tabs__item-label" for="transfer_history">{lang('transfer_history')|capitalize}</label>
        <div class="tabs__item-content">
          
          <div class="filter-new">
            <form  id="ewallet_transfer_history_filter_form">
              <div class="row">
                {* <div class="col-lg-4 col-sm-4 padding_both_small"> *}
                    <div class="form-group">
                      <div id="ewallet_transfer_history_daterangepicker"
                          class="date-range-picker">
                          <i class="fa fa-calendar"></i>&nbsp;
                          <span></span> <i class="fa fa-caret-down"></i>
                      </div>
                    </div>
                    
                    <div class="form-group">
                        <select name="transaction_type" id="transaction_type" class="form-control select2-category" multiple>
                            <option value="user_debit">{lang('debit')}</option>
                            <option value="user_credit">{lang('credit')}</option>
                        </select>
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
              {* </div> *}
            </form>
          </div>
         
          <div class="table-wallet">
             <div class="table-responsive">
                 <table id="transfer_history_table" class="display">
                    <thead>
                       <tr>
                          <th>{lang('description')}</th>
                          <th>{lang('amount')}</th>
                          <th>{lang('transaction_fee')}</th>
                          <th>{lang('transaction_date')}</th>
                       </tr>
                    </thead>
                    <tbody>
                    </tbody>
                 </table>
            </div>
          </div>
        </div>

        {if $MODULE_STATUS['purchase_wallet'] == "yes" }
          <input class="tabs__item-input" type="radio" name="tabs" id="purchase_wallet">
          <label class="tabs__item-label" for="purchase_wallet">{lang('purchase_wallet')|capitalize}</label>
          <div class="tabs__item-content">
            
            <div class="filter-new">
              <form  id="purchase_wallet_filter_form">
                <div class="row">
                  
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
                            <th>{lang('transaction_date')}</th>
                            <th>{lang('balance')}</th>
                         </tr>
                      </thead>
                      <tbody>
                      </tbody>
                   </table>
              </div>
            </div>
          </div>
        {/if}

        {* User Earnings *}
        <input class="tabs__item-input" type="radio" name="tabs" id="user_earnings">
        <label class="tabs__item-label" for="user_earnings">{lang('my_earnigs')|capitalize}</label>
        <div class="tabs__item-content">
          <div class="filter-new row" id="">
            <div class="col-md-8">
              <form  id="user_earnings_filter_form">
                <div class="row">
                  <div class="form-group">
                        <div id="user_earnings_daterangepicker"
                            class="date-range-picker">
                            <i class="fa fa-calendar"></i>&nbsp;
                            <span></span> <i class="fa fa-caret-down"></i>
                        </div>
                      </div>
                      
                      <div class="form-group">
                          <select name="user_earnings_categories" id="user_earnings_categories" class="form-control select2-category" multiple>
                              {foreach from=$user_earnings_categories item=c}
                                  <option value="{$c}">{lang("bs_`$c`")}</option>
                              {/foreach}
                          </select>
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
            <div class="col-md-4" id="user_earnings_report_btn"></div>
          </div>
         
          <div class="table-wallet">
             <div class="table-responsive">
                 <table id="user_earnings_table" class="display" data-title="user_earnings">
                    <thead>
                       <tr>
                          <th>{lang('category')}</th>
                          <th>{lang('total_amount')}</th>
                          <th>{lang('tax')}</th>
                          <th>{lang('service_charge')}</th>
                          <th>{lang('amount_payable')}</th>
                          <th>{lang('transaction_date')}</th>
                       </tr>
                    </thead>
                    <tbody>
                    </tbody>
                 </table>
            </div>
          </div>
        </div>
        {* ./User Earnings *}
      </div>
    </div>

  </div>

  <div class="new-dashborad-summary">
    {include file="newui/user/ewallet/fund_transfer.tpl"}
    {include file="newui/user/ewallet/fund_transfer_to_agent.tpl"}
    {include file="newui/user/ewallet/add_fund.tpl"}
  </div>
  
  <div class="no-display" id="template_amount">
    <span class="badge bg-amount">[amount]</span>
  </div>
   <div class="no-display" id="template_balance">
    <span class="badge bg-amount">[balance]</span>
  </div>
  <div class="no-display" id="template_credit_debit">
    <span class="badge bg-[type]">[amount]</span>
</div>

<div id="print_title" class="hidden">
  {include file="user/report/header.tpl" name=""}
</div>

{/block}

{block name="style"}
    {$smarty.block.parent}
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/datepicker.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.css">
    <link href="{$PUBLIC_URL}theme/newui/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{$PUBLIC_URL}javascript/toastr/jquery.toast.min.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/ewallet.css">
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/pay-out.css">
    {* Datatable *}
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable_with_btn.min.css">
    {* ./datatable *}
{/block}

{block name=script}
    {$smarty.block.parent}
    <script type="text/javascript" src="{$PUBLIC_URL}javascript/toastr/jquery.toast.min.js"></script>
    {* Datatable *}
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/datatables_with_btn.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/datatable_sum.min.js"></script>
    {* ./Datatable *}
   
    <script src="{$PUBLIC_URL}theme/newui/js/select2.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/moment.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/daterangepicker.min.js"></script>
    <script src="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/toastr.min.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/user_ewallet.js" charset="utf-8" type="text/javascript"></script>
{/block}