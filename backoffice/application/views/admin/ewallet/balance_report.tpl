{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display: none;">
        <span id="errmsg">{lang('You_must_enter_keyword_to_search')}</span>
</div>

{include file="layout/search_member_get.tpl" search_url="admin/balance_report"}

<div class="panel panel-default">
    <div class="panel-header">
        <ul class="list-group m-b-none list-group-overview text-right">
            <li class="list-group-item">
                <span class="text-md" style="">{lang('total_ewallet_balance')}:</span>
                <span class="text-md text-primary" style="">{format_currency($grand_total_ewallet_balance)}</span>
            </li>
        </ul>
    </div>   
    <div class="table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">
        <thead>
            <tr>
                <th>{lang('slno')}</th>
                <th>{lang('member_name')}</th>
                <th>{lang('ewallet_balance')}</th>
            </tr>
        </thead>
        {if count($report_data)>0}
            {assign var="i" value=0}
            <tbody>
                {foreach from=$report_data item=v}

                <tr>
                <td>{$i + $page_id + 1}</td>
                <td>{user_with_name($v.user_name, "`$v.full_name`", true, null)}</td>
                <td>{format_currency($v.balance_amount)}</td>
                </tr>
                {$i=$i+1}
                {/foreach}
            </tbody>
        {else}
            <tbody>
                <tr><td colspan="8" align="center"><h4 align="center"> {lang('no_records_found')}</h4></td></tr>
            </tbody>
        {/if}
    </table>
</div>
{$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
</div>
{/block}
