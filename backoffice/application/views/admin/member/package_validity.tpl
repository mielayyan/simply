{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
    <div id="span_js_messages" style="display: none;">
        <span id="errmsg1">{lang('please_enter_a_username')}</span>
        <span id="row_msg">{lang('rows')}</span>
        <span id="show_msg">{lang('shows')}</span>
    </div>
    {* <legend><span class="fieldset-legend">{lang('search_member')}</span></legend> *}
    {* {include file="layout/search_member.tpl"} *}
    {include file="layout/search_member_get.tpl" search_url="admin/package_validity"}
    {if $MODULE_STATUS['opencart_status'] != 'yes'}
    <p class="text-right" style="margin-bottom: 70px">
        <a href="{$BASE_URL}admin/pending_subscription">
            <button type="button" class="btn btn-sm btn-primary pull-right btn-addon">
                {lang('approve_pending_subscription')}
            </button>
        </a>
    </p>
    {/if}

    <div class="panel panel-default ng-scope">
   {*  <div class="panel-body"> *}
    <div class="table-responsive">
    
        <table st-table="rowCollectionBasic" class="table table-striped over_flw_btn">
            <thead>
                <tr>
                    <th>{lang('sl_no')}</th>
                    <th>{lang('member_name')}</th>
                    {if $subscription_config['based_on'] == 'member_package'}
                    <th>{lang('current_package')}</th>
                    {/if}
                    <th>{lang('renewal_amount')}</th>
                    <th>{lang('subscription_end')}</th>
                    {if $MODULE_STATUS['opencart_status'] != 'yes'}
                    <th>{lang('action')}</th>
                    {/if}
                </tr>
            </thead>
            {if count($expired_users)>0}
                {assign var="i" value=$page_num}
                {assign var="class" value=""}
                <tbody>
                    {foreach from=$expired_users item=v}
                        {$i=$i+1}

                        {assign var="id" value="{$v.id}"}
                        {* {assign var="user_name" value="{$v.user_name}"} *}
                        {assign var="product_validity" value="{$v.product_validity}"}
                        {assign var="current_package" value="{$v.product_name}"}
                        {*{assign var="renewal_amount" value="{$v.product_value}"}*}
                        {assign var="renewal_amount" value="{$v.subscription_value}"}
                        {assign var="encrypt_id" value="{$v.user_name}"}
                        <tr>      
                            <td>{$i}</td>
                            <td>{user_with_name($v.user_name, "`$v.user_detail_name` `$v.user_detail_second_name`", true, null)}</td>
                            {if $subscription_config['based_on'] == 'member_package'}
                            <td>{$current_package}</td>
                            {/if}
                            <td>{format_currency($renewal_amount)}</td>
                            <td>{$product_validity|date_format:"d M Y - h:i:s A"}</td>
                            {if $MODULE_STATUS['opencart_status'] != 'yes'}
                            <td>
                                <center> 
                                    <a href="{$PATH_TO_ROOT_DOMAIN}admin/member/upgrade_package_validity/{$encrypt_id}" data-original-title="{lang('upgrade_package_validity')}" data-content="{$v.user_name} - {$product_validity}" data-placement="left" data-trigger="hover" class="btn btn-light-grey btn-xs text-black">
                                        <i class="fa fa-arrow-right"></i> 
                                    </a>
                                </center>
                            </td>
                            {/if}
                        </tr>
                {/foreach}
                </tbody>
            {else}
                <tbody>
                    <tr><td colspan="8" align="center"><h4 align="center"> {lang('Product_validity_is_not_expired')}</h4></td></tr>
                </tbody>
            {/if}
        </table>
         {$ci->pagination->create_links("<div class='panel-footer'>", "</div>")}
        </div>
        {* </div> *}
        
    </div>
   
{/block} 
{block name='script'}
<script>
    jQuery(document).ready(function () {
        ValidateSearchMember.init();
    });
</script>
{/block}