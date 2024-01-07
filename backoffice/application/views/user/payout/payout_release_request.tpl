{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
    <style>
        .help-block {
            color: #a94442;
        }
    </style>
    <div id="span_js_messages" style="display: none;">
        <span id="error_msg1">{lang('you_must_enter_transaction_password')}</span>
        <span id="error_msg2">{lang('transaction_password_atleast_8_characters_long')}</span>
        <span id="error_msg3">{lang('you_must_enter_payout_amount')}</span>
        <span id="error_msg4">{lang('payout_amount_must_be_greater_than_0')}</span>
        <span id="error_msg5">{lang('payout_amount_must_be_an_integer')}</span>
        <!--edited for cancel waiting withrawal-->
        <span id="show_msg1">{lang('are_you_sure_you_want_to_cancel_There_is_NO_undo')}</span>
        <!--edited for cancel waiting withrawal ends-->
        <span id="show_msg2">{lang('digits_only')}</span>
    </div> 
    <div class="panel panel-default">
        <div class="panel-body">
            
                {form_open('user/payout/post_payout_release_request','role="form" class="" method="post"  name="payout_request" id="payout_request" ')}
                <div class="col-md-12 padding_both">
                    <div id="req-err" class="errorHandler alert alert-danger no-display">
                        <i class="fa fa-times-sign"></i>  {lang('errors_check')}   
                    </div>
                </div>
                <input type="hidden" id="payout_fee_amount" value="{$config_details['payout_fee_amount']}">

                <input type="hidden" id="payout_fee_mode" value="{$config_details['payout_fee_mode']}">

                <div class="col-sm-3 padding_both">
                <div class="form-group">
                    <label class="control-label required" for="company">{lang('withdraw_amount')}</label>

                    <div class="input-group">
                        {if $DEFAULT_SYMBOL_LEFT}
                            <span class="input-group-addon">{$DEFAULT_SYMBOL_LEFT}</span>
                        {/if}
                        <input class="form-control" type="text" name="payout_amount" id="payout_amount" value="{convert_currency($available_max_payout)}"  autocomplete="Off" >
                        {if $DEFAULT_SYMBOL_RIGHT}
                            <span class="input-group-addon">{$DEFAULT_SYMBOL_RIGHT}</span>
                        {/if}
                    </div>
                    <span id="errmsg1"></span>
                    {form_error('payout_amount')}
                    </div>
                </div>

                <div class="col-sm-3 padding_both_small">
                <div class="form-group">
                    <label class="control-label" for="company">{lang('transaction_password')}<span class="symbol required"></span></label>

                    <input class="form-control" type="password" name="transation_password" id="transation_password" value=""  autocomplete="Off" >
                    {form_error('transation_password')}
                 </div>
                </div>

                <div class="col-sm-3 padding_both_small">
                  <div class="form-group" >
                    <button class="btn btn-sm btn-primary" style="margin-top: 25px;margin-left: 5px;" name="payout_request_submit" id="payout_request_submit" value="Send Request">
                        {lang('withdraw')}
                    </button>
                 </div>
             </div>
                
             <div class="col-sm-12 padding_both no-display">
                <p class="text-primary" id="payout_amount_text">
                    {lang('payout_fee_ded_text')}
                </p>
                 
             </div>
                
                {form_close()}             
             
        </div>
    </div>

    <div class="panel panel-default">
        <div class="table-responsive">
                   
                 
                 <table st-table="rowCollectionBasic" class="table table-striped">
                        <!--edited for cancel waiting withrawal--> 
                        {assign var="path" value="{$BASE_URL}user/"}
                        <!--edited for cancel waiting withrawal ends-->
                        <thead class="">
                        <tr class="th">
                            <th>{lang('particulars')}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tr>
                            <td>{lang('ewallet_balance')}</td>
                            <td>{format_currency($balance_amount)}</td>
                        </tr> 
                        <tr>
                                <td>{lang('ewallet_amount_already_in_payout_process')}</td>
                                <td>
                                {format_currency($req_amount)}

                            <!--edited for cancel waiting withrawal-->
                            {if $req_amount  > '0'}
                                <a href = "javascript:cancel_withdrawal('{$path}')" class="pull-right h4" title="{lang('cancel')}" data-original-title="{lang('cancel')}"><i class="fa fa-close text-primary" style=" color: red;"></i> 
                                </a>
                            {/if}
                            <!--edited for cancel waiting withrawal ends-->
                            </td>
                        </tr> 
                        <tr>
                            <td>{lang('total_paid_amount')}</td>
                            <td>
                                {format_currency($total_amount)}
                            </td>
                        </tr> 
                        <tr>
                            <td>{lang('preffered_payout_method')}</td>
                            <td>
                                {if $payout_method == "bank"}
                                    Bank
                                {elseif $payout_method == "Bitcoin"}
                                    Blocktrail
                                {else}
                                    {$payout_method}
                                {/if}
                            </td>
                        </tr>
                        <tr>
                            <td>{lang('minimum_withdrawal_amount')}</td>
                            <td>{format_currency($min_payout)}</td>
                        </tr>
                        <tr>
                            <td>{lang('maximum_withdrawal_amount')}</td>
                            <td>{format_currency($max_payout)}</td>
                        </tr>
                        <tr>
                            <td>{lang('available_maximum_withdrawal_amount')}</td>
                            <td>{format_currency($available_max_payout)}</td>
                        </tr>
                        <tr>
                            <td>{lang('payout_request_validity')}{lang('(days)')}</td>
                            <td>{$config_details['payout_request_validity']}</td>
                        </tr>
                        <tr>
                            <td>{lang('payout_fee')}</td>
                            <td>
                                {if $config_details['payout_fee_mode'] == 'percentage'}
                                    {$config_details['payout_fee_amount']}% {lang('of')} {lang('withdraw_amount')}
                                {else}
                                    {format_currency($config_details['payout_fee_amount'])}
                                {/if}
                            </td>
                        </tr>

                    </table>
                </div>
    </div>

{/block}
{block name=script}{$smarty.block.parent} 
    <script>
        jQuery(document).ready(function () {
            ValidateUser.init();
        });
    </script>
{/block}