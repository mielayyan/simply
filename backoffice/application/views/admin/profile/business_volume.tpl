{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" class="no-display">
    <span id="error_msg">{lang('select_user_id')}</span>
    <span id="errmsg1">{lang('you_must_enter_username')}</span>
</div>

<div id="page_path" style="display:none;">{$PATH_TO_ROOT_DOMAIN}admin/</div>
{if $ci->input->get('business_volume')!="" && $ci->input->get('user_name')!=""}
<div class="button_back">
  <a href="{$BASE_URL}admin/user_account?user_name={$ci->input->get('user_name')}"> 
    <button class="btn m-b-xs btn-sm btn-info btn-addon"><i class="fa fa-backward"></i>{lang('back')}</button>
  </a>
</div>
{elseif str_contains($coming_from, 'profile_view')}
    <div class="back-btn" style="padding-right: 10px; text-align: right;">
        <a href="{BASE_URL}/admin/profile_view?user_name={$ci->input->get('user_name')}" class="btn m-b-xs btn-sm btn-info btn-addon" style="height: 32px"><i class="fa fa-backward"></i> {lang('back')}</a>
    </div>
{/if}

{include file="layout/search_member_get.tpl" search_url="admin/business_volume"}
    <div id="user_account"></div>
    <div id="username_val" style="display:none;">{$user_name}</div>
    <div class="panel panel-default">
    <div class="table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped">
            <thead>
                <tr class="th">
                    <th>{lang('slno')}</th>
                    <th>{lang('name')}</th>
                    <th>{lang('left_leg')}</th>
                    <th>{lang('left_leg_carry')}</th>
                    <th>{lang('right_leg')}</th>
                    <th>{lang('right_leg_carry')}</th>
                    <th>{lang('description')}</th>
                    <th>{lang('date')}</th>
                </tr>
            </thead>
            {if count($details)>0}
                <tbody>
                    {assign var=i value="0"}
                    {foreach from=$details key=key item=v}
                        {$i = $i+1}
                        {$amount_type = $v.amount_type}
                        {$action = $v.action}
                        {$sign = ""}
                        {if $amount_type == "user_join"} 
                            {$type ="{lang('volume_added_from_member')}  {$v.from_name} {lang('join')} "} 
                            {$sign="+"}
                        {else if $amount_type == "user_repurchase"} 
                            {$type ="{lang('volume_added_from_member')}  {$v.from_name} {lang('repurchase')} "} 
                            {$sign="+"}
                        {else if $amount_type == "leg" && $action != "deducted_without_pair"}
                            {$type="{lang('volume_taken_for_commission')}"}
                            {$sign="-"}
                        {else if $amount_type == "repurchase_leg" && $action != "deducted_without_pair"}
                            {$type="{lang('volume_taken_for_commission_repurchase')}"}
                            {$sign="-"}
                        {else if $action == "deducted_without_pair"} 
                            {$type="{lang('volume_deducted')}"}
                            {$sign="-"}
                        {else} 
                            {$type=lang($v.amount_type)} 
                        {/if}
                        <tr>
                            <td>{$ci->input->get('offset')+$key+1}</td>
                            <td>{user_with_name($v.user_name, $v.full_name, true)}</td>
                            <td>{if $v.left_leg_carry == '0'}{$v.left_leg_carry}{else}{$sign}{$v.left_leg_carry}{/if}</td>
                            <td>{$v.left_leg}</td>
                            <td>{if $v.right_leg_carry == '0'}{$v.right_leg_carry}{else}{$sign}{$v.right_leg_carry}{/if}</td>
                            <td>{$v.right_leg}</td>
                            <td>{$type}</td>
                            <td>{$v.date|date_format:"d M Y - h:i:s A"}</td>
                        </tr>
                    {/foreach}
                </tbody>
            {else}
                <tbody>
                    <tr>
                        <td colspan="8">
                            <h4>{lang('no_details')}</h4>
                        </td>
                    </tr>
                </tbody>
            {/if}
        </table>
        {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
        </div>
    </div>
{/block}