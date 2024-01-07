{extends file='newui/layout/user.tpl'}
{block name=$CONTENT_BLOCK}
<style>
    .new-dashboard-tile-ewallet-all {
        grid-template-columns: repeat(4, 1fr);
    }
    .dataTables_length select
    {
      /*margin-right: -9px;*/
      width: 33px;
    }
    div.dt-buttons {
        position: unset;
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
    .dt-buttons {
        left: 75%;
    }

    /*table > tbody > tr:last-child > td {
          border-top: 1px solid #000 !important;
          border-bottom: 1px solid #000 !important;
      }
      table > tbody > tr:first-child > td {
          border-top: 1px solid #ddd !important;
          border-bottom: 1px solid #ddd !important;
      }*/
    @media (max-width: 1024px) {
    .new-dashboard-tile-ewallet-all {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    }
  }
</style>

<div class="main-content-new-dashboard">
    <div class="breadcrumb-header-new-dashboard justify-content-between">
        <div>
            <h4>{lang('payout')}</h4>
        </div>
        <div class="d-flex my-auto">
        <div class="new-dashboard-btn">
          <div class="btn-group dropdown">
            <a href="#" data-toggle="modal" data-target="#payout_request_modal" style="float: left;">
              <button class="btn m-b-xs btn-sm btn-primary add-btn" aria-expanded="false">{lang('payout_request')}</button>
            </a>
          </div>
         </div>
      </div>
    </div>
</div>

<!--Tiles-->
<div class="tile-new-dashboard-top">
    <div class="new-dashboard-tile-ewallet-all justify-content-center">
        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left yellow-bg-new payout_p11">
                    <img src="{$PUBLIC_URL}/images/newui/pending.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total_amount_active_request)}">
                    <h4>{lang('pending')}</h4>
                    <span id="total_amount_active_request">{thousands_currency_format($total_amount_active_request)}</span>
                </div>
            </div>
        </div>
        
        <div class="new-dashboard-tile-ewallet">
            <div class="tile-new-dashboard-w-wallet d-flex">
                <div class="e-wallet-image-left blue-bg-new">
                    <img src="{$PUBLIC_URL}/images/newui/Approved.png">
                </div>
                <div class="e-wallet-content-right" title="{format_currency($total_amount_waiting_requests)}">
                    <h4>{lang('approved')}</h4>
                    <span id="total_amount_waiting_requests">{thousands_currency_format($total_amount_waiting_requests)}</span>
                </div>
            </div>
        </div>

        <div class="new-dashboard-tile-ewallet">
          <div class="tile-new-dashboard-w-wallet d-flex">
             <div class="e-wallet-image-left green-bg-new ">
                <img src="{$PUBLIC_URL}/images/newui/paid.png">
             </div>
             <div class="e-wallet-content-right" title="{format_currency($total_amount_paid_request)}">
                <h4>{lang('paid')}</h4>
                <span id="total_amount_paid_request">{thousands_currency_format($total_amount_paid_request)}</span>
             </div>
          </div>
       </div>

        <div class="new-dashboard-tile-ewallet">
          <div class="tile-new-dashboard-w-wallet d-flex">
             <div class="e-wallet-image-left red-bg-new payout_p11">
                <img src="{$PUBLIC_URL}/images/newui/Rejected.png">
             </div>
             <div class="e-wallet-content-right" title="{format_currency($total_amount_rejected_requests)}">
                <h4>{lang('rejected')}</h4>
                <span id="total_amount_rejected_requests">{thousands_currency_format($total_amount_rejected_requests)}</span>
             </div>
          </div>
        </div>
    </div>
</div>

<!-- Tabs -->
<div class="new-dashborad-summary pt-10 m-b-xxl">
    <div class="tabs pt-15">

      <input class="tabs__item-input" type="radio" name="tabs" id="tab_payout_status" checked="checked">
      <label class="tabs__item-label" for="tab_payout_status">{lang('payout_summary')}</label>
      <div class="tabs__item-content">
            <div class="filter-new row">
                <div class="col-md-8">
                    <form action="#"  id="payout_status_filter_form">
                       <div class="row">
                            <div class="padding_both_small">
                                <div class="form-group">
                                    <select class="form-control" id="payout_status_filter">
                                      <option value="pending">{lang('status')} - {lang('pending')}</option>
                                      <option value="approved">{lang('status')} - {lang('approved')}</option>
                                      <option value="paid" selected>{lang('status')} - {lang('paid')}</option>
                                      <option value="rejected">{lang('status')} - {lang('rejected')}</option>
                                    </select>
                                </div>
                            </div>
                            <div class="padding_both" style="padding-left: 5px;">
                                <div class="form-group">
                                    <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">
                                    {lang('search')}
                                    </button>
                                    <button class="btn btn-sm btn-info search_clear" type="button">
                                    {lang('reset')}
                                    </button>
                                </div>
                            </div>
                       </div>
                    </form>
                </div>
                <div class="col-md-4" id="table_export_btns"></div>
            </div>
            
            <div class="table-wallet table-payout-summary table-payout-summary-pending hidden">
                <div class="table-responsive">
                    <table id="payout_status_pending_requests_table" class="display">
                        <thead>
                            <tr>
                                <th>
                                <div class="checkbox">
                                <label class="i-checks">
                                <input type="checkbox" name="release_all" id="release_all" class="payout-manual-release-all">
                                <i></i>
                                </label>
                                </div>
                                </th>
                                <th>{lang('requested_date')}</th>
                                <th>{lang('amount')}</th>
                                <th>{lang('ewallet_balance')}</th>
                            </tr>
                        </thead>
                        <tbody>
                            
                        </tbody>
                    </table>
                </div>
            </div>
              
            <div class="table-wallet table-payout-summary table-payout-summary-approved-pending hidden">
                <div class="table-responsive">
                    <table id="payout_status_approved_pending_table" class="display">
                        <thead>
                            <tr>
                                <th>{lang('approved_date')}</th>
                                <th>{lang('amount')}</th>
                                <th>{lang('payout_method')}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
                
            <div class="table-wallet table-payout-summary table-payout-summary-paid hidden">
                <div class="table-responsive">
                    <table id="payout_status_approved_paid_table" class="display">
                        <thead>
                            <tr>
                                <th>{lang('paid_date')}</th>
                                <th>{lang('amount')}</th>
                                <th>{lang('payout_method')}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
                
            <div class="table-wallet table-payout-summary table-payout-summary-rejected hidden">
                <div class="table-responsive">
                    <table id="payout_status_rejected_table" class="display">
                        <thead>
                            <tr>
                                <th>{lang('rejected_date')}</th>
                                <th>{lang('amount')}</th>
                                <th>{lang('requested_date')}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>

<div class="popup-btn-area hidden" id="payout_manual_release_popup">
  <ul>
     <li><a href="#" class="close-popup"><i class="fa fa-times"></i></a></li>
     <li><span class="gray-round" id="payout_manual_release_popup_span">2</span>{lang('items_selected')}</li>
     <li><button class="btn btn-sm btn-primary h3" type="button" id="payout_release_manual_btn"><i class="fa fa-check-square-o" aria-hidden="true"></i>{lang('release')}</button></li>
  </ul>
</div>
   
<div class="popup-btn-area hidden" id="payout_process_popup">
  <ul>
     <li><a href="#" class="close-popup"><i class="fa fa-times"></i></a></li>
     <li><span class="gray-round" id="payout_process_popup_span">2</span>{lang('items_selected')}</li>
     <li><button class="btn btn-sm btn-primary h3" type="button" id="payout_process_btn"><i class="fa fa-check-square-o" aria-hidden="true"></i>{lang('approve')}</button></li>
  </ul>
</div>

<div class="popup-btn-area hidden" id="payout_requests_release_popup">
  <ul>
     <li><a href="#" class="close-popup"><i class="fa fa-times"></i></a></li>
     <li><span class="gray-round" id="payout_requests_release_popup_span">2</span>{lang('items_selected')}</li>
     
     <li><button class="btn btn-sm btn-danger h3" type="button" id="payout_delete_requests_btn"><i class="fa fa-trash-o" aria-hidden="true"></i>{lang('cancel')}</button></li>
  </ul>
</div>
{include file="newui/user/payout/payout_request.tpl"}
</div>
{* Common templates *}
    <div class="no-display" id="template_ewallet_balance">
        <span class='badge bg-success'>[balance_amount]</span>
    </div>  

    <div class="no-display" id="template_amount">
        <span class='badge bg-amount'>[amount]</span>
    </div>    

{* ./Common Templates *}

{* Process Payment list table *}
<div class="no-display" id="template_process_payment_table_checkbox">
    <div class='checkbox'>
        <label class='i-checks'>
            <input type='checkbox' name='request_id[]' class='payout-checkbox payout-process-single' value='[paid_id]'>
            <i></i>
        </label>
    </div>
</div>


<div class="no-display" id="payout_status_approved_pending_list">
    <span class='badge bg-amount'>[paid_amount]</span>
</div>
{* ./Process Payment list table  *}

{* payout_status_pending_requests_table *}
    

    <div class="no-display" id="template_payout_status_pending_requests_table_payout_amount">
        <span class='badge bg-amount'>[payout_amount]</span>
    </div>
{* ./ payout_status_pending_requests_table *}

<div class="no-display" id="template_payout_cancel_checkbox">
    <div class='checkbox'>
        <label class='i-checks'>
            <input type='checkbox' name='request_id[]' class='payout-checkbox payout-manual-release-single' value='[request_id]' data-user_name='[user_name]'>
            <i></i>
        </label>
    </div>
</div>


<div class="no-display" id="template_payout_release_payout_amount_admin">
    <div class='input_width'>
        <div class='input-group'> <span class='input-group-addon'>{$DEFAULT_SYMBOL_LEFT}</span>                                                
            <input type='number' class='payout_amount form-control' value='[payout_amount]'>
        </div>
    </div>
</div>

<div class="no-display" id="template_payout_release_payout_amount_user">
    <span class='badge bg-amount'>[payout_amount]</span> 
    <input type='hidden' class='payout_amount' value='[payout_amount]'>
</div>

<div class="no-display" id="template_payout_release_ewallet_balance">
    <span class='badge bg-amount'>[ewallet_balance_amount]</span>
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
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable_with_btn.min.css">
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable_with_btn.min.css">
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/pay-out.css">
{/block}

{block name=script}
    {$smarty.block.parent}
    <script type="text/javascript" src="{$PUBLIC_URL}javascript/toastr/jquery.toast.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/datatables_with_btn.min.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/select2.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/moment.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/daterangepicker.min.js"></script>
    <script src="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/toastr.min.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/user_payout.js" charset="utf-8" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}javascript/validate_payout_release.js" type="text/javascript"></script>
    {* <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/datatable_sum.min.js"></script> *}
<script>
{literal}
  $(document).ready(function() {
    
  });
{/literal}
</script>

{/block}
