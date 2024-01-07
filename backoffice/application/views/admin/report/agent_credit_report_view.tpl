{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK} {assign var="report_name" value="{lang('agent_report')}"} {assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_agent_view_report"} {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_agent_view_report"}
{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
<div class="">

 
    {include file="admin/report/header.tpl" name=""}
    {* <h4 align="center"><b>{$filterString}</b></h4> *}
    <div class="panel panel-default  ng-scope">
    <div class="table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{if count($details)!=0}
      <tbody>
      <thead>
        <tr class="th">
            <th>{lang('sl_no')}</th>
            <th>{lang('full_name')}</th>
            <th>{lang('from_name')}</th>
            <th>{lang('user_name')}</th>
            <th>{lang('agent')}</th>
            <th>{lang('country')}</th>
            <th>{lang('wallet_amount')}</th>
            <th>{lang('join_date')}</th>
            
        </tr>
      </thead>
      {* {dd($ci->input->get('offset'))} *}
            {assign var="i" value="0"}
        {foreach  from= $details item=v}
            {assign var="tr_class" value=""}
            {$i=$i+1}

            <tr>
                <td>{$ci->input->get('offset')+$i}</td>
                {* <td>{$i++}</td> *}
                <td>{$v.full_name}</td>
                <td>{$v.from_name}</td>
                <td>{$v.user_name}</td>
                <td>{$v.agent_username}</td>
                <td>{$v.country}</td>
                <td>{$v.wallet_amount}</td>
                <td>{date('d M Y - h:i:s A', strtotime($v.date_added))}</td>
            </tr>
        {/foreach}
        </tbody>
      {else}
        <h4 align="center">{lang('no_data')}</h4>
    {/if}
    </table>
        </div>
    </div>
</div>
{$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
 </div>
{/block}