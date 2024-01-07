{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<style type="text/css">
    .right-search260 {
    grid-template-columns: 260px 135px auto;
}
</style>
    <div id="span_js_messages" style="display: none;">
        <span id="errmsg">{lang('You_must_enter_keyword_to_search')}</span>
        <span id="row_msg">{lang('rows')}</span>
        <span id="show_msg">{lang('shows')}</span>
    </div>
    <div class="panel panel-default">
        <div class="panel-body">
            <div class="row">

                <div class="col-sm-12 col-md-4">
                    <div class="dr-members">
                    <div class="referral_count purple-light-bg"><div class="text-sm text-purple">{lang('total_joinings')}</div> <div class="text-1x text-purple">{$total_joinings}</div></div>
                    <div class="referral_count green-light-bg"><div class="text-sm text-green">{lang('today_joinings')}</div><div class="text-1x text-green">{$today_joinings}</div></div>
                    </div>
                </div>

                <div class="col-sm-12 col-md-8">
                    {form_open('admin/search_member','role="form" method="get"')}
                    <div class="errorHandler alert alert-danger no-display">
                        <i class="fa fa-times-sign"></i> {lang('errors_check')}
                    </div>
                    <div class="right-search260">
                    <div class="padding_both_small">
                        <div class="form-group m-b-n-xs">
                            <label>{lang('keyword')}</label>
                            <input type="text" class="form-control user_autolist" placeholder="{lang('Username_Name_Address_MobileNo_Email')}.." name="keyword" id="keyword"autocomplete="Off" value="{$keyword|default:''}" >
                        </div>
                    </div>
                    <div class=" padding_both_small">
                        <div class="form-group m-b-n-xs">
                            <label>{lang('status')}</label>

                            <select name="status" id="status" class="form-control">
                            <option value="yes" {if $status=="yes"}selected{/if}>{lang('Active')}</option>
                            <option value="no"  {if $status=="no"}selected{/if}>{lang('blocked')}</option>
                        </select> 

                        </div>
                    </div>
                    <div class=" padding_both_small">
                        <div class="form-group m-b-n-xs">
                             <button class="btn btn-sm btn-primary" type="submit">
                            {lang('search')}
                        </button>
                        <a class="btn btn-info btn-sm" href="{base_url('admin/search_member')}">
                            {lang('reset')} 
                        </a>
                        </div>
                    </div>
                    </div>
                    {form_close()}
                    
                </div>

            </div>
            
        </div>
    </div>


    {if $MODULE_STATUS['opencart_status'] != 'yes' && ($MODULE_STATUS['package_upgrade'] == 'yes' || $MODULE_STATUS['subscription_status'] == 'yes')}
        <p class="text-right" style="margin-bottom: 24px">

            {if $MODULE_STATUS['package_upgrade'] == 'yes'}
            <a href="{$BASE_URL}admin/pending_upgrades" class="mr-3">
                <button type="button" class="btn btn-sm btn-primary pull-right btn-addon mr-3">
                    {lang('pending_upgrades')}
                </button>
            </a>
            {/if}
            {if $MODULE_STATUS['subscription_status'] == 'yes'}
            <a href="{$BASE_URL}admin/pending_subscription">
                <button type="button" class="btn btn-sm btn-primary pull-right btn-addon mr-3">
                    {lang('pending_subscription')}
                </button>
            </a>
            {/if}
        </p>
    {/if}

    <div class="panel panel-default">
        {form_open('admin/activate_block_member','role="form" class="smart-wizard" method="post"')}
        <div class="table-responsive">
            <table class="table table-striped m-b-none">
                <thead>
                    <tr>
                        <th>
                            <div class="checkbox">
                                <label class="i-checks">
                                    <input type="checkbox" class="select-checkbox-all">
                                    <i></i>
                                </label>
                            </div>
                        </th>
                        <th>{lang('member_name')}</th>
                        <th>{lang('sponsor')}</th>
                       {*  {if $MODULE_STATUS['product_status'] == 'yes'}
                            <th>{lang('current_package')}</th>
                        {/if}
                        {if $MODULE_STATUS['rank_status'] == 'yes'}
                            <th>{lang('current_rank')}</th>
                        {/if} *}
                        <th>{lang('email')}</th>
                        <th>{lang('mobile')}</th>
                        <th>{lang('date_of_joining')}</th>
                    </tr>
                </thead>
                <tbody>
                    {if !empty($members)}
                        {foreach from=$members item=$member key=key name=name}
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label class="i-checks">
                                            <input type="checkbox" name="member_id[]" class="payout-checkbox select-checkbox-single" value="{$member.id}">
                                            <i></i>
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    <div class="table-avatar-details">
                                    <div>
                                        <img class="thumb-table" title="{if $member.active =='yes'} {lang('active')} {else} {lang('blocked')} {/if}" src="{profile_image_path($member.user_photo)}"/>
                                        {if $member.active =='yes'}
                                        <i class="on b-white bottom" style="background-color: #27c24c;"></i> 
                                        {else}
                                        <i class="on b-white bottom" style="background-color: red;"></i> 
                                        {/if}
                                    </div>
                                    <div>
                                        <div class="table-av-name">{$member.full_name}</div>
                                        <a class="table-av-package" href="{$BASE_URL}admin/profile/profile_view?user_name={$member.user_name}">{$member.user_name}</a>
                                    </div>                                    
                                </div>
                                {* {user_with_name($member.user_name, $member.full_name, true)} *}</td>
                                <td>{$member.sponsor_name}</td>
                                
                                {* {if $MODULE_STATUS['product_status'] == 'yes'}
                                    <td>{$member.product_name}</td>
                                {/if}
                                {if $MODULE_STATUS['rank_status'] == 'yes'}
                                    <td>
                                        {$member.rank_name|default:lang('na')}
                                    </td>
                                {/if} *}
                                <td>{$member.user_detail_email}</td>
                                <td>{$member.user_detail_mobile}</td>
                                <td>{date("d F Y", strtotime($member.date_of_joining))}</td>
                            </tr>        
                        {/foreach}    
                    {else}
                        <tr>
                            <td colspan="8">
                                <h4 class="text-center">{lang('no_records_found')}</h4>
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>
        </div>
        {if !empty($members)}
            <div class="panel-footer">
                {if $status == "yes"}
                    <button class="btn btn-primary" type="submit" name="action" value="block_member">{lang('block')}</button>
                {elseif $status == "no"}
                    <button class="btn btn-primary" type="submit" name="action" value="activate_member" >{lang('activate')}</button>
                {/if}
                {$ci->pagination->create_links()}
            </div>
        {/if}
        {form_close()}
    </div>
{/block} 
{block name=script}
    <script>
        jQuery(document).ready(function () {
            ValidateMember.init();
            highlightSearchKey('{$search_key}');
        });
    </script>
{/block} 