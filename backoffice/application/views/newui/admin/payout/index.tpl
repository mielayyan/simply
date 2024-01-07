{extends file='newui/layout/admin.tpl'}
{block name=$CONTENT_BLOCK}


<div class="main-content-new-dashboard">
    <div class="breadcrumb-header-new-dashboard justify-content-between">
        <div>
            <h4>{lang('payout')}</h4>
        </div>
    </div>
</div>
{block name="style"}
<link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable_with_btn.min.css">
{/block}
<!--Tiles-->
<style type="text/css">
   .new-dashboard-tile-ewallet-all.grid-four {
    grid-template-columns: repeat(4, 1fr);
}
.modal-dialog {
    width: 786px !important;
}
.invoice_shopping table {
    border: 1px solid #ddd !important;
}
</style>
<div class="tile-new-dashboard-top">
{if str_contains($coming_from, 'profile_view')}
    <div class="back-btn" style="padding-right: 10px; text-align: right;">
        <a href="{BASE_URL}/admin/profile_view?user_name={$ci->input->get('user_name')}" class="btn m-b-xs btn-sm btn-info btn-addon" style="height: 32px"><i class="fa fa-backward"></i> {lang('back')}</a>
    </div>
{/if}
    <div class="new-dashboard-tile-ewallet-all grid-four justify-content-center">
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
    {block name="style"}
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable_with_btn.min.css">
    {/block}
<!-- Tabs -->
<div class="new-dashborad-summary pt-10 m-b-xxl">
    <div class="tabs pt-15">

      <input class="tabs__item-input" type="radio" name="tabs" id="tab_payout_status" checked="checked">
      <label class="tabs__item-label" for="tab_payout_status">{lang('payout_summary')}</label>
      <div class="tabs__item-content">
            <div class="filter-new">
                <form action="#"  id="payout_status_filter_form">
                   <div class="row">
                      <div class="padding_both">
                        <div class="form-group">
                            <select class="form-control user-search-select2" id="payout_status_user_name">
                              {if $active_user_name != ""}
                                <option value="{$active_user_name}" selected="selected">{$active_user_name}</option>
                              {/if}
                            </select>
                        </div>
                      </div>
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
            
            <div class="table-wallet table-payout-summary table-payout-summary-pending hidden">
                <div class="table-responsive">
                    <table id="payout_status_pending_requests_table" class="display">
                        <thead>
                            <tr>
                                <th>{lang('member_name')}</th>
                                <th>{lang('amount')}</th>
                                <th>{lang('ewallet_balance')}</th>
                                <th>{lang('requested_date')}</th>
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
                                <th>{lang('member_name')}</th>
                                <th>{lang('amount')}</th>
                                <th>{lang('payout_method')}</th>
                                <th>{lang('approved_date')}</th>
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
                                <th>{lang('member_name')}</th>
                                <th>{lang('invoice_no')}</th>
                                <th>{lang('amount')}</th>
                                <th>{lang('payout_method')}</th>
                                <th>{lang('paid_date')}</th>
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
                                <th>{lang('member_name')}</th>
                                <th>{lang('amount')}</th>
                                <th>{lang('requested_date')}</th>
                                <th>{lang('rejected_date')}</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>

      <!-- Ewallet Summary -->
        <input class="tabs__item-input" type="radio" name="tabs" id="tab_payout_release_manual">
        <label class="tabs__item-label" for="tab_payout_release_manual">{lang('payout_release')}</label>
        <div class="tabs__item-content">
            <div class="filter-new">
                <form action="" id="payout_manual_filter_form">
                   <div class="row">
                        <div class="padding_both">
                            <div class="form-group">
                                <select class="form-control user-search-select2"></select>
                            </div>
                        </div>

                        <div class="padding_both_small">
                            <div class="form-group">
                                <select name="payment_method" id="payment_method" class="form-control" >
                                    {if count($payment_method) >0}
                                        {* <option value="bank">{lang('bank')}</option> *}
                                        {foreach from=$payment_method item="v"}
                                            <option {if $payment_type==$v.gateway_name} selected="selected" {/if} value="{$v.gateway_name}">
                                                {if $v.gateway_name=="Bitcoin"}
                                                    {lang('blocktrail')}
                                                {else}
                                                    {lang($v.gateway_name)}
                                                {/if}
                                            </option>
                                        {/foreach}
                                    {/if}
                                </select>
                            </div>
                        </div>
                        
                        <div class="padding_both_small">
                            <div class="form-group">
                                <select name="payout_release_type" id="payout_release_type" class="form-control">
                                    {if $payout_release == 'both'}
                                        <option value="admin">{lang('manual')}</option>
                                        <option value="user">{lang('user_request')}</option>
                                    {else if $payout_release == 'from_ewallet'}
                                        <option value="admin">{lang('manual')}</option>                         
                                    {else if $payout_release == 'ewallet_request'}
                                        <option value="user">{lang('user_request')}</option>
                                    {/if}
                                </select>
                            </div>
                        </div>
                        {if $MODULE_STATUS['kyc_status'] == "yes"}
                            <div class="padding_both_small">
                                <div class="form-group">
                                    <select name="kyc_status" id="kyc_status" class="form-control">
                                        <option value="active" selected="selected">{lang('kyc_verified')}</option>
                                        <option value="not_active">{lang('kyc_not_verified')}</option>
                                    </select>
                                </div>
                            </div>
                        {/if}

                      <div class="padding_both">
                         <div class="form-group">
                            <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">{lang('search')}</button>
                            <button class="btn btn-sm btn-info search_clear" type="button">{lang('reset')}</button>
                         </div>
                      </div>
                   </div>
            </form>
         </div>
         <div class="table-wallet">
            <div class="table-responsive">
               <table id="payout_release_table" class="display">
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
                        <th>{lang('member_name')}</th>
                        <th>{lang('payout_amount')}</th>
                        <th>{lang('payout_method')}</th>
                        <th>{lang('payout_type')}</th>
                        <th>{lang('ewallet_balance')}</th>
                     </tr>
                  </thead>
                  <tbody></tbody>
               </table>
            </div>
         </div>
    </div>

      <input class="tabs__item-input" type="radio" name="tabs" id="tab_process_payment">
      <label class="tabs__item-label" for="tab_process_payment">{lang('process_payment')}</label>
      <div class="tabs__item-content">
         <div class="filter-new">
            <form action="" id="process_payment_filter_form">
               <div class="row">
                  
                  <div class="padding_both">
                     <div class="form-group">
                        <select class="form-control user-search-select2" id="process_payment_filter_users"><select>
                     </div>
                  </div>
                  <div class="padding_both">
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
         <div class="table-wallet">
            <div class="table-responsive">
               <table id="process_payment_table" class="display">
                  <thead>
                     <tr>
                          <th>
                           <div class="checkbox">
                              <label class="i-checks">
                              <input type="checkbox" name="release_all" id="release_all" class="payout-process-all">
                              <i></i>
                              </label>
                           </div>
                        </th>
                        <th>{lang('member_name')}</th>
                        <th>{lang('amount')}</th>
                        <th>{lang('approved_date')}</th>
                     </tr>
                  </thead>
                  <tbody>
                     
                  </tbody>
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
     <li><button class="btn btn-sm btn-primary h3" type="button" id="payout_release_requests_btn"><i class="fa fa-check-square-o" aria-hidden="true"></i>{lang('release')}</button></li>
     <li><button class="btn btn-sm btn-danger h3" type="button" id="payout_delete_requests_btn"><i class="fa fa-trash-o" aria-hidden="true"></i>{lang('reject')}</button></li>
  </ul>
</div>
</div>

{* Common templates *}
    <div class="no-display" id="template_ewallet_balance">
        <span class='badge bg-success'>[balance_amount]</span>
    </div>  

    <div class="no-display" id="template_amount">
        <span class='badge bg-amount'>[amount]</span>
    </div>    

    <div class="no-display" id="template_payout_member">
        <div class='d-flex'>
            <img src='[user_photo]' alt='img' class='ht-30 wd-30 mr-2'> 
            <div class='margin-wallet-img'>
                <h5>[full_name]</h5>
                <span class='sub-text'>[user_name]</span>
            </div>
        </div>
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

<div class="no-display" id="template_process_payment_table_member">
    <div class='d-flex'>
        <img src='[user_photo]' alt='img' class='ht-30 wd-30 mr-2'> 
        <div class='margin-wallet-img'>
            <h5>[full_name]</h5>
            <span class='sub-text'>[user_name]</span>
        </div>
    </div>
</div>
<div class="no-display" id="payout_status_approved_pending_list">
    <span class='badge bg-amount'>[paid_amount]</span>
</div>
{* ./Process Payment list table  *}

{* payout_status_pending_requests_table *}
    <div class="no-display" id="template_payout_status_pending_requests_table_member">
        <div class='d-flex'>
            <img src='[user_photo]' alt='img' class='ht-30 wd-30 mr-2'> 
            <div class='margin-wallet-img'>
                <h5>[full_name]</h5>
                <span class='sub-text'>[user_name]</span>
            </div>
        </div>
    </div>

    <div class="no-display" id="template_payout_status_pending_requests_table_payout_amount">
        <span class='badge bg-amount'>[payout_amount]</span>
    </div>
{* ./ payout_status_pending_requests_table *}

<div class="no-display" id="template_payout_release_checkbox">
    <div class='checkbox'>
        <label class='i-checks'>
            <input type='checkbox' name='request_id[]' class='payout-checkbox payout-manual-release-single' value='[request_id]' data-user_name='[user_name]'>
            <i></i>
        </label>
    </div>
</div>

<div class="no-display" id="template_payout_release_name">
    <div class="d-flex">
        <img src="[profile_image]" alt="img" class="ht-30 wd-30 mr-2"> 
        <div class="margin-wallet-img">
            <h5>[full_name]</h5>
            <span class="sub-text">[user_name]</span>
        </div>
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

<div class="modal fade" id="invoice_modal" role="dialog">
  <div class="modal-dialog">
    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">{lang('Invoice')}</h4>
      </div>
      <div class="modal-body invoice_shopping">

      
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
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
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/pay-out.css">
{/block}

{block name=script}
    {$smarty.block.parent}
    <script type="text/javascript" src="{$PUBLIC_URL}javascript/toastr/jquery.toast.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/datatables.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/select2.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/moment.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/daterangepicker.min.js"></script>
    <script src="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/toastr.min.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/payout.js" charset="utf-8" type="text/javascript"></script>
<script>
{literal}
  $(document).ready(function() {
    
  });
{/literal}
</script>
{/block}