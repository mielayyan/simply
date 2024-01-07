{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}

<ul class="list-group list-group-overview b-a">
     <li class="list-group-item">
        <span class="text-md">{lang('pending_requests')}</span>
        <span class="block text-md text-warning-dker">{format_currency($total_amount_active_request)}</span>
    </li>
   
    <li class="list-group-item">
        <span class="text-md">{lang('approved_waiting_for_transfer')}</span>
        <span class="block text-md text-info">{format_currency($total_amount_waiting_requests)}</span>
    </li>
     <li class="list-group-item">
        <span class="text-md">{lang('approved_paid')}</span>
        <span class="block text-md text-success">{format_currency($total_amount_paid_request)}</span>
    </li>
    <li class="list-group-item">
        <span class="text-md">{lang('rejected_requests')}</span>
        <span class="block text-md text-danger">{format_currency($total_amount_rejected_requests)}</span>
    </li>
   
    
</ul>

{include file="layout/search_member_get.tpl" search_url="admin/my_withdrawal_request"}

    
    <div class="tabsy">
        <input type="radio" id="tab1" name="tab" {$tab1}>
        <label class="tabButton" for="tab1">{lang('pending_requests')}</label>
        <div class="tab">
            <div class="content">
                <div class="panel-default">
                    {if !empty($active_requests)}
                        <input type="hidden" name="current_tab" id="current_tab" value="tab1" >
                        <div class="table-responsive">
                        <table st-table="rowCollectionBasic" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{lang('sl_no')}</th>
                                    <th>{lang('member_name')}</th>
                                    <th>{lang('amount')}</th>
                                    <th>{lang('ewallet_balance')}</th>
                                    <th>{lang('requested_date')}</th>
                                </tr>
                            </thead>
                            {assign var="i" value=0}
                            {assign var="class" value=""}
                            {assign var="path" value="{$BASE_URL}admin/"}
                            <tbody>
                                {foreach from=$active_requests item=v key=key}
                                    <tr>
                                        <td>{$page1 + $i + 1}</td>
                                        <td>
                                                {if $v.user_delete_status == "active"}
                                                    {user_with_name($v.user_name, $v.user_detail_name, true)}
                                                {else}
                                                    {$v.user_name}
                                                {/if}
                                        </td>
                                        <td>
                                            {format_currency($v.payout_amount)}
                                        </td>
                                        <td>{format_currency($v.balance_amount)}</td>
                                        <td>{$v.requested_date|date_format:"d M Y - h:i:s A"}</td>
                                    </tr>
                                    {$i=$i+1}
                                {/foreach}                
                            </tbody>
                        </table>
                        </div>
                        <div class="panel-footer panel-footer-pagination text-right">
                        {$result_per_page1}
                        </div>
                    {else}
                        <h4 align="center">{lang('no_records_found')}</h4>
                    {/if}
                </div>
            </div>
        </div>
        <input type="radio" id="tab2" name="tab" {$tab2}>
        <label class="tabButton" for="tab2">{lang('approved_waiting_for_transfer')}</label>
        <div class="tab">
            <div class="content">
                <div class="panel-default">
                    {if count($waiting_requests)>0}
                        <input type="hidden" name="current_tab" id="current_tab" value="tab2" >
                        <div class="table-responsive">
                        <table st-table="rowCollectionBasic" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>{lang('sl_no')}</th>
                                    <th>{lang('member_name')}</th>
                                    <th>{lang('amount')}</th>
                                    <th>{lang('payout_method')}</th>
                                    <th>{lang('approved_date')}</th>
                                </tr>
                            </thead>
                            {assign var="i" value=0}
                            {assign var="class" value=""}
                            {assign var="path" value="{$BASE_URL}admin/"}
                            <tbody>
                                {foreach from=$waiting_requests item="v"}
                                    <tr class="{$class}">
                                        <td>{$page2 + $i +1}</td>
                                        <td>
                                            {if $v.user_delete_status == "active"}
                                                {user_with_name($v.user_name, $v.user_detail_name, true)}
                                            {else}
                                                {$v.user_name}
                                            {/if}
                                        </td>
                                        <td>{format_currency($v.paid_amount)}</td>
                                        <td>
                                            {if $v.payment_method eq 'bank'}
                                                {lang('bank')}
                                            {elseif $v.payment_method eq 'Bitcoin'}
                                                {lang('blocktrail')}
                                            {else}      
                                                {$v.payment_method}
                                            {/if}
                                        </td>
                                        <td>{$v.paid_date|date_format:"d M Y - h:i:s A"}</td>
                                    </tr>
                                    {$i=$i+1}
                                {/foreach}                
                            </tbody>
                        </table>
                        </div>
                        <div class="panel-footer panel-footer-pagination text-right">
                        {$result_per_page2}
                        </div>
                    {else}
                        <h4 align="center">{lang('no_records_found')}</h4>
                    {/if}
                </div>
            </div>
        </div>
        <input type="radio" id="tab3" name="tab" {$tab3}>
        <label class="tabButton" for="tab3">{lang('approved_paid')}</label>
        <div class="tab">
            <div class="content">
                <div class="panel-default">
                {if count($paid_requests)>0}
                    <input type="hidden" name="current_tab" id="current_tab" value="tab3" >
                <div class="table-responsive">
                <table class="table table-striped" id="">
                    <thead>
                        <tr>
                            <th>{lang('sl_no')}</th>
                            <th>{lang('member_name')}</th>
                            <th>{lang('amount')}</th>
                            <th>{lang('payout_method')}</th>
                            <th>{lang('paid_date')}</th>
                        </tr>
                        </thead>
                            {assign var="i" value=0}
                            {assign var="class" value=""}
                            {assign var="path" value="{$BASE_URL}admin/"}
                            <tbody>
                                {foreach from=$paid_requests item="v"}
                                    <tr class="{$class}">
                                        <td>{$page3 + $i + 1}</td>
                                        <td>
                                            {if $v.user_delete_status == "active"}
                                                {user_with_name($v.user_name, $v.user_detail_name, true)}
                                            {else}
                                                {$v.user_name}
                                            {/if}
                                        </td>
                                        <td>{format_currency($v.paid_amount)}
                                        </td>
                                        <td>
                                            {if $v.payment_method eq 'bank'}
                                                {lang('bank')}
                                            {elseif $v.payment_method eq 'Bitcoin'}
                                                {lang('blocktrail')}
                                            {else}      
                                                {$v.payment_method}
                                            {/if}
                                        </td>
                                        <td>{$v.paid_date|date_format:"d M Y - h:i:s A"}</td>
                                    </tr>
                                    {$i=$i+1}
                                {/foreach}                
                            </tbody>
                        </table>
                        </div>
                        <div class="panel-footer panel-footer-pagination text-right">
                        {$result_per_page3}
                        </div>
                    {else}
                        <h4 align="center">{lang('no_records_found')}</h4>
                    {/if}        
                    </div>
            </div>
        </div>
        <input type="radio" id="tab4" name="tab" {$tab4}>
        <label class="tabButton" for="tab4">{lang('rejected_requests')}</label>
        <div class="tab">
            <div class="content">
                    <div class="panel-default">
                    {if count($rejected_requests)>0}
                        <input type="hidden" name="current_tab" id="current_tab" value="tab1" >
                        <div class="table-responsive">
                    <table class="table table-striped" id="">
                            <thead class="">
                                <tr>
                                    <th>{lang('sl_no')}</th>
                                    <th>{lang('member_name')}</th>
                                    <th>{lang('amount')}</th>
                                    <th>{lang('requested_date')}</th>
                                    <th>{lang('rejected_date')}</th>
                                </tr>
                            </thead>
                            {assign var="i" value=0}
                            {assign var="class" value=""}
                            {assign var="path" value="{$BASE_URL}admin/"}
                            <tbody>
                                {foreach from=$rejected_requests item="v"}
                                    {if $i%2==0}
                                        {$class='tr1'}
                                    {else}
                                        {$class='tr2'}
                                    {/if}
                                    <tr class="{$class}">
                                        <td>
                                            {$page4 +$i +1}
                                        </td>
                                        <td>
                                            {if $v.user_delete_status == "active"}
                                                {user_with_name($v.user_name, $v.user_detail_name, true)}
                                            {else}
                                                {$v.user_name}
                                            {/if}
                                        </td>
                                        <td>
                                            {format_currency($v.payout_amount)}
                                        </td>
                                        <td>{$v.requested_date|date_format:"d M Y - h:i:s A"}</td>
                                        <td>{$v.updated_date|date_format:"d M Y - h:i:s A"}</td>
                                    </tr>
                                    {$i=$i+1}
                                {/foreach}                
                            </tbody>
                        </table>
                        </div>
                        <div class="panel-footer panel-footer-pagination text-right">
                        {$result_per_page4}
                        </div>
                    {else}
                        <h4 align="center">{lang('no_records_found')}</h4>
                    {/if}       
                </div>
            </div>
        </div>
    </div>
    {/block}
{block name=script} {$smarty.block.parent}
    <script>
        jQuery(document).ready(function () {
        });
    </script>
    <script type="text/javascript">
    </script>
{/block}
