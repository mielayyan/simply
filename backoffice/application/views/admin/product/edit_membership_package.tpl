{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display:none;">
    <span id="error_msg1">{lang('you_must_enter_your_product_identifying_number')}</span>
    <span id="error_msg">{lang('you_must_enter_your_product_name')}</span>
    <span id="error_msg3">{lang('you_must_enter_your_product_amount')}</span>
    {if $mlm_plan == "Stair_Step"}
    <span id="error_msg4">{lang('you_must_enter_your_product_pv_value')}</span>
    {else}
    <span id="error_msg4">{lang('you_must_enter_your_product_pair_value')}</span>
    {/if}
    <span id="validate_msg">{lang('enter_digits_only')}</span>
    <span id="error_msg5">{lang('you_must_enter_package_id')}</span>
    <span id="row_msg">{lang('rows')}</span>
    <span id="show_msg">{lang('shows')}</span>
    <span id="validate_msg1">{lang('digits_only')}</span>
    <span id="validate_msg_img1">{lang('you_must_select_a_product_name')}</span>
    <span id="validate_msg_img2">{lang('you_must_select_a_product_image')}</span>
    <span id="validate_msg7">{lang('you_must_select_a_type_of_package')}</span>
    <span id="validate_msg8">{lang('you_must_enter_package_validity')}</span>
    <span id="validate_msg9">{lang('digits_only')}</span>
    <span id="validate_msg11">{lang('you_must_enter_your_product_pair_price')}</span>
    <span id="validate_msg12">{lang('you_must_enter_product_referral_commission')}</span>
    <span id="validate_msg13">{lang('product_referral_commission_must_be_number_between')}</span>
    <span id="validate_msg14">{lang('alphanumeric_chars_only')}</span>
    <span id="validate_msg15">{lang('you_must_enter_product_roi')}</span>
    <span id="validate_msg16">{lang('you_must_enter_product_days')}</span>
    <span id="validate_msg17">{lang('roi_should_be_between_0_to_100')}</span>
    <span id="validate_msg18">{lang('roi_should_be_greater_than_zero')}</span>
    <span id="validate_msg19">{lang('package_validity_should_be_a_positive_number')}</span>
    <span id="validate_msg20">{lang('digit_limit_is_five')}</span>
    <span id="validate_msg22">{lang('you_must_enter_product_referral_commission')}</span>
    <span id="validate_msg24">{lang('product_referral_commission_must_be_number_between')}</span>
    <span id="amount_greater_than_zero">{sprintf(lang('field_greater_than_zero'), ucfirst(strtolower(lang('product_amount'))))}</span>
</div>

    <div class="button_back">
        <a href="{BASE_URL}/admin/membership_package" class="btn m-b-xs btn-sm btn-info btn-addon"><i class="fa fa-backward"></i> {lang('back')}</a>
    </div>

    <div class="panel panel-default">
        <div class="panel-body">
         {form_open('admin/edit_membership_package/','class="" role="form" id="form"')}
            <input type="hidden" name="product_id" value="{$package_details.product_id}">
                {include file="layout/error_box.tpl" id="err_edit_membership"}
                <div class="form-group">
                    <label class="control-label required" for="package_id">{lang('id')}</label>
                        <input type="text" class="form-control" readonly="true" name="package_id" id="package_id" value="{set_value('package_id', $package_details['prod_id'])}" autocomplete="off"/>
                        <span id="errmsg1"></span>
                        <span name ='form_err'>{form_error('package_id')}</span>
                </div>
                
                 <div class="form-group">
                    
                    <label class="control-label required" for="pck_type">{lang('package_type')}</label>
                    
                    <select class="form-control" name="pck_type" id="pck_type">
                        
                    <option {if $package_details['pck_type'] == "normal_pack" } selected {/if} value="normal_pack">{lang('normal_pack')}</option>
                    <option {if $package_details['pck_type'] == "founder_pack" } selected {/if} value="founder_pack">{lang('founder_pack')}</option>
                        
                    </select>
                    
                    <span name ='form_err'>{form_error('pck_type')}</span>
                    
                </div>

                <div class="form-group">
                    <label class="control-label required" for="prod_name">{lang('name')}</label>
                        <input type="text" class="form-control" name="prod_name" id="prod_name"  value="{set_value('prod_name', $package_details['product_name'])}" autocomplete="off"/>
                        <span name ='form_err'>{form_error('prod_name')}</span>
                </div>
                {if $MODULE_STATUS['multy_currency_status']=="no"}
                <div class="form-group">
                    <label>{lang('amount')}</label>
                    <input type="text" class="form-control" name="product_amount" id="product_amount" value="{set_value('product_amount', round($package_details['product_value']*$DEFAULT_CURRENCY_VALUE,$PRECISION))}" autocomplete="off" />
                    <span name ='form_err'>{form_error('product_amount')}</span>
                </div>
                {else}
                <div class="form-group">
                    <label class="control-label required" for="product_amount">{lang('amount')}</label>
                        <div class="input-group">
                        {if $DEFAULT_SYMBOL_LEFT}
                            <span class="input-group-addon">{$DEFAULT_SYMBOL_LEFT}</span>
                        {/if}
                        <input type="text" class="form-control" name="product_amount" id="product_amount" value="{set_value('product_amount', round($package_details['product_value']*$DEFAULT_CURRENCY_VALUE,$PRECISION))}" autocomplete="off" />
                        {if $DEFAULT_SYMBOL_RIGHT}
                            <span class="input-group-addon">{$DEFAULT_SYMBOL_RIGHT}</span>
                        {/if}
                        <span id="errmsg1"></span>
                    </div>
                    <span name ='form_err'>{form_error('product_amount')}</span>
                </div>
                {/if}

                {if $pv_visible == 'yes'}
                <div class="form-group">
                    <label class="control-label required" for="pair_value">{if $mlm_plan == "Stair_Step"}{lang('pv_value')}{else}{lang('product_pv')}{/if}</label>
                        <input type="text" class="form-control" name="pair_value" id="pair_value" value="{set_value('pair_value', $package_details['pair_value'])}" autocomplete="off"/>
                        <span id="errmsg2"></span>
                        <span name ='form_err'>{form_error('pair_value')}</span>
                </div>
                {/if}

                {if $bv_visible == 'yes'}
                    <div class="form-group">
                        <label class="control-label required" for="bv_value">{lang('bv_value')}</label>
                            <input type="text" class="form-control" name="bv_value" id="bv_value" value="{set_value('bv_value', $package_details['pair_value'])}" autocomplete="off"/>
                            <span id="errmsg2"></span>
                            <span name ='form_err'>{form_error('bv_value')}</span>
                    </div>
                {/if}

                {if $MODULE_STATUS['subscription_status'] == "yes"}
                    <div class="form-group">
                        <label class="control-label required" for="package_validity">{lang('validity')}({lang('in_months')})</label>
                           <input type="text" class="form-control" name="package_validity" id="package_validity" value="{set_value('package_validity', $package_details['subscription_period'])}" autocomplete="off"/>
                           <span id="errmsg3"></span>
                           <span name ='form_err'>{form_error('package_validity')}</span>
                    </div>
                {/if}
                <div class="form-group">
                    
                    <label class="control-label required" for="simply_url">{lang('Simply Url')}</label>
                    
                    <select class="form-control" name="simply_url" id="simply_url">
                        
                    <option {if $package_details['simply_url_status'] == "yes" } selected {/if} value="yes">{lang('Yes')}</option>
                    <option {if $package_details['simply_url_status'] == "no" } selected {/if} value="no">{lang('No')}</option>
                        
                    </select>
                    
                    <span name ='form_err'>{form_error('simply_url')}</span>
                    
                </div>
                <div class="form-group">
                    
                    <label class="control-label required" for="board_system">{lang('Support Board System')}</label>
                    
                    <select class="form-control" name="board_system" id="board_system">
                        
                    <option {if $package_details['board_system'] == "yes" } selected {/if} value="yes">{lang('Yes')}</option>
                    <option {if $package_details['board_system'] == "no" } selected {/if} value="no">{lang('No')}</option>
                        
                    </select>
                    
                    <span name ='form_err'>{form_error('board_system')}</span>
                    
                </div>
                <div class="form-group">
                    
                    <label class="control-label required" for="service_system">{lang('Support Services System')}</label>
                    
                    <select class="form-control" name="service_system" id="service_system">
                        
                    <option {if $package_details['services_system'] == "yes" } selected {/if} value="yes">{lang('Yes')}</option>
                    <option {if $package_details['services_system'] == "no" } selected {/if} value="no">{lang('No')}</option>
                        
                    </select>
                    
                    <span name ='form_err'>{form_error('service_system')}</span>
                    
                </div>
                <div class="form-group">
                    
                    <label class="control-label required" for="tourism_system">{lang('Support Tourism System')}</label>
                    
                    <select class="form-control" name="tourism_system" id="tourism_system">
                        
                    <option {if $package_details['tourism_system'] == "yes" } selected {/if} value="yes">{lang('Yes')}</option>
                    <option {if $package_details['tourism_system'] == "no" } selected {/if} value="no">{lang('No')}</option>
                        
                    </select>
                    
                    <span name ='form_err'>{form_error('tourism_system')}</span>
                    
                </div>
                 {*added //  package combo*}
                 {if !empty($package_details['combo_pack'])}
                 {foreach from=$package_details['combo_pack'] item=v}
                 <!--<div class="form-group">
                    
                    <label class="control-label required" for="simply_url">Combo {$v['package_name']} Count</label>
                    
                        <input type="text" class="form-control" name="{$v['package_type']}_count" value="{$v['count']}" autocomplete="off"/>
                    <span id="errmsg3"></span>
                    <span name ='form_err'>{form_error('package_combo')}</span>
                        
                    
                </div>-->
                 {/foreach}
                 {/if}
                 {*<div class="form-group">
                    
                 <label class="control-label required" for="package_combo">{lang('package_combo')}</label>
                 
                 <select class="form-control select2" id="package_combo" multiple name="package_combo[]">

                 {foreach from=$package_details item=v}
                     <option value="{$v.prod_id}">{lang($v.product_name)}</option>
                 {/foreach}
                     
                 </select>
                 
                 <span name ='form_err'>{form_error('package_combo')}</span>
                 
             </div>*}
              {*added //  package combo*}
                <div class="form-group">
                    <button class="btn btn-sm btn-primary" type="submit" name="update_prod" id="update_prod" value="update_prod">{lang('update_Product')}</button>
                </div>

         {form_close()}
        </div>
    </div>

 {/block}