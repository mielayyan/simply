<!-- Modal -->
<div class="modal right fade" id="payout_request_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
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
    <div class="modal-dialog" role="document"> 
        <div class="modal-content">
            <div class="modal-header modal-ewallet-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="modal-ewallet-area">
                    <h3>{lang('payout_request')}</h3>
                </div>
                <div class="popup-input">
                   {form_open('','id="payout_request_form"')}
                    <div class="row">
                        <input type="hidden" id="payout_fee_amount" value="{$config_details['payout_fee_amount']}">

                        <input type="hidden" id="payout_fee_mode" value="{$config_details['payout_fee_mode']}">
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                            <label class=""> {lang('withdraw_amount')} </label>
                                {* <input class="form-control user-search" id="user_name" type="text" name="user_name" placeholder="{lang('username')}"> *}
                               <div class="input-group m-b"> <span class="input-group-addon">{$DEFAULT_SYMBOL_LEFT}</span>
                                <input class="form-control" type="text" name="payout_amount" id="payout_amount" value="{convert_currency($available_max_payout)}"  autocomplete="Off" >
                                {if $DEFAULT_SYMBOL_RIGHT}<span class="input-group-addon">{$DEFAULT_SYMBOL_RIGHT}</span>{/if}
                                {form_error('payout_amount')}
                             </div>
                            </div>
                        </div>
                        <div class="col-lg-12 col-xs-12">
                            <div class="form-group">
                                <label class="required">{lang('transaction_password')}</label>
                                <input class="form-control" tabindex="3" type="password" name="transaction_password" id="transaction_password" size="20" value="" title=""/>
                                 {form_error('transaction_password')}
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group ">
                                <button class="btn btn-primary"n ame="payout_request_submit" id="payout_request_submit" type="submit" value="Send Request">{lang('withdraw')}</button>
                            </div>
                        </div>
                    </div>
                    {form_close()}
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <p class="text-primary" id="payout_amount_text">
                                {lang('payout_fee_ded_text')}
                            </p>
                        </div>
                    </div>
                </div>
                 <div class="table-responsive">
                   
                 
                 <table st-table="rowCollectionBasic" class="table ">
                        <thead class="">
                        <tr class="th">
                            <th>{lang('particulars')}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tr>
                            <td>{lang('ewallet_balance')}</td>
                            <td id="balance_amount">{format_currency($balance_amount)}</td>
                        </tr> 
                        <tr>
                                <td>{lang('ewallet_amount_already_in_payout_process')}</td>
                                <td id="req_amount">
                                {format_currency($req_amount)}

                                </td>
                        </tr> 
                        <tr>
                            <td>{lang('total_paid_amount')}</td>
                            <td id="total_amount">
                                {format_currency($total_amount)}
                            </td>
                        </tr> 
                        <tr>
                            <td>{lang('preffered_payout_method')}</td>
                            <td >
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
                            <td id="minimum_payout_amount">{format_currency($min_payout)}</td>
                        </tr>
                        <tr>
                            <td>{lang('maximum_withdrawal_amount')}</td>
                            <td id="maximum_payout_amount">{format_currency($max_payout)}</td>
                        </tr>
                        <tr>
                            <td>{lang('available_maximum_withdrawal_amount')}</td>
                            <td >{format_currency($available_max_payout)}</td>
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
        </div>
        <!-- modal-content -->
    </div>
    <!-- modal-dialog -->
</div>
<!-- modal -->