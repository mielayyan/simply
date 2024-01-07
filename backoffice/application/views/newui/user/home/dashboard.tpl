{extends file='newui/layout/user.tpl'}
{block name=script}
{$smarty.block.parent}
<script src="{$PUBLIC_URL}plugins/clipboard.min.js" type="text/javascript"></script>
<script src="{$PUBLIC_URL}javascript/chart/chart.min.js"></script>
<script src="{$PUBLIC_URL}javascript/ajax-dynamic-dashboard-user.js" type="text/javascript"></script>
<script src="{$PUBLIC_URL}javascript/user_dashboard.js"></script>
<script src="{$PUBLIC_URL}javascript/user_dashboard.js"></script>
<link rel="stylesheet" href="{$PUBLIC_URL}css/owl.carousel.min.css"/>
<script src="{$PUBLIC_URL}javascript/owl.carousel.js"></script>
<script>
  country_map_data = {$map_data};
</script>
{/block}

{* {block name=overview}

  <div class="header-user-details">
    {$Profit = $amount-$expense }
    <div class="header-user-cnt">
      <h4 class="text-info">{lang('turnover')}</h4>
      <p>
        {format_currency($amount)}
      </p>
    </div>
     <div class="header-user-cnt">
        <h4 class="text-danger">{lang('expense')}</h4>
        <p>{format_currency($expense)}</p>
    </div>
    <div class="header-user-cnt">
      <h4 class="text-success">{lang('profit')}</h4>
      <p>{format_currency($Profit)}</p>
    </div>
  </div>
{/block} *}

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
  <div class="col">
    <div class="row">
      <div class="col-lg-5 col-md-12" id="section_tile">
        <div class="row row-sm text-center"> 

          <!-- Commission earned -->
          {if $dashboardConfig["commission_earned"] == 1 }
          <div class="col-xs-6" id="section_tile1">
            <div class="tile-dropdown">
                <div class="dropdown">
                  <div data-toggle="dropdown" aria-expanded="false"> <i class="fas fa fa-cog fa-spin text-white"></i> </div>
                   <ul class="dropdown-menu dropdown-menu_right" id="commission_dash">
                    <li class="active"><a href="javascript:void(0);" id="all_commission"><i class="fa fa-list margin-r-5"></i> {lang('all')}</a></li>
                    <li><a href="javascript:void(0);" id="yearly_commission"><i class="fa fa-calendar margin-r-5"></i> {lang('this_year')}</a></li>
                    <li><a href="javascript:void(0);" id="monthly_commission"><i class="fa fa-calendar margin-r-5"></i> {lang('this_month')}</a></li>
                    <li><a href="javascript:void(0);" id="weekly_commission"><i class="fa fa-calendar margin-r-5"></i> {lang('this_week')}</a></li>
                  </ul> 
                </div>
              </div>
            <a href="{base_url('user/income')}">
            <div class="panel padder-v item bg-info">
              <div class="h1 text-white font-thin h1" id="total_commission">
                {format_currency($total_commission)}
              </div>
              <span class="text-muted">{lang('commision_earned')}</span>              
            </div>
            </a>
          </div>
          {/if}
          <!-- Commission earned end -->
          
          <!-- Payout released -->
          {if $dashboardConfig["payout_released"] == 1 }
          <div class="col-xs-6" id="section_tile3">
            <div class="tile-dropdown">
                <div class="dropdown">
                  <div data-toggle="dropdown" aria-expanded="false"> <i class="fas fa fa-cog fa-spin"></i> </div>
                  <ul class="dropdown-menu dropdown-menu_right" id="payout_dash">
                    <li class="active"><a href="javascript:void(0);" id="all_payout"><i class="fa fa-list margin-r-5"></i> {lang('all')}</a></li>
                    <li><a href="javascript:void(0);" id="yearly_payout"><i class="fa fa-calendar margin-r-5"></i> {lang('this_year')}</a></li>
                    <li><a href="javascript:void(0);" id="monthly_payout"><i class="fa fa-calendar margin-r-5"></i> {lang('this_month')}</a></li>
                    <li><a href="javascript:void(0);" id="weekly_payout"><i class="fa fa-calendar margin-r-5"></i> {lang('this_week')}</a></li>
                  </ul>
                </div>
              </div>
            <a href="{base_url('user/income/tab2')}">
            <div class="panel padder-v item">
              <div class="text-info font-thin h1 block1" id="total_payout"> {$total_payout} </div>
              <span class="text-muteds">{lang('payout_released')}</span>              
            </div>
            </a>
          </div>
          {/if}
          <!-- Payout released end -->
       
          <!-- Payout pending -->
          {if $dashboardConfig["payout_pending"] == 1 }
          <div class="col-xs-6" id="section_tile4">
            <div class="tile-dropdown">
                <div class="dropdown">
                  <div data-toggle="dropdown" aria-expanded="false"> <i class="fas fa fa-cog"></i> </div>
                </div>
              </div>
            <a href="{base_url('user/my_withdrawal_request')}">
            <div class="panel padder-v item">
              <div class="font-thin text-info h1" id="mail_total">{format_currency($payout_pending)}</div>
              <span class="text-muted">{lang('payout_pending')}</span>
              
            </div>
            </a>
          </div>
          {/if}
          <!-- Payout pending end -->
        
        
          <!-- Total sales -->
          {if $dashboardConfig["total_sales"] == 1 }
          <div class="col-xs-6" id="section_tile2">
            <div class="tile-dropdown">
                <div class="dropdown">
                  <div data-toggle="dropdown" aria-expanded="false"> <i class="fas fa fa-cog fa-spin text-white"></i> </div>
                  <ul class="dropdown-menu dropdown-menu_right" id="sales_dash">
                    <li class="active"><a href="javascript:void(0);" id="all_sales"><i class="fa fa-list margin-r-5"></i> {lang('all')}</a></li>
                    <li><a href="javascript:void(0);" id="yearly_sales"><i class="fa fa-calendar margin-r-5"></i> {lang('this_year')}</a></li>
                    <li><a href="javascript:void(0);" id="monthly_sales"><i class="fa fa-calendar margin-r-5"></i> {lang('this_month')}</a></li>
                    <li><a href="javascript:void(0);" id="weekly_sales"><i class="fa fa-calendar margin-r-5"></i> {lang('this_week')}</a></li>
                  </ul>
                </div>
              </div>
            <a href="">
            <div class="panel padder-v item bg-primary">
              <div class="text-white font-thin h1 block1" id="sales_total">{format_currency($sales)}</div>
              <span class="text-muted">Total {lang('sales')}</span>
              
            </div>
            </a>
          </div>
          {/if}
          <!-- Total sales end -->
          
          <!-- Ewallet -->
          {if $dashboardConfig["ewallet"] == 1 }
          <div class="col-xs-12" id="section_tile1">
            <a href="{base_url('user/my_ewallet')}">
            <div class="panel padder-v item">
              <div class="row">
                <div class="col-lg-3 col-md-5 wallet text-center"><img src="{$PUBLIC_URL}/images/wallet.png" /></div>
                <div class="col-lg-9 col-md-7 text-left">
                  <div class="h1 text-info font-thin h1"> {$total_amount}</div>
                  <span class="text-muted">{lang('e_wallet')}</span>
                </div>
              </div>           
            </div>
            </a>
          </div>
          {/if}
          <!-- Ewallet end -->

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
                <span class="text-muted text-xs">{lang('your_current_status')}</span> </div>
            </div>
          </div>
          {/if}
       
        
          <!--END DONATION TILE-->
        
        
        </div>
      </div> 
      {* Left column end *}
      <!-- Joinings -->
      {if $dashboardConfig["member_joinings"] == 1 }
      <div class="col-lg-7 col-md-12">
        <div class="panel hbox hbox-auto-xs no-border">
          <div class="col wrapper">
            <div class="dropdown pull-right">
              <div data-toggle="dropdown" aria-expanded="false"><i class="fas fa fa-cog fa-spin"></i></div>
              <ul class="dropdown-menu dropdown-menu_right" id="joinings_graph">
                <li class=""><a id="yearly_joining_graph" href="javascript:void(0);"><i class="fa fa-calendar margin-r-5"></i> {lang('year')}</a></li>
                <li class="active"><a id="monthly_joining_graph" href="javascript:void(0);"><i class="fa fa-calendar margin-r-5"></i> {lang('month')}</a></li>
                <li><a id="daily_joining_graph" href="javascript:void(0);"><i class="fa fa-calendar margin-r-5"></i> {lang('day')}</a></li>
              </ul>
            </div>
            <h4 class="font-thin m-t-none m-b-none text-primary-lt">{lang('joinings')}</h4>
            <span class="m-b block text-sm text-muted"></span>
            <canvas id="join_chart" style="height: 263px; width: 100%"></canvas>
          </div>
        </div>
      </div> 
      {/if}
      <!-- Joinings end -->
      {* Rtight graph column end *}
    </div> {* First Row end *}

    <div class="row">
      <div class="graph-profile-grid">
        
      <!-- Profile/Promotion -->
      {if $dashboardConfig["summary_or_promotions"] == 1 }
        <div class="panel item">
          <div class="panel-body">
            <div class="dashbord-profile">
              <div class="profile-avatar">
                <img class="" src="{$SITE_URL}/uploads/images/profile_picture/{$profile_photo}">
                <div class="clearfix"></div>
                <a href="{BASE_URL}/user/profile/profile_view" class="profile-edit" style="color: #337ab7">{lang('view_profile')}</a>
              </div>

              <div class="profile-content">
                <h4 class="profile-name">{$LOG_USER_NAME}</h4>
                {if $MODULE_STATUS['product_status'] == 'yes'}
                  <p class="Current-Package">
                    {lang('current_package')}:
                    {if $user_details['membership'] == ""}
                      {lang('na')}
                    {else}
                      <span>{$user_details['membership']}</span>
                    {/if}
                  </p>
                {/if}
                {if $MODULE_STATUS['subscription_status'] == "yes"}
                  <p class="Package-Expire text-info">
                    {lang('membership_will_expire')} :{$user_validity}
                  </p>
                {/if}
                {if $MODULE_STATUS['package_upgrade'] }
                  {if !empty($upgradable_package_list)}
                    <div class="dropdown change-package-dropdown-section">
                      <button class="btn btn-primary dropdown-toggle" type="button" id="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                        {lang('upgrade_package')}
                        <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu change-package-dropdown" aria-labelledby="dropdownMenu1">
                        {foreach from=$upgradable_package_list item=list}
                          <li> 
                             {$list.product_name}
                            <a href="{$BASE_URL}package_upgrade?package={$list.product_id}" class="text-primary font-bold" style=" ">{lang('upgrade')}</a>
                          </li>
                        {/foreach}
                      </ul>
                    </div>
                  {/if}
                {/if}
              </div>
            </div>
          </div>

          {if $is_app}
            {if $MODULE_STATUS['replicated_site_status'] == "yes" || $MODULE_STATUS['lead_capture_status'] == "yes"}
              <div class="" id="section_tile5">
                <div class="panel-body">
                  <h4 class="m-t-none m-b text-primary-lt font-thin-bold">
                    {lang('promotion_tools')}
                  </h4>
                  
                  {if $MODULE_STATUS['replicated_site_status'] == "yes"}
                    <div class="referal-link">
                      <form>
                        <div class="form-group">
                          <div class="rep-head">
                            <h4>{lang('replica_link')}</h4>
                            {if DEMO_STATUS == "yes"}
                              <button type="button" class="rpl-social">
                                <i class="fa fa-facebook" aria-hidden="true" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/replica/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}');">
                                </i>
                              </button>
                              <button type="button" class="rpl-social" onClick="twittershare('{$site_url}/replica/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}');">
                                <i class="fa fa-twitter" aria-hidden="true"></i>
                              </button>
                              <a type="button" class="rpl-social" href="http://www.linkedin.com/shareArticle?url={$site_url}/replica/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}" target="_blank">
                                <i class="fa fa-linkedin" aria-hidden="true"></i>
                              </a>
                            {else}
                              <button type="button" class="rpl-social">
                                <i class="fa fa-facebook" aria-hidden="true" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/replica/{$LOG_USER_NAME}');">
                                </i>
                              </button>
                              <button type="button" class="rpl-social" onClick="twittershare('{$site_url}/replica/{$LOG_USER_NAME}');">
                                <i class="fa fa-twitter" aria-hidden="true"></i>
                              </button>
                              <a type="button" class="rpl-social" href="http://www.linkedin.com/shareArticle?url={$site_url}/replica/{$LOG_USER_NAME}" target="_blank">
                                <i class="fa fa-linkedin" aria-hidden="true"></i>
                              </a>
                            {/if}
                            <div class="link-replica">
                              {if DEMO_STATUS == "yes"}
                                <input type="text" class="form-control"  id="" maxlength="" value="{$site_url}/replica/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}">
                                <button type="button" class="btn btn-sm btn-info" data-clipboard-text="{$site_url}/replica/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}" id="copy_link_replica">{lang('copy')}
                                </button>
                              {else}
                                <input type="text" class="form-control"  id="" maxlength="" value="{$site_url}/replica/{$LOG_USER_NAME}">
                                <button type="button" class="btn btn-sm btn-info" data-clipboard-text="{$site_url}/replica/{$LOG_USER_NAME}" id="copy_link_replica">{lang('copy')}
                                </button>
                              {/if}
                            </div>
                          </div>
                        </div>
                      </form>
                    </div>
                  {/if}
                  
                  {if $MODULE_STATUS['lead_capture_status'] == "yes"}
                    <div class="referal-link">
                      <form>
                        <div class="form-group">
                          {if DEMO_STATUS == "yes"}
                            <div class="rep-head">
                              <h4>{lang('lead_capture')}</h4>
                              <button type="button" class="rpl-social"><i class="fa fa-facebook" aria-hidden="true" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/lcp/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}');"></i></button>
                              <button type="button" class="rpl-social" onClick="twittershare('{$site_url}/lcp/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}');">
                                <i class="fa fa-twitter" aria-hidden="true"></i>
                              </button>
                              <a type="button" class="rpl-social" href="http://www.linkedin.com/shareArticle?url={$site_url}/lcp/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}" target="_blank"><i class="fa fa-linkedin"   aria-hidden="true"></i></a>
                            </div>
                            <div class="link-replica">
                              <input type="text" class="form-control"  id="" maxlength="" value="{$site_url}/lcp/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}">
                              <button type="button" class="btn btn-sm btn-info" data-clipboard-text="{$site_url}/lcp/{$ADMIN_USER_NAME}/{$LOG_USER_NAME}" id="copy_link_lcp">{lang('copy')}</button>
                            </div>
                          {else}
                          <div class="rep-head">
                            <h4>{lang('lead_capture')}</h4>
                            <button type="button" class="rpl-social"><i class="fa fa-facebook" aria-hidden="true" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/lcp/{$LOG_USER_NAME}');"></i></button>
                            <button type="button" class="rpl-social" onClick="twittershare('{$site_url}/replica/{$LOG_USER_NAME}');">
                              <i class="fa fa-twitter" aria-hidden="true"></i>
                            </button>
                            <a type="button" class="rpl-social" href="http://www.linkedin.com/shareArticle?url={$site_url}/lcp/{$LOG_USER_NAME}" target="_blank"><i class="fa fa-linkedin"   aria-hidden="true"></i></a>
                          </div>
                          <div class="link-replica">
                            <input type="text" class="form-control"  id="" maxlength="" value="{$site_url}/lcp/{$LOG_USER_NAME}">
                            <button type="button" class="btn btn-sm btn-info" data-clipboard-text="{$site_url}/lcp/{$LOG_USER_NAME}" id="copy_link_lcp">{lang('copy')}</button>
                          </div>
                          {/if}
                        </div>
                      </form>
                     </div>
                  {/if}
               
              </div>
            </div>
          {else if isset($profile_extra_data)}
            <div class="" id="section_tile5">
              <div class="panel-body profile-hidden-details">
                  <p class="">
                    {lang('sponsor_name')} : <span class="text-primary-lt">{$profile_extra_data.sponsor_name}</span>
                  </p>
                  <p class="">
                    {lang('placement_username')} : <span class="text-primary-lt">{$profile_extra_data.placement_user_name}</span>
                  </p>
                  <p class="">
                    {lang('personal_pv')} : <span class="text-primary-lt">{$profile_extra_data.personal_pv}</span>
                  </p>
                  <p class="">
                    {lang('group_pv')} : <span class="text-primary-lt">{$profile_extra_data.group_pv}</span>
                  </p>
              </div>
            </div>
          {/if}
        
        {if $MODULE_STATUS['lead_capture_status'] == "yes"}
        {/if}
      {/if}
      </div>
      {/if}
      <!-- Profile/Promotion end -->

      <!-- Member map -->
      {if $dashboardConfig["members_map"] == 1 }
      <div class="panel wrapper" id="section_country_graph">
          <h4 class="m-t-none m-b text-primary-lt font-thin-bold">{lang(member_joinings)}</h4>
          <h4 class="font-thin m-t-none m-b text-muted hidden"></h4>
          <div id="country_graph"style="height:255.7px;"></div>
        </div>
      {/if}
      <!-- Member map end -->

      </div>
    </div>

    <!-- Rank Div New -->
{if $MODULE_STATUS['rank_status'] == "yes" && $rank_criteria[0] != 'joinee_package' }
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
                    <h4 class="m-t-none m-b text-primary-lt font-thin-bold">Current Rank - <span class="text-dark">{$crank}</span></h4>{/if}
                    
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
                              <h5>Required</h5></div></div><!-- CN three -->
                            <div class="CN-four"><div><h4 class="text-success">{$rank['achieved']}</h4>
                              <h5>Achieved</h5></div></div><!-- CN four -->
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
{/if}
{/if}
<!-- Rank Div New End -->

    <div class="row">
      <div class="dashbord-tab-section">
        {if $dashboardConfig["earnings_nd_expenses"] == 1 }
        <div class="panel dashbord-tab">
          <div class="wrapper"><h4 class="font-thin m-t-none m-b-none text-primary-lt">{lang('earnings&expences')}</h4></div>
          <div class="tabsy">
            {$checked = 'checked'}
            {if $dashboardConfig["earnings"] == 1 }
            <input type="radio" id="tab1" name="tab" {$checked}>
            <label class="tabButton" for="tab1">{lang('Earnings')}</label>
            <div class="tab" id="personal_info">
              {if empty($incomes)}
                <div>{lang('no_data_found')}</div>
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
                            <span class="text-md text-success">{$DEFAULT_SYMBOL_LEFT} {round($income.amount, 2)}</span>
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
                <div>{lang('no_data_found')}</div>
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
                            <span class="text-md text-success">{$DEFAULT_SYMBOL_LEFT} {round($expense.amount, 2)}</span>
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
                            <span class="text-md text-default">{$DEFAULT_SYMBOL_LEFT} 0</span>
                          {else}
                            <span class="text-md text-primary">{$DEFAULT_SYMBOL_LEFT} {round($payout, 2)}</span> 
                          {/if}
                        {else if $key == 'approved'}
                          {if $payout == ''}
                            <span class="text-md text-primary">{$DEFAULT_SYMBOL_LEFT} 0</span>
                          {else}
                            <span class="text-md text-primary">{$DEFAULT_SYMBOL_LEFT} {round($payout, 2)}</span> 
                          {/if}
                        {else if $key == 'paid'}
                          {if $payout == ''}
                            <span class="text-md text-success">{$DEFAULT_SYMBOL_LEFT} 0</span>
                          {else}
                            <span class="text-md text-success">{$DEFAULT_SYMBOL_LEFT} {round($payout, 2)}</span> 
                          {/if}
                        {else if $key == 'rejected'}
                          {if $payout == ''}
                            <span class="text-md text-danger">{$DEFAULT_SYMBOL_LEFT} 0</span>
                          {else}
                            <span class="text-md text-danger">{$DEFAULT_SYMBOL_LEFT} {round($payout, 2)}</span> 
                          {/if}
                        {else}
                          {if $payout == ''}
                            <span class="text-md text-default">{$DEFAULT_SYMBOL_LEFT} 0</span>
                          {else}
                            <span class="text-md text-default">{$DEFAULT_SYMBOL_LEFT} {round($payout, 2)}</span> 
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
          <div class="wrapper"><h4 class="font-thin m-t-none m-b-none text-primary-lt">{lang('team_perfomance')}</h4></div>
          <div class="tabsy2">
            {$checked = 'checked'}
            {if $dashboardConfig["top_earners"] == 1 }
            <input type="radio" id="tab4" name="tab2" {$checked}>
            <label class="tabButton2" for="tab4">{lang('top_earners')}</label>
            <div class="tab2 top-earners-scroll" id="personal_info2">
              <div class="table-responsive">
                <table class="table user-tale top-earners-table">
                  <tbody>
                    {assign var="i" value=0 }
                    {foreach from=$top_earners item=j}
                      <tr>
                        <td valign="v-middle">
                          <div class="top-earners-img">
                          <img src="{$j['profile_picture_full']}" class="r r-2x"></div>
                          <div class="top-earners-cnt">
                            <span class="text-black">{$j['user_name']}</span>
                            <span class="text-muted">{$j['balance_amount']}</span>
                          </div>
                        </td>
                      </tr>
                      {$i=$i+1}
                    {/foreach}
                  </tbody>
                </table>
              </div>
              {if empty($top_earners)}
                <div> {lang('no_data_found')}</div>
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
                      <td valign="v-middle"><div class="top-earners-img"><img src="{$j['profile_picture_full']}" class="r r-2x"></div><div class="top-earners-cnt"><span class="text-black">{$j.user_name}</span><span class="text-muted">{$j.count}</span></div></td>
                    </tr>
                   {$i=$i+1}
                   {/foreach}
                    
                  </tbody>
                </table>
              </div>
             {if empty($top_recruters)}
              <div> {lang('no_data_found')}</div>
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
                <div> {lang('no_data_found')}</div>
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
                  {lang('no_data_found')}
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
                        <td> {lang('no_data_found')}</td>
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
               <td> {lang('no_data_found')}</td>
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
      <div class="panel-body setting_margin news home-news " style="display: block;" >
        <div class="panel wrapper">
          <button class="news-hide"><i class="fa fa-angle-down" aria-hidden="true"></i>  {lang('hide')}</button>
          <h4 class="font-thin m-t-none m-b-none text-primary-lt">{lang('latest_news')}</h4>
          <div id="news_carousel" class="owl-carousel owl-theme news-carousel">
            {foreach from=$news item=$news_item}
              <div class="item">
                <div class="rela-blog-img">
                  <a href="{$BASE_URL}user/view_news">
                    <img src="{$SITE_URL}/uploads/images/news/{$news_item.news_image}" alt="{$news_item.news_title}">
                  </a>
                </div>
                <div class="rela-blog-cnt">
                  <h5 class="rela-blog-cnt-title"><a href="{$BASE_URL}user/view_news">{$news_item.news_title|truncate:64}</a></h5>
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



