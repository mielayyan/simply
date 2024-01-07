{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display:none;">
    <span id="error_msg">{lang('You_must_select_from_date')}</span>
    <span id="error_msg1">{lang('You_must_select_to_date')}</span>
    <span id="error_msg2">{lang('You_must_Select_From_To_Date_Correctly')}</span>
    <span id="error_msg3">{lang('You_must_select_a_date')}</span>
    <span id="error_msg4">{lang('you_must_select_a_to_date_greater_than_from_date')}</span>
    <span id="error_msg5">{lang('digits_only')}</span>
</div>


{if $from_report}
    <p class="text-right">
        <a href="{BASE_URL}/admin/top_earners_report" class="btn btn-sm btn-info btn-addon"><i class="fa fa-backward"></i>{lang('back')}</a>
    </p>
{/if}
{if $ci->input->get('pv_details')!="" && $ci->input->get('user_name')!=""}
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
{include file="layout/search_member_pv.tpl" search_url="admin/select_report/pv_report"}


    <div class="panel panel-default">
        <div class="panel-header">
            <ul class="list-group m-b-none list-group-overview text-right">
                <li class="list-group-item">
                    <span class="text-md">{lang('total_pv')}:</span>
                    <span class="text-md text-primary">{$pv}</span>
                </li>
                <li class = "list-group-item">
                   <span class="text-md">{lang('total_gpv')}:</span>
                    <span class="text-md text-primary">{$gpv}</span>
               </li>
            </ul>
        </div>
         <div class="table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped">
            <thead class="">
                <tr class="">
                    <th>{lang('slno')}</th>
                    <th>{lang('pvs')}</th>
                    <th>{lang('description')}</th>
                    <th>{lang('date')}</th>
                </tr>
            </thead>
            {if count($pv_details) > 0}
                {$i = 0}
                <tbody>
                   {foreach from=$pv_details item=v}
                 <tr>

                          {if $v.pv_obtained_by == 'register' || $v.pv_obtained_by == 'repurchase' || $v.pv_obtained_by == 'upgrade' || $v.pv_obtained_by == 'manualpv_add_by_admin'}
                            {$amount_class = 'text-success-dker'}
                            {$amount_font_class = 'fa-plus'}
                          {else}
                             
                            {$amount_class = 'text-danger-dker'}
                            {$amount_font_class = 'fa-minus'}
                       
                          {/if}

                          <td>{$page_id + $i + 1}</td>
                          <td><span class = "{$amount_class}"><i class="currency-symbol fa {$amount_font_class}"></i>{$v.pv_amount}</span> ({lang($v.pv_type)})</td>
                           {if $v.pv_obtained_by == 'register'}
                           <td>{lang('registration')} {lang('of')} {user_with_name($v.from_user, "`$v.full_name`", true, null)}</td>
                           {else if $v.pv_obtained_by == 'repurchase'}
                           <td>{lang('repurchase')} {lang('from')} {user_with_name($v.from_user, "`$v.full_name`", true, null)}</td>
                           {else if $v.pv_obtained_by == 'upgrade'}
                           <td>{lang('package_upgrade')} {lang('by')} {user_with_name($v.from_user, "`$v.full_name`", true, null)}</td>
                           {else if $v.pv_obtained_by == 'manualpv_add_by_admin'}
                          <td>{lang('credited')} {lang('by')} admin {user_with_name($v.from_user, "`$v.full_name`", true, null)}</td>
                           {else if $v.pv_obtained_by == 'manualpv_deduct_by_admin'}
                          <td>{lang('debited')} {lang('by')} admin {user_with_name($v.from_user, "`$v.full_name`", true, null)}</td>
                           {else}
                           <td></td>
                           {/if} 
                          <td>{$v.date|date_format:"d M Y - h:i:s A"}</td>
                  </tr>
                  {$i = $i + 1}
                   {/foreach}
                    
                </tbody>
            {else}
                <tbody>
                    <tr>
                        <td align="center" colspan="8">
                            <h4>{lang('no_data')}</h4>
                        </td>
                    </tr>
                </tbody>
            {/if}
        </table>
        </div>
        {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
    </div>

{/block}