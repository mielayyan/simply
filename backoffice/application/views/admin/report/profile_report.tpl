{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK} {assign var="report_name" value="{lang('profile_report')}"} {assign var="excel_url" value="{$BASE_URL}admin/excel/user_profiles_excel"} {assign var="csv_url" value="{$BASE_URL}admin/excel/user_profiles_csv"}
{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
{include file="admin/report/header.tpl" name=""}
  <div class="panel panel-default table-responsive  ng-scope">
    <table st-table="rowCollectionBasic" class="table table-striped">{if count($profile_arr)!=0}
      <tbody>
      <thead>
        <tr class="th">
          <th>#</th>
            <th>{lang('member_name')}</th>
            <th>{lang('sponsor')}</th>
            <th>{lang('email')}</th>
            <th>{lang('mobile_no')}</th>
            <th>{lang('country')}</th>
            <th>{lang('zipcode')}</th>
            <th>{lang('enrollment_date')}</th>
        </tr>
      </thead>
        {foreach  from= $profile_arr item=v}
            {assign var="tr_class" value=""}
            <tr>
                <td>{counter}</td>
                <td>{$v.user_detail_name} {$v.user_detail_second_name} ({$v.uname})</td>
                <td>{$v.sponser_name}</td>
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
{/block}