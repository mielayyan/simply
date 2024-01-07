{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" class="no-display">
    <span id="error_msg">{lang('you_must_enter_user_name')}</span>
    <span id="row_msg">{lang('rows')}</span>
    <span id="show_msg">{lang('shows')}</span>
</div>

<div id="page_path" style="display:none;">{$PATH_TO_ROOT_DOMAIN}admin/</div>
{if $ci->input->get('binary_details')!="" && $ci->input->get('user_name')!=""}
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
{include file="layout/search_member_get.tpl" search_url="admin/view_leg_count"}

    <div id="user_account"></div>
    <div id="username_val" style="display:none;">{$user_name}</div>
    <div class="panel panel-default">
    <div class="table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped">
            <thead>
                <tr class="th">
                    <th>{lang('name')}</th>
                    <th>{lang('left_point')}</th>
                    <th>{lang('right_point')}</th>
                    <th>{lang('left_carry')}</th>
                    <th>{lang('right_carry')}</th>
                    <th>{lang('total_pair')}</th>
                    <th>{lang('amount')}</th>
                </tr>
            </thead>
            {if count($user_leg_detail)>0}
                <tbody>
                    {assign var=i value="0"}
                    {foreach from=$user_leg_detail key=key item=v}{$i=$i+1}
                        {$i= $i+1}
                        {assign var="left" value ="{$v.left}"}
                        {assign var="right" value ="{$v.right}"}
                        {assign var="left_carry" value ="{$v.left_carry}"}
                        {assign var="right_carry" value ="{$v.right_carry}"}
                        {assign var="tot_leg" value ="{$v.total_leg}"}
                        {assign var="tot_amt" value ="{$v.total_amount}"}
                        <tr>
                            <td>{user_with_name($v.user, $v.detail, true)}</td>
                            <td>{$left}</td>
                            <td>{$right}</td>
                            <td>{$left_carry}</td>
                            <td>{$right_carry}</td>
                            <td>{$tot_leg}</td>
                            <td>{format_currency($tot_amt)}</td>
                        </tr>
                    {/foreach}
                </tbody>
            {else}
                <tbody>
                    <tr>
                        <td colspan="8">
                            <h4 class="text-center">{lang('no_records_found')}</h4>
                        </td>
                    </tr>
                </tbody>
            {/if}
        </table>
        
        </div>
        {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
    </div>
{/block}