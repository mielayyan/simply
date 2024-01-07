{extends file=$BASE_TEMPLATE}

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
</div>


{include file="admin/configuration/system_setting_common.tpl"}


<div class="panel panel-default">
<div class="panel-body">
<legend><span class="fieldset-legend">{lang('payment_methods')}</span></legend>

{form_open('admin/configuration/update_payment_config', 'name="payment_status_form" id="payment_status_form" method="post"')}

  
    <div class="table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>{lang('Payment_method')}</th>
                    <th>{lang('action')}</th>
                    <th>{lang('status')}</th>
                    <th>{lang('registration')}</th>
                    {if $MODULE_STATUS['repurchase_status'] == 'yes'}
                    <th>{lang('repurchase')}</th>
                    {/if}
                     {if $MODULE_STATUS['subscription_status'] == 'yes'}
                    <th>{lang('membership_renewal')}</th>
                    {/if}
                    {if $MODULE_STATUS['package_upgrade'] == 'yes'}
                    <th>{lang('upgradation')}</th>
                    {/if}
                    <th>{lang('admin_only')}</th>

                   
                </tr>
            </thead>
            <tbody id="sortable">
              
                {assign var="i" value=0}
                {foreach from=$gateway key=key item=v}
                
                    <tr data-id="{$v.id}">
                        <td>{if isset($gateway_only[$v.gateway_name])}
                                <img class="img_width_payout" src="{$BASE_URL}/public_html/images/logos/{$gateway_only[$v.gateway_name]['logo']}"/>
                            {/if}
                        </td>
                        <td>{$v.gateway_name} 
                            {if isset($gateway_only[$v.gateway_name])} &nbsp;
                                <b>[{lang($gateway_only[$v.gateway_name]['mode'])}]</b>
                            {/if}
                        </td>
                        <td>
                            {if isset($gateway_only[$v.gateway_name])}
                                {$link=""}
                                {if $gateway_only[$v.gateway_name]['id']==1}
                                    {$link="paypal_config"}
                                {elseif $gateway_only[$v.gateway_name]['id']==2}
                                    {$link="authorize_config"}
                               {*  {elseif $gateway_only[$v.gateway_name]['id']==5}
                                    {$link="bitcoin_configuration"} *}
                                {elseif $gateway_only[$v.gateway_name]['id']==3}
                                    {$link="blockchain_configuration"}
                                {elseif $gateway_only[$v.gateway_name]['id']==4}
                                    {$link="bitgo_configuration"}
                                {elseif $gateway_only[$v.gateway_name]['id']==5}
                                    {$link="payeer_configuration"}
                                {elseif $gateway_only[$v.gateway_name]['id']==6}
                                    {$link="sofort_configuration"}
                                {elseif $gateway_only[$v.gateway_name]['id']==7}
                                    {$link="squareup_configuration"}
                                {elseif $gateway_only[$v.gateway_name]['id']==11}
                                    {$link="bank_configuration"}    
                                {/if}
                                <a href="{$link}" class=""> <i class="fa fa-cog fa-1-5-x"></i></a>
                                <input type="hidden" id="number" name="number" value="{$i}">
                                <input type="hidden" id="id" name="id{$i}" value="{$v.id}">
                            {elseif $v.id==11}
                                {$link="bank_configuration"}
                                <a href="{$link}" class=""> <i class="fa fa-cog fa-1-5-x"></i></a>
                                <input type="hidden" id="number" name="number" value="{$i}">
                                <input type="hidden" id="id" name="id{$i}" value="{$v.id}">  
                            {/if}

                        </td>
                     
                        <td>
                         <div class="form-group-button">
                                <label class="i-switch bg-primary">
                                    <input type="checkbox" name="status[]" id="status[]" value="{$v.id}" {if $v.status=='yes'} checked {/if} class="switch-input">
                                    <i></i>
                                </label>
                            </div>
                        </td>

                        <td class="payment_width_td">
                            <div class="form-group-button">
                                <label class="i-switch bg-primary">
                                    <input type="checkbox" {if $v.registration} checked {/if} value="{$v.id}" name="registration[]" class="switch-input">
                                    <i></i>
                                </label>
                            </div>
                        </td>

                         {if $MODULE_STATUS['repurchase_status'] == 'yes'}
                            <td  class="payment_width_td">
                                <div class="form-group-button">
                                    <label class="i-switch bg-primary">
                                        <input type="checkbox" {if $v.repurchase} checked {/if} value="{$v.id}" name="repurchase[]" class="switch-input">
                                        <i></i>
                                    </label>
                                </div>
                            </td>
                        {/if}

                       
                        {if $MODULE_STATUS['subscription_status'] == 'yes'}
                            <td class="payment_width_td" >
                                <div class="form-group-button">
                                    <label class="i-switch bg-primary">
                                        <input type="checkbox" {if $v.membership_renewal} checked {/if} value="{$v.id}" name="membership_renewal[]" class="switch-input">
                                        <i></i>
                                    </label>
                                </div>
                            </td>
                        {/if}
                        {if $MODULE_STATUS['package_upgrade'] == 'yes'}
                            <td class="payment_width_td">
                                <div class="form-group-button">
                                    <label class="i-switch bg-primary">
                                        <input type="checkbox" {if $v.upgradation} checked {/if} value="{$v.id}" name="upgradation[]" class="switch-input">
                                        <i></i>
                                    </label>
                                </div>
                            </td>
                        {/if}
                          <td class="payment_width_td">
                            <div class="form-group-button">
                                <label class="i-switch bg-primary">
                                    <input type="checkbox" {if $v.admin_only} checked {/if}  value="{$v.id}" name="admin_status[]" id="admin_status[]" class="switch-input">
                                    <i></i>
                                </label>
                            </div></td>



                       
                    </tr>
                {/foreach}



            </tbody>
        </table>
    </div>
     <div class="panel-footer">
         <button type="submit" id="update" value="update" name="update" class="btn btn-sm btn-primary update_config">{lang('Update')}</button>
     </div>    
  </div>      
</div>
{form_close()}

{*
<div class="panel panel-default">

    <div class="panel-body">
    <legend><span class="fieldset-legend">{lang('payment_gateway_configuration')}</span></legend>


    {form_open('', 'name="payment_status_form" id="payment_status_form" method="post"')}
    <div class="panel panel-default table-responsive">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th></th>
                    <th>{lang('Payment_method')}</th>
                    <th>{lang('payment_logo')}</th>
                    
                    <th>{lang('mode')}</th>
                   
                    <th>{lang('action')}</th>
                </tr>
            </thead>
            <tbody>
                {assign var="i" value=0}
                {foreach from=$gateway_only item=v}
                    <tr>
                        <td></td>
                        <td>{if $v.gateway_name=="Bitcoin"}{lang('blocktrail')}{else}{$v.gateway_name}{/if}</td>
                        <td> {if $v.gateway_name == "Bank Transfer"}
                            <p>{lang('NA')}</p>
                            {else}
                            <img class="img_width_payout" src="{$BASE_URL}/public_html/images/logos/{$v.logo}" />
                           {/if} 
                        
                        </td>
                        
                        <td>
                            {if $v.mode=='live'}{lang('live')}{elseif $v.gateway_name =='Bank Transfer'}{lang('NA')}{else}{lang('test')}{/if}
                        </td>
                   
                        <td>
                            {$link=""}
                            {if $v.id==1}
                                {$link="paypal_config"}
                            {elseif $v.id==2}
                                {$link="authorize_config"}
                           {elseif $v.id==5}
                                {$link="bitcoin_configuration"}
                            {elseif $v.id==3}
                                {$link="blockchain_configuration"}
                            {elseif $v.id==4}
                                {$link="bitgo_configuration"}
                            {elseif $v.id==5}
                                {$link="payeer_configuration"}
                            {elseif $v.id==6}
                                {$link="sofort_configuration"}
                            {elseif $v.id==7}
                                {$link="squareup_configuration"}
                            {elseif $v.id==11}
                                {$link="bank_configuration"}    
                            {/if}
                            <a href="{$link}" class=""> <i class="fa fa-cog fa-1-5-x"></i></a>
                            <input type="hidden" id="number" name="number" value="{$i}">
                            <input type="hidden" id="id" name="id{$i}" value="{$v.id}">
                        </td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        </div>
         
    </div>
</div>
    {form_close()}
*}
    

{/block}
{block name=script}
    <script src="{$PUBLIC_URL}javascript/sortableui.js"></script>
{/block}