{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
    {if $from_tree}
    <p class="text-right">
        <a href="{BASE_URL}/user/genology_tree" class="btn btn-sm btn-info btn-addon"><i class="fa fa-backward"></i>{lang('back')}</a>
    </p>
    {/if}
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">
                <div class="col-sm-12 col-md-6">
                    <div class="dr-members">
                    <div class="referral_count purple-light-bg"><div class="text-sm text-purple">{lang('total_downline_member_count')}</div> <div class="text-1x text-purple">{$total_downline_count}</div></div>
                    <div class="referral_count green-light-bg"><div class="text-sm text-green">{lang('total_level')}</div><div class="text-1x text-green">{$level_arr}</div></div>
                    </div>
                </div> 
                <div class="col-sm-12 col-md-6">   
                    {form_open_multipart('user/binary_history', 'role="form" class="" name="searchform" id="searchform" method="get"')}
                    <div class="errorHandler alert alert-danger no-display">
                        <i class="fa fa-times-sign"></i> {lang('errors_check')}
                    </div>
                    <div class="right-search260">
                        <div class="padding_both_small">
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
                        <div class="padding_both_small">
                            <div class="form-group m-b-n-xs">
                                <button type="submit" class="btn btn-sm btn-primary" id="user_details" value="{lang('search')}">{lang('search')}</button>
                                <a class="btn btn-sm btn-info" href="{$BASE_URL}user/binary_history">
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
                    {* <th></th> *}
                    <th>{lang('member_name')}</th>
                    {if in_array($MLM_PLAN, ['Binary', 'Matrix'])}
                        <th>{lang('placement')}</th>
                    {/if}
                    <th>{lang('sponsor')}</th>
                    <th>{lang('level')}</th>
                   {*  {if $MODULE_STATUS['product_status'] == 'yes' || $MODULE_STATUS['opencart_status'] == 'yes'}
                        <th>{lang('current_package')}</th>
                    {/if}
                    {if $MODULE_STATUS['rank_status'] == 'yes'}
                        <th>{lang('current_rank')}</th>
                    {/if} *}
                    <th>{lang('action')}</th>
                </tr>
            </thead>
            {if count($binary)>0}
                {assign var=i value="{$start}"}
                {assign var=class value=""}
                <tbody>
                    {foreach from=$binary item=v}
                        {$i=$i+1}
                        <tr>
                            {* <td>{$i}</td> *}
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
                                        <a class="table-av-package" href="">{$v.user_name}</a>
                                    </div>                                    
                                </div>
                            </td>
                            {if in_array($MLM_PLAN, ['Binary', 'Matrix'])}
                                <td>{$v.placement}</td>
                            {/if}
                            <td>{$v.sponsor}</td>
                            <td>{$v.ref_level}</td>
                            {* {if $MODULE_STATUS['product_status'] == 'yes' || $MODULE_STATUS['opencart_status'] == 'yes'}
                                <td>{$v.current_package|default:lang('na')}</td>
                            {/if}
                            {if $MODULE_STATUS['rank_status'] == 'yes'}
                                <td>{$v.current_rank|default:lang('na')}</td>
                            {/if} *}
                            <td>
                                <a title="{lang('view_genealogy')}" href="{$BASE_URL}user/tree/genology_tree?user_name={$v.user_name}" target="_blank" class="btn btn-light-grey btn-xs text-black"><i class="fa fa-sitemap"></i></a>
                                </td>
                        </tr>
                    {/foreach}
                </tbody>
            {else}
                <tbody>
                    <tr><td colspan="9" align="center"><h4 align="center">{lang('no_details_found')}</h4></td></tr>
                </tbody>
            {/if}
        </table>
        </div>
    {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
    </div>
{/block}