{extends file=$BASE_TEMPLATE} {block name=script} {$smarty.block.parent}
    <script src="{$PUBLIC_URL}javascript/fund_transfer_admin.js" type="text/javascript"></script>
{/block} {block name=$CONTENT_BLOCK}
    <div id="span_js_messages" style="display:none;">
        <span id="error_msg1">{lang('You_must_enter_user_name')}</span>
        <span id="error_msg2">{lang('NO_BALANCE')}</span>
        <span id="error_msg3">{lang('Please_type_transaction_password')}</span>
        <span id="error_msg4">{lang('Please_type_To_User_name')}</span>
        <span id="error_msg5">{lang('Please_type_Amount')}</span>
        <span id="error_msg11">{lang('you_dont_have_enough_balance')}</span>
        <span id="validate_msg1">{lang('digits_only')}</span>
        <span id="validate_msg17">{lang('please_enter_transaction_concept')}</span>
        <span id="error_name">{lang('invalid_user_name')}</span>
        <span id="error_msg12">{lang('digits_only')}</span>
        <span id="next">{lang('next')}</span>
        <span id="previous">{lang('back')}</span>
        <span id="finish">{lang('finish')}</span>
        <span id="otp_err1">{lang('you_must_enter_otp')} </span>
        <span id="otp_err2">{lang('otp_is_numeric')} </span>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            {form_open('admin/fund_transfer','role="form" method="post" name="fund_transfer_fee" id="fund_transfer_fee"')} {include file="layout/error_box.tpl"}
            <legend><span class="fieldset-legend">{lang('fund_transfer_fee')}</span></legend>
            <div class="form-group">
                <label class="required">{lang('transaction_fee')}</label>
                <div class="input-group {$input_group_hide}">
                    {$left_symbol}
                    <input class="form-control" type="text" name="trans_fee"  id="trans_fee" value="{round($trans_fee*$DEFAULT_CURRENCY_VALUE,$PRECISION)}" autocomplete="off" >
                    {$right_symbol}
                </div>
                {form_error('trans_fee')}
            </div>
            <div class="form-group">
                <input type="submit" id="transfer_fee_submit" name="transfer_fee_submit" class="btn btn-primary" value="{lang('submit')}"/>
            </div>
            {form_close()}
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            {form_open('/admin/post_fund_transfer','role="form" method="post" name="fund_form" id="msform"')} {include file="layout/error_box.tpl"}
            <input type="password" autocomplete="off" style="display: none;" />
            <div class="form-group">
                <label class="required"> {lang('user_name')} </label>
                <input class="form-control user_autolist autocomplete-off" type="text" id="user_name" name="user_name" autocomplete="Off" value="{set_value('user_name')}"/> 
                {form_error('user_name')}
            </div>
            <div class="form-group">
                <label class="required">{lang('transfer_to')}</label>
                <input class="form-control user_autolist" type="text" id="to_user_name" name="to_user_name" onkeypress="getAmountLeg();" autocomplete="Off" value="{set_value('to_user_name')}" /> 
                {form_error('to_user_name')}
                <input id="to_user_name1" name="to_user_name1" type="hidden">
            </div>
            <div class="form-group">
                <div id="user_amount_div"> </div>
            </div>
            {if $MODULE_STATUS['multy_currency_status']=="no"} 
                <div class="form-group">
                    <label>{lang('amount')}</label>
                    <input type="text" class="form-control" id="amount" name="amount"/>
                    {form_error('amount')} 
                </div> 
            {else}
                <div class="form-group">
                    <label class="required"> {lang('amount')} </label>
                    <div class="form-group">
                        <div class="input-group">
                            {if $DEFAULT_SYMBOL_LEFT}<span class="input-group-addon">{$DEFAULT_SYMBOL_LEFT}</span>{/if}
                            <input class="form-control" type="text" id="amount" name="amount"  value="{set_value('amount')}"/> {if $DEFAULT_SYMBOL_RIGHT}<span class="input-group-addon">{$DEFAULT_SYMBOL_RIGHT}</span>{/if} 
                        </div>
                        {form_error('amount')}
                    </div>
                    <span id="errmsg1"></span>
                </div>
            {/if}
            <div class="form-group">
                <label class="required">{lang('transaction_note')}</label>
                <textarea class="form-control" name="transaction_note" style="height: 120px;"id="transaction_note" cols="30" rows="10">{set_value('transaction_note')}</textarea>
                {form_error('transaction_note')}
            </div>

            {if $MODULE_STATUS['multy_currency_status']=="no"} 
                <div class="form-group">
                    <label>{lang('transaction_fee')}</label>
                    <input class="form-control" type="text" id="tran_fee" name="tran_fee" disabled value="{round($trans_fee*$DEFAULT_CURRENCY_VALUE,$PRECISION)}" autocomplete="Off" />
                </div> 
            {else}
                <div class="form-group">
                    <label>{lang('transaction_fee')}</label>
                    <div class="form-group">
                        <div class="input-group">
                            {if $DEFAULT_SYMBOL_LEFT}<span class="input-group-addon">{$DEFAULT_SYMBOL_LEFT}</span>{/if}
                            <input class="form-control" type="text" id="tran_fee" name="tran_fee" readonly="1" value="{round($trans_fee*$DEFAULT_CURRENCY_VALUE,$PRECISION)}" autocomplete="Off" /> {if $DEFAULT_SYMBOL_RIGHT}<span class="input-group-addon">{$DEFAULT_SYMBOL_RIGHT}</span>{/if}
                        </div>
                    </div>
                </div>
            {/if}
            <input type="hidden" name="path" id="path" value="{$PATH_TO_ROOT_DOMAIN}admin">
            <input type="hidden" name="tran_fees" id="tran_fees" value="{$trans_fee*$DEFAULT_CURRENCY_VALUE}">
            <input type="hidden" value="1" name="dotransfer">
            <input type="hidden" value="{$PRECISION}" id="precision">
            <div class="form-group">
                <label class="required">{lang('transaction_password')}</label>
                <input type="password" id="pswd" class="form-control" name="pswd" data-bv-field="pswd"/> 
                {form_error('pswd')}
                {* <input type="hidden" name="transaction_note" id="transaction_note" value="{$transaction_note}"> *}
            </div>
            <input type="hidden" value="0" name="dotransfer">
            <input type="hidden" value="f627cf15b4adbe7e689b7db8a5d09fc9" name="token">
            {* <input type="button" name="" id="product" class="next action-button" value="{lang('next')}" /> *}
            <div class="form-group">
                <button type="submit" id="transfer" name="transfer" class="btn btn-primary">{lang('submit')}</button>
            </div>
            {form_close()}
        </div>
    </div>
    {include file="layout/otp_modal.tpl"} {/block}