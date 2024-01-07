{extends file='newui/layout/admin.tpl'}
{block name=$CONTENT_BLOCK}
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/datepicker.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.css">
    <link href="{$PUBLIC_URL}theme/newui/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/newui/css/toastr.min.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/ewallet.css">
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable.min.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/newui/css/pay_out.css">
<div class="main-content-new-dashboard">
   <div class="breadcrumb-header-new-dashboard justify-content-between">
      <div>
         <h4>{lang('payout')}</h4>
         <nav aria-label="breadcrumb">
            <ol class="breadcrumb-new-dashboard">
               <li class="breadcrumb-item-new-dashboard"><a href="#">{lang('dashboard')}</a> <i class="fa fa-angle-double-right"></i></li>
               <li class="breadcrumb-item-new-dashboard active" aria-current="page">{lang('payout')}</li>
            </ol>
         </nav>
      </div>
   </div>
</div>
<!--new tile-->
<div class="tile-new-dashboard-top">
<div class="new-dashboard-tile-pay-all">
   <div class="new-dashboard-tile-ewallet">
      <div class="tile-new-dashboard-w-wallet d-flex">
         <div class="e-wallet-image-left pink-bg-new">
            <img src="http://demo8.iossmlm.com/IMS/uploads/images/profile_picture/pending.png">
         </div>
         <div class="e-wallet-content-right">
            <h4>Pending  <span>{format_currency($total_amount_active_request)}</span></h4>
         </div>
      </div>
   </div>
   <div class="new-dashboard-tile-ewallet">
      <div class="tile-new-dashboard-w-wallet d-flex">
         <div class="e-wallet-image-left blue-bg-new">
            <img src="http://demo8.iossmlm.com/IMS/uploads/images/profile_picture/Approved.png">
         </div>
         <div class="e-wallet-content-right">
            <h4>Approved  <span>{format_currency($total_amount_waiting_requests)}</span></h4>
         </div>
      </div>
   </div>
   <div class="new-dashboard-tile-ewallet">
      <div class="tile-new-dashboard-w-wallet d-flex">
         <div class="e-wallet-image-left green-bg-new">
            <img src="http://demo8.iossmlm.com/IMS/uploads/images/profile_picture/paid.png">
         </div>
         <div class="e-wallet-content-right">
            <h4>Paid <span>{format_currency($total_amount_paid_request)}</span></h4>
         </div>
      </div>
   </div>
   <div class="new-dashboard-tile-ewallet">
      <div class="tile-new-dashboard-w-wallet d-flex">
         <div class="e-wallet-image-left oreng-bg-new">
            <img src="http://demo8.iossmlm.com/IMS/uploads/images/profile_picture/Rejected.png">
         </div>
         <div class="e-wallet-content-right">
            <h4>Rejected  <span>{format_currency($total_amount_rejected_requests)}</span></h4>
         </div>
      </div>
   </div>
</div>
<!---tab-->
<div class="new-dashborad-summary m-b-xxl">
   <div class="tabs">
      <input class="tabs__item-input" type="radio" name="tabs" id="tabone" checked="checked">
      <label class="tabs__item-label" for="tabone">Payout Release Manual</label>
      <div class="tabs__item-content">
         <div class="filter-new">
            <form action="" id="payout_manual_filter_form">
               <div class="row">
                    <div class="col-lg-2 col-sm-6 padding_both">
                        <div class="form-group">
                            <select class="form-control user-search-select2"></select>
                        </div>
                    </div>

                  <div class="col-lg-2 col-sm-3 col-xs-12 padding_both_small">
                     <div class="form-group">
                        <select name="payment_method" id="payment_method" class="form-control" multiple="multiple">
                           {* <option value="all" selected="">{lang('payout_method')}</option> *}
                            {if count($payment_method) >0}
                            {foreach from=$payment_method item="v"}
                                <option {if $payment_type==$v.gateway_name} selected="selected" {/if} value="{$v.gateway_name}">
                                    {if $v.gateway_name=="Bitcoin"}
                                        {lang('blocktrail')}
                                    {else}
                                        {$v.gateway_name}
                                    {/if}
                                </option>
                            {/foreach}
                            {/if}
                        </select>
                     </div>
                  </div>
                  {* <div class="col-lg-2 col-sm-3 col-xs-12 padding_both_small">
                     <div class="form-group">
                        <select name="cat_type" id="cat_type" class="form-control">
                           <option value="all" selected="">{lang('payout_type')}</option>
                           {if $payout_release == 'both'}
                            <option value="admin" {if $payout_type == "admin"} selected="selected" {/if}>{lang('manual')}</option>
                            <option value="user" {if $payout_type == "user"} selected="selected" {/if}>{lang('user_request')}</option>
                            {else if $payout_release == 'from_ewallet'}
                            <option value="admin" {if $payout_type == "admin"} selected="selected" {/if}>{lang('manual')}</option>                         
                            {else if $payout_release == 'ewallet_request'}
                            <option value="user" {if $payout_type == "user"} selected="selected" {/if}>{lang('user_request')}</option>
                            {else}
                           {/if}
                        </select>
                     </div>
                  </div>*}
                  <div class="col-lg-3 col-sm-3 padding_both">
                     <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">{lang('search')}</button>
                        <a class="btn btn-sm btn-info clear-btn" href="">{lang('reset')}</a>
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
      
        <!-- Payout Release Requests -->
        <input class="tabs__item-input" type="radio" name="tabs" id="tab_payout_requests">
        <label class="tabs__item-label" for="tab_payout_requests">{lang('payout_release')} ({lang('requests')})</label>
        <div class="tabs__item-content">
         <div class="filter-new">
            <form action="" id="payout_requests_release_filter_form">
               <div class="row">
                    <div class="col-lg-2 col-sm-6 padding_both">
                        <div class="form-group">
                            <select class="form-control user-search-select2" id="payout_request_users"></select>
                        </div>
                    </div>

                  <div class="col-lg-2 col-sm-3 col-xs-12 padding_both_small">
                     <div class="form-group">
                        <select name="payment_method" id="payout_requests_payment_method" class="form-control" multiple="multiple">
                           {* <option value="all" selected="">{lang('payout_method')}</option> *}
                            {if count($payment_method) >0}
                            {foreach from=$payment_method item="v"}
                                <option {if $payment_type==$v.gateway_name} selected="selected" {/if} value="{$v.gateway_name}">
                                    {if $v.gateway_name=="Bitcoin"}
                                        {lang('blocktrail')}
                                    {else}
                                        {$v.gateway_name}
                                    {/if}
                                </option>
                            {/foreach}
                            {/if}
                        </select>
                     </div>
                  </div>
                  {* <div class="col-lg-2 col-sm-3 col-xs-12 padding_both_small">
                     <div class="form-group">
                        <select name="cat_type" id="cat_type" class="form-control">
                           <option value="all" selected="">{lang('payout_type')}</option>
                           {if $payout_release == 'both'}
                            <option value="admin" {if $payout_type == "admin"} selected="selected" {/if}>{lang('manual')}</option>
                            <option value="user" {if $payout_type == "user"} selected="selected" {/if}>{lang('user_request')}</option>
                            {else if $payout_release == 'from_ewallet'}
                            <option value="admin" {if $payout_type == "admin"} selected="selected" {/if}>{lang('manual')}</option>                         
                            {else if $payout_release == 'ewallet_request'}
                            <option value="user" {if $payout_type == "user"} selected="selected" {/if}>{lang('user_request')}</option>
                            {else}
                           {/if}
                        </select>
                     </div>
                  </div>*}
                  <div class="col-lg-3 col-sm-3 padding_both">
                     <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit" id="search_member_get_" value="search_member_get">{lang('search')}</button>
                        <a class="btn btn-sm btn-info clear-btn" href="">{lang('reset')}</a>
                     </div>
                  </div>
               </div>
            </form>
         </div>
         <div class="table-wallet">
            <div class="table-responsive">
               <table id="payout_requests_table" class="display">
                  <thead>
                     <tr>
                        <th>
                           <div class="checkbox">
                              <label class="i-checks">
                              <input type="checkbox" name="release_all" id="release_all" class="payout-request-release-all">
                              <i></i>
                              </label>
                           </div>
                        </th>
                        <th>{lang('member_name')}</th>
                        <th>{lang('payout_amount')}</th>
                        <th>{lang('payout_method')}</th>
                        <th>{lang('payout_type')}</th>
                        <th>{lang('requested_date')}</th>
                     </tr>
                  </thead>
                  <tbody></tbody>
               </table>
            </div>
         </div>
      </div>
        <!-- Payout Release Requests -->
      
      <input class="tabs__item-input" type="radio" name="tabs" id="tabonetwo">
      <label class="tabs__item-label" for="tabonetwo">{lang('payout_status')}</label>
        
        <div class="tabs__item-content">
            <div class="filter-new">
                <form action="#"  id="payout_status_filter_form">
                   <div class="row">
                        <div class="col-sm-8 col-xs-12  padding_both">
                            <div class="col-lg-4 col-sm-6 col-xs-12 padding_both">
                                <div class="form-group">
                                    <select class="form-control user-search-select2" id="payout_status_user_name"></select>
                                </div>
                            </div>
                            <div class="col-lg-4 col-sm-4 padding_both">
                                <div class="form-group">
                                    <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">
                                    {lang('search')}
                                    </button>
                                    <a class="btn btn-sm btn-info clear-btn" href="">{lang('reset')}</a>
                                </div>
                            </div>
                        </div>
                   </div>
                </form>
            </div>
            
            <ul class="nav nav-tabs">
              <li class="active"><a data-toggle="tab" href="#home">{lang('pending_requests')}</a></li>
              <li><a data-toggle="tab" href="#menu1">{lang('approved_pending_payment')}</a></li>
              <li><a data-toggle="tab" href="#menu2">{lang('approved_paid')}</a></li>
              <li><a data-toggle="tab" href="#menu3">{lang('rejected_requests')}</a></li>
            </ul>
            <div class="tab-content">
                <div id="home" class="tab-pane fade in active">
                    <div class="table-wallet">
                        <div class="table-responsive">
                            <table id="payout_status_pending_requests_table" class="display">
                                <thead>
                                    <tr>
                                        <th>#</th>  
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
                </div>
                            
                <div id="menu1" class="tab-pane fade">
                    <div class="table-wallet">
                        <div class="table-responsive">
                            <table id="payout_status_approved_pending_table" class="display">
                                <thead>
                                    <tr>
                                        <th>#</th>  
                                        <th>{lang('member_name')}</th>
                                        <th>{lang('amount')}</th>
                                        <th>{lang('payout_method')}</th>
                                        <th>{lang('aprroved_date')}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div id="menu2" class="tab-pane fade">
                    <div class="table-wallet">
                        <div class="table-responsive">
                            <table id="payout_status_approved_paid_table" class="display">
                                <thead>
                                    <tr>
                                        <th>#</th>  
                                        <th>{lang('member_name')}</th>
                                        <th>{lang('amount')}</th>
                                        <th>{lang('payout_method')}</th>
                                        <th>{lang('paid_date')}</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                <div id="menu3" class="tab-pane fade">
                    <div class="table-wallet">
                        <div class="table-responsive">
                            <table id="payout_status_rejected_table" class="display">
                                <thead>
                                    <tr>
                                        <th>#</th>  
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
            </div>
        </div>
      <input class="tabs__item-input" type="radio" name="tabs" id="tabthree">
      <label class="tabs__item-label" for="tabthree">{lang('process_payment')}</label>
      <div class="tabs__item-content">
         <div class="filter-new">
            <form action="" id="process_payment_filter_form">
               <div class="row">
                   <div class="col-sm-8 col-xs-12  padding_both">
                  <div class="col-lg-4 col-sm-6 col-xs-12 padding_both">
                     <div class="form-group">
                        <select class="form-control user-search-select2" id="process_payment_filter_users"><select>
                     </div>
                  </div>
                  <div class="col-lg-6 col-sm-4 padding_both">
                     <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">
                        {lang('search')}
                        </button>
                        <a class="btn btn-sm btn-info clear-btn" href="">{lang('reset')} </a>
                     </div>
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
         <li><a href=""><i class="fa fa-times"></i></a></li>
         <li><span class="gray-round" id="payout_manual_release_popup_span">2</span>{lang('items_selected')}</li>
         <li><button class="btn btn-sm btn-primary h3" type="button" id="payout_release_manual_btn"><i class="fa fa-spinne"></i>{lang('release')}</button></li>
      </ul>
   </div>
   
   <div class="popup-btn-area hidden" id="payout_process_popup">
      <ul>
         <li><a href=""><i class="fa fa-times"></i></a></li>
         <li><span class="gray-round" id="payout_process_popup_span">2</span>{lang('items_selected')}</li>
         <li><button class="btn btn-sm btn-primary h3" type="button" id="payout_process_btn"><i class="fa fa-check"></i>{lang('confirm')}</button></li>
      </ul>
   </div>
   
   <div class="popup-btn-area hidden" id="payout_requests_release_popup">
      <ul>
         <li><a href=""><i class="fa fa-times"></i></a></li>
         <li><span class="gray-round" id="payout_requests_release_popup_span">2</span>{lang('items_selected')}</li>
         <li><button class="btn btn-sm btn-primary h3" type="button" id="payout_release_requests_btn"><i class="fa fa-check"></i>{lang('release')}</button></li>
         <li><button class="btn btn-sm btn-danger h3" type="button" id="payout_delete_requests_btn"><i class="fa fa-trash-o"></i>{lang('delete')}</button></li>
      </ul>
   </div>
</div>
<!--popup-->

{/block}
{block name="style"}
{$smarty.block.parent}
<link rel="stylesheet" href="{$PUBLIC_URL}theme/newui/css/head-tile-new.css">
<link rel="stylesheet" href="{$PUBLIC_URL}theme/newui/css/pay-out.css">
<link rel="stylesheet" href="{$PUBLIC_URL}theme/newui/css/ewallet.css">
</link>
<link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/css/datatable.css">
{/block}
{block name=script}
{$smarty.block.parent}

<script type="text/javascript" charset="utf8" src="{$PUBLIC_URL}theme/newui/js/datatables.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/select2.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/moment.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/daterangepicker.min.js"></script>
    <script src="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/toastr.min.js"></script>
<script>
{literal}
$(document).ready(function() {
    // $('.clear-btn').on('click', function(e) {
    //   e.preventDefault();
    //   $(this).closest('form').find("select").prop('selectedIndex',0);
    // });
    
    $('.user-search-select2').select2({
        minimumInputLength: 1,
        multiple: true,
        placeholder : `${trans("user")}`,
        allowClear: true,
        closeOnSelect : false,
        width: 'element',
        ajax: {
            url: $('#base_url').val()+"admin/ewallet/user_search",
            dataType: 'json',
            delay: 250,
            processResults: function (response) {
               return {
                  results: response
               };
            }
        }
    });
    
    $('#payment_method').select2({
        'placeholder': `${trans('payment_method')}`
    });
    
    var payout_release_table = $('#payout_release_table').DataTable({
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/payout_release",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('.user-search-select2').val(),
                'payment_method': $('#payment_method').val(),
              });
            }
        },
        "columns": [
            { "data": "checkbox", "orderable": false},
            { "data": "member_name" },
            { "data": "payout_amount"},
            { "data": "payout_method" },
            { "data": "payout_type" },
            { "data": "ewallet_balance" }
        ]
    });
    
    $('#payout_manual_filter_form').on('submit', function(e) {
       e.preventDefault();
       payout_release_table.draw()
    });
    
    $('.payout-manual-release-all').click(function () {
        $(this).is(':checked') ? $('.payout-manual-release-single').prop('checked', true) : $('.payout-manual-release-single').prop('checked', false);
        showPayoutManualActionPopup();
    });
    
    $('body').on('click', '.payout-manual-release-single', function() {
       //alert('her');
       showPayoutManualActionPopup();
    });
    
    
    
    $('#payout_release_manual_btn').on('click', function() {
        let selected_payouts = [];
        $('.payout-manual-release-single:checked').each(function(){	
            selected_payouts.push({
                'user_name': $(this).val(),
                'amount': $(this).closest('tr').find('.payout_amount').val()
            });
        });
        $.ajax({
           'method': 'POST',
           'url': $('#base_url').val()+"/admin/payout/payout_release_action",
           'data': {
               'payouts': selected_payouts,
               'payout_type': 'admin',
               'payment_method': 'bank'
           }, 
           success: function(response) {
               response = JSON.parse(response);
                if(response.status == "failed") {
                    if(response.error_type == "validation") {
                        toastr.error(response.message);
                    } else if(response.error_type == "unknown") {
                        toastr.error(response.message)
                    }
                } else if(response.status == "success") {
                    payout_release_table.draw()
                    toastr.success(response.message)
                    $('#payout_manual_release_popup').addClass('hidden')
                }
           }, 
           beforeSend: function() {
                $('#payout_release_manual_btn').button('loading');
                $('#payout_release_manual_btn').button('loading');
          },
          complete: function() {
                $('#payout_release_manual_btn').button('reset');
                $('#payout_release_manual_btn').button('reset');
          }
        }); 
    });
    
    // Payout Requests 
    var payout_requests_table = $('#payout_requests_table').DataTable({
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/payout_requests",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('#payout_request_users').val(),
                'payment_method': $('#payout_requests_payment_method').val(),
              });
            }
        },
        "columns": [
            { "data": "checkbox", "orderable": false},
            { "data": "member_name" },
            { "data": "payout_amount"},
            { "data": "payout_method" },
            { "data": "payout_type" },
            { "data": "requested_date" }
        ]
    });
    
    $('#payout_requests_payment_method').select2();
    
    $('.payout-request-release-all').click(function () {
        $(this).is(':checked') ? $('.payout-requests-release-single').prop('checked', true) : $('.payout-requests-release-single').prop('checked', false);
        showPayoutRequestsActionPopup();
    });
    
    $('body').on('click', '.payout-requests-release-single', function() {
       showPayoutRequestsActionPopup();
    });
    
    $('#payout_requests_release_filter_form').on('submit', function(e) {
        e.preventDefault(); 
        payout_requests_table.draw();
    });
    
    $('#payout_release_requests_btn').on('click', function() {
        let selected_payouts = [];
        $('.payout-requests-release-single:checked').each(function(){	
            selected_payouts.push({
                'user_name': $(this).val(),
                'amount': $(this).closest('tr').find('.payout_amount').val()
            });
        });
        $.ajax({
           'method': 'POST',
           'url': $('#base_url').val()+"/admin/payout/payout_requests_release_action",
           'data': {
               'payouts': selected_payouts,
               'payout_type': 'user',
               'payment_method': 'bank'
           },
           success: function(response) {
               response = JSON.parse(response);
                if(response.status == "failed") {
                    if(response.error_type == "validation") {
                        toastr.error(response.message);
                    } else if(response.error_type == "unknown") {
                        toastr.error(response.message)
                    }
                } else if(response.status == "success") {
                    payout_requests_table.draw()
                    toastr.success(response.message)
                    $('#payout_requests_release_popup').addClass('hidden')
                }
           }, 
           beforeSend: function() {
                $('#payout_release_requests_btn').button('loading');
                $('#payout_delete_requests_btn').button('loading');
          },
          complete: function() {
                $('#payout_release_requests_btn').button('reset');
                $('#payout_delete_requests_btn').button('reset');
          }
        }); 
    });
    
    $('#payout_delete_requests_btn').on('click', function() {
        let selected_payouts = [];
        $('.payout-requests-release-single:checked').each(function(){	
            selected_payouts.push($(this).val());
        });
        $.ajax({
           'method': 'POST',
           'url': $('#base_url').val()+"/admin/payout/payout_requests_delete_action",
           'data': {
               'payouts': selected_payouts,
               'payout_type': 'user',
               'payment_method': 'bank'
           },
           success: function(response) {
               response = JSON.parse(response);
                if(response.status == "failed") {
                    if(response.error_type == "validation") {
                        toastr.error(response.message);
                    } else if(response.error_type == "unknown") {
                        toastr.error(response.message)
                    }
                } else if(response.status == "success") {
                    payout_requests_table.draw()
                    toastr.success(response.message)
                    $('#payout_requests_release_popup').addClass('hidden')
                }
           }, 
           beforeSend: function() {
                $('#payout_release_requests_btn').button('loading');
                $('#payout_delete_requests_btn').button('loading');
          },
          complete: function() {
                $('#payout_release_requests_btn').button('reset');
                $('#payout_delete_requests_btn').button('reset');
          }
        }); 
    });
    
    
    // Process Payment 
    var process_payment_table =  $('#process_payment_table').DataTable({
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/process_payment_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('#process_payment_filter_users').val(),
              });
            }
        },
        "columns": [
            { "data": "checkbox", "orderable": false},
            { "data": "member_name" },
            { "data": "amount"},
            { "data": "approved_date" }
        ]
    });
    
    $('#process_payment_filter_form').on('submit', function(e) {
        e.preventDefault();
        process_payment_table.draw();
    })
    
    $('.payout-process-all').click(function () {
        $(this).is(':checked') ? $('.payout-process-single').prop('checked', true) : $('.payout-process-single').prop('checked', false);
        showPayoutProcessActionPopup();
    });
    
    $('body').on('click', '.payout-process-single', function() {
       showPayoutProcessActionPopup();
    });
    
    
    $('#payout_process_btn').on('click', function() {
        let selected_payouts = [];
        $('.payout-process-single:checked').each(function(){	
            selected_payouts.push($(this).val());
        });
        $.ajax({
           'method': 'POST',
           'url': $('#base_url').val()+"/admin/payout/process_payout_action",
           'data': {
               'payouts': selected_payouts,
           },
           success: function(response) {
               response = JSON.parse(response);
                if(response.status == "failed") {
                    if(response.error_type == "validation") {
                        toastr.error(response.message);
                    } else if(response.error_type == "unknown") {
                        toastr.error(response.message)
                    }
                } else if(response.status == "success") {
                    process_payment_table.draw()
                    toastr.success(response.message)
                    $('#payout_process_popup').addClass('hidden')
                }
           }, 
           beforeSend: function() {
                $('#payout_process_btn').button('loading');
          },
          complete: function() {
                $('#payout_process_btn').button('reset');
          }
        }); 
    });
    
    // Payout staus
    var payout_status_pending_requests_table =  $('#payout_status_pending_requests_table').DataTable({
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/payout_status_pending_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('#payout_status_user_name').val()
              });
            }
        },
        "columns": [
            { "data": "slno"},
            { "data": "member_name" },
            { "data": "amount"},
            { "data": "ewallet_balance", 'orderable': false },
            { "data": "requested_date" }
        ]
    });
    
    var payout_status_approved_pending_table =  $('#payout_status_approved_pending_table').DataTable({
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/payout_status_approved_pending_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('#payout_status_user_name').val()
              });
            }
        },
        "columns": [
            { "data": "slno", 'orderable': false},
            { "data": "member_name" },
            { "data": "amount"},
            { "data": "payout_method" },
            { "data": "approved_date" }
        ]
    });
    
    var payout_status_approved_paid_table =  $('#payout_status_approved_paid_table').DataTable({
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/payout_status_approved_paid_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('#payout_status_user_name').val()
              });
            }
        },
        "columns": [
            { "data": "slno"},
            { "data": "member_name" },
            { "data": "amount"},
            { "data": "payout_method" },
            { "data": "paid_date" }
        ]
    });
    
    var payout_status_rejected_table =  $('#payout_status_rejected_table').DataTable({
        "searching": false,
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "ajax": {
            url : $('#base_url').val()+"/admin/payout/payout_status_rejected_list",
            type : 'GET',
            "data": function ( d ) {
              return $.extend( {}, d, {
                'user_names': $('#payout_status_user_name').val()
              });
            }
        },
        "columns": [
            { "data": "slno"},
            { "data": "member_name" },
            { "data": "amount"},
            { "data": "requested_date" },
            { "data": "rejected_date" }
        ]
    });
    
    $('#payout_status_filter_form').on('submit', function(e) {
        e.preventDefault();
        payout_status_pending_requests_table.draw();
        payout_status_approved_pending_table.draw();
        payout_status_approved_paid_table.draw();
        payout_status_rejected_table.draw();
    });
    
    $('.tabs__item-input').on('change', function() {
        $('.popup-btn-area').addClass('hidden');
    })
    
});
    function showPayoutProcessActionPopup() {
        if ($(".payout-process-single:checked").length > 0) { // any one is checked
            let items_selected = $(".payout-process-single:checked").length;
            $('#payout_process_popup_span').text(items_selected);
            $('#payout_process_popup').removeClass('hidden');
        } else { // none is checked
            $('.payout-process-single').prop('checked', false);
            $('#payout_process_popup').addClass('hidden');
        }
    }
    
    function showPayoutManualActionPopup() {
        if ($(".payout-manual-release-single:checked").length > 0) { // any one is checked
            
            // $('.epin-list-select-checkbox-all').prop('checked', true);
            let items_selected = $(".payout-manual-release-single:checked").length;
            //alert(items_selected);
            $('#payout_manual_release_popup_span').text(items_selected);
            $('#payout_manual_release_popup').removeClass('hidden');
        } else { // none is checked
            $('.payout-manual-release-single').prop('checked', false);
            $('#payout_manual_release_popup').addClass('hidden');
        }
    }
    
    function showPayoutRequestsActionPopup() {
        if ($(".payout-requests-release-single:checked").length > 0) { // any one is checked
            let items_selected = $(".payout-requests-release-single:checked").length;
            $('#payout_requests_release_popup_span').text(items_selected);
            $('#payout_requests_release_popup').removeClass('hidden');
        } else { // none is checked
            $('.payout-requests-release-single').prop('checked', false);
            $('#payout_requests_release_popup').addClass('hidden');
        }
    }
{/literal}
</script>
{/block} 