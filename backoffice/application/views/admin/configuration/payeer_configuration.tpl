{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

    <div id="span_js_messages" style="display: none;"> 
        <span id="err_msg1">{lang('you_must_enter_merchant_id')}</span>
        <span id="err_msg2">{lang('you_must_enter_merchant_key')}</span>
        <span id="err_msg3">{lang('you_must_enter_encryption_key')}</span>
        <span id="err_msg4">{lang('you_must_enter_account')}</span>
    </div>



    <div class="panel panel-default">
        <div class="panel-body">
            <legend>
                <span class="fieldset-legend">{lang('payeer_configuration')}</span>
                <a href="{$BASE_URL}admin/configuration/{if $link_origin == 0}payment_view{else}payout_setting{/if}" class="btn btn-addon btn-sm btn-info pull-right">
                    <i class="fa fa-backward"></i>
                    {lang('back')}
                </a>
            </legend>
            {form_open('', 'role="form" class="" method="post" name="payeer_configuration_form" id="payeer_configuration_form"')}
            {include file="layout/error_box.tpl"}
            <div class="form-group">
                <label class="required">{lang('merchant_id')}</label>
                <input type="text" class="form-control" name="merchant_id" id="merchant_id" value="{$payeer_details['merchant_id']}" maxlength="20" autocomplete="off">
                {form_error('merchant_id')}
            </div>
            <div class="form-group">
                <label class="required">{lang('merchant_key')}</label>
                <input type="text" class="form-control" name="merchant_key" id="merchant_key" value="{$payeer_details['merchant_key']}" maxlength="20" autocomplete="off">
                {form_error('merchant_key')}
            </div>
            <div class="form-group">
                <label class="required">{lang('encryption_key')}</label>
                <input type="text" class="form-control" name="encryption_key" id="encryption_key" value="{$payeer_details['encryption_key']}" maxlength="20" autocomplete="off">
                {form_error('encryption_key')}
            </div>
            <div class="form-group">
                <label class="required">{lang('account')}</label>
                <input type="text" class="form-control" name="account" id="account" value="{$payeer_details['account']}" maxlength="20" autocomplete="off">
                {form_error('account')}
            </div>
            <div class="form-group">
                <button class="btn btn-sm btn-primary" name="update_payeer" type="submit" value="update">{lang('update')}</button>
            </div>
            {form_close()}
        </div>
    </div>

{/block}
{block name='script'}
    <script src="{$PUBLIC_URL}javascript/validate_paypal_config.js" type="text/javascript" ></script>
{/block}