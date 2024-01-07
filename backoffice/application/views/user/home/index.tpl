{extends file=$BASE_TEMPLATE}
{block name=script}
{$smarty.block.parent}
<script src="{$PUBLIC_URL}plugins/clipboard.min.js" type="text/javascript"></script>
<script src="{$PUBLIC_URL}javascript/chart/chart.min.js"></script>
<script src="{$PUBLIC_URL}javascript/ajax-dynamic-dashboard-user.js" type="text/javascript"></script>
<script src="{$PUBLIC_URL}javascript/user_dashboard.js"></script>
<script src="{$PUBLIC_URL}javascript/user_dashboard.js"></script>
<link rel="stylesheet" href="{$PUBLIC_URL}css/owl.carousel.min.css"/>
<link rel="stylesheet" href="{$PUBLIC_URL}theme/css/dashboard_profile.css"/>
<script src="{$PUBLIC_URL}javascript/owl.carousel.js"></script>
<script>
  country_map_data = {$map_data};
</script>
<style type="text/css">
    .extra_data_new{
        font-size: 20px;
    }
    .new-members.panel {
    min-height: 314px;
}
    .user-det > div .sponsor-details:nth-child(1) {
    padding-left: 20px;
}
    .renew_upgrade:hover,.renew_upgrade:active ,.renew_upgrade:focus .view_user:hover {
    background: #55489c;
    color: #fff;
}
    .mr-20 {
    margin-right:20px
    }

    .pl-2 {
    padding-left:5px
    }

    .vertical_line
    {
        border-right: 1px dotted #bfbfbf;
    }
.rela-blog-cnt-title {
    font-size: 15px;
    text-align: center;
    background: #e4e4e433;
    padding: 10px;
    height: 52px;
    margin: auto 0;
    overflow: hidden;
    font-weight: 600;
}
.panel .h1.font-thin.h1
{
    font-weight: 400;
    color: #6c787f;
}

.rela-blog-img {
    border-radius: 0%;
    }
    .news_content
    {
        height: 80px;
        overflow: hidden;
    }
    .read_full_news
    {
        text-align: center;
        color: #857cc3;
    }
    .read_full_news a:hover, a:focus {
    color: #717d84;
    text-decoration: none;
    }
    .rela-blog-cnt a.read_full_news {
    float: right;
    border: 1px solid #857cc3;
    border-radius: 15px;
    padding: 2px 8px;
    font-size: 11px;
    }
    .padding_bottom_zero
    {
        padding-bottom:0 !important; 
    }
    .padding_top_zero
    {
        padding-top: 0 !important;
    }
    .news-carousel .item
    {
       border: 1px solid #eee;
       padding: 6px 4px;
    }
    .rela-blog-img
    {
        border: unset;
    }



.view_user {
border-radius:2px
}


@media screen and (max-width: 1199px) {
    .col-md-12
    {
        float: unset;
    }
    .box::after, .box1::after, .box2::after, .box3::after {
    width: 141px;
    height: 112px;
    top: -23%;
    left: -22%;
    }
    .box::before, .box1::before, .box2::before, .box3::before {
    top: -43%;
    left: -8%;
    }
    
}
@media screen and (max-width: 800px)
{
    .box::after, .box1::after, .box2::after, .box3::after {
        width: 141px;
        height: 112px;
        top: -27%;
        left: -40%
    }
    .box::before, .box1::before, .box2::before, .box3::before {
    top: -47%;
    left: -18%;
    }
}
   
 </style>
{/block}

{block name=$CONTENT_BLOCK}
  <style>
    .hidden {
      display: none;
    }
  </style>
  <div id="span_js_messages" style="display: none;"> 
    <span id="left_join">{lang('left_join')}</span> 
    <span id="right_join">{lang('right_join')}</span> 
    <span id="join">{lang('joinings')}</span> 
    <span id="confirm_msg">{lang('are_you_sure_want_delete')}</span> 
  </div>
  <input name="mlm_plan" id="mlm_plan" type="hidden" value="{$MLM_PLAN}" />

  <div class="row">
        <div class="col-lg-5 col-md-12 padding-zero">
            <div class="col-lg-12 col-md-12" id="section_tile">
                        <div class="row row-sm text-center">
                             
                            <!-- Ewallet -->
                            {if $dashboardConfig["ewallet"] == 1 }
                            <div class="col-xs-12 col-sm-6  col-lg-6" id="section_tile1">
                                <div class="box">
                                <a href="{base_url('user/ewallet')}">
                                    <div class="panel shadow padder-v item lg-panel">
                                        <span class="text-muted">{lang('e_wallet')}</span>
                                        <div class="h1  font-thin h1"> {$total_amount}</div>
                                    </div>
                                </a>
                                </div>
                            </div>
                            {/if}
                            <!-- Ewallet end -->
         
                            <!-- Commission earned -->
                            {if $dashboardConfig["commission_earned"] == 1 }
                            <div class="col-xs-12 col-sm-6  col-lg-6 " id="section_tile1">
                                <div class="box1">
                                <div class="tile-dropdown">
                                    <div class="dropdown">
                                        <div data-toggle="dropdown" aria-expanded="false"> <i class="fa fa-filter text-info" aria-hidden="true"></i>
                                         </div>
                                        <ul class="dropdown-menu dropdown-menu_right" id="commission_dash">
                                            <li class="active"><a href="javascript:void(0);" id="all_commission">{lang('all')}</a></li>
                                            <li><a href="javascript:void(0);" id="yearly_commission"> {lang('this_year')}</a></li>
                                            <li><a href="javascript:void(0);" id="monthly_commission"> {lang('this_month')}</a></li>
                                            <li><a href="javascript:void(0);" id="weekly_commission">{lang('this_week')}</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <a href="{base_url('user/ewallet')}">
                                    <div class="panel shadow padder-v item lg-panel">
                                         <span class="text-muted">{lang('commision_earned')}</span>
                                        <div class="h1  font-thin h1" id="total_commission">
                                            {format_currency($total_commission)}
                                        </div>
                                       
                                    </div>
                                </a>
                                </div>
                            </div>
                            {/if}
                            <!-- Commission earned end -->
         
                            <!-- Payout released -->
                            {if $dashboardConfig["payout_released"] == 1 }
                            <div class="col-xs-12 col-sm-6  col-lg-6" id="section_tile3">
                                <div class="box2">
                                <div class="tile-dropdown">
                                    <div class="dropdown">
                                        <div data-toggle="dropdown" aria-expanded="false"> <i class="fa fa-filter text-info" aria-hidden="true"></i></div>
                                        <ul class="dropdown-menu dropdown-menu_right" id="payout_dash">
                                            <li class="active"><a href="javascript:void(0);" id="all_payout">{lang('all')}</a></li>
                                            <li><a href="javascript:void(0);" id="yearly_payout">{lang('this_year')}</a></li>
                                            <li><a href="javascript:void(0);" id="monthly_payout">{lang('this_month')}</a></li>
                                            <li><a href="javascript:void(0);" id="weekly_payout"> {lang('this_week')}</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <a href="{base_url('user/payout')}">
                                    <div class="panel shadow padder-v item lg-panel">
                                         <span class="text-muted">{lang('payout_released')}</span>
                                        <div class=" font-thin h1 block1" id="total_payout"> {$total_payout} </div>
                                       
                                    </div>
                                </a>
                                </div>
                            </div>
                            {/if}
                            <!-- Payout released end -->
         
                            <!-- Payout pending -->
                            {if $dashboardConfig["payout_pending"] == 1 }
                            <div class="col-xs-12 col-sm-6 col-lg-6" id="section_tile4">
                                <div class="box3">
                                <a href="{base_url('user/payout')}">
                                    <div class="panel shadow padder-v item lg-panel">
                                         <span class="text-muted">{lang('payout_pending')}</span>
                                        <div class="font-thin  h1" id="mail_total">{format_currency($payout_pending)}</div>
                                       
                                    </div>
                                </a>
                                </div>
                            </div>
                            {/if}
                            <!-- Payout pending end -->
                           
                            {if $MLM_PLAN == 'Donation' && $donation_type == 'manuel'}
                            <div class="col-xs-12 m-b-md">
                                <div class="r bg-light dker item hbox no-border">
                                    <div class="col w-xs v-middle hidden-md">
                                        <div class="sparkline inline">
                                            <button class="btn btn-sm btn-primary" type="submit">
                                                <a class="btn1 btn-4 btn-4a icon-arrow" href="{$BASE_URL}user/donation/donation_view">{lang('donate')}</a>
                                            </button>
                                        </div>
                                    </div>
                                    <div class="col dk padder-v r-r">
                                        <div class="text-primary-dk font-thin h1"><span>{$donation_level}</span></div>
                                        <span class="text-muted text-xs">{lang('your_current_status')}</span>
                                    </div>
                                </div>
                            </div>
                            {/if}
                            <!--END DONATION TILE-->
                            
                        </div>
                    </div>
        </div>
        <div class="col-lg-7 col-md-12">
         <div class="graph-profile-grid padding-zero">
            <!-- Profile/Promotion -->
            {if $dashboardConfig["profile"] == 1 }
            <div class="panel item">
                
                <div class="userprof-top profile_user" >
                    
                    <div class="panel-body profile-section">
                        {if $dashboardConfig["profile"] == 1 }
                        <div class="dashbord-profile">
                            <div class="profile-avatar">
                                <img class="" src="{$SITE_URL}/uploads/images/profile_picture/{$profile_photo}">
                                <h3 class="profile-name full_name" title="{$user_details['full_name']}" >
                               {if strlen($user_details['full_name']) > 14}
                               {$user_details['full_name']|truncate:19}
                               {else}
                               {$user_details['full_name']}
                               {/if}
                               </h3>
                                <h5 class="profile-name2 user_name2">{$LOG_USER_NAME}</h5>
                                {if $MODULE_STATUS['rank_status'] == 'yes' }
                                {if empty($crank)}
                                {assign var="crank" value="NA"}
                                {/if}
                                <h6 class="rankPro">{lang('rank')} :<span class="orange" style="color: {$rank_color}"> {$crank}</span></h6>
                                {/if}
                                <a href="{BASE_URL}/user/profile/profile_view" class="profile-edit c-profile-edit view_profile btn-info"  >{lang('view_profile')}</a>
                            </div>
                        </div>
                        {/if}
                    </div>
                    
                    
                    <!-- Start Promotion Tools html  -->
                    <div class="Promotion-Tools" >
                        <div class="pakage-renewal">
                            
                            {if $product_status == "yes"}
                            <div class="pakage">
                                <p class="fontH-small">{lang('current_package')}</p>
                                {if empty($product_name)}
                                {assign var="product_name" value="NA"}
                                {/if}
                                <p class="fontH-medium"><span class="black_text">{$product_name}</span></p>
                                {if $MODULE_STATUS['package_upgrade'] == "yes"}
                                {if !empty($upgradable_package_list)}
                                <div class="pakage_button m-t-xs">
                                <a  href="{BASE_URL}/package_upgrade" class="profile-edit c-profile-edit renew_upgrade" >{lang('upgrade')}</a>
                                </div>
                              {/if}
                              {/if}
                              {if $board_status=='no'}
                                <div class="pakage_button m-t-xs">
                                {form_open('register_board','class="" role="form" id="form"')}
                                {if $down_count >1 }
                                  <div class="form-group">
                                  <label class="control-label required" for="tourism_system">{lang('choose_user_for_gifting')}</label>
                    
                                    <select class="form-control" name="gift" id="gift">
                                    {foreach from=$down_board item=j}
                                      <option  value="{$j['id']}">{$j['username']}</option>
                                    {/foreach}
                                      
                                  </select>
                                  </div>

                                {/if}
                                  <div class="form-group">
                                    <button type="submit" class="btn btn-info">{lang('register_board')}</button>
                                  </div>
                                {form_close()}
                                </div>
                              {/if}
                            </div>
                            {/if}
                            {if $MODULE_STATUS['subscription_status'] == 'yes'}
                            <div class="renewal">
                                <div class="expiry">
                                    <p class="fontH-small">{lang('membership_will_expire')}</p>
                                    <p class="fontH-medium">
                                        <span class="black_text">
                                            {if $product_validity != 'NA'}
                                                {date("d F Y - h:i:s A", strtotime($product_validity))}
                                            {else}
                                                NA
                                            {/if}
                                        </span>
                                    </p>
                                </div>
                                <div class="renewal_button m-t-xs">
                                    
                                  {if $MODULE_STATUS['subscription_status'] == "yes"}
                                  {if $MODULE_STATUS['opencart_status'] == "yes"}

                                    
                                        <a href="{SITE_URL}/store/index.php?route=renewal/renewal{$store_id}" class="profile-edit c-profile-edit renew_upgrade" >{lang('renew_membership')}</a>
                                  {else}
                                        <a href="{BASE_URL}/user/upgrade_package_validity" class="profile-edit c-profile-edit renew_upgrade" >{lang('renew_membership')}</a>
                                  {/if}
                                  {/if}
                                </div>
                            </div>
                            {/if}
                        </div> <!--Close pakage-renewal  -->
                            <div class="clearfix"></div>
                            <div class="col-md-12 bb bbreplica_lead"></div>
                            <div class="social-promotion df">
                                <div class="replica_lead mt-10">
                                    {if $MODULE_STATUS['replicated_site_status'] == "yes"}
                                     <div class="">
                                         <form autocomplete="off">
                                             <div class="">
                                                 <div class="link-replica link-replica2 coply-link" >
                                                     {* <button type="button" class="btn btn-sm btn-info copy-text" data-clipboard-text="" id="copy_link_lcp" ><i class="fa fa-link"></i></button> *}
                                                    
                                                     {if DEMO_STATUS == "yes"}
                                                     
                                                     <input type="hidden" readonly="" class="form-control link" id="" maxlength="" value="{$site_url}/replica/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}" >
                                                     <div class="promotion-social social_iocns">
                                                         <div class="col-lg-12 p-0 black_text">{lang('replica_link')}</div>
                                                         <button type="button" class="rpl-social icons_bg" data-clipboard-text="{$site_url}/replica/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}" id="copy_link_replica" style="">
                                                             <i class="fa fa-files-o" aria-hidden="true" ></i>
                                                         </button>
                                                         <button type="button" class="rpl-social icons_bg" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/replica/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}');" style="min-width: 28px;">
                                                             <i class="fa fa-facebook" aria-hidden="true"  ></i></button>
                                                         <button type="button" class="rpl-social icons_bg" onClick="twittershare('{$site_url}/replica/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}');" >
                                                             <i class="fa fa-twitter" aria-hidden="true" ></i>
                                                         </button>
                                                         <a href="http://www.linkedin.com/shareArticle?url={$site_url}/replica/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}" target="_blank">
                                                         <button type="button" class="rpl-social icons_bg" onclick="" >
                                                             <i class="fa fa-linkedin" aria-hidden="true" ></i>
                                                         </button>
                                                       </a>
                                                     </div>
                                                     {else}
                                                     <input type="hidden" readonly="" class="form-control link" id="" maxlength="" value="{$site_url}/replica/{$LOG_USER_NAME}" >
                                                     <div class="promotion-social social_iocns" >
                                                          <div class="col-lg-12 black_text">{lang('replica_link')}
                                                          </div>
                                                         <button type="button" class="rpl-social icons_bg" data-clipboard-text="{$site_url}/replica/{$LOG_USER_NAME}" id="copy_link_replica" >
                                                             <i class="fa fa-files-o" aria-hidden="true" ></i>
                                                         </button>
                                                         <button type="button" class="rpl-social icons_bg" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/replica/{$LOG_USER_NAME}');" style="min-width: 28px;">
                                                             <i class="fa fa-facebook" aria-hidden="true"  ></i></button>
                                                         <button type="button" class="rpl-social icons_bg" onClick="twittershare('{$site_url}/replica/{$LOG_USER_NAME}');" >
                                                             <i class="fa fa-twitter" aria-hidden="true" ></i>
                                                         </button>
                                                         <a href="http://www.linkedin.com/shareArticle?url={$site_url}/replica/{$LOG_USER_NAME}" target="_blank">
                                                         <button type="button" class="rpl-social icons_bg" onclick="" >
                                                             <i class="fa fa-linkedin" aria-hidden="true" ></i>
                                                         </button>
                                                       </a>
                                                        {if $check_package_support_services=='yes'}<a href="{$simply_url}" target="_blank">
                                                         <button type="button" class="rpl-social icons_bg" onclick="" >
                                                             <i class="" aria-hidden="true" > S </i>
                                                         </button>
                                                       </a>{/if}
                                                       {if $check_package_support_board=='yes'}
                                                        <a href ="http://mlm.magicalcodeco.com" target ="_blank"><button type="button" class="rpl-social icons_bg" onclick="" >
                                                             <i class="" aria-hidden="true" > B </i>
                                                         </button></a>
                                                       {/if}
                                                       {if $check_package_support_tourism=='yes'}
                                                        <button type="button" class="rpl-social icons_bg" onclick="" >
                                                             <i class="" aria-hidden="true" > T </i>
                                                         </button>
                                                       {/if}
                                                     </div>
                                                     {/if}
     
                                                 </div>
                                                 <div>
                                                 </div>
                                             </div>
                                             <div></div>
                                         </form>
                                     </div>
                                     {/if} 
                                      {if $MODULE_STATUS['lead_capture_status'] == "yes"}
                                    <div class="">
                                         <form autocomplete="off">
                                             <div class="">
                                                 <div class="link-replica link-replica2 coply-link" >
                                                    {*  <button type="button" class="btn btn-sm btn-info copy-text" data-clipboard-text="" id="copy_link_lcp" ><i class="fa fa-link"></i></button> *}

                                                     
                                                     {if DEMO_STATUS == "yes"}
                                                     
                                                     <input type="hidden" readonly="" class="form-control link" id="" maxlength="" value="{$site_url}/lcp/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}" >
                                                     <div class="promotion-social social_iocns">
                                                          <div class="col-lg-12 p-0 black_text">{lang('lead_capture')}
                                                          </div>
                                                         <button type="button" class="rpl-social icons_bg" data-clipboard-text="{$site_url}/lcp/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}" id="copy_link_replica" style="">
                                                             <i class="fa fa-files-o" aria-hidden="true" ></i>
                                                         </button>
                                                         <button type="button" class="rpl-social icons_bg" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/lcp/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}');" style="min-width: 28px;">
                                                             <i class="fa fa-facebook" aria-hidden="true"  ></i></button>
                                                         <button type="button" class="rpl-social icons_bg" onClick="twittershare('{$site_url}/lcp/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}');" >
                                                             <i class="fa fa-twitter" aria-hidden="true" ></i>
                                                         </button>
                                                         <a href="http://www.linkedin.com/shareArticle?url={$site_url}/lcp/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}" target="_blank">
                                                         <button type="button" class="rpl-social icons_bg" onclick="" >
                                                             <i class="fa fa-linkedin" aria-hidden="true" ></i>
                                                         </button>
                                                       </a>
                                                     </div>
                                                     {else}
                                                     <input type="hidden" readonly="" class="form-control link" id="" maxlength="" value="{$site_url}/lcp/{$LOG_USER_NAME}" >
                                                     <div class="promotion-social social_iocns" >
                                                          <div class="col-lg-12 black_text">{lang('lead_capture')}
                                                          </div>
                                                         <button type="button" class="rpl-social icons_bg" data-clipboard-text="{$site_url}/lcp/{$LOG_USER_NAME}" id="copy_link_replica" >
                                                             <i class="fa fa-files-o" aria-hidden="true" ></i>
                                                         </button>
                                                         <button type="button" class="rpl-social icons_bg" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/lcp/{$LOG_USER_NAME}');" style="min-width: 28px;">
                                                             <i class="fa fa-facebook" aria-hidden="true"  ></i></button>
                                                         <button type="button" class="rpl-social icons_bg" onClick="twittershare('{$site_url}/lcp/{$LOG_USER_NAME}');" >
                                                             <i class="fa fa-twitter" aria-hidden="true" ></i>
                                                         </button>
                                                         <a href="http://www.linkedin.com/shareArticle?url={$site_url}/lcp/{$LOG_USER_NAME}" target="_blank">
                                                         <button type="button" class="rpl-social icons_bg" onclick="" >
                                                             <i class="fa fa-linkedin" aria-hidden="true" ></i>
                                                         </button>
                                                       </a>
                                                     </div>
                                                     {/if}
     
                                                 </div>
                                                 <div>
                                                 </div>
                                             </div>
                                             <div></div>
                                         </form>
                                     </div>
                                     {/if}
                                 </div>
                            </div>

                        
{* pv history *}
            {* <div class="pakage_button m-t-xs">
                                <a  href="{BASE_URL}/user/select_report/pv_report" class="profile-edit c-profile-edit renew_upgrade" >{lang('pv_history')}</a>
            </div> *}
                        
                        <div class="row mob-center">
                            <div class="panel padder-v item padding_top_zero padding_bottom_zero">
                                
                                
                                
                                
                                



                                



                            </div>

                           
                        </div>
                    
                        
                    </div>
                    <!-- Close Promotion Tools html -->
                </div>
                
            </div>
            {/if}
            <!-- Profile/Promotion end -->
            
        </div>
        </div>
    
    
        <div class="col-lg-12 ">
            <div class="panel-body panel">
                <!-- referal-link -->
                <!-- Lead Capture-link -->
                <div class="user-det">
                    <div class=" grid {if isset($binary_tree_carry)}grid_four{else} grid-four{/if} {if $MLM_PLAN != 'Binary'} grid_three{/if} ">
                      {if $is_app}
                        {if isset($profile_extra_data)}
                            
                           {if $dashboardConfig["pv"] == 1 }
                            <div class="sponsor-details vertical_line">
                              <div class="sponsor-details-icon"><i class="fa fa-user" aria-hidden="true"></i></div>
                                <span style="padding-left: 20px;" class="extra_data_title">
                                {lang('personal_pv')}</span>
                                <div class="extra_data">{$profile_extra_data.personal_pv }
                                </div>
                                
                            </div>
                            <div class="sponsor-details vertical_line">
                              <div class="sponsor-details-icon"><i class="fa fa-users" aria-hidden="true"></i></div>
                              <span class="extra_data_title">
                                {lang('group_pv')}</span>
                                <div class="extra_data">{$profile_extra_data.group_pv}</div>
                                    
                            </div>
                            {/if} 
                            {if isset($binary_tree_carry)}
                                <div class="sponsor-details vertical_line">
                                  <div class="sponsor-details-icon"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></div>
                                  <span class="extra_data_title">
                                    {lang('total_left_carry')}
                                </span>
                                    <div class="extra_data">{$binary_tree_carry.total_left_carry}
                                    </div>
                                    
                                </div> 
                                <div class="sponsor-details vertical_line">
                                  <div class="sponsor-details-icon"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></div>
                                  <span class="extra_data_title">
                                    {lang('total_right_carry')}
                                </span>
                                    <div class="extra_data">{$binary_tree_carry.total_right_carry}
                                    </div>
                                    
                                </div> 
                            {/if}
                        {/if}
                    {/if}
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-12 ">
        <div class="panel-body panel">
            <!-- referal-link -->
            <!-- Lead Capture-link -->
            <div class="user-det">
                <div class=" grid {if isset($binary_tree_carry)}grid_four{else} grid-four{/if} {if $MLM_PLAN != 'Binary'} grid_three{/if} ">
                   {if $is_app}
                        <div class="sponsor-details vertical_line">
                          <div class="sponsor-details-icon"><i class="fa fa-step-forward" aria-hidden="true"></i></div>
                            <span style="padding-left: 20px;" class="extra_data_title">{lang('steps')}</span>
                            <div class="extra_data_new" style="color:#58666e;padding-left: 20px;">{lang('today_steps')} : {$today_steps }</div>
                            <div class="extra_data_new" style="color:#58666e;padding-left: 20px;">{lang('monthly_steps')} : {$monthly_steps }</div>
                            
                        </div>
                        <div class="sponsor-details vertical_line">
                          <div class="sponsor-details-icon"><i class="fa fa-briefcase" aria-hidden="true"></i></div>
                            <span class="extra_data_title">{lang('R-wallet')}</span>
                            <div class="extra_data_new">{$rwallet_steps }</div>
                            
                        </div>
                        <div class="sponsor-details vertical_line">
                          <div class="sponsor-details-icon"><i class="fa fa-users" aria-hidden="true"></i></div>
                          <span class="extra_data_title">{lang('cumulative_count')}</span>
                            <div class="extra_data_new">{$cumulativecount}</div>
                                
                        </div>
                        <div class="sponsor-details vertical_line">
                          <div class="sponsor-details-icon"><i class="fa fa-user-circle" aria-hidden="true"></i></div>
                          <span class="extra_data_title">{lang('user_status')}</span>
                          <div class="extra_data_new">{lang($user_status)}</div>
                            
                        </div> 
                        
                    {/if}
                </div>
            </div>
        </div>
    </div>
     <div class="col-lg-12 ">
            <div class="panel-body panel">
                <!-- referal-link -->
                <!-- Lead Capture-link -->
                <div class="user-det">
                    <div class=" grid grid_four">
                      {if $is_app}
                        {if isset($profile_extra_data)}
                            <div class="sponsor-details vertical_line" >
                                <span class="extra_data_title">
                                {lang('sponsor_name')}</span>
                                <div class="extra_data">{$profile_extra_data.sponsor_name}
                                </div>
                                
                            </div>
                                <div class="sponsor-details vertical_line">
                                  <div class="sponsor-details-icon"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></div>
                                  <span class="extra_data_title">
                                    {lang('travel_voucher')}
                                </span>
                                    <div class="extra_data">{$travel_voucher}
                                    </div>
                                    
                                </div> 
                                <div class="sponsor-details vertical_line">
                          <div class="sponsor-details-icon"><i class="fa fa-check" aria-hidden="true"></i></div>
                          <span class="extra_data_title">{lang('kyc_status')}</span>
                            {* <div class="renewal_button m-t-xs"> *}
                              {if $kyc_status == "no"}
                                <div class="extra_data_new">{lang(not_verified)}</div>
                                <a href="{BASE_URL}/user/kyc_upload" class="profile-edit c-profile-edit renew_upgrade" >{lang('update_kyc')}</a>
                              {else}
                                 <div class="extra_data_new">{lang(verified)}</div>
                              {/if}
                            {* </div> *}
                            
                        </div> 
                          <div class="sponsor-details vertical_line">
                                  <div class="sponsor-details-icon"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></div>
                                  <span class="extra_data_title">
                                    {lang('maxout_days_counter')}
                                </span>
                                    <div class="extra_data">{$max_out_count}
                                    </div>
                                    
                                </div>   
                        {/if}
                    {/if}
                </div>
            </div>
        </div>
    </div>

</div>

            
<div class="col">   
<div class="row">   
    <div class="col-lg-5 col-md-12" id="section_tile">  
        {block name="new_members_panel"}    
            {if $dashboardConfig["new_members"] == 1}
            <div class="new-members panel"> 
                <h4>{lang('new_members')}</h4>  
                {if !empty($latest_joinees)}
                <div ui-jq="slimScroll">
                    
                    <ul class="list-group list-group-lg no-bg auto">    
                        {foreach from=$latest_joinees item=j}
                                <li class="list-group-item clearfix no-shadows">    
                                    <div class="col-lg-12 padding-15">  
                                        <div class="col-lg-2 col-xs-2 padding-zero ">   
                                            <span class="thumb-sm avatar "> 
                                            <img src="{$j['profile_picture_full']}">    
                                            <i class="on b-white bottom"></i>   
                                            </span> 
                                        </div>  
                                        <div class="col-lg-10 col-xs-10 padding-zero">  
                                            <div class="col-lg-8 pull-left">    
                                                <div class="member-full-name">{$j['user_full_name']}</div>  
                                                <span class="member-user-name" >{$j['user_name']}</span>
                                            </div>  
                                            <div class="col-lg-4 pull-right padding-zero">  
                                                <div class="member-package">    
                                                    {if $MODULE_STATUS['product_status'] == "yes"}
                                                        {format_currency($j['product_amount'])} 
                                                    {/if}   
                                                    <small class="text-msuted clear text-ellipsis"  style="font-weight: 300;">{$j['date_of_joining']}</small>   
                                                </div>  
                                            </div>  
                                        </div>  
                                    </div>  
                                </li>   
                        {/foreach}  
                    </ul>
                </div>
                {else}
                 <div class="text-center text-primary-lt" ><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">
                {lang('no_data_found')}</h3>
                </div>
                {/if}
            </div>
            {/if}
        {/block}    
    </div>  
 
            

  
      {* Left column end *}
      <!-- Joinings -->
      {if $dashboardConfig["member_joinings"] == 1 }
      <div class="col-lg-7 col-md-12">
        <div class="panel hbox hbox-auto-xs no-border member_joinings-graph">
          <div class="col wrapper ">
            <div class="dropdown pull-right">
              <div data-toggle="dropdown" aria-expanded="false"><i class="fa fa-filter text-info" aria-hidden="true"></i></div>
              <ul class="dropdown-menu dropdown-menu_right" id="joinings_graph">
                <li class=""><a id="yearly_joining_graph" href="javascript:void(0);">{lang('year')}</a></li>
                <li class="active"><a id="monthly_joining_graph" href="javascript:void(0);"> {lang('month')}</a></li>
                <li><a id="daily_joining_graph" href="javascript:void(0);"> {lang('day')}</a></li>
              </ul>
            </div>
            <h4 class="font-thin m-t-none m-b-none">{lang('joinings')}</h4>
            <span class="m-b block text-sm text-muted"></span>
            <canvas id="join_chart" style="height: 263px; width: 100%"></canvas>
          </div>
        </div>
      </div> 
      {/if}
      <!-- Joinings end -->
      {* Rtight graph column end *}
    </div> {* First Row end *}

   
    <!-- Rank Div New -->
{if $MODULE_STATUS['rank_status'] == "yes"  &&$rank_configuration['joinee_package'] != 1 }
{if $dashboardConfig["rank_details"] == 1 }
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="panel wrapper">
              {if empty($crank) || empty($nrank)}
                {assign var="next_column_class" value=""}
              {else}
                {assign var="next_column_class" value="two-col"}
              {/if}
            
            <div class="current-next-new {$next_column_class}">
                <div class="current-new">
                  {if !empty($crank)}
                    <h4 class="m-t-none m-b text-primary-lt font-thin-bold">{lang('current_rank')} - <span class="text-dark">{$crank}</span></h4>{/if}
                    
                    <div class="current-new-section">
                      {foreach from=$current_rank item=$rank key=$key}
                        {if $key != 'downline_package_count' && $key != 'downline_rank' && $key != 'criteria'}
                        <div class="current-new-container">
                            <div class="CN-one"><div ui-jq="easyPieChart" ui-options="{
                                  percent: {round($rank['percentage'], 1)},
                                  lineWidth: 6,
                                  trackColor: '#e8eff0',
                                  barColor: '#23b7e5',
                                  scaleColor: false,
                                  color: '#fff',
                                  size: 55,
                                  lineCap: 'butt',
                                  rotate: 55,
                                  animate: 1000
                                  }">
                                    <div>
                                      <span class="step">{round($rank['percentage'], 1)}</span>%
                                    </div>
                            </div></div> <!-- CN one -->
                            <div class="CN-two">{lang($key)}</div><!-- CN two -->
                            <div class="CN-three"><div><h4 class="text-info">{$rank['required']}</h4>
                              <h5>{lang('required')}</h5></div></div><!-- CN three -->
                            <div class="CN-four"><div><h4 class="text-success">{$rank['achieved']}</h4>
                              <h5>{lang('achieved')}</h5></div></div><!-- CN four -->
                        </div>
                        {else if $key != 'criteria'}
                          {foreach from=$rank  item=$r key=$key2}
                            <div class="current-new-container">
                            <div class="CN-one"><div ui-jq="easyPieChart" ui-options="{
                                  percent: {round($r['percentage'], 1)},
                                  lineWidth: 6,
                                  trackColor: '#e8eff0',
                                  barColor: '#23b7e5',
                                  scaleColor: false,
                                  color: '#fff',
                                  size: 55,
                                  lineCap: 'butt',
                                  rotate: 55,
                                  animate: 1000
                                  }">
                                    <div>
                                      <span class="step">{round($r['percentage'], 1)}</span>%
                                    </div>
                            </div></div> <!-- CN one -->
                            <div class="CN-two">{lang($key)}
                            {if $key == 'downline_package_count'}
                                 {if $MODULE_STATUS['opencart_status'] == "yes"}
                                  <h5 class="text-primary-lt">{$r['model']}</h5>
                                  {else}
                                  <h5 class="text-primary-lt">{$r['product_name']}</h5>
                                 {/if}
                                {else}
                                  <h5 class="text-primary-lt">{$r['rank_name']}</h5>
                                {/if}<!-- CN two -->
                            </div>
                            <div class="CN-three"><div><h4 class="text-info">{$r.required}</h4>
                              <h5>Required</h5></div></div><!-- CN three -->
                            <div class="CN-four"><div><h4 class="text-success">{$r.achieved}</h4>
                              <h5>Achieved</h5></div></div><!-- CN four -->
                        </div>
                          {/foreach}
                        {/if}
                        {/foreach}
                    </div>
                </div>
                <div class="next-new">{if !empty($nrank)}
                    <h4 class="m-t-none m-b text-primary-lt font-thin-bold">Next Rank - <span class="text-dark">{$nrank}</span></h4>{/if}
                    <div class="current-new-section">
                      {foreach from=$next_rank item=$nrank key=$key}
                        {if $key != 'downline_package_count' && $key != 'downline_rank' && $key != 'criteria'}
                        <div class="current-new-container">
                            <div class="CN-one"><div ui-jq="easyPieChart" ui-options="{
                                  percent: {round($nrank['percentage'], 1)},
                                  lineWidth: 6,
                                  trackColor: '#e8eff0',
                                  barColor: '#23b7e5',
                                  scaleColor: false,
                                  color: '#fff',
                                  size: 55,
                                  lineCap: 'butt',
                                  rotate: 55,
                                  animate: 1000
                                  }">
                                    <div>
                                      <span class="step">{round($nrank['percentage'], 1)}</span>%
                                    </div>
                            </div></div> <!-- CN one -->
                            <div class="CN-two">{lang($key)}</div><!-- CN two -->
                            <div class="CN-three"><div><h4 class="text-info">{$nrank['required']}</h4>
                              <h5>Required</h5></div></div><!-- CN three -->
                            <div class="CN-four"><div><h4 class="text-success">{$nrank['achieved']}</h4>
                              <h5>Achieved</h5></div>
                            </div><!-- CN four -->
                        </div>
                        {else if $key != 'criteria'}
                          {foreach from=$nrank  item=$n key=$key2}
                           <div class="current-new-container">
                            <div class="CN-one"><div ui-jq="easyPieChart" ui-options="{
                                  percent: {round($n['percentage'], 1)},
                                  lineWidth: 6,
                                  trackColor: '#e8eff0',
                                  barColor: '#23b7e5',
                                  scaleColor: false,
                                  color: '#fff',
                                  size: 55,
                                  lineCap: 'butt',
                                  rotate: 55,
                                  animate: 1000
                                  }">
                                    <div> 
                                      <span class="step">{round($n['percentage'], 1)}</span>%
                                    </div>
                            </div></div> <!-- CN one -->
                            <div class="CN-two">
                              {lang($key)}
                              {if $key == 'downline_package_count'}
                                    {if $MODULE_STATUS['opencart_status'] == "yes"}
                                      <h5 class="text-primary-lt">{$n['model']}</h5>
                                    {else}
                                    <h5 class="text-primary-lt">{$n['product_name']}</h5>
                                   {/if}
                                  {else}
                                    <h5 class="text-primary-lt">{$n['rank_name']}</h5>
                                  {/if}<!-- CN two -->
                            </div>
                            <div class="CN-three"><div><h4 class="text-info">{$n.required}</h4>
                              <h5>Required</h5></div></div><!-- CN three -->
                            <div class="CN-four"><div><h4 class="text-success">{$n.achieved}</h4>
                              <h5>Achieved</h5></div>
                            </div><!-- CN four -->
                        </div>
                          {/foreach}
                        {/if}
                        {/foreach}
                    </div>

                </div>
            </div>
            {if empty($crank)}
            <div class="achivement-status"><h4 class="bg-light">{lang('you_haven_no_rank')}</h4></div>
            {else if empty($nrank)}
            <div class="achivement-status"><h4 class="bg-light">{lang('you_are_already_in_higher_rank')}</h4></div>
            {/if}
        </div>
    </div>
</div>
{if $promo_status=='yes'}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="panel wrapper">
              
                {assign var="next_column_class" value="two-col"}
              
            
            <div class="current-next-new {$next_column_class}">
                <div class="current-new">
                   
                    <h4 class="m-t-none m-b text-primary-lt font-thin-bold">{lang('Rank Promo')}  : {date('F j, Y',strtotime($promo['promo_start_date']))} to {date('F j, Y',strtotime($promo['promo_end_date']))}</h4>
                    <h4 class="m-t-none m-b text-primary-lt font-thin-bold">{lang('Current Promo')}{if empty($cpool)} - <span class="text-dark">NA</span>{else} - <span class="text-dark">{$cpool['promo_rank']}</span>{/if}</h4>
                    <div class="current-new-section">
                       <div class="table-responsive">
                      <table class="table user-tale1">
                        <tbody>
                          
                          <tr>
                              <th>{lang('referral_count')}</th>
                              <th>{lang('group_pv')}</th>
                              <th>{lang('bonus')}</th>
                              <th>{lang('travel_voucher')}</th>
                          </tr>
                          {foreach from=$rank_promo item=$r_promo}
                            <tr style="text-align:center">
                              <td >
                                {$r_promo['direct']}
                              </td>
                              <td>
                
                                {$r_promo['group_pv']}
                              </td>
                              <td>
                                {format_currency($r_promo['bonus'])}
                              </td>
                              <td>
                                {$r_promo['voucher']}
                              </td>
                            </tr>
                          {/foreach}
                        </tbody>
                      </table>
                      </div>
                </div>
                </div>
                <div class="next-new">{if empty($npool)}
                    <h4 class="m-t-none m-b text-primary-lt font-thin-bold">Next Promo - <span class="text-dark">NA</span></h4>{else}
                    <h4 class="m-t-none m-b text-primary-lt font-thin-bold">Next Promo - <span class="text-dark">{$npool['promo_rank']}</span></h4>{/if}
                    <div class="current-new-section">
                      {foreach from=$next_pool item=$nrank key=$key}
                        {if $key != 'downline_package_count' && $key != 'downline_rank' && $key != 'criteria' && $key !='group_users'}
                        <div class="current-new-container">
                            <div class="CN-one"><div ui-jq="easyPieChart" ui-options="{
                                  percent: {round($nrank['percentage'], 1)},
                                  lineWidth: 6,
                                  trackColor: '#e8eff0',
                                  barColor: '#23b7e5',
                                  scaleColor: false,
                                  color: '#fff',
                                  size: 55,
                                  lineCap: 'butt',
                                  rotate: 55,
                                  animate: 1000
                                  }">
                                
                                    <div>
                                      <span class="step">{round($nrank['percentage'], 1)}</span>%
                                    </div>
                                
                            </div></div> <!-- CN one -->
                            <div class="CN-two">{lang($key)}</div><!-- CN two -->
                            {if $key!='bonus'}
                            <div class="CN-three"><div><h4 class="text-info">{$nrank['required']}</h4>
                              <h5>Required</h5></div></div><!-- CN three -->
                            <div class="CN-four"  {if $key=='group_pv'} data-toggle="modal" data-target="#groupModalCenter" {/if} ><div><h4 class="text-success">{$nrank['achieved']}</h4> 
                              <h5>Achieved</h5>{if $key=='group_pv'}<i class="fa fa-info-circle" aria-hidden="true"></i>{/if}
</div>
                            </div><!-- CN four -->
                            {else}
                                <div class="CN-four" {if $key=='group'} data-toggle="modal" data-target="#groupModalCenter" {/if}><div><h4 class="text-success"><h5>{format_currency($nrank['bonus_amount'])}</h4>
                                  </h5></div>
                                </div>
                            {/if}
                        </div>
                        {else if $key != 'criteria' && $key !='group_users'}
                          {foreach from=$nrank  item=$n key=$key2}
                           <div class="current-new-container">
                            <div class="CN-one"><div ui-jq="easyPieChart" ui-options="{
                                  percent: {round($n['percentage'], 1)},
                                  lineWidth: 6,
                                  trackColor: '#e8eff0',
                                  barColor: '#23b7e5',
                                  scaleColor: false,
                                  color: '#fff',
                                  size: 55,
                                  lineCap: 'butt',
                                  rotate: 55,
                                  animate: 1000
                                  }">
                                
                                    <div> 
                                      <span class="step">{round($n['percentage'], 1)}</span>%
                                    </div>
                                
                            </div></div> <!-- CN one -->
                            <div class="CN-two">
                                
                              {lang($key)}
                              {if $key == 'downline_package_count'}
                                    {if $MODULE_STATUS['opencart_status'] == "yes"}
                                      <h5 class="text-primary-lt">{$n['model']}</h5>
                                    {else}
                                    <h5 class="text-primary-lt">{$n['product_name']}</h5>
                                   {/if}
                                  {else}
                                    <h5 class="text-primary-lt">{$n['rank_name']}</h5>
                                  {/if}<!-- CN two -->
                            </div>
                            {if $key!='rank' && $key!='bonus'}
                            <div class="CN-three"><div><h4 class="text-info">{$n.required}</h4>
                              <h5>Required</h5></div></div><!-- CN three -->
                            <div class="CN-four" ><div><h4 class="text-success">{$n.achieved}</h4>
                              <h5>Achieved</h5></div>
                            </div><!-- CN four -->
                            {elseif $key=='bonus'}
                                <div class="CN-four"><div><h4 class="text-success"><h5>Promo Bonus:{format_currency($n.bonus_amount)}</h4>
                                  </h5></div>
                                </div>
                            {/if}
                        </div>
                          {/foreach}
                        {/if}
                        {/foreach}
                    </div>

                </div>
            </div>
            {if empty($npool)}
            <div class="achivement-status"><h4 class="bg-light">{lang('you_are_already_in_higher_rank_promo')}</h4></div>
            {/if}
        </div>
    </div>
</div> 
{/if}
{/if}
   {if !empty($npool)}
    <div class="modal fade" id="groupModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle">{lang('group_pv')}</h5>
            
          </div>
          <div class="modal-body">
            <div class="tab2 top-earners-scroll" id="personal_info2">
              <div class="table-responsive">
                <table class="table user-tale ">
                    <thead>
                        <tr>
                        <th>{lang('user_name')}</th>
                         <th>{lang('group_pv')}</th>
                         </tr>
                    </thead>
                  <tbody>
                    {foreach from=$next_pool['group_users'] item=j}
                      <tr>
                        <td>
                            {$j['user_name']}
                        </td>
                        <td> {$j['gpv']}</td>
                      </tr>
                      
                    {/foreach}
                  </tbody>
                </table>
              </div>
              </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            
          </div>
        </div>
      </div>
    </div>
    {/if}
{/if}
<!-- Rank Div New End -->
<div class="row">
    <div class="col-md-12">
      <div class="dashbord-tab-section">
        {if $dashboardConfig["earnings_nd_expenses"] == 1 }
        <div class="panel dashbord-tab">
          <div class="wrapper"><h4 class="font-thin m-t-none m-b-none">{lang('earnings&expences')}</h4></div>
          <div class="tabsy">
            {$checked = 'checked'}
            {if $dashboardConfig["earnings"] == 1 }
            <input type="radio" id="tab1" name="tab" {$checked}>
            <label class="tabButton" for="tab1">{lang('Earnings')}</label>
            <div class="tab" id="personal_info">
              {if empty($incomes)}
               <div class="text-center text-primary-lt" ><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">
                {lang('no_data_found')}</h3>
                </div>
              {else}
                <div class="table-responsive">
                  <table class="table user-tale">
                    <tbody>
                      {foreach from=$incomes item=$income}
                        <tr>
                          <td valign="v-middle">
                            {strtoupper(lang($income.amount_type))}
                            {* {strtoupper(str_replace('_', ' ', $income.amount_type))} *}
                          </td>
                          <td>
                            
                            <span class="text-md text-success"> {format_currency($income.amount)}</span>
                          </td>
                          <td>
                            <span class="comm-type btn btn-info">{firstLetter(strtoupper(lang($income.amount_type)))}</span>
                          </td>
                          <!-- <td>
                            <a class="btn btn-primary" href="">{lang('view_more')}</a>
                          </td> -->
                        </tr>
                      {/foreach}
                    </tbody>
                  </table>
                </div>
              {/if}
            </div>
            {$checked = ""}
            {/if}
            {if $dashboardConfig["expenses"] == 1 }
            <input type="radio" id="tab2" name="tab" {$checked}>
            <label class="tabButton" for="tab2">{lang('expenses')}</label>
            <div class="tab" id="contact_info">
              {if empty($expenses)}
                <div class="text-center text-primary-lt" ><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">
                {lang('no_data_found')}</h3>
                </div>
              {else}
                <div class="table-responsive">
                  <table class="table user-tale">
                    <tbody>
                      {foreach from=$expenses item=$expense}
                        <tr>
                          <td valign="v-middle">
                            {strtoupper(lang($expense.amount_type))}
                            {* {strtoupper(str_replace('_', ' ', $expense.amount_type))} *}
                          </td>
                          <td>
                            <span class="text-md text-success">{format_currency($expense.amount)}</span>
                          </td>
                          <td>
                            <span class="comm-type btn btn-info">{firstLetter(strtoupper(lang($expense.amount_type)))}</span>
                          </td>
                          {* <td><a class="btn btn-primary" href="">View More</a></td> *}
                        </tr>
                      {/foreach}
                    </tbody>
                  </table>
                </div>
              {/if}
            </div>
            {$checked = ""}
            {/if}
            {if $dashboardConfig["payout_status"] == 1 }
            <input type="radio" id="tab3" name="tab" {$checked}>
            <label class="tabButton" for="tab3">{lang('payout_status')}</label>
            <div class="tab" id="social_profiles">
              <div class="table-responsive">
                <table class="table user-tale">
                  <tbody>
                  {foreach from=$payouts item=$payout key=$key}
                    <tr>
                      <td class="v-middle">{strtoupper(lang($key))}</td>
                      <td>
                        {if $key == 'requested'}
                          {if $payout == ''}
                            <span class="text-md text-default">{format_currency(0)}</span>
                          {else}
                            <span class="text-md text-primary">{format_currency($payout)}</span> 
                          {/if}
                        {else if $key == 'approved'}
                          {if $payout == ''}
                            <span class="text-md text-primary">{format_currency(0)}</span>
                          {else}
                            <span class="text-md text-primary">{format_currency($payout)}</span> 
                          {/if}
                        {else if $key == 'paid'}
                          {if $payout == ''}
                            <span class="text-md text-success">{format_currency(0)}</span>
                          {else}
                            <span class="text-md text-success">{format_currency($payout)}</span> 
                          {/if}
                        {else if $key == 'rejected'}
                          {if $payout == ''}
                            <span class="text-md text-danger">{format_currency(0)}</span>
                          {else}
                            <span class="text-md text-danger">{format_currency($payout)}</span> 
                          {/if}
                        {else}
                          {if $payout == ''}
                            <span class="text-md text-default">{format_currency(0)}</span>
                          {else}
                            <span class="text-md text-default">{format_currency($payout)}</span> 
                          {/if}
                        {/if}
                      </td>
                    </tr>
                  {/foreach}
                  </tbody>
                </table>
              </div>
            </div>
            {$checked = ""}
            {/if}
          </div>
        </div>
        {/if}
      
        {if $dashboardConfig["team_perfomance"] == 1 }
        <div class="panel dashbord-tab">
          <div class="wrapper"><h4 class="font-thin m-t-none m-b-none ">{lang('team_perfomance')}</h4></div> 
          <div class="tabsy2">
            {$checked = 'checked'}
            {if $dashboardConfig["top_earners"] == 1 }
            <input type="radio" id="tab4" name="tab2" {$checked}>
            <label class="tabButton2" for="tab4">{lang('top_earners')}</label>
            <div class="tab2 top-earners-scroll" id="personal_info2">
              <div class="table-responsive">
                <table class="table user-tale top-earners-table">
                  <tbody>
                    {assign var="i" value=0}
                    {foreach from=$top_earners item=j}
                      <tr>
                        <td valign="v-middle" class="grid-1">
                              {*<div class="top-earners-img">
                              <img src="{$j['profile_picture_full']}" class="r r-2x"></div>
                              <div class="top-earners-cnt">
                                <span class="text-black">{$j['user_name']}</span>
                                <span class="text-muted">{$j['balance_amount']}</span>
                              </div>*}
                              <div class="col-lg-12">
                                    <div class="col-lg-2 col-xs-2 padding-zero ">
                                        <span class="thumb-sm avatar ">
                                        <img src="{$j['profile_picture_full']}">
                                        <i class="on b-white bottom"></i>
                                        </span>
                                    </div>
                                    <div class="col-lg-10 col-xs-10 padding-zero">
                                        <div class="col-lg-8 pull-left">
                                            <div class="member-full-name">{$j['user_full_name']}</div>
                                            <span class="user-name" >{$j['user_name']}</span>
                                            
                                        </div>
                                        <div class="col-lg-4 text-center padding-zero member-package-Center">
                                        <div class="member-package">
                                        {$j['balance_amount']}
                                        </div>
                                        </div>
                                    </div>
                                </div>

                        </td>
                      </tr>
                      {$i=$i+1}
                    {/foreach}
                  </tbody>
                </table>
              </div>
              {if empty($top_earners)}
                <div class="text-center text-primary-lt" ><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">
                {lang('no_data_found')}</h3>
                </div>
              {/if}
            </div>
            {$checked = ""}
            {/if}

            {if $dashboardConfig["top_recruiters"] == 1 }
            <input type="radio" id="tab5" name="tab2" {$checked}>
            <label class="tabButton2" for="tab5">{lang('top_recruiters')}</label>
            <div class="tab2 top-earners-scroll" id="personal_info2">

              <div class="table-responsive">
                <table class="table table-hover user-tale top-earners-table">
                  <tbody>
                    <tr>
                      {assign var="i" value=0}
                      {foreach from=$top_recruters item=j}
                      {$i=$i+1}
                      {$k=fmod($i, 4)}
                      <td valign="v-middle" class="grid-1">
                        {*<div class="top-earners-img"><img src="{$j['profile_picture_full']}" class="r r-2x"></div><div class="top-earners-cnt"><span class="text-black">{$j.user_name}</span><span class="text-muted">{$j.count}</span></div>*}
                        <div class="col-lg-12">
                                    <div class="col-lg-2 col-xs-2 padding-zero ">
                                        <span class="thumb-sm avatar ">
                                        <img src="{$j['profile_picture_full']}">
                                        <i class="on b-white bottom"></i>
                                        </span>
                                    </div>
                                    <div class="col-lg-10 col-xs-10 padding-zero">
                                        <div class="col-lg-8 pull-left">
                                            <div class="member-full-name">{$j['user_full_name']}</div>
                                            <span class="user-name" >{$j['user_name']}</span>
                                            
                                        </div>
                                        <div class="col-lg-4 text-center padding-zero member-package-Center">
                                        <div class="member-package">
                                        {$j.count}
                                        </div>
                                        </div>
                                    </div>
                            </div>
                    </td>
                    </tr>
                   {$i=$i+1}
                   {/foreach}
                    
                  </tbody>
                </table>
              </div>
             {if empty($top_recruters)}
              <div class="text-center text-primary-lt" ><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
              <h3 class="no_data">
              {lang('no_data_found')}</h3>
              </div>
             {/if}
            </div>
            {$checked = ""}
            {/if}

            {if $MODULE_STATUS['product_status'] == "yes"}
            {if $dashboardConfig["package_overview"] == 1 }
            <input type="radio" id="tab6" name="tab2" {$checked}>
            <label class="tabButton2" for="tab6">{lang('package_overview')}
            </label>
            <div class="tab2" id="contact_info2">
             <table class="table user-tale">
                  <tbody>
                    <tr>
                       {$j=0}
                      {foreach from=$prgrsbar_data item=v}
                      {*<td valign="v-middle" width="80"><img src="{$PUBLIC_URL}/images/package-37.png" /></td>*}
                      <td><h5 class="text-md">{$v.package_name}</h5><p>{lang('you_have')} {$v.joining_count} {$v.package_name} {lang('package_purchases_in_your_team')} </p></td>
                      <td><span class="comm-type btn btn-info">{$v.joining_count}</span></td>
                    </tr>
                   {$j=$j+1}
                    
                   {/foreach}
                
                  </tbody>
                </table>
                 {if empty($prgrsbar_data)}
                <div class="text-center text-primary-lt" ><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">
                {lang('no_data_found')}</h3>
                </div>
              {/if}
            </div>
            {$checked = ""}
            {/if}
            {/if}

            {if $MODULE_STATUS['rank_status'] == "yes"}
            {if $dashboardConfig["rank_overview"] == 1 }
            <input type="radio" id="tab7" name="tab2" {$checked}>
            <label class="tabButton2" for="tab7">{lang('rank_overview')}</label>
            <div class="tab2" id="social_profiles2">
               <table class="table user-tale">
                  <tbody>
                    <tr>
                       {$j=0}
                      {foreach from=$rank_data item=v}
                      {*<td valign="v-middle" width="80"><img src="{$PUBLIC_URL}/images/package-37.png" /></td>*}
                      <td><h5 class="text-md">{$v.rank_name}</h5><p>{lang('you_have')} {$v.count} {$v.rank_name} {lang('rank_in_your_team')}</p></td>
                      <td><span class="comm-type btn btn-info">{$v.count}</span></td>
                    </tr>
                   {$j=$j+1}
                    
                   {/foreach}
                  </tbody>
                </table>
                {if empty($rank_data)}
                <div class="text-center text-primary-lt" ><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">
                {lang('no_data_found')}</h3>
                </div>
                {/if}                
            </div>
            {$checked = ""}
            {/if}
            {/if}

           {* {if $MODULE_STATUS['rank_status'] != "yes" && $MODULE_STATUS['product_status'] != "yes" }
           {if $dashboardConfig["joinings"] == 1 }
            <input type="radio" id="tab8" name="tab2">
            <label class="tabButton2" for="tab8">{lang('joinings')}</label>
              <div class="tab2" id="social_profiles2">
                <table class="table user-tale top-earners-table">
                    <tbody>
                      {assign var="i" value=0 }
                      {foreach from=$latest_joinees item=j}
                        <tr>
                          <td valign="v-middle">
                            <div class="top-earners-img">
                              <img src="{$j['profile_picture_full']}" class="r r-2x">
                            </div>
                            <div class="top-earners-cnt">
                              <span class="text-black">{$j['user_name']}</span>
                              <span class="text-muted">{$j['date_of_joining']}</span>
                            </div>
                          </td>
                        </tr>
                        {$i=$i+1}
                      {/foreach}
                      {if empty($latest_joinees)}
                        <div class="text-center text-primary-lt" ><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                        <h3 class="no_data">
                            {lang('no_data_found')}</h3>
                        </div>
                      {/if} 
                    </tbody>
                </table>
              </div>
            {/if}
            {/if} *}

          </div>
        </div>
        {/if}
      </div>
    </div>
</div>
    
   <!-- hidden -->
  <div class="row" style="display: none;">
    <div class="col-md-6">
      <div class="panel no-border" id="section_top_earners">
        <div class="panel-heading wrapper b-b b-light">
          <h4 class="font-thin m-t-none m-b-none text-muted">{lang('top_earners')}</h4>
        </div>
        <ul class="list-group list-group-lg m-b-none">
          {assign var="i" value=0 }
          {foreach from=$top_earners item=j}
          <li class="list-group-item"> <a href="javascript:void(0);" class="thumb-sm m-r"> <img src="{$j['profile_picture_full']}" class="r r-2x"> </a> <span class="pull-right text-muted inline m-t-sm">{$j['balance_amount']}</span> <a href="javascript:void(0);">{$j['user_name']}</a> </li>
          {$i=$i+1}
          {/foreach}
          {if empty($top_earners)}
            <div class="text-center text-primary-lt" ><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
            <h3 class="no_data">
                {lang('no_data_found')}</h3>
            </div>
          {/if}
        </ul>
      </div>
    </div>
    <div class="col-md-6" style="">
      <div class="list-group list-group-lg list-group-sp" id="section_social_media"> <a {if $is_app} href="{$social_media_info['fb_link']}" target="_blank" {else} href="#" target="" {/if} class="list-group-item clearfix"> <span class="pull-left m-r">
        <button class="btn btn-rounded btn-lg btn-icon btn-facebook"> <i class="fa fa-facebook"></i> </button>
        </span> <span class="clear"> <span>{lang('facebook_users')}</span> <span class="social_iocs pull-center hidden-xs">{lang('facebook')}</span> <small class="text-muted clear text-ellipsis">{$social_media_info['fb_count']} +</small> </span> </a> {* <a {if $is_app} href="{$social_media_info['gplus_link']}" target="_blank" {else} href="#" target="" {/if} class="list-group-item clearfix"> <span class="pull-left m-r">
        <button class="btn btn-rounded btn-lg btn-icon btn-lkin"> <i class="fa fa-linkedin"></i> </button>
        </span> <span class="clear"> <span>{lang('linkedin_users')}</span> <span class="social_iocs pull-center hidden-xs">{lang('linkedin')}</span> <small class="text-muted clear text-ellipsis">{$social_media_info['gplus_count']} +</small> </span> </a> *} <a {if $is_app} href="{$social_media_info['twitter_link']}" target="_blank" {else} href="#" target="" {/if} class="list-group-item clearfix"> <span class="pull-left m-r">
        <button class="btn btn-rounded btn-lg btn-icon btn-info"> <i class="fa fa-twitter"></i> </button>
        </span> <span class="clear"> <span>{lang('twitter_users')}</span> <span class="social_iocs pull-center hidden-xs">{lang('twitter')}</span> <small class="text-muted clear text-ellipsis">{$social_media_info['twitter_count']} +</small> </span> </a> <a {if $is_app} href="{$social_media_info['inst_link']}" target="_blank" {else} href="#" target="" {/if} class="list-group-item clearfix"> <span class="pull-left m-r">
        <button class="btn btn-rounded btn-lg btn-icon btn-instagarm"> <i class="fa fa-instagram"></i> </button>
        </span> <span class="clear"> <span>{lang('instagram_users')}</span> <span class="social_iocs pull-center hidden-xs">{lang('instagram')}</span> <small class="text-muted clear text-ellipsis">{$social_media_info['inst_count']} +</small> </span> </a> </div>
      </div>
    </div>
  </div>
  <br>
  <div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" id="reservation_detail_model" data-backdrop="static"
    class="modal fade" style="display: none;">
    <div class="modal-dialog modal-md">
      <div class="modal-content">
        <div class="modal-header">
          <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
          <h4 class="modal-title" id="modaltitle"></h4>
        </div>
        <div class="modal-body" id="reservation_detail_model_body"> </div>
      </div>
    </div>
  </div>

 

  {/block}
  {block name=right_content}
  

  <style>
  .demo_section {
  
  display: none ;
  }
  .setting_margin_top {
  
  margin-top: -50px;
  }
  .setting_margin{
  margin-left: 230px;
  }
  .wrapper_index{
  
  padding:15px
  }
  .demo_margin_top {
  
  margin-top: -30px;
  }
  .demo_footer_user {
  margin-top: -50px;
  }
  <!--opoup-->
  .demo_margin_top {
  
  margin-top: -30px;
  }
  .modal-content_1 {
  background-color: #fff;
  
  }
  .pager {
  
  margin: 0px 0;
  box-shadow: 0px 2px 17px 0px #19191942;
  }
  .modal-content_1 {
  position: relative;
  
  -webkit-background-clip: padding-box;
  background-clip: padding-box;
  border: 1px solid #999;
  border: 1px solid rgba(255, 255, 255, 0.58);
  outline: 0;
  -webkit-box-shadow: 0 3px 9px rgba(0, 0, 0, .5);
  box-shadow: 0 3px 9px rgba(0, 0, 0, .5);
  }
  .modal-dialog.modal-md.index {
  width: 35%;
  top: 7%;
  margin: 0 auto;
  position: relative;
  right: 0;
  }
  div#subscribeModal {
  padding-right: 0px !important;
  }
  @media (max-width:767px)
  {
  .demo_section {
  
  display: block;
  }
  .moblie_demo {
  
  display: none ;
  
  }
  .setting_margin {
  margin-left: 0px;
  }
  .setting_margin_top {
  margin-top: -49px !important;
  }
  .demo_margin_top {
  margin-top: -27px !important;
  }
  .modal-content_1 {
  background-color: #fff !important;
  
  }
  }

  </style>
  {/block}
  {block name=home_wrapper_out}
    {if $dashboardConfig["latest_news"] == 1 } 
    {if !empty($news)}
 <!-- remove class news-fixed  and remove id id="news_div" change style display: none; to display: block to remove the pop new when reloading;  -->
      <div class="panel-body setting_margin news home-news " style="display: none;" >
        <div class="panel wrapper">
          <button class="news-hide"><i class="fa fa-angle-down" aria-hidden="true"></i>  {lang('hide')}</button>
          <h4 class="font-thin m-t-none m-b-none">{lang('latest_news')}</h4>
          <div id="news_carousel" class="owl-carousel news-carousel">
            {foreach from=$news item=$news_item}
              <div class="item">
                <div class="rela-blog-img">
                  <a href="{$BASE_URL}user/view_news">
                    <img src="{$SITE_URL}/uploads/images/news/{$news_item.news_image}" alt="{$news_item.news_title}">
                  </a>
                </div>
                <div class="rela-blog-cnt">
                  <h5 class="rela-blog-cnt-title">
                <a href="{$BASE_URL}user/view_news/{$news_item.news_id}">{$news_item.news_title|truncate:64}</a></h5>
                  <p class="news_content">{$news_item.news_desc|truncate:164}</p>
                  <a class="read_full_news" href="{$BASE_URL}user/view_news/{$news_item.news_id}">
                    {lang('read_more')} <i class="fa fa-forward" aria-hidden="true"> </i>
                </a>
                </div>

              </div>
            {/foreach}
          </div>
        </div>
      </div>
    {/if}
    {/if}
  <div class="demo_footer_user"> {include file="layout/demo_footer.tpl"} </div>

  {/block}



