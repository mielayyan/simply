{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK} {assign var="report_name" value="{lang('profile_report')}"} {assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_profile_view_report"} {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_profile_view_report"}
{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
<div class="">

 
    {include file="admin/report/header.tpl" name=""}
    <h4 align="center"><b>{$filterString}</b></h4>
    <div class="panel panel-default  ng-scope">
    <div class="table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{if count($details)!=0}
      <tbody>
      <thead>
        <tr class="th">
            <th>#</th>
            {* <th>{lang('sl_no')}</th> *}
            {* <th>{lang('full_name')}</th> *}
            {* <th>{lang('user_name')}</th> *}
            <th>{lang('sponsor')}</th>
            <th>{lang('email')}</th>
            <th>{lang('mobile_no')}</th>
            <th>{lang('country')}</th>
            <th>{lang('zipcode')}</th>
            <th>{lang('enrollment_date')}</th>
        </tr>
      </thead>
        {foreach  from= $details item=v}
            {assign var="tr_class" value=""}
            {assign var="i" value="0"}

            <tr>
                <td>{counter}</td>
                {* <td>{$v.user_detail_name} {$v.user_detail_second_name}</td> *}
                {* <td>{$user_name}</td> *}
                <td>{if $user_name ==$v.user_name}{lang('NA')}{else}{$v.user_name}{/if}</td>
                <td>{$v.user_detail_email}</td>
                <td>{$v.user_detail_mobile}</td>
                <td>{$v.user_detail_country}</td>
                <td>{$v.user_detail_pin}</td>
                <td>{date('d M Y - h:i:s A', strtotime($v.join_date))}</td>
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
 </div>
{/block}