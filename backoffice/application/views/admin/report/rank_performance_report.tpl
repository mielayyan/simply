{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}{assign var="report_name" value="{lang('rank_performance_report')}"} {assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_rank_performance_report?username={$user_name}"} {assign var="csv_url"
value="{$BASE_URL}admin/excel/create_csv_rank_performance_report?username={$user_name}"} 
 <div class="panel panel-default">
<div class="panel-body">
{form_open('admin/report/rank_performance_report','role="form" class="" method="get" name="user" id="searchform"')}
  {include file="layout/error_box.tpl"}
  <div class="col-sm-3 padding_both">
  <div class="form-group">
    <label class="required" for="user_name">{lang('user_name')}</label>
     <input type="text" class="form-control user_autolist" id="user_name" name="user_name" autocomplete="Off" value="{$user_name}">
  </div>
  </div>
  <div class="col-sm-3 padding_both_small">
  <div class="form-group credit_debit_button">
    <button class="btn btn-primary" name="user_submit" type="submit" value="{lang('view')}">{lang('view')}</button>
  </div>
  </div>
{form_close()}
</div>
</div>
{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="panel-body panel">
{include file="admin/report/header.tpl" name=""}
 
<h2 class="text-center">{$report_name}</h2>
{* <h3>
    <center>{lang('current_rank')} :
        <font color="#ff0000">
        {if $rank_achievement.current_rank.rank_id}
            {$rank_achievement.current_rank.rank_name}
        {else}
            NA
        {/if}
        </font>
    </center>
</h3> *}
{* <h4>
    <center style="font-size: 12px;">{lang('next_rank')} :
        <font color="green">
        {if isse($rank_achievement.next_rank.rank_id)}
            {$rank_achievement.next_rank.rank_name}
        {else}
            NA
        {/if}
        </font>
    </center>
</h4> *}
<div class="panel panel-default ng-scope">
<div class="table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">
        <tbody>
            <tr class="text">
                <td><strong>{lang('member_name')}</strong></td>
                <td>{$full_name}({$user_name})</td>
            </tr>
            <tr>
                <td><strong> {lang('current_rank')}</strong></td>
                <td>
                    {if $rank_achievement.current_rank.rank_id}
                        {$rank_achievement.current_rank.rank_name}
                    {else}
                        NA
                    {/if}
                </td>
            </tr>
            <tr>
                <td><strong>{lang('next_rank')}</strong></td>
                <td>
                    {if isset($rank_achievement.next_rank.rank_id)}
                        {$rank_achievement.next_rank.rank_name}
                    {else}
                        NA
                    {/if}
                </td>
            </tr>
            <tr>
                <td><strong>{lang('current_referral_count')}</strong></td>
                <td>{$rank_achievement.current_rank.referal_count}</td>
            </tr>
            {if isset($rank_achievement.next_rank.rank_id)}
                <tr>
                    <td><strong>{lang('referral_count_for')} {$rank_achievement.next_rank.rank_name}</strong></td>
                    <td>{$rank_achievement.next_rank.referal_count}</td>
                </tr>
                <tr>
                    <td><strong>{lang('needed_referral_count')}</strong></td>
                    <td>{max($rank_achievement.next_rank.referal_count-$rank_achievement.current_rank.referal_count,0)}</td>
                </tr>
            {/if}

            {if $rank_achievement.criteria.personal_pv}
                <tr>
                    <td><strong>{lang('current_personal_pv')}</strong></td>
                    <td>{$rank_achievement.current_rank.personal_pv}</td>
                </tr>
                {if isset($rank_achievement.next_rank.rank_id)}
                    <tr>
                        <td><strong>{lang('personal_pv_for')} {$rank_achievement.next_rank.rank_name}</strong></td>
                        <td>{$rank_achievement.next_rank.personal_pv}</td>
                    </tr>
                    <tr>
                        <td><strong>{lang('needed_personal_pv')}</strong></td>
                        <td>{max($rank_achievement.next_rank.personal_pv-$rank_achievement.current_rank.personal_pv|intval,0)}</td>
                    </tr>
                {/if}
            {/if}

            {if $rank_achievement.criteria.group_pv}
                <tr>
                    <td><strong>{lang('current_group_pv')}</strong></td>
                    <td>{$rank_achievement.current_rank.group_pv}</td>
                </tr>
                {if isset($rank_achievement.next_rank.rank_id)}
                    <tr>
                        <td><strong>{lang('gpv_for')} {$rank_achievement.next_rank.rank_name}</strong></td>
                        <td>{$rank_achievement.next_rank.group_pv}</td>
                    </tr>
                    <tr>
                        <td><strong>{lang('needed_group_pv')}</strong></td>
                        <td>{max($rank_achievement.next_rank.group_pv-$rank_achievement.current_rank.group_pv,0)}</td>
                    </tr>
                {/if}
            {/if}
            
            {if $rank_achievement.criteria.downline_count}
                <tr>
                    <td><strong>{lang('current_downline_count')}</strong></td>
                    <td>{$rank_achievement.current_rank.downline_count}</td>
                </tr>
                {if isset($rank_achievement.next_rank.rank_id)}
                    <tr>
                        <td><strong>{lang('downline_count_for')} {$rank_achievement.next_rank.rank_name}</strong></td>
                        <td>{$rank_achievement.next_rank.downline_count}</td>
                    </tr>
                    <tr>
                        <td><strong>{lang('needed_downline_count')}</strong></td>
                        <td>{max($rank_achievement.next_rank.downline_count-$rank_achievement.current_rank.downline_count,0)}</td>
                    </tr>
                {/if}
            {/if}

            {if $rank_achievement.criteria.downline_package_count && $rank_achievement.current_rank.package_name}
                {foreach from=$rank_achievement.current_rank.package_name item=v key=k}
                    <tr>
                        <td><strong>{lang('current_downline_count')}({$v})</strong></td>
                        <td>{$rank_achievement.current_rank.downline_package_count[$k]}</td>
                    </tr>
                    {if isset($rank_achievement.next_rank.rank_id)}
                        <tr>
                            <td><strong>{lang('downline_count_for')} {$rank_achievement.next_rank.rank_name}({$v})</strong></td>
                            <td>{$rank_achievement.next_rank.downline_package_count[$k]}</td>
                        </tr>
                        <tr>
                            <td><strong>{lang('needed_downline_count')}({$v})</strong></td>
                            <td>{max($rank_achievement.next_rank.downline_package_count[$k]-$rank_achievement.current_rank.downline_package_count[$k],0)}</td>
                        </tr>
                    {/if}
                {/foreach}
            {/if}
        </tbody>

    </table>
    </div>
    </div>
</div>
{/block}
