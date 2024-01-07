{extends file=$BASE_TEMPLATE}
{block name="script"}{$smarty.block.parent}
<script>
    $(function() {
        if (window.location.href.indexOf("kyc_settings") >= 0) {
            $(document).scrollTop($('#kyc_config').offset().top);
        }
    });
</script>
<script src="{$PUBLIC_URL}javascript/sortableui.js"></script>
{literal}
<script type="text/javascript">
    $(document).ready(function(){
 $("#sortable_payout").sortable({
  placeholder : "ui-state-highlight",
  update  : function(event, ui)
  {
   var page_id_array = new Array();
   $('#sortable_payout tr').each(function(){
    page_id_array.push($(this).attr("data-id"));
   });

    var base_url = $('#base_url').val();
   $.ajax({
    url:base_url + "admin/configuration/payment_gateway_sort_payout",
    method:"POST",
    data:{page_id_array:page_id_array},
    success:function(data)
    {
        $('#sortable_payout > tr >td:first-child').each(function(index, tr) { 
        
           $(this).text(index + 1);
        });
        
    }
   });
  }
 });

});
</script>

{/literal}

{/block}
{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display:none;">
    <span id="confirm_msg1">{lang('sure_you_want_to_edit_this_news_there_is_no_undo')}</span>
    <span id="row_msg">{lang('rows')}</span>
    <span id="show_msg">{lang('shows')}</span>
    <span id="error_msg1">{lang('you_must_enter_rank_name')}</span>
    <span id="error_msg2">{lang('you_must_enter_referal_count')}</span>
    <span id="error_msg3">{lang('digits_only')}</span>
    <span id="error_msg4">{lang('Digit limit is five')}</span>
    <span id="error_msg5">{lang('digit_greater_than_0')}</span>
    <span id="error_msg6">{lang('min_payout_required')}</span>
    <span id="error_msg7">{lang('max_payout_required')}</span>
    <span id="error_msg8">{lang('payout_validity_required')}</span>
</div>

{include file="admin/configuration/system_setting_common.tpl"}


<div class="panel panel-default">
    <div class="panel-body">
    <legend><span class="fieldset-legend">{lang('payout_settings')}</span></legend>
        {form_open('','role="form" class="" method="post" name="payout_form" id="payout_form"')}
            {include file="layout/error_box.tpl"}
            {if $MODULE_STATUS['payout_release_status']=="from_ewallet"
            ||$MODULE_STATUS['payout_release_status']=="ewallet_request" ||$MODULE_STATUS['payout_release_status']=="both"}
                <div class="form-group">
                    <label class="required">{lang('Minimum_Payout_Amount')}</label>
                    <div class="input-group {$input_group_hide}">
                        {$left_symbol}
                        <input type="text" class="form-control" name='min_payout' id='payout_amount_min' value="{round($obj_arr["min_payout"]*$DEFAULT_CURRENCY_VALUE,$PRECISION)}" >
                        {$right_symbol}
                    </div>
                    {form_error('min_payout')}
                </div>
                <div class="form-group">
                    <label class="required">{lang('Maximum_Payout_Amount')}</label>
                    <div class="input-group {$input_group_hide}">
                        {$left_symbol}
                        <input type="text" class="form-control" name='max_payout' id='payout_amount_max' value="{round($obj_arr["max_payout"]*$DEFAULT_CURRENCY_VALUE,$PRECISION)}" >
                        {$right_symbol}
                    </div>
                    {form_error('max_payout')}
                </div>
                <div class="form-group">
                    <label class="required">{lang('payout_fee')}</label>
                    <select class="form-control" name="payout_fee_mode"  id="payout_fee_mode"">
                        <option value="percentage" {if $obj_arr["payout_fee_mode"]=='percentage'} selected="true"{/if}>{lang('percentage')}</option>
                        <option value="flat" {if $obj_arr["payout_fee_mode"]=='flat'} selected="true"{/if}>{lang('flat')}</option>
                    </select>
                    {form_error("payout_fee_mode")}
                </div>
                <div class="form-group" >
                    <div class="input-group {$input_group_hide} {if $obj_arr["payout_fee_mode"] != "flat"}input-group-hide{/if}">
                        {$left_symbol}
                        <input type="text" class="form-control" name='payout_fee_amount' id='payout_fee_amount' {if $obj_arr["payout_fee_mode"] != "percentage"} value="{round($obj_arr['payout_fee_amount']*$DEFAULT_CURRENCY_VALUE,$PRECISION)}"{else} value="{$obj_arr['payout_fee_amount']}"{/if}>
                        {$right_symbol}
                    </div>
                    {form_error('payout_fee_amount')}
                </div>
            {else}
                <input type="hidden" name="min_payout" id="min_payout" value="0" />
                <input type="hidden" name="max_payout" id="max_payout" value="0" />
                <input type="hidden" name="payout_fee_mode" id="payout_fee_mode" value="flat" />
                <input type="hidden" name="payout_fee_amount" id="payout_fee_amount" value="0" />
            {/if}
            {if $MODULE_STATUS['payout_release_status']=="ewallet_request" ||$MODULE_STATUS['payout_release_status']=="both"}
                <div class="form-group">
                    <label class="required">{lang('Payout_Request_Validity')}</label>
                    <input type="text" class="form-control" name="payout_validity" id="payout_amount" value="{$obj_arr["payout_request_validity"]}" maxlength="5">{form_error('payout_validity')}
                </div>
            {else}
                <input type="hidden" name="payout_validity" id="payout_amount" value="{$obj_arr["payout_request_validity"]}">
            {/if}
            <div class="form-group">
                <label class="required">{lang('payout_method')}</label>
                <select class="form-control" name="payout_status" id="payout_status">
                    <option value="from_ewallet" {if $MODULE_STATUS['payout_release_status']=='from_ewallet'} selected {/if}>{lang('manual_payout')}</option>
                    <option value="ewallet_request" {if $MODULE_STATUS['payout_release_status']=='ewallet_request'} selected {/if}>{lang('payout_by_request')}</option>
                    <option value="both" {if $MODULE_STATUS['payout_release_status']=='both'} selected {/if}>{lang('payout_both_manual_request')}</option>
                </select>
                {* <div class="radio radio-inline">
                    <label class="i-checks i-checks-sm">
                        <input type="radio" name="payout_status" id="payout_ewallet" value="from_ewallet" {if $MODULE_STATUS['payout_release_status']=='from_ewallet'} checked {/if}>
                        <i></i>
                        {lang('from_e_wallet')}
                    </label>
                    <label class="i-checks i-checks-sm">
                        <input type="radio" name="payout_status" id="payout_ewallet_req" value="ewallet_request" {if $MODULE_STATUS['payout_release_status']=='ewallet_request'} checked {/if}>
                        <i></i>
                        {lang('e_wallet_request')}
                    </label>
                    <label class="i-checks i-checks-sm">
                        <input type="radio" name="payout_status" id="both" value="both" {if $MODULE_STATUS['payout_release_status']=='both'} checked {/if}>
                        <i></i>
                        {lang('both')}
                    </label>
                </div> *}
                {form_error('payout_status')}
            </div>
               <div class="form-group">
             <div class="checkbox">
                <label class="i-checks">
                  <input type="checkbox" name="mail_status" {if $status == "yes"} checked = "checked" {/if}><i></i> {lang('enable_payout_mail_status')}
                    </label>
            </div>
        </div>
            <div class="form-group">
                <button type="submit" class="btn btn-sm btn-primary" value="{lang('update')}" name="setting" id="setting">{lang('update')}</button>
            </div>
        {form_close()}
    </div>
</div>

{if DEMO_STATUS == 'yes' && $MODULE_STATUS['basic_demo_status'] == 'yes' && $is_preset_demo}
{else}
{form_open('', 'name="payment_status_form" id="payment_status_form" method="post"')}
<div class="panel panel-default">
<div class="panel-body">
<legend><span class="fieldset-legend">{lang('payment_gateway_configuration')}</span></legend>
<div class="panel panel-default table-responsive">
    <table class="table table-striped">
        <thead>
            <tr>
                <th>{lang('sl_no')}</th>
                <th>{lang('Payment_method')}</th>
                <th>{lang('status')}</th>
            </tr>
        </thead>
        <tbody id="sortable_payout">
            {assign var="i" value=0}
            {foreach from=$card_status item=v}
                    <tr data-id="{$v.id}" style = "cursor: move;">
                        <td>{assign var="i" value=$i+1}{$i}</td>
                        <td>{if $v.gateway_name=="Bitcoin"}{lang('blocktrail')}{else}{$v.gateway_name}{/if}</td>
                        <td>
                            <div class="form-group-button">
                                <label class="i-switch bg-primary">
                                    <input type="checkbox" name="status[]" id="status[]" value="{$v.id}" {if $v.status=='yes'} checked {/if} class="switch-input">
                                    <i></i>
                                </label>
                            </div>
                        </td>
                    </tr>
            {/foreach}
        </tbody>
    </table>
    </div>
    <button type="submit" id="update" value="update" name="update" class="btn btn-sm btn-primary">{lang('update')}</button>
    </div>
</div>
{form_close()}
{/if}

{if $MODULE_STATUS['kyc_status']=="yes"}
        <div class="panel panel-default">
            <div class="panel-body">
                <legend><span class="fieldset-legend" id="kyc_config">{lang('kyc_configuration')}</span>
                    <a href="{$BASE_URL}admin/configuration/kyc_configuration" class="btn m-b-xs btn-sm btn-primary btn-addon pull-right"><i class="fa fa-plus"></i>{lang('add_new_category')}</a>
                </legend>
                {form_open('', 'name="payment_status_form" id="payment_status_form" method="post"')}
                    <div class="panel panel-default table-responsive">
                            {if count($kyc_docs) > 0}
                        <table class="table table-striped" id="">
                            <thead>
                                <tr>
                                    <th>{lang('sl_no')}</th>
                                    <th>{lang('category')}</th>
                                    <th>{lang('action')}</th>
                                </tr>
                            </thead>
                                <tbody>
                                    {foreach from=$kyc_docs item=v}
                                        <tr>
                                            <td>{counter}</td>
                                            <td>{$v.category}</td>
                                            <td style="width:20%">
                                            {form_open('admin/payout_setting', '')}
                                            <a href="{$BASE_URL}admin/configuration/kyc_configuration/?id={$v.id}" class="btn btn-light-grey btn-xs text-black" title="{lang('edit')}"><i class="fa fa-edit"></i></a>
                                            <button class="btn btn-light-grey btn-xs text-black" name="delete_category" id="delete_category" type="submit" title="{lang('delete')}" value="{$v.id}" onclick="return confirm('{lang('Sure_you_want_to_Delete_this_category_There_is_NO_undo')}')"><i class="fa fa-trash-o"></i></button>
                                            {form_close()}
                                            </td>
                                        </tr>
                                    {/foreach}
                                </tbody>
                        </table>
                            {else}
                                <h4 align="center">{lang('no_category_found')}</h4>
                            {/if}
                    </div>
                {form_close()}
            </div>
        </div>
{/if}

{/block}