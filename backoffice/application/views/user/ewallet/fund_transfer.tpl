{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK} 
    <div id="span_js_messages" style="display:none;">
        <span id="error_msg1">{lang('You_must_enter_user_name')}</span>
        <span id="error_msg2">{lang('NO_BALANCE')}</span>
        <span id="error_msg3">{lang('Please_type_transaction_password')}</span>
        <span id="error_msg4">{lang('Please_type_To_User_name')}</span>                     
        <span id="error_msg5">{lang('Please_type_Amount')}</span>
        <span id="error_msg6">{lang('NO_BALANCE')}</span>     
        <span id="validate_msg1">{lang('digits_only')}</span>
        <span id="validate_msg17">{lang('please_enter_transaction_concept')}</span>
        <span id="error_name">{lang('invalid_user_name')}</span>
        <span id="error_msg11">{lang('you_dont_have_enough_balance')}</span>
        <span id="error_msg12">{lang('digits_only')}</span>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
        {form_open_multipart('user/ewallet/post_fund_transfer', 'role="form"  method="post" name="form" id="msform"')}
            <input type="hidden" id="path_temp" name="path_temp" value="{$PUBLIC_URL}">
            <input type="hidden" id="path_root" name="path_root" value="{$PATH_TO_ROOT_DOMAIN}">
            <div class="form-group">
                <label> {lang('transfer_to')}<span class="symbol required"></span></label>
                <input class="form-control autocomplete-off" type="text" id="to_user_name" name="to_user_name" autocomplete="Off" value="{set_value('to_user_name')}"/><span id="errormsg1"></span>
                {form_error('to_user_name')}
            </div>
            {if $MODULE_STATUS['multy_currency_status']=="no"} 
                <div class="form-group">
                    <label>{lang('available_amount')}</label>
                    <input class="form-control" type="text" id="avb_amount" name="avb_amount" readonly="1" value="{round($balamount*$DEFAULT_CURRENCY_VALUE,$PRECISION)}"  autocomplete="Off" />
                    <input type="hidden" id="bal" name="bal"   value="{$balamount}" />
                    <input type="hidden" id="blnc" name="blnc"   value="{round($balamount*$DEFAULT_CURRENCY_VALUE,$PRECISION)}" />
                </div>
            {else}
                <div class="form-group">
                    <label>{lang('available_amount')}</label>
                    <div class="form-group">
                        <div class="input-group">
                            {if $DEFAULT_SYMBOL_LEFT}<span class="input-group-addon">{$DEFAULT_SYMBOL_LEFT}</span>{/if}
                            <input class="form-control" type="text" id="avb_amount" name="avb_amount" readonly="1" value="{round($balamount*$DEFAULT_CURRENCY_VALUE,$PRECISION)}"  autocomplete="Off" />
                            {*{if $DEFAULT_SYMBOL_RIGHT}<span class="input-group-addon">{$DEFAULT_SYMBOL_RIGHT}</span>{/if}*}
                            <input type="hidden" id="bal" name="bal"   value="{$balamount}" />
                            <input type="hidden" id="blnc" name="blnc"   value="{round($balamount*$DEFAULT_CURRENCY_VALUE,$PRECISION)}" />
                        </div>
                    </div>
                </div>
            {/if}
            {if $MODULE_STATUS['multy_currency_status']=="no"} 
                <div class="form-group">
                    <label>{lang('amount')}</label>
                    <input class="form-control" type="text" id="amount1" name="amount1" value="{set_value('amount1')}"/>
                </div>
            {else}
                <div class="form-group">
                    <label> {lang('amount')}<span class="symbol required"></span> </label>
                    <div class="form-group">
                        <div class="input-group">
                            {if $DEFAULT_SYMBOL_LEFT}<span class="input-group-addon">{$DEFAULT_SYMBOL_LEFT}</span>{/if}
                            <input class="form-control" type="text" id="amount1" name="amount1" value="{set_value('amount_1')}" />
                            {*{if $DEFAULT_SYMBOL_RIGHT}<span class="input-group-addon">{$DEFAULT_SYMBOL_RIGHT}</span>{/if}*}
                            {form_error('amount1')}
                        </div>
                    </div>
                </div>
            {/if}
            <div class="form-group">
                <label> {lang('transaction_note')}<span class="symbol required"></span></label>
                <textarea class="form-control" name="tran_concept" rows="" placeholder="" id="tran_concept" style="height: 45px; resize: none;">{set_value('tran_concept')}</textarea>
                {form_error('tran_concept')}
            </div>
            {if $MODULE_STATUS['multy_currency_status']=="no"} 
                <div class="form-group">
                    <label>{lang('transaction_fee')}</label>
                    <input class="form-control" type="text" id="trans_fee" name="trans_fee" readonly="1" value="{round($trans_fee*$DEFAULT_CURRENCY_VALUE,$PRECISION)}"  autocomplete="Off" />
                </div>
            {else}
                <div class="form-group">
                    <label>{lang('transaction_fee')}</label>
                    <div class="form-group">
                        <div class="input-group">
                            {if $DEFAULT_SYMBOL_LEFT}<span class="input-group-addon">{$DEFAULT_SYMBOL_LEFT}</span>{/if}
                            <input class="form-control" type="text" id="trans_fee" name="trans_fee" readonly="1" value="{round($trans_fee*$DEFAULT_CURRENCY_VALUE,$PRECISION)}"  autocomplete="Off" />
                            {* {if $DEFAULT_SYMBOL_RIGHT}<span class="input-group-addon">{$DEFAULT_SYMBOL_RIGHT}</span>{/if}*}
                        </div>
                    </div>
                </div>
            {/if}
            <input type="hidden" name="transaction_note" id="transaction_note" value="">
            <input type="hidden" name="path" id="path" value="{$PATH_TO_ROOT_DOMAIN}admin" >
            <input type="hidden" name="tran_fees" id="tran_fees" value="{$trans_fee*$DEFAULT_CURRENCY_VALUE}" >
            <input type="hidden" value="1" name="dotransfer"> 
            <div class="form-group">
                <label >{lang('transaction_password')}</label>
                <input class="form-control" type="password" id="pswd" name="pswd" />
                {form_error('pswd')}
            </div>   
            <table st-table="rowCollectionBasic" class="table table-bordered table-striped">
                    <tr>
                        <td>{lang('ewallet_balance')}</td>
                        <td class="ebal2">{round($balamount*$DEFAULT_CURRENCY_VALUE,$PRECISION)}</td>
                    </tr>
                    <tr>
                        <td>{lang('ewallet_amount already_payout_process')}</td>
                        <td class="ebal2">{round($request_amount*$DEFAULT_CURRENCY_VALUE,$PRECISION)}</td>
                    </tr>
             </table>
             <div class="form-group">
                <button type="submit" id="transfer" name="transfer" value="1" id="transfer" class="btn btn-primary">{lang('submit')}</button>
             </div>
        {form_close()}
    </div>
    </div>
      <div class="col-md-7 col-md-offset-2 min_height" style="margin-top: 10px; margin-bottom: 120px">      
      </div>
{/block}
{block name=script}{$smarty.block.parent} 
    <script src="{$PUBLIC_URL}/javascript/fund_transfer_user.js"></script>
{/block}