{extends file='newui/layout/admin.tpl'}

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
                    <span class="top-widget-text">{lang('Payout Released')}</span>
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
                    <span class="top-widget-text">{lang('Payout Pending')}</span>
                    </div>
                </div>
            </div>
        {/block}
    </div> 
</div>



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
