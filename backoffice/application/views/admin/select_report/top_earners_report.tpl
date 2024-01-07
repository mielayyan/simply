{extends file=$BASE_TEMPLATE} {block name=script} {$smarty.block.parent}

<script src="{$PUBLIC_URL}javascript/misc.js" type="text/javascript"></script>
{/block}{block name=$CONTENT_BLOCK} {if count($top_earners) > 0 }
{assign var="report_name" value="{lang('top_earners')}"} {assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_top_earners_report"} {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_top_earners_report"}
{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
    <div class="panel panel-default table-responsive">
    {* <div class="panel-body"> *}
    
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>{lang('sl_no')}</th>
                    <th>{lang('member_name')}</th>
                   {*  <th>{lang('username')}</th> *}
                    <th>{lang('total_earnings')}</th>
                    <th>{lang('ewallet_balance')}</th>
                    <th>{lang('action')}</th>
                </tr>
            </thead>
            <tbody>
                {assign var="root" value="{$BASE_URL}admin/"} {assign var="i" value=0} {foreach from=$top_earners item=v} {$i=$i+1}
                <tr>
                    <td>{$ci->input->get('offset')+$i}</td>
                    <td>{* {$v.name} *} {user_with_name($v.user_name,"`$v.name`", true, null)}</td>
                    {* <td>{$v.user_name}</td> *}
                    <td>{format_currency($v.total_earnings)}</td>
                    <td>{format_currency($v.current_balance)}</td>
                    <td>
                        <a href="#" onclick="javascript:view_user_earnings('{$v.user_name}', 'top_earners','{$root}')">
                            <div class="field1">
                                <button class="btn-link h4 has-tooltip text-info"><i class="fa fa-eye" title="{lang('more_info')}"></i></button>
                                <span class="tooltip green">
                <p>{lang('details')}</p>
                </span> </div>
                        </a>
                    </td>
                </tr>
                {/foreach}
            </tbody>
        </table>
        {* </div> *}
       
    </div>
    {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
</div>
{else}
<div class="panel-body">
    <br/>
    <p align="center">{lang('no_top_earners')}</p>
</div>
 {$ci->pagination->create_links()}
{/if} {/block}