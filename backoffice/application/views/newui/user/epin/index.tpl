{extends file='newui/layout/admin.tpl'}
{block name=$CONTENT_BLOCK}
  <div class="main-content-new-dashboard">
    <div class="breadcrumb-header-new-dashboard justify-content-between">
      <div>
        <h4>{lang('epin')}</h4>
      </div>
      
      <div class="d-flex my-auto">
        <div class="new-dashboard-btn">
          <div class="btn-group dropdown">
            <a href="#" data-toggle="modal" data-target="#epin_purchase_modal" style="float: left;">
              <button class="btn m-b-xs btn-sm btn-primary add-btn" aria-expanded="false">{lang('epin_purchase')}</button>
            </a>

            <button class="btn m-b-xs btn-sm btn-primary btn-addon" data-toggle="dropdown" aria-expanded="false">
              <i class="fa fa-caret-down pull-right"></i>
            </button>

            <ul class="dropdown-menu">
              <li><a href="" data-toggle="modal" data-target="#request_epin_modal">{lang('request_e_pin')}</a></li>
              <li><a href="" data-toggle="modal" data-target="#epin_transfer_modal ">{lang('epin_transfer')}</a></li>
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
    </div>
    {* ./ Tiles end *}

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
                         <th>{lang('epin')}</th>
                          <th>{lang('amount')}</th>
                          <th>{lang('balance_amount')}</th>
                          <th>{lang('status')}</th>
                          <th>{lang('expiry_date')}</th>
                          <th>{lang('Action')}</th>
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
          
         <div class="table-wallet">
             <div class="table-responsive">
                 <table id="epin_requests_table" class="display">
                    <thead>
                       <tr>
                          <th>{lang('requested_date')}</th>
                          <th>{lang('expiry_date')}</th>
                          <th>{lang('requested_pin_count')}</th>
                          <th>{lang('amount')}</th>
                       </tr>
                    </thead>
                    <tbody>
                    </tbody>
                 </table>
            </div>
         </div>
          
      </div>
      

      <input class="tabs__item-input" type="radio" name="tabs" id="epin_transfer_report">
      <label class="tabs__item-label" for="epin_transfer_report">{lang('epin_transfer_history')}</label>
      
      <div class="tabs__item-content">

        <div class="filter-new row">
          <div class="col-md-8">
            <form  id="epin_transfer_history_filter_form">
              <div class="row">
                {* <div class="col-lg-4 col-sm-4 padding_both_small"> *}
                    <div class="form-group">
                      <div id="epin_transfer_history_daterangepicker"
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
              {* </div> *}

            </form>
          </div>
            <div class="col-md-4" id="user_transfer_report_btn"></div>
          </div>

         <div class="table-wallet">
             <div class="table-responsive">
                 <table id="epin_transfer_report_table" class="display">
                    <thead>
                       <tr>
                          <th>{lang('member_name')}</th>
                          <th>{lang('epin')}</th>
                          <th>{lang('amount')}</th>
                          <th>{lang('transferred_date')}</th>
                          <th>{lang('transfered_or_recieved')}</th>
                       </tr>
                    </thead>
                    <tbody>
                    </tbody>
                 </table>
            </div>
         </div>
      </div>

</div>


<div class="popup-btn-area hidden" id="epin_requested_list_action_popup">
    <ul>
         <li><a href="#" class="close-popup"><i class="fa fa-times"></i></a></li>
         <li><span class="gray-round" id="requested_items_selected_epin_span">2</span>{lang('items_selected')}</li>
         <li><button class="btn btn-sm btn-primary h3" id="allocate_epin_btn" type="submit"><i class="fa fa-check"> </i> {lang('allocate')} </button></li>
         <li><button class="btn btn-sm btn-danger h3" id="delete_epin_requests_btn" type="submit"><i class="fa fa-trash-o"></i>{lang('delete')}</button></li>
    </ul>
</div>
{include file="newui/user/epin/request_epin.tpl"}
{include file="newui/user/epin/epin_purchase.tpl"}
{include file="newui/user/epin/epin_transfer.tpl"}
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
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/epin_tranfer_report.css">
{/block}

{block name=script}
    {$smarty.block.parent}
    <script type="text/javascript" src="{$PUBLIC_URL}javascript/toastr/jquery.toast.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/datatable_pdfmake.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/datatables_with_btn.min.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/select2.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/moment.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/daterangepicker.min.js"></script>
    <script src="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/toastr.min.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/user_epin.js" charset="utf-8" type="text/javascript"></script>
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
