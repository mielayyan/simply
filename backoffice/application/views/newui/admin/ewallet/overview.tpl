
{extends file='newui/layout/admin.tpl'}
    {block name=$CONTENT_BLOCK}
        <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/datepicker.css">
        <link rel="stylesheet" href="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.css">
        <link href="{$PUBLIC_URL}theme/newui/css/select2.min.css" rel="stylesheet" />
        <link rel="stylesheet" href="{$PUBLIC_URL}theme/newui/css/toastr.min.css">
        <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/ewallet.css">
        <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable.min.css">
        <input type = "hidden" name = "data_tabel_lang" id = "data_tabel_lang" value = '{$data_table_lang}' />
        <input type = "hidden" name = "date_range_lage" id = "date_range_lage" value = '{$date_range_lage}' />
        <input type = "hidden" name = "date_range_label" id = "date_range_label" value = '{$date_range_label}' />
        <div class="main-content-new-dashboard">
           <div class="breadcrumb-header-new-dashboard justify-content-between">
                <div>
                    <h4>{lang('ewallet')}</h4>
                    
                </div>
                <div class="d-flex my-auto">
                    <div class="new-dashboard-btn">
                        <div class="btn-group dropdown">
                            <a href="#" data-toggle="modal" data-target="#fund_transfer_modal" style="float: left;">
                            <button class="btn m-b-xs btn-sm btn-primary add-btn" aria-expanded="false">{lang('fund_transfer')}</button>
                            </a>
                            <button class="btn m-b-xs btn-sm btn-primary btn-addon" data-toggle="dropdown" aria-expanded="false"><i class="fa fa-caret-down pull-right"></i></button>
                            <ul class="dropdown-menu">
                                <li><a href="#" data-toggle="modal" data-target="#fund_credit_modal">{lang('credit_fund')}</a></li>
                                <!-- <li class="divider"></li> -->
                                <li><a href="#" data-toggle="modal" data-target="#debit_fund_modal">{lang('debit_fund')}</a></li>
                                <!-- <li class="divider"></li> -->
                                
                            </ul>
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
                        <div class="e-wallet-image-left pink-bg-new">
                            <img src="{$PUBLIC_URL}/images/newui/total_credited.png">
                        </div>
                        <div class="e-wallet-content-right">
                            <h4>{lang('total_credited')}</h4>
                             <span> {format_currency($ewallet_summary['total']['credit']) }</span>
                        </div>
                    </div>
                </div>
              
                <div class="new-dashboard-tile-ewallet">
                    <div class="tile-new-dashboard-w-wallet d-flex">
                        <div class="e-wallet-image-left blue-bg-new">
                            <img src="{$PUBLIC_URL}/images/newui/total_debited.png">
                        </div>
                        <div class="e-wallet-content-right">
                            <h4>{lang('total_debited')}</h4>
                             <span> {format_currency($ewallet_summary['total']['debit']) }</span>
                        </div>
                    </div>
                </div>
           
                <div class="new-dashboard-tile-ewallet">
                    <div class="tile-new-dashboard-w-wallet d-flex">
                        <div class="e-wallet-image-left green-bg-new">
                            <img src="{$PUBLIC_URL}/images/newui/ewallet_balance.png">
                        </div>
                 
                        <div class="e-wallet-content-right">
                            <h4>{lang('total_ewallet_balance')}</h4>
                             <span> {format_currency($ewallet_summary['total']['balance'])}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="new-dashborad-summary pt-10 m-b-xxl">
            <div class="tabs pt-15">
                <input class="tabs__item-input" type="radio" name="tabs" id="tabone" checked="checked">
                <label class="tabs__item-label" for="tabone">{lang('ewallet_summary')}</label>
                    <div class="tabs__item-content">
                        <div class="filter-new">
                            <form action="" id="ewallet_summary_form">
                                <div class="row">
                                    <div class="col-lg-4 col-sm-6 padding_both">
                                        <div class="form-group">
                                            <div  id="date_range_ewallet_summary" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                              <input type="hidden" id="join_date" name="custId" value="{$join_date}"> 
                                                <i class="fa fa-calendar"></i>&nbsp;
                                                <span></span> <i class="fa fa-caret-down"></i>
                                            </div> 

                                        </div>
                                    </div>
                                   
                                </div>
                            </form>
                        </div>
                        <div class="debit-credit-all">
                            <div class="debit-credit">
                                <div class="list-group">
                                    <div class="list-group-item list-group-item-header color-text credit">{lang('credit')}</div>
                                        <div id="credited_items">
                                            {foreach from=$credited item=item key="type"}
                                                <div class="list-group-item">
                                                    <span class="badge bg-success">{format_currency($item['amount'])}</span>
                                                    {lang($type)}
                                                </div>
                                            {/foreach}
                                        </div>
                                   </div>
                                </div>
                                <div class="debit-credit">
                                    <div class="list-group">
                                        <div class="list-group-item list-group-item-header color-text debit">{lang('debit')}</div>
                                            <div id="debited_items">
                                            {foreach from=$debited item=item key="type"}
                                                <div class="list-group-item">
                                                    <span class="badge bg-success">{format_currency($item['amount'])}</span>
                                                    {lang($type)}
                                                </div>
                                            {/foreach}
                                        </div>
                                   </div>
                                </div>
                            </div>
                        </div>
                        <input class="tabs__item-input" type="radio" name="tabs" id="tabtwo">
                        <label class="tabs__item-label" for="tabtwo">{lang('ewallet_transactions')}</label>
                        <div class="tabs__item-content">
                            <div class="filter-new">
                                <form action="" id="ewallet_transaction_filter_form">
                                    <div class="row">
                                        
                                            <div class="form-group">
                                                <select class="user-search-selectize user-search-select2"></select>
                                            </div>
                                        
                                            <div class="form-group">
                                                <select name="cat_type" id="cat_type" class="form-control  select2-category" multiple>
                                                    <option value="credit">{lang('credited')}</option>
                                                    <option value="debit">{lang('debited')}</option>
                                                </select>
                                            </div>
                                        
                                            <div class="form-group select-check">
                                                <select name="category" class="form-control select2-category" id="transaction_category" multiple>

                                                {foreach $ewallet_categories as $category}
                                                    <option value="{$category}">{lang($category)}</option>
                                                {/foreach}
                                            </select>
                                        </div>
                                    
                                        <div class="form-group">
                                            <div id="reportrange" style="background: #fff; cursor: pointer; padding: 5px 10px; border: 1px solid #ccc;">
                                                <i class="fa fa-calendar"></i>&nbsp;
                                                <span></span> <i class="fa fa-caret-down"></i>
                                            </div>    
                                        </div>
                                   
                                        <div class="form-group">
                                            <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">
                                                {lang('search')}
                                            </button>
                                            <button class="btn btn-sm btn-info" type="button" id="ewallet_transaction_filter_form_clear" >
                                                {lang('reset')}
                                            </button>
                                           
                                        </div>
                                    
                                </div>
                            </form>
                        </div>
                        <div class="table-wallet">
                            <div class="table-responsive">
                                <table id="table_id" class="display">
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
                    <input class="tabs__item-input" type="radio" name="tabs" id="tabthree">
                    <label class="tabs__item-label" for="tabthree">{lang('ewallet_balance')}</label>
                    <div class="tabs__item-content">
                        <div class="filter-new">
                            <form action="" id="ewallet_balance_form">
                               <div class="row">
                                     <div class="form-group">
                                        <select class="user-search-selectize user-search-select2" id="ewallet_balance_users" multiple="multiple" ></select>
                                     </div>
                                  
                                     <div class="form-group">
                                        <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">
                                            {lang('search')}
                                        </button>
                                        <a id="ewallet_balance_form_clear" class="btn btn-sm btn-info" href="">{lang('reset')}</a>
                                  </div>
                               </div>
                            </form>
                        </div>
                      <div class="table-wallet td-right">
                          <div class="table-responsive">
                            <table id="ewallet_balance_table" class="display">
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
            </div>
   
   <!-- Modal -->
    <div class="modal right fade" id="fund_credit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header modal-ewallet-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
         </div>
         <div class="modal-body">
            <div class="modal-ewallet-area">
                 <h3>{lang('fund_credit')}</h3>
                  
            </div>
            
            <form action="" method="POST" id="fund_credit_form">
                <div class="popup-input">
                    <div class="row">
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group"  id="fund_credit_user_name_form_group">
                                <input class="form-control user-search" type="text" placeholder="{lang('username')}" id="credit_fund_username">
                                <div class="error"></div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">{$left_symbol} </span> 
                                    <input type="text" class="form-control" id="credit_fund_amount" name="amount" value="" placeholder="{lang('amount')}">
                                </div>
                                <div class="error"></div>
                            </div>
                        </div>
                
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                                <textarea class="form-control" name="tran_concept" style="height: 120px;" rows="10" placeholder="Notes" id="credit_fund_tran_concept"></textarea> 
                                <div class="error"></div>
                            </div>
                        </div>
                    
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group ">
                                <button class="btn btn-primary" name="add_amount" id="fund_credit_btn" type="submit" value="Credit">{lang('save_and_close')}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
         </div>
      </div>
      <!-- modal-content -->
   </div>
   <!-- modal-dialog -->
</div>
<!-- modal -->


<!-- Debit fund modal -->
<div class="modal right fade" id="debit_fund_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
         </div>
         <div class="modal-body">
            <div class="modal-ewallet-area">
                 <h3>{lang('fund_debit')}</h3>
                  
            </div>
            
            <form action="" method="POST" id="fund_debit_form">
                <div class="popup-input">
                    <div class="row">
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group"  id="fund_debit_user_name_form_group">
                                <input class="form-control user-search" type="text" placeholder="{lang('username')}" id="debit_fund_username">
                                <div class="error"></div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">{$left_symbol} </span> 
                                    <input type="text" class="form-control" id="debit_fund_amount" name="amount" value="" placeholder="{lang('amount')}">
                                </div>
                                <div class="error"></div>
                            </div>
                        </div>
                
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                                <textarea class="form-control" name="tran_concept" style="height: 120px;" rows="10" placeholder="{lang('notes')}" id="debit_fund_tran_concept"></textarea> 
                                <div class="error"></div>
                            </div>
                        </div>
                    
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group ">
                                <button class="btn btn-primary" name="add_amount" id="fund_debit_btn" type="submit" value="{lang('debit')}">{lang('save_and_close')}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
         </div>
      </div>
      <!-- modal-content -->
   </div>
   <!-- modal-dialog -->
</div>
<!-- modal -->

<!-- Fund Transfer -->
<div class="modal right fade" id="fund_transfer_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header modal-ewallet-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            
         </div>
         <div class="modal-body">
            <div class="modal-ewallet-area">
                 <h3>{lang('fund_transfer')}</h3>
            </div>
            
            <form action="" method="POST" id="fund_transfer_form">
                <div class="popup-input">
                    <div class="row">
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group"  id="fund_transfer_user_name_form_group">
                                <input class="form-control user-search" type="text" placeholder="{lang('from_username')}" id="fund_transfer_from_username">
                                <div class="error"></div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12 hidden" id="fund_transfer_balance_group">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">{$left_symbol} </span> 
                                    <input type="text" disabled="disabled" class="form-control" id="fund_transfer_from_user_balance_amount" name="transaction_fee" value="5" placeholder="">
                                </div>
                                <div class="error"></div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group"  id="fund_debit_user_name_form_group">
                                <input class="form-control user-search" type="text" placeholder="{lang('to_username')}" id="fund_transfer_to_username">
                                <div class="error"></div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">{$left_symbol} </span> 
                                    <input type="text" class="form-control" id="fund_transfer_amount" name="amount" value="" placeholder="{lang('amount')}">
                                </div>
                                <div class="error"></div>
                            </div>
                        </div>
                
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                                <textarea class="form-control" name="tran_concept" style="height: 120px;" rows="10" placeholder="{lang('notes')}" id="fund_transfer_notes"></textarea> 
                                <div class="error"></div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group"  id="fund_debit_user_name_form_group">
                                <input class="form-control" type="password" placeholder="{lang('transaction_password')}" id="fund_transaction_password">
                                <div class="error"></div>
                            </div>
                        </div>
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                                <div class="input-group">
                                    <span class="input-group-addon">{$left_symbol} </span> 
                                    <input type="text" disabled="disabled" class="form-control" id="fund_transfer_transaction_fee" name="transaction_fee" value="{$transaction_fee}" placeholder="">
                                </div>
                                <div class="error"></div>
                            </div>
                        </div>
                    
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group ">
                                <button class="btn btn-primary" name="add_amount" id="fund_transfer_btn" type="submit" value="Debit">{lang('save_and_close')}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
         </div>
      </div>
      <!-- modal-content -->
   </div>
   <!-- modal-dialog -->
</div>
<!-- modal -->

</div>
<!--popup-->

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
    <script src="{$PUBLIC_URL}theme/newui/js/ewallet.js" charset="utf-8" type="text/javascript"></script>
{/block}