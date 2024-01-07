{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display: none;">
    <span id="validate_msg13">{lang('digit_only')}</span>
    <span id="validate_msg20">{lang('service_charge_required')}</span>
    <span id="validate_msg21">{lang('service_charge_must_between_0_to_100')}</span>
    <span id="validate_msg22">{lang('tds_required')}</span>
    <span id="validate_msg23">{lang('tds_must_between_0_to_100')}</span>
    <span id="validate_msg24">{lang('sum_of_tds_and_service_charge_should_be_less_equal_to_100')}</span>
    <span id="validate_msg25">{lang('commission_must_be_less_than_100')}</span>
    <span id="validate_msg26">{lang('field_is_required')}</span>
    <span id="validate_msg27">{lang('field_must_be_between_0_100')}</span>
    <span id="validate_msg30">{lang('digit_greater_than_0')}</span>
    <span id="registration_amount_required">{lang('registration_amount_required')}</span>
    <span id="trans_fee_required">{lang('trans_fee_required')}</span>
    <span id="you_must_enter">{lang('you_must_enter')}</span>
    <span id="purchase_income_perc_required">{lang('purchase_wallet_commission')|strtolower}</span>
</div>

{include file="admin/configuration/system_setting_common.tpl"}

<div class="panel panel-default">
    <div class="panel-body">

            {form_open('','role="form" class="" method="post" name="form_general_setting" id="form_general_setting"')}

            {if $MODULE_STATUS['purchase_wallet']=="yes"}
            <div class="form-group">
                <label class="required">{lang('purchase_wallet_commission')} (%)</label>
                <input class="form-control" type="text" name="purchase_income_perc" id="purchase_income_perc" value="{set_value("purchase_income_perc",$obj_arr["purchase_income_perc"])}">
                {form_error('purchase_income_perc')}
            </div>
            {/if}
            
            <div class="form-group">
                <label class="required">{lang('service_charge')} (%)</label>
                <input class="form-control" type="text" name="service_charge" id="service" value="{set_value('service_charge',round($obj_arr["service_charge"],$PRECISION))}">
                {form_error('service_charge')}
            </div>

            <div class="form-group">
                <label class="required"> {lang('tds')} (%)</label>
                <input class="form-control" type="text" name="tds" id="tds" value="{set_value("tds",round($obj_arr["tds"],$PRECISION))}">
                {form_error('tds')}
            </div>

            {if $MODULE_STATUS['compression_status'] == "yes"}
                <div class="form-group">
                    <div class="checkbox">
                    <label class="i-checks">
                        <input type="checkbox" name="compression_commission" {if $signup_config['general_signup_config']['compression_commission'] == 'yes'} checked {/if}><i></i> {lang('enable_dynamic_compression')}
                    </label>
                    </div>
                </div>
            {/if}

            <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" name="skip_blocked_users_commission" {if $obj_arr['skip_blocked_users_commission'] == 'yes'} checked {/if}><i></i> {lang('skip_blocked_users_commission')}
                  </label>
                </div>
            </div>

            <div class="form-group">
                <button class="btn btn-sm btn-primary" type="submit" value="{lang('update')}" name="setting" id="setting">{lang('update')}</button>
            </div>
            {include file="common/notes.tpl" notes=lang('note_tax_transaction_fee')}

            {form_close()}

        </div>
</div>

{/block}

{block name=style}
     {$smarty.block.parent}
{/block}

{block name='script'}
    {$smarty.block.parent}
    <script src="{$PUBLIC_URL}javascript/general_settings.js"></script>
{/block}