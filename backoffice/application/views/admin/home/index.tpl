{extends file=$BASE_TEMPLATE}

{block name=script}
    {$smarty.block.parent}
    <script src="{$PUBLIC_URL}plugins/clipboard.min.js" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}javascript/todo_config.js" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}javascript/chart/chart.min.js" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}javascript/ajax-dynamic-dashboard-admin.js" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}javascript/admin/dashboard.js"></script>
    <script>
        country_map_data = {$map_data};
        barChartData = {$barChartData};
        doughnutDataArray = {$doughnutDataArray};
        doughnutLabelsArray = {$doughnutLabelsArray};
        doughnutDataViewArray = {$doughnutDataViewArray};
        joiningLineGraphData = {$joiningLineGraphData};
    </script>
    <style type="text/css">
        
    </style>
{/block}
 
{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display: none;">
    <span id="left_join">{lang('left_join')}</span>
    <span id="right_join">{lang('right_join')}</span>
    <span id="join">{lang('joinings')}</span>
    <span id="confirm_msg">{lang('are_you_sure_want_delete')}</span>
</div>

<input name="mlm_plan" id="mlm_plan" type="hidden" value="{$MLM_PLAN}" />

<div class="admin-row1">
    <div class="admin-top-widget">
        {block name="ewallet_balance"}
            <div class="top-widget E-Wallet border-4">         
                <div class="top-widget-inline">
                    <div class="top-widget-icon"><img src="{$SITE_URL}/uploads/images/logos/E-Wallet-w.png"/></div>
                    <div class="top-widget-cnt">
                    <h3 title="{format_currency($ewalletBalance)}">
                    {thousands_currency_format($ewalletBalance)}</h3>
                    <span class="top-widget-text">{lang('ewalletbalance')}</span>
                    </div>
                </div>
            </div>
        {/block}
        <div class="grid-3">
        {block name="total_income"}
            <div class="top-widget income">             
                <div class="top-widget-inline">
                    <div class="top-widget-icon"><img src="{$SITE_URL}/uploads/images/logos/income-w.png"/></div>
                    <div class="top-widget-cnt">
                    <h3 id="total_incom_value"  title="{format_currency($TotalIncome)}">{thousands_currency_format($TotalIncome)}</h3>
                    <span class="top-widget-text">{lang('total_income')}</span>
                    </div>
                </div>
                <div class="dropdown top-widget-dropdown display-none">
                    <div data-toggle="dropdown" aria-expanded="false"> <i class="fa fa-filter text-info" aria-hidden="true"></i> </div>
                    <ul class="dropdown-menu dropdown-menu_right filter-dashboard-tiles" id="income">
                        <li class=""><a href="#" data-value="all" data-type="income">{lang('all')}</a></li>
                        <li class=""><a href="#" data-value="year" data-type="income">{lang('year')}</a></li>
                        <li class=""><a href="#" data-value="month" data-type="income">{lang('month')}</a></li>
                        <li class=""><a href="#" data-value="week" data-type="income">{lang('week')}</a></li>
                    </ul>
                </div>
            </div>
        {/block}

        {block name="bonus"}
            <div class="top-widget Bonus">            
                <div class="top-widget-inline">
                    <div class="top-widget-icon"><img src="{$SITE_URL}/uploads/images/logos/Bonus-w.png"/></div>
                    <div class="top-widget-cnt">
                    <h3 id="total_bonus_value" title="{format_currency($bussinessBonus)}">{thousands_currency_format($bussinessBonus)}</h3>
                    <span class="top-widget-text">{lang('bonus_generated')}</span>
                    </div>
                </div>
                <div class="dropdown top-widget-dropdown display-none ">
                    <div data-toggle="dropdown" aria-expanded="false"> <i class="fa fa-filter text-info" aria-hidden="true"></i> </div>
                    <ul class="dropdown-menu dropdown-menu_right filter-dashboard-tiles">
                        <li class=""><a href="#" data-value="all" data-type="bonus">{lang('all')}</a></li>
                        <li class=""><a href="#" data-value="year" data-type="bonus">{lang('year')}</a></li>
                        <li class=""><a href="#" data-value="month" data-type="bonus">{lang('month')}</a></li>
                        <li class=""><a href="#" data-value="week" data-type="bonus">{lang('week')}</a></li>
                    </ul>
                </div>
            </div>
        {/block}

        {block name="payout_paid"}
            <div class="top-widget Paid border-4">
                <div class="top-widget-inline">
                    <div class="top-widget-icon"><img src="{$SITE_URL}/uploads/images/logos/Paid-w.png"/></div>
                    <div class="top-widget-cnt">
                    <h3 id="total_payout_paid" title="{format_currency($bussinessPaid)}">{thousands_currency_format($bussinessPaid)}</h3>
                    <span class="top-widget-text">{lang('paid_amount')}</span>
                    </div>
                </div>
                <div class="dropdown top-widget-dropdown mobile-display-none">
                    <div data-toggle="dropdown" aria-expanded="false"><i class="fa fa-filter text-info" aria-hidden="true"></i> </div>
                    <ul class="dropdown-menu dropdown-menu_right filter-dashboard-tiles-all ">
                        <li class=""><a href="#" data-value="all" data-type="paid">{lang('all')}</a></li>
                        <li class=""><a href="#" data-value="year" data-type="paid">{lang('year')}</a></li>
                        <li class=""><a href="#" data-value="month" data-type="paid">{lang('month')}</a></li>
                        <li class=""><a href="#" data-value="week" data-type="paid">{lang('week')}</a></li>
                    </ul>
                    
                </div>
                <div class="dropdown top-widget-dropdown display-none">
                    <div data-toggle="dropdown" aria-expanded="false"> <i class="fa fa-filter text-info" aria-hidden="true"></i> </div>
                    <ul class="dropdown-menu dropdown-menu_right filter-dashboard-tiles">
                        <li class=""><a href="#" data-value="all" data-type="paid">{lang('all')}</a></li>
                        <li class=""><a href="#" data-value="year" data-type="paid">{lang('year')}</a></li>
                        <li class=""><a href="#" data-value="month" data-type="paid">{lang('month')}</a></li>
                        <li class=""><a href="#" data-value="week" data-type="paid">{lang('week')}</a></li>
                    </ul>
                    
                </div>
            </div>
        {/block}
        </div>
        {block name="payout_pending"}
            <div class="top-widget Pending">            
                <div class="top-widget-inline">
                    <div class="top-widget-icon"><img src="{$SITE_URL}/uploads/images/logos/Pending-w.png"/></div>
                    <div class="top-widget-cnt">
                    <h3 title="{format_currency($bussinessPending)}" >{format_currency($bussinessPending)}</h3>
                    <span class="top-widget-text">{lang('pending_amount')}</span>
                    </div>
                </div>
            </div>
        {/block}
    </div> 
</div>


<div class="income-graph">
    
    {block name="chart_income_commission"}
        <div class="panel chart-container"> 
            <div class="position-dropdown"> 
                <div class="dropdown top-widget-dropdown">
                    <div data-toggle="dropdown" aria-expanded="false"> <i class="fa fa-filter text-info" aria-hidden="true"></i> </div>
                    <ul class="dropdown-menu dropdown-menu_right filter-dashboard-graph">
                        <li class=""><a href="" data-value="year" data-graph="income_commission">{lang('year')}</a></li>
                        <li class=""><a href="" data-value="month" data-graph="income_commission">{lang('month')}</a></li>
                    </ul>
                </div>
            </div>    
            <h4>{lang('income_v_commission')}</h4>   
            <div class="ibox">            
                <div class="ibox-content">
                    <div><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                        <canvas id="barChart" height="280" style="display: block; width:250px; height: 280px;" width="250" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
        </div>
    {/block}

    {block name="chart_payout"}
        <div class="panel chart-container">
            <h4>{lang('payout_overview')}</h4> 
            <div class="ibox-content"> 
                <div><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                 
                {if $piechart[0]+$piechart[1]+$piechart[2] > 0 }
                    <canvas id="doughnutChart" class="Payout-chart" style="display: block; width:280px; height:280px;"  class="chartjs-render-monitor"></canvas>
                {else}
                <canvas id="doughnutChart" class="Payout-chart" style="display: none;"  class="chartjs-render-monitor"></canvas>
                <div class="text-center text-primary-lt" ><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">
                    {lang('no_data_found')}</h3>
                </div>
                {/if}
                </div>
            </div>
        </div>
    {/block}
</div>



<div class="joinings-graph">
    {block name='joinings'} 
        <div class="joinings panel">
            <div class="dropdown top-widget-dropdown">
                <div data-toggle="dropdown" aria-expanded="false"> <i class="fa fa-filter text-info" aria-hidden="true"></i> </div>
                <ul class="dropdown-menu dropdown-menu_right filter-dashboard-graph">
                    <li class=""><a href="" data-value="year" data-graph="joinings">{lang('year')}</a></li>
                    <li class=""><a href="" data-value="month" data-graph="joinings">{lang('month')}</a></li>
                    <li class=""><a href="" data-value="day" data-graph="joinings">{lang('day')}</a></li>
                </ul>
            </div>
            <h4>{lang('joinings')}</h4>
            {if strtolower($MLM_PLAN) == "binary"}
            <div class="left-right-join">
                <div>{lang('total_joinings')} : <span>{$total_joinings}</span></div>
                <div>{lang('today_joinings')} : <span>{$today_joinings}</span></div>
                <div>{lang('left_carry')} : <span>{$leftJoinings}</span></div>
                <div>{lang('right_carry')} : <span>{$rightJoinings}</span></div>
            </div> 
            {/if}
            <div class="ibox">            
                <div class="ibox-content">
                    <div><div class="chartjs-size-monitor"><div class="chartjs-size-monitor-expand"><div class=""></div></div><div class="chartjs-size-monitor-shrink"><div class=""></div></div></div>
                        <canvas id="lineChart" height="300" style="display: block; width:250px; height:270px;" width="250" class="chartjs-render-monitor"></canvas>
                    </div>
                </div>
            </div>
        </div>
    {/block} 
    {block name="new_members_panel"}
        <div class="new-members panel">
            <h4>{lang('new_members')}</h4>
            {if !empty($latest_joinees)}
            <div ui-jq="slimScroll">
            <ul class="list-group list-group-lg no-bg auto">
              {foreach from=$latest_joinees item=j}
                <li class="list-group-item clearfix">
                  {*<span class="pull-left thumb-sm avatar m-r">
                                      <img src="{$j['profile_picture_full']}">
                                      <i class="on b-white bottom"></i>
                                    </span>
                                    <span class="clear">
                                      <span>{$j['user_name']}</span>
                                      <small class="text-muted clear text-ellipsis">{$j['date_of_joining']}</small>
                   </span>*}
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
                        <div class="col-lg-4 text-center padding-zero">
                        <div class="member-package">
                        {if $MODULE_STATUS['product_status'] == "yes"}   {format_currency($j['product_amount'])}
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
    {/block}
    </div>
{if $MODULE_STATUS['replicated_site_status'] == "yes" || $MODULE_STATUS['lead_capture_status'] == "yes"}
<div class="one-col-grid">
    {block name="promotional_tools"}   
    <div class="panel">
        <div class="panel-body">
            
                  <h4 class="m-t-none m-b  font-thin-bold">
                    {lang('promotion_tools')}
                  </h4>
                <div class="two-col-grid">
                  {if $MODULE_STATUS['replicated_site_status'] == "yes"}
                  <div class="referal-link">
                      <form autocomplete="off">
                        <div class="form-group">
                          <div class="rep-head">
                            <h4>{lang('replica_link')}</h4>
                             {if DEMO_STATUS == "yes"}
                              <button type="button" class="rpl-social">
                                <i class="fa fa-facebook" aria-hidden="true" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/replica/{$ADMIN_USER_NAME}/{$replica_lcp_user_name}');">
                                </i>
                              </button>
                              <button type="button" class="rpl-social" onClick="twittershare('{$site_url}/replica/{$ADMIN_USER_NAME}/{$replica_lcp_user_name}');">
                                <i class="fa fa-twitter" aria-hidden="true"></i>
                              </button>
                              <a type="button" class="rpl-social" href="http://www.linkedin.com/shareArticle?url={$site_url}/replica/{$ADMIN_USER_NAME}/{$replica_lcp_user_name}" target="_blank">
                                <i class="fa fa-linkedin" aria-hidden="true"></i>
                              </a>
                             {else}
                              <button type="button" class="rpl-social">
                                <i class="fa fa-facebook" aria-hidden="true" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/replica/{$replica_lcp_user_name}');">
                                </i>
                              </button>
                              <button type="button" class="rpl-social" onClick="twittershare('{$site_url}/replica/{$replica_lcp_user_name}');">
                                <i class="fa fa-twitter" aria-hidden="true"></i>
                              </button>
                              <a type="button" class="rpl-social" href="http://www.linkedin.com/shareArticle?url={$site_url}/replica/{$replica_lcp_user_name}" target="_blank">
                                <i class="fa fa-linkedin" aria-hidden="true"></i>
                              </a>
                             {/if}
                            </div>
                              <div class="link-replica">
                               {if DEMO_STATUS == "yes"}
                                <input type="text" class="form-control" id="" maxlength="" value="{$site_url}/replica/{$ADMIN_USER_NAME}/{$replica_lcp_user_name}">
                                <button type="button" class="btn btn-sm btn-info" data-clipboard-text="{$site_url}/replica/{$ADMIN_USER_NAME}/{$replica_lcp_user_name}" id="copy_link_replica">{lang('copy')}
                                </button>
                               {else}
                                <input type="text" class="form-control" id="" maxlength="" value="{$site_url}/replica/{$replica_lcp_user_name}">
                                <button type="button" class="btn btn-sm btn-info" data-clipboard-text="{$site_url}/replica/{$replica_lcp_user_name}" id="copy_link_replica">{lang('copy')}
                                </button>
                               {/if}
                              </div>
                          
                        </div>
                      </form>
                  </div>
                  {/if}

                    {if $MODULE_STATUS['lead_capture_status'] == "yes"}         
                        <div class="referal-link">
                            <form autocomplete="off">
                                <div class="form-group">
                                    {if DEMO_STATUS == "yes"}
                                        <div class="rep-head">
                                            <h4>{lang('lead_capture')}</h4>
                                            <button type="button" class="rpl-social"><i class="fa fa-facebook" aria-hidden="true" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/lcp/{$ADMIN_USER_NAME}/{$replica_lcp_user_name}');"></i></button>
                                            <button type="button" class="rpl-social" onClick="twittershare('{$site_url}/lcp/{$ADMIN_USER_NAME}/{$replica_lcp_user_name}');">
                                                <i class="fa fa-twitter" aria-hidden="true"></i>
                                            </button>
                                            <a type="button" class="rpl-social" href="http://www.linkedin.com/shareArticle?url={$site_url}/lcp/{$ADMIN_USER_NAME}/{$replica_lcp_user_name}" target="_blank"><i class="fa fa-linkedin"   aria-hidden="true"></i></a>
                                        </div>
                                        <div class="link-replica">
                                            <input type="text" class="form-control" id="" maxlength="" value="{$site_url}/lcp/{$ADMIN_USER_NAME}/{$replica_lcp_user_name}">
                                            <button type="button" class="btn btn-sm btn-info" data-clipboard-text="{$site_url}/lcp/{$ADMIN_USER_NAME}/{$replica_lcp_user_name}" id="copy_link_lcp">{lang('copy')}</button>
                                        </div>
                                    {else}
                                        <div class="rep-head">
                                            <h4>{lang('lead_capture')}</h4>
                                            <button type="button" class="rpl-social"><i class="fa fa-facebook" aria-hidden="true" onClick="facebookShare('https://www.facebook.com/sharer/sharer.php?u={$site_url}/lcp/{$replica_lcp_user_name}');"></i></button>
                                            <button type="button" class="rpl-social" onClick="twittershare('{$site_url}/lcp/{$replica_lcp_user_name}');">
                                            <i class="fa fa-twitter" aria-hidden="true"></i>
                                            </button>
                                            <a type="button" class="rpl-social" href="http://www.linkedin.com/shareArticle?url={$site_url}/lcp/{$replica_lcp_user_name}" target="_blank"><i class="fa fa-linkedin"   aria-hidden="true"></i></a>
                                        </div>
                                        <div class="link-replica">
                                            <input type="text" class="form-control" id="" maxlength="" value="{$site_url}/lcp/{$replica_lcp_user_name}">
                                            <button type="button" class="btn btn-sm btn-info" data-clipboard-text="{$site_url}/lcp/{$replica_lcp_user_name}" id="copy_link_lcp">{lang('copy')}</button>
                                        </div>
                                    {/if}
                                </div>
                            </form>
                        </div>
                    {/if}
                </div>
            
           
            
            <!-- <div class="social_media" id="section_social_media" >
                <a href="" target="_blank" class="list-group-item clearfix">
                    <span class="">
                        <button class="btn btn-rounded btn-lg btn-icon btn-facebook">
                            <i class="fa fa-facebook"></i>
                        </button>
                    </span>
                    <span class="clear">
                        <span>{lang('facebook_users')}</span>
                        <span class="social_iocs pull-center hidden-xs"></span>
                        <small class="text-muted clear text-ellipsis">{$social_media_info['fb_count']} +</small>
                    </span>
                </a>
                
                <a href="" target="_blank" class="list-group-item clearfix">
                    <span class="">
                        <button class="btn btn-rounded btn-lg btn-icon btn-info">
                            <i class="fa fa-twitter"></i>
                        </button>
                    </span>
                    <span class="clear">
                        <span>{lang('twitter_users')}</span>
                        <span class="social_iocs pull-center hidden-xs"></span>
                        <small class="text-muted clear text-ellipsis">{$social_media_info['twitter_count']} +</small>
                    </span>
                </a>
                <a href="" target="_blank" class="list-group-item clearfix">
                    <span class="">
                        <button class="btn btn-rounded btn-lg btn-icon btn-instagarm">
                            <i class="fa fa-instagram"></i>
                        </button>
                    </span>
                    <span class="clear">
                        <span>{lang('instagram_users')}</span>
                        <span class="social_iocs pull-center hidden-xs"></span>
                        <small class="text-muted clear text-ellipsis">{$social_media_info['inst_count']} +</small>
                    </span>
                </a>
            </div> -->
        </div>
    </div>
    
    {/block}

   <!--  {block name='country_graph'}
        <div class="panel">
            <div class="panel-body" id="section_country_graph">
               <h4 class="m-t-none m-b text-primary-lt font-thin-bold">{lang(member_joinings)}</h4>
                <div style="height: 300px" id="country_graph"></div>
            </div>
        </div>
    {/block} -->
</div>
 {/if}
 {if $promo_status=='yes'}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="panel wrapper">
              
                {assign var="next_column_class" value="two-col"}
              
            
            <div class="current-next-new {$next_column_class}">
                <div class="current-new">
                   
                    <h4 class="m-t-none m-b text-primary-lt font-thin-bold">{lang('Rank Promo')} : {date('F j, Y',strtotime($promo['promo_start_date']))} to {date('F j, Y',strtotime($promo['promo_end_date']))}</h4>
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
                            <div class="CN-four" {if $key=='group_pv'} data-toggle="modal" data-target="#groupModalCenter" {/if}><div><h4 class="text-success">{$nrank['achieved']}</h4>
                              <h5>Achieved</h5>{if $key=='group_pv'}<i class="fa fa-info-circle" aria-hidden="true"></i>{/if}</div>
                            </div><!-- CN four -->
                            {else}
                                <div class="CN-four"><div><h4 class="text-success"><h5>{format_currency($nrank['bonus_amount'])}</h4>
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
                            <div class="CN-four"><div><h4 class="text-success">{$n.achieved}</h4>
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
<div class="dashbord-tab-section">
    {block name="team_performance"}
    <div class="panel dashbord-tab">
        <div class="wrapper">
            <h4 class="font-thin m-t-none m-b-none ">{lang('team_perfomance')}</h4>
        </div>
        <div class="tabsy2">
            <input type="radio" id="tab4" name="tab2" checked="">
            <label class="tabButton2" for="tab4">{lang('top_earners')}</label>
            <div class="tab2 top-earners-scroll" id="personal_info2">
                <div class="table-responsive">
                    <table class="table user-tale top-earners-table">
                      <tbody>
                          {assign var="i" value=0 }
                        {foreach from=$top_earners item=j}                                                              
                          <tr>
                            <td class="grid-1">
                              <!-- <div class="top-earners-img">
                              <img src="{$j['profile_picture_full']}" class="r r-2x"></div>
                              <div class="top-earners-cnt">
                                <span class="text-black">{$j['user_name']}</span>
                                <span class="text-muted">{$j['balance_amount']}</span>
                              </div> -->
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

            <div class="text-center text-primary-lt"><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">{lang('no_data_found')}</h3>
            </div>

            {/if}

            </div>
            
            <input type="radio" id="tab5" name="tab2">
            <label class="tabButton2" for="tab5">{lang('top_recruiters')}</label>
            <div class="tab2 top-earners-scroll" id="personal_info2">
                <div class="table-responsive">
                    <table class="table table-hover user-tale top-earners-table">
                      <tbody>
                        {assign var="i" value=0}
                        {foreach from=$top_recruters item=j}
                         {$i=$i+1}
                         {$k=fmod($i, 4)}
                        <tr>
                          <td class="grid-1">
                            {*<div class="top-earners-img">
                             <img src="{$j['profile_picture_full']}" class="r r-2x">
                            </div>
                             <div class="top-earners-cnt"><span class="text-black">{$j.user_name}</span><span class="text-muted">{$j.count}</span>
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
                                        {$j.count}
                                        </div>
                                        </div>
                                    </div>
                            </div>
                          </td>
                        </tr>
                        {/foreach}                             
                      </tbody>
                    </table>
                </div>

            {if empty($top_recruters)}

            <div class="text-center text-primary-lt"><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">{lang('no_data_found')}</h3>
            </div>

            {/if}

           </div>

            {if $MODULE_STATUS['product_status'] == 'yes' }
            <input type="radio" id="tab6" name="tab2">
            <label class="tabButton2" for="tab6">{lang('package_overview')}</label>
                <div class="tab2" id="contact_info2">
                    <table class="table user-tale">
                        <tbody>
                        {$j=0}
                        {foreach from=$prgrsbar_data item=v}
                        <tr>
                          <td><h5 class="text-md">{$v.package_name}</h5><p>{lang('you_have')} {$v.joining_count} {$v.package_name} {lang('package_purchases_in_your_team')} </p></td>
                          <td><span class="comm-type btn btn-info">{$v.joining_count}</span></td>
                        </tr>
                       {$j=$j+1}
                       {/foreach}                         
                      </tbody>
                    </table>
                </div>
            {/if}

            {if empty($prgrsbar_data)}

           <div class="text-center text-primary-lt"><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">{lang('no_data_found')}</h3>
            </div>

            {/if}


            {if $MODULE_STATUS['rank_status'] == 'yes' }  

            <input type="radio" id="tab7" name="tab2">
            <label class="tabButton2" for="tab7">{lang('rank_overview')}</label>
            <div class="tab2" id="social_profiles2">
               <table class="table user-tale">
                  <tbody>
                    <tr>
                       {$j=0}
                      {foreach from=$rank_data item=v}
                      <td><h5 class="text-md">{$v.rank_name}</h5><p>{lang('you_have')} {$v.count} {$v.rank_name} {lang('rank_in_your_team')}</p></td>
                      <td><span class="comm-type btn btn-info">{$v.count}</span></td>
                    </tr>
                   {$j=$j+1}
                    
                   {/foreach}                              
                  </tbody>
                </table>

            {if empty($rank_data)}

            <div class="text-center text-primary-lt"><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">{lang('no_data_found')}</h3>
            </div>

            {/if}

            </div>

            {/if}

        </div>
    </div>
    {/block}

    {block name="income_and_commission"}
        <div class="panel dashbord-tab">
            <div class="wrapper">
                <h4 class="font-thin m-t-none m-b-none ">{lang('income')} &amp; {lang('commission')}</h4>
            </div>
            <div class="tabsy">
            <input type="radio" id="tab1" name="tab" checked="">
            <label class="tabButton" for="tab1">{lang('income')}</label>
            <div class="tab" id="personal_info">

            <div class="table-responsive">
                  <table class="table user-tale">
                    <tbody>
                       {foreach from=$incomeandCommission['income'] item=v}
                       {assign var="str" value="{lang("bs_`$v.type`")}"}
                       {$words = explode(' ', $str)}
                       <tr>
                          <td>   
                            {lang("bs_`$v.type`")}      
                          </td>
                          <td>
                            <span class="text-md text-success">{$DEFAULT_SYMBOL_LEFT}{($v.amount*$DEFAULT_CURRENCY_VALUE)|round:2}{$DEFAULT_SYMBOL_RIGHT}</span>
                          </td>
                          <td>
                            <span class="comm-type btn btn-info">{$words[0][0]}{if isset($words[1][0])}{$words[1][0]}{/if}</span>
                          </td>
                        </tr>
                        {/foreach}
                    </tbody>
                  </table>
                </div>

            {if empty($incomeandCommission['income'])}

            <div class="text-center text-primary-lt"><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">{lang('no_data_found')}</h3>
            </div>

            {/if}

            </div>
            <input type="radio" id="tab2" name="tab">
            <label class="tabButton" for="tab2">{lang('commission')}</label>
            <div class="tab" id="contact_info">

            <div class="table-responsive">
                  <table class="table user-tale">
                    <tbody>
                      {foreach from=$incomeandCommission['commission'] item=v}
                      {assign var="str" value="{lang("bs_`$v.type`")}"}
                       {$words = explode(' ', $str)}
                       <tr>
                          <td valign="v-middle">
                            {lang("bs_`$v.type`")}      
                          </td>
                          <td>
                            <span class="text-md text-success">{$DEFAULT_SYMBOL_LEFT}{($v.amount*$DEFAULT_CURRENCY_VALUE)|round:2}{$DEFAULT_SYMBOL_RIGHT}</span>
                          </td>
                          <td>
                            <span class="comm-type btn btn-info">{$words[0][0]}{if isset($words[1][0])}{$words[1][0]}{/if}</span>
                          </td>
                        </tr>
                        {/foreach}
                    </tbody>
                  </table>
                </div>

            {if empty($incomeandCommission['commission'])}

            <div class="text-center text-primary-lt"><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">{lang('no_data_found')}</h3>
            </div>

            {/if}


             </div>
              <input type="radio" id="tab3" name="tab">
            <label class="tabButton" for="tab3">{lang('Pools')}</label>
            <div class="tab" id="contact_info">

            <div class="table-responsive">
                  <table class="table user-tale">
                    <tbody>
                      {foreach from=$rank_pool item=v}
                       <tr>
                          <td valign="v-middle">
                            {$v.rank_name}      
                          </td>
                          <td>
                            <span class="text-md text-success">{$DEFAULT_SYMBOL_LEFT}{($v.amount*$DEFAULT_CURRENCY_VALUE)|round:2}{$DEFAULT_SYMBOL_RIGHT}</span>
                          </td>
                          
                        </tr>
                        {/foreach}
                    </tbody>
                  </table>
                </div>

            {if empty($rank_pool)}

            <div class="text-center text-primary-lt"><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                <h3 class="no_data">{lang('no_data_found')}</h3>
            </div>

            {/if}


             </div>
          </div>
        </div>
    {/block}
</div>

<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" id="reservation_detail_model" data-backdrop="static"
    class="modal fade" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">x</button>
                <h4 class="modal-title" id="modaltitle"></h4>
            </div>
            <div class="modal-body" id="reservation_detail_model_body">
            </div>
        </div>
    </div>
</div>


<!---POPUP DESIGN--->
 
{if $DEMO_STATUS == 'yes' && $from_login}
    {if $is_preset_demo}
        <div class="demo-note">
            <button class="close" type="button"><i class="icon-close"></i></button>
            <div class="demo-note-container">
                <div class="info-icon">
                    <div><i class="fa fa-info" aria-hidden="true"></i></div>
                </div>
                <div class="demo-note-cnt">
                    <p class="">You are viewing shared demo. Multiple users may try this demo simultaneously. Try<a class="text-info" href="https://infinitemlmsoftware.com/register.php" target="_blank" > Custom Demo </a>as per your configurations</p>
                </div>
            </div>
        </div>
    {/if}
{/if}
<!---END POPUP DESIGN-->

<style>
.demo_section {
    
    display: none ;
}
.setting_margin_top {    
        margin-top: -50px;
        top: -10px;
        position: relative;
}
.setting_margin{
        margin-left: 260px;
}
.wrapper_index{
    
    padding:15px
}
.demo_margin_top {
    
    margin-top: -30px;
}
.modal-content_1 {
     background-color: #c713138c;
    
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
}

.notepopup .modal-dialog.modal-md.index {
       width: 460px;
    position: relative;
    right: auto;
    top: 20%;
    max-width: 95%!important;
    border-radius: 4px;
    overflow: hidden;
}
.notepopup .modal-content_1 {
    background-color: #f7f7f7;
    border: none;
}
.modal-body{
    padding: 10px;
    /*display: grid;*/
    grid-template-columns: 55px auto;
  
}

@media (max-width:767px)
{
.setting_margin_top {
    margin-top: -30px !important;
}
}

#right_section {
    display: none;
}


</style>
{/block}

{block name=right_content}

<div class="col w-md  bg-auto no-border-xs b-r" id="right_section">
   {block name=new_members}
    <div class="b-l bg-white  tab-content" id="right_section_new_member">
        <div role="tabpanel" class="tab-pane active" id="tab-1">
            <div class="wrapper-md">
                <div class="bg-primary text-center wrapper-sm m-l-n-new m-r-n">{lang('new_members')|capitalize}</div>
                <ul class="list-group no-bg no-borders pull-in list_link">
                    {assign var="i" value=0 }
                    {foreach from=$latest_joinees item=j}
                        <li class="list-group-item">
                            <a href="javascript:void(0);" class="pull-left thumb-sm m-r">
                                <img src="{$j['profile_picture_full']}">
                            </a>
                            <div class="clear">
                                <div><a href="javascript:void(0);">{$j['user_name']}</a></div>
                                <span class="text-muted">{$j['date_of_joining']}</span>
                            </div>
                        </li>
                        {$i=$i+1}
                    {/foreach}
                    {if $i==0}
                        <div class="text-center text-primary-lt" ><img class="nodataimg" src="{$SITE_URL}/uploads/images/logos/no-datas-found.png"> 
                        <h3 class="no_data">
                            {lang('no_data_found')}</h3>
                        </div>
                    {/if}
                </ul>
            </div>
        </div>
    </div>
    {/block}
    {block name=top_recruiters}
    <div class="b-l bg-white padder-md height_top_recruiters" id="right_section_top_recruiter">
        <div class="streamline  m-b">
            <div class="bg-primary wrapper-sm m-l-n m-r-n m-b text-center">{lang('top_recruiters')}</div>
            {assign var="i" value=0}
            {foreach from=$top_recruters item=j}
                {$i=$i+1}
                {$k=fmod($i, 4)}
                <div class="sl-item b-l {if $k==1}b-primary{elseif $k==2}b-warning{elseif $k==3}b-info{/if}">
                    <div class="m-l margin-list-mobile">
                        <li class="list-group-item">
                            <a href="javascript:void(0);" class="pull-left thumb-sm m-r">
                                <img src="{$j['profile_picture_full']}">
                            </a>
                            <div class="clear">
                                <div><a href="javascript:void(0);">{$j.user_name}</a></div>
                                <span class="text-muted">{$j.count}</span>
                            </div>
                        </li>
                    </div>
                </div>
            {/foreach}
            {if $i==0}
                <div class="sl-item b-l b-primary">
                    <div class="m-l">
                        <li class="list-group-item">{lang('no_data_found')}</li>
                    </div>
                </div>
            {/if}
        </div>
        
    </div>
   {/block}
</div>

  
{/block}

{block name=home_wrapper_out}
    {include file="admin/configuration/system_setting_common.tpl"}
    {include file="layout/demo_footer.tpl"}
{/block}
