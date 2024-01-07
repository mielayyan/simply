{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<style type="text/css">
    .right-search260 {
    grid-template-columns: 260px 135px auto;
}
</style>
    
    {if $from_tree}
        <p class="text-right">
            <a href="{BASE_URL}/admin/sponsor_tree" class="btn btn-sm btn-info btn-addon"><i class="fa fa-backward"></i>{lang('back')}</a>
        </p>
    {elseif str_contains($coming_from, 'profile_view')}
        <div class="back-btn" style="padding-right: 10px; text-align: right;">
            <a href="{BASE_URL}/admin/profile_view?user_name={$ci->input->get('user_name')}" class="btn m-b-xs btn-sm btn-info btn-addon" style="height: 32px"><i class="fa fa-backward"></i> {lang('back')}</a>
        </div>
    {/if}

   <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">

                <div class="col-sm-12 col-md-4">
                    <div class="dr-members">
                    <div class="referral_count purple-light-bg"><div class="text-sm text-purple">{lang('total_referral_member_count')}</div> <div class="text-1x text-purple">{$total_downline_count}</div></div>
                    <div class="referral_count green-light-bg"><div class="text-sm text-green">{lang('total_level')}</div><div class="text-1x text-green">{$total_levels}</div></div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-8">
                    
                    {form_open_multipart('admin/unilevel_history', 'role="form" class="" name="searchform" id="searchform" method="get"')}
                    <div class="errorHandler alert alert-danger no-display">
                        <i class="fa fa-times-sign"></i> {lang('errors_check')}
                    </div>
                    <div class="right-search260">
                    <div class="padding_both_small">
                        <div class="form-group m-b-n-xs">
                            <label>{lang('user_name')}</label>
                            <input class="form-control user_autolist" name="user_name" id="user_name" type="text" autocomplete="off" {if isset($username)} value="{$username}" {/if} >
                        </div>
                    </div>
                    <div class=" padding_both_small">
                        <div class="form-group m-b-n-xs">
                            <label>{lang('level')}</label>

                            <select name="level" id="level" class="form-control">
                                <option value="all">{lang('all')}</option>
                                {for $level=1 to $level_arr}
                                    <option value="{$level}" {if $binary_level==$level}selected=""{/if}>{$level}</option>
                                {/for}
                            </select>

                        </div>
                    </div>
                    <div class=" padding_both_small">
                        <div class="form-group m-b-n-xs">
                            <button type="submit" class="btn btn-sm btn-primary" id="user_details" value="{lang('search')}">{lang('search')}</button>
                            <a class="btn btn-sm btn-info" href="{$BASE_URL}admin/unilevel_history">
                            {lang('reset')} </a>
                        </div>
                    </div>
                    </div>
                    {form_close()}
                    
                </div>

            </div>
            
        </div>
    </div>

    <div class="panel panel-default">
        <div class="table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped">
            <thead>
                <tr>
                    <th>{lang('member_name')}</th>
                    <th>{lang('sponsor')}</th>
                    <th>{lang('level')}</th>
                    <th>{lang('joining_date')}</th>
                    <th>{lang('action')}</th>
                </tr>
            </thead>
            {if count($binary)>0}
                {assign var=i value="{$start}"}
                {assign var=class value=""}
                <tbody>
                    {foreach from=$binary item=v}
                        <tr>
                            <td>
                                <div class="table-avatar-details">
                                    <div>
                                        <img class="thumb-table" title="{if $v.active =='yes'} {lang('active')} {else} {lang('blocked')} {/if}" src="{profile_image_path($v.user_photo)}"/>
                                        
                                        {if $v.active =='yes'}
                                        <i class="on b-white bottom" style="background-color: #27c24c;"></i> 
                                        {else}
                                        <i class="on b-white bottom" style="background-color: red;"></i> 
                                        {/if}
                                    </div>
                                    <div>
                                        <div class="table-av-name">{$v.user_detail_name} {$v.user_detail_second_name}</div>
                                        <a class="table-av-package" href="{$BASE_URL}admin/profile/profile_view?user_name={$v.user_name}">{$v.user_name}</a>
                                    </div>                                    
                                </div>
                            </td>
                            <td>{$v.sponsor}</td>
                            <td>{$v.ref_level}</td>
                            <td>{$v.date_of_joining|date_format:"d M Y - h:i:s A"}</td>
                            <td>
                                <a title="{lang('view_sponsor')}" href="{$BASE_URL}admin/tree/sponsor_tree?user_name={$v.user_name}" target="_blank" class="btn btn-light-grey btn-xs text-black"><i class="fa fa-sitemap"></i></a>
                                </td>
                        </tr>
                    {/foreach}
                </tbody>
            {else}
                <tbody>
                    <tr><td colspan="9" align="center"><h4 align="center">{lang('no_records_found')}</h4></td></tr>
                </tbody>
            {/if}
        </table>
        </div>
        {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
        </div>
    
{/block}