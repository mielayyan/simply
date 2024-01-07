{extends file='newui/layout/admin.tpl'}
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
  </style>
  
  <div class="main-content-new-dashboard">
    <div class="breadcrumb-header-new-dashboard justify-content-between">
      <div>
        <h4>{lang('epin')}</h4>
      </div>
      
      <div class="d-flex my-auto">

        {if str_contains($coming_from, 'profile_view')}
          <div class="back-btn" style="padding-right: 10px; text-align: right;">
              <a href="{BASE_URL}/admin/profile_view?user_name={$ci->input->get('user_name')}" class="btn m-b-xs btn-sm btn-info btn-addon" style="height: 32px"><i class="fa fa-backward"></i> {lang('back')}</a>
          </div>
        {/if}
        
        <div class="new-dashboard-btn">
          <div class="btn-group dropdown">
            <a href="#" data-toggle="modal" data-target="#create_epin_modal_agent" style="float: left;">
              <button class="btn m-b-xs btn-sm btn-primary add-btn" aria-expanded="false">{lang('add')}</button>
            </a>

            {*<button class="btn m-b-xs btn-sm btn-primary btn-addon" data-toggle="dropdown" aria-expanded="false">
              <i class="fa fa-caret-down pull-right"></i>
            </button>

            <ul class="dropdown-menu">
              <li><a href="" data-toggle="modal" data-target="#epin_transfer_modal">{lang('epin_transfer')}</a></li>
              <li><a href="" data-toggle="modal" data-target="#epin_purchase_modal">{lang('epin_purchase')}</a></li>
            </ul>
            *}
          </div>
         </div>
      </div>
    </div>
  </div>
{block name="style"}
<link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable_with_btn.min.css">
{/block}
  {* Tiles *}
  <div class="tile-new-dashboard-top">
    <div class="new-dashboard-tile-ewallet-all justify-content-center">
      
      <div class="tile-new-dashboard-w-wallet d-flex">
        <div class="e-wallet-image-left green-bg-new">
          <img src="{$SITE_URL}/uploads/images/logos/Paid-w.png">
        </div>
        
        <div class="e-wallet-content-right">
          <h4>{lang('active_epin_count')}</h4>
          <span id="summary_active_epins_count">{$active_epins->count}</span>
        </div>
      </div>
      
      <div class="new-dashboard-tile-ewallet">
        <div class="tile-new-dashboard-w-wallet d-flex">
          <div class="e-wallet-image-left blue-bg-new">
            <img src="{$SITE_URL}/uploads/images/logos/E-Wallet-w.png">
          </div>
        
          <div class="e-wallet-content-right" title="{format_currency($active_epins->amount)}">
            <h4>{lang('active_epinsss')}
            </h4>
             <span id="summary_active_epins">{thousands_currency_format($active_epins->amount)}</span>
          </div>
        </div>
      </div>
      
      <div class="new-dashboard-tile-ewallet">
        <div class="tile-new-dashboard-w-wallet d-flex">
          <div class="e-wallet-image-left yellow-bg-new">
            <img src="{$SITE_URL}/uploads/images/logos/Pending-w.png">
          </div>
          <div class="e-wallet-content-right">
            <h4>
              {lang('epin_requests')} 
            </h4>
             <span id="summary_epin_requests">{$epin_requests_count}</span>
          </div>
        </div>
      </div>
      <div class="new-dashboard-tile-ewallet">
        <div class="tile-new-dashboard-w-wallet d-flex">
          <div class="e-wallet-image-left blue-bg-new">
            <img src="{$SITE_URL}/uploads/images/logos/E-Wallet-w.png">
          </div>
        
          <div class="e-wallet-content-right" title="{format_currency($wallet_balance)}">
            <h4>{lang('Wallet Balance')}
            </h4>
             <span id="summary_active_epins">{thousands_currency_format($wallet_balance)}</span>
          </div>
        </div>
      </div>
    </div>
    {* ./ Tiles end *}
    {block name="style"}
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable_with_btn.min.css">
    {/block}
    <div class="new-dashborad-summary pt-10 m-b-xxl">
      <div class="tabs pt-15">
        
        <input class="tabs__item-input" type="radio" name="tabs" id="epin_list" checked="checked">
        <label class="tabs__item-label" for="epin_list">{lang('epin_list')}</label>
        <div class="tabs__item-content">
          
          <div class="filter-new">
            <form  id="epin_list_filter_form">
              <div class="row">
                <div class="">
                  <div class="form-group">
                    <select class="form-control user-search-select2 user-search-dropdown">
                      {if $active_user_name != ""}
                        <option value="{$active_user_name}" selected="selected">{$active_user_name}</option>
                      {/if}
                    </select>
                  </div>
                </div>
                    
                <div class="">
                  <div class="form-group">
                    <select class="form-control epin-search-select2"></select>
                  </div>
                </div>
                  
                <div class="">
                  <div class="form-group select-check">
                    <select name="amount[]" class="form-control amount-search-select2" multiple="multiple">
                      {foreach $amounts as $amount}
                          <option value="{$amount['amount']}">{format_currency($amount['amount'])}</option>
                      {/foreach}
                    </select>
                  </div>
                </div>
                
                <div class="">
                  <div class="form-group select-check">
                    <select class="form-control" id="epin_status"   name="status">
                      <option value="active" selected="selected">{lang('active')}</option>
                      <option value="blocked">{lang('blocked')}</option>
                      <option value="used_expired">{lang('used_or_expired')}</option>
                      <option value="deleted">{lang('deleted')}</option>
                    </select>
                  </div>
                </div>
                  
                <div class="col-lg-3 col-sm-4 padding_both_small">
                  <div class="form-group">
                    <button class="btn btn-sm btn-primary search_filter" type="submit" id="epin_list_filter_btn" value="search_member_get">
                      {lang('search')}
                    </button>
                    <button class="btn btn-sm btn-info search_clear" type="button">{lang('reset')}</button>
                  </div>
                </div>
              </div>
            </form>
          </div>
         
          <div class="table-wallet">
             <div class="table-responsive">
                 <table id="epin_list_table" class="display">
                    <thead>
                       <tr>
                          <th>
                               <div class="checkbox">
                                      <label class="i-checks">
                                      <input type="checkbox" name="release_all" id="release_all" class="release_requests_all epin-list-select-checkbox-all">
                                      <i></i>
                                      </label>
                                   </div>
                                </th>
                          <th>{lang('allocated_member')}</th>
                          <th>{lang('epin')}</th>
                          <th>{lang('amount')}</th>
                          <th>{lang('balance_amount')}</th>
                          <th>{lang('status')}</th>
                          <th>{lang('expiry_date')}</th>
                       </tr>
                    </thead>
                    <tbody>
                    </tbody>
                 </table>
            </div>
         </div>
    </div>
        <input class="tabs__item-input" type="radio" name="tabs" id="epin_requests">
      <label class="tabs__item-label" for="epin_requests">{lang('epin_requests')}</label>
      <div class="tabs__item-content">
          <div class="filter-new">
            <form action="" id="epin_requests_filter_form">
               <div class="row">
                  <div class="">
                     <div class="form-group">
                        <select class="form-control user-search-select2 user-search-dropdown" id="epin_requests_users"></select>
                     </div>
                  </div>
                  
                  <div class="">
                     <div class="form-group">
                        <button class="btn btn-sm btn-primary search_filter" type="button" id="epin_requests_filter_btn" value="search_member_get">
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
                 <table id="epin_requests_table" class="display">
                    <thead>
                       <tr>
                           <th>
                               <div class="checkbox">
                                      <label class="i-checks">
                                      <input type="checkbox" name="release_all" id="release_all" class="release_requests_all epin-request-select-checkbox-all">
                                      <i></i>
                                      </label>
                                   </div>
                                </th>
                          <th>{lang('name')}</th>
                          <th>{lang('requested_pin_count')}</th>
                          <th>{lang('count')}</th>
                          <th>{lang('amount')}</th>
                          <th>{lang('requested_date')}</th>
                          <th>{lang('expiry_date')}</th>
                       </tr>
                    </thead>
                    <tbody>
                    </tbody>
                 </table>
            </div>
         </div>
          
      </div>
</div>
<div class="popup-btn-area hidden" id="epin_active_list_action_popup">
    <ul>
         <li><a href="#" class="close-popup"> <i class="fa fa-times"></i></a></li>
         <li><span class="gray-round" id="active_items_selected_epin_span">2</span>{lang('items_selected')}</li>
         <li><button class="btn btn-sm btn-primary h3" id="block_epin_btn" type="submit"><i class="fa fa-ban"> </i>  <span id="epin_activate_list_block_text">{lang('block')}</span> </button></li>
         <li id="unbloack_li"><button class="btn btn-sm btn-primary h3" id="unblock_epin_btn" type="submit"><i class="fa fa-ban"> </i>  <span id="epin_activate_list_block_text">{lang('unblock')}</span> </button></li>
         <li><button class="btn btn-sm btn-danger h3 popup-delete-epin-button" type="submit"><i class="fa fa-trash-o"></i>{lang('delete')}</button></li>
    </ul>
</div>

<div class="popup-btn-area hidden" id="epin_requested_list_action_popup">
    <ul>
         <li><a href="#" class="close-popup"><i class="fa fa-times"></i></a></li>
         <li><span class="gray-round" id="requested_items_selected_epin_span">2</span>{lang('items_selected')}</li>
         <li><button class="btn btn-sm btn-primary h3" id="allocate_epin_btn_agent" type="submit"><i class="fa fa-check"> </i> {lang('allocate')} </button></li>
         <li><button class="btn btn-sm btn-danger h3" id="delete_epin_requests_btn" type="submit"><i class="fa fa-trash-o"></i>{lang('delete')}</button></li>
    </ul>
</div>
{include file="newui/admin/epin/create_epin_agent.tpl"}
{include file="newui/admin/epin/epin_purchase.tpl"}
{include file="newui/admin/epin/epin_transfer.tpl"}
</div>
<!-- data table render data assign -->
<div class="no-display" id="template_pin_count">
    <input name='count[]' type='number' min='1' max="[pin_count_max]" class='count' size='4' maxlength='50' value="[pin_count_value]" style='text-align:  center;'>
</div>

<div class="no-display" id="template_balance_amount">
    <span class="badge bg-success">[balance_amount]</span>
</div>
<div class="no-display" id="template_status">
    <span class="text-[type]">[status]</span>
</div>
<div class="no-display" id="template_amount">
    <span class="badge bg-vilot">[amount]</span>
</div>
<div class="no-display" id="template_epin_id">
    <div class='checkbox'>
      <label class='i-checks'>
        <input type='checkbox' name='epin_id[]' class='payout-checkbox epin-list-select-checkbox-single' value="[epin_id]">
        <i></i>
      </label>
    </div>
</div>
<div class="no-display" id="template_epin_request_id">
    <div class='checkbox'>
      <label class='i-checks'>
        <input type='checkbox' name='epin_id[]' class='payout-checkbox epin-request-list-select-checkbox-single' value="[epin_id]">
        <i></i>
      </label>
    </div>
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
    <script src="{$PUBLIC_URL}theme/newui/js/epin.js" charset="utf-8" type="text/javascript"></script>
    <script>
      {literal}
        $(document).ready(function() {
    

        $('#epin_status').select2({
          'placeholder': `${trans('status')}`,
           width: 'auto',
        });
      });

      {/literal}
    </script>
{/block}