<!-- header -->
<header id="header" class="app-header navbar" role="menu">
    <!-- navbar header -->
    <div class="navbar-header bg-dark">
        <button class="pull-right visible-xs dk" ui-toggle-class="show" target=".navbar-collapse">
            <i class="glyphicon glyphicon-cog"></i>
        </button>
        <button class="pull-right visible-xs" ui-toggle-class="off-screen" target=".app-aside" ui-scroll="app">
            <i class="glyphicon glyphicon-align-justify"></i>
        </button>
        <!-- brand -->
        <a href="{$BASE_URL}user/home/index" class="navbar-brand text-lt">
            {* <i class="fa fa-btc"></i> *}
            <img src="{$SITE_URL}/uploads/images/logos/{$site_info["logo"]}" alt="." class="">
            <img src="{$SITE_URL}/uploads/images/logos/logo_icon.png" alt="logo" class="logo-shrink">
            {* <span class="hidden-folded m-l-xs">Angulr</span> *}
        </a>
        <!-- / brand -->
    </div>
    <!-- / navbar header -->

    <!-- navbar collapse -->
    <div class="collapse pos-rlt navbar-collapse box-shadow bg-white-only">
        <!-- buttons -->
        <div class="nav navbar-nav hidden-xs">
            <a href="#" class="btn no-shadow navbar-btn" ui-toggle-class="app-aside-folded" target=".app">
                <i class="fa fa-dedent fa-fw text"></i>
                <i class="fa fa-indent fa-fw text-active"></i>
            </a>
        <!--  <a href="#" class="btn no-shadow navbar-btn" ui-toggle-class="show" target="#aside-user">
                <i class="icon-user fa-fw"></i>
            </a> -->
        </div>
        <!-- / buttons -->

        <ul class="nav navbar-nav">
            {if $MODULE_STATUS['multy_currency_status']=='yes'}
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                    {$DEFAULT_SYMBOL_LEFT}{$DEFAULT_SYMBOL_RIGHT}
                    <span class="visible-xs-inline">{lang('change_your_currency')}</span>
                    <b class="caret"></b>
                </a>
                <!-- dropdown -->
                <ul class="dropdown-menu animated fadeInRight">
                    {foreach from=$CURRENCY_ARR item=v}
                    <li>
                        <a href="javascript:switchCurrency('{$v.id}');">{$v.symbol_left}{$v.title}{$v.symbol_right}</a>
                    </li>
                    {/foreach}
                </ul>
                <!-- / dropdown -->
            </li>
            {/if}

            {if $LANG_STATUS=='yes'}
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle width_flag">
                    {foreach from=$LANG_ARR item=v}
                        {if $LANG_ID == $v.lang_id}
                            <img src="{$PUBLIC_URL}images/flags/{$v.lang_code}.png" />
                        {/if}
                    {/foreach}
                    <span class="visible-xs-inline">{lang('change_your_language')}</span>
                    <b class="caret"></b>
                </a>
                <!-- dropdown -->
                <ul class="dropdown-menu animated fadeInRight">
                    {foreach from=$LANG_ARR item=v}
                    <li>
                        <a href="javascript:changeDefaultLanguage('{$v.lang_id}');">
                            <img src="{$PUBLIC_URL}images/flags/{$v.lang_code}.png" /> {$v.lang_name}
                        </a>
                    </li>
                    {/foreach}
                </ul>
                <!-- / dropdown -->
            </li>
            {/if}
        </ul>

        <!-- navbar right -->
        <ul class="nav navbar-nav navbar-right">
            <li class="dropdown" style="line-height:50px;vertical-align: middle;color:#737f86">	
            <span class="date" style="padding: 0px 0px 0px 10px;">
                            <timestamp id="date"></timestamp> 
                        </span>
                <div id="clock" style="float: right;margin-left:5px"></div>
                <div style="float: left;padding: 0px 10px 0px 0px;">
                    <input value="{$SERVER_TIME}" id="serverClock_input" hidden>
                    <input value="{$SERVER_DATE}" id="serverDate_input" hidden>
                    <span class="date">
                        <timestamp id="server_date"></timestamp> 
                    </span>
                    <div id="server_clock" style="float: right;margin-left:5px"></div> 
                </div>
            </li>  
            {if $LOG_USER_TYPE != 'Unapproved'}
            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                    <i class="fa fa-fw fa-envelope-o"></i>
                    <span class="visible-xs-inline">{lang('mail_notification')}</span>
                    {if $unread_mail>0}
                    <span class="badge badge-sm up bg-danger pull-right-xs">{$unread_mail}</span>
                    {/if}
                </a>
                <!-- dropdown -->
                <div class="dropdown-menu w-xl animated fadeInUp">
                    <div class="panel bg-white">
                        <div class="panel-heading b-light bg-light">
                            <strong>
                                {if $unread_mail>0}
                                {lang('you_have_n_mail')|sprintf:$unread_mail}
                                {else}
                                {lang('you_have_no_new_mail')}
                                {/if}
                            </strong>
                        </div>
                        <div class="list-group">
                            {foreach from=$mail_content item=v}
                            <a href="{$BASE_URL}user/mail/mail_management" class="list-group-item">
                                <span class="pull-left m-r thumb-sm">
                                    <img  src="{$SITE_URL}/uploads/images/profile_picture/{$v.image}" alt="..." class="img-circle">
                                </span>
                                <span class="clear block m-b-none">
                                    {$v.username}<br>
                                    {$v.mailadsubject}<br>
                                    <small class="text-muted">{$v.mailadiddate}</small>
                                </span>
                            </a>
                            {/foreach}
                        </div>
                        <div class="panel-footer text-sm">
                            <a href="{$BASE_URL}user/mail/mail_management" class="pull-right"><i class="fa fa-cog"></i></a>
                            <a href="{$BASE_URL}user/mail/mail_management" data-toggle="class:show animated fadeInRight">{lang('see_all_messages')}</a>
                        </div>
                    </div>
                </div>
                <!-- / dropdown -->
            </li>

            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle">
                    <i class="fa fa-fw fa-bell-o"></i>
                    <span class="visible-xs-inline">{lang('notification')}</span>
                    {if $notification_count>0}
                    <span class="badge badge-sm up bg-danger pull-right-xs">{$notification_count}</span>
                    {/if}
                </a>
                <!-- dropdown -->
                <div class="dropdown-menu w-xl animated fadeInUp">
                    <div class="panel bg-white">
                        <div class="panel-heading b-light bg-light">
                            <strong>
                                {if $notification_count>0}
                                {$notification_count} {lang('missed_notification')}
                                {else}
                                {lang('you_have_no_notification')}
                                {/if}
                            </strong>
                        </div>
                        <div class="list-group">
                            {if $payout_count>0}
                            <a href="{$BASE_URL}user/payout/payout_release_request" class="list-group-item">
                                <i class="fa fa-fw fa-bullhorn"></i>
                                {lang('you_have_n_payout_released')|sprintf:$payout_count}
                            </a>
                            {/if}
                            {if $pin_count>0 && $MODULE_STATUS['pin_status']=="yes"}
                            <a href="{$BASE_URL}user/epin/my_epin" class="list-group-item">
                                <i class="fa fa-fw fa-bell"></i>
                                {lang('you_have_n_epin_confirmed')|sprintf:$pin_count}
                            </a>
                            {/if}
                            {if $feedback_count>0}
                            <a href="{$BASE_URL}user/document/download_document" class="list-group-item">
                                <i class="fa fa-fw fa-film"></i>
                                {* {lang('you_have_n_feedback')|sprintf:$feedback_count} *}
                                # {$feedback_count} {lang('documents')}
                            </a>
                            {/if}
                            {if $news_count>0}
                            <a href="{$BASE_URL}user/view_news" class="list-group-item">
                                <i class="fa fa-fw fa-newspaper-o"></i>
                                {lang('you_have_n_news')|sprintf:$news_count}
                            </a>
                            {/if}
                        </div>
                    </div>
                </div>
                <!-- / dropdown -->
            </li>
            {/if}

            <li class="dropdown">
                <a href="#" data-toggle="dropdown" class="dropdown-toggle clear" data-toggle="dropdown">
                    {if $LOG_USER_TYPE!='employee'}
                    <span class="thumb-sm avatar pull-right m-t-n-sm m-b-n-sm m-l-sm">
                        <img  id="header_pro_pic" src="{$SITE_URL}/uploads/images/profile_picture/{$profile_pic}" alt="...">
                        <i class="on md b-white bottom"></i>
                    </span>
                    {/if}
                    <span class="hidden-sm hidden-md">{$LOG_USER_NAME}</span>
                    <b class="caret"></b>
                </a>
                <!-- dropdown -->
                <ul class="dropdown-menu animated fadeInRight w">
                    {if $LOG_USER_TYPE != 'Unapproved'}
                    <li>
                        <a href="{$PATH_TO_ROOT}user/profile/profile_view">
                            <span>{lang('profile_management')}</span>
                        </a>
                    </li>
                    <li>
                        <a href="{$PATH_TO_ROOT}register/user_register">
                            <span>{lang('signup')}</span>
                        </a>
                    </li>
                    {/if}
                    {if $HELP_STATUS==='yes' && DEMO_STATUS == 'yes'}
                    <li>
                        <a href="https://infinitemlmsoftware.com/help" target="_blank">
                            <span>{lang('help')|ucfirst}</span>
                        </a>
                    </li>
                    {/if}
                    <li class="divider"></li>
                    <li>
                        <a href="{$PATH_TO_ROOT}login/logout">
                            <span>{lang('logout')}</span>
                        </a>
                    </li>
                </ul>
                <!-- / dropdown -->
            </li>

        </ul>
        <!-- / navbar right -->
    </div>
    <!-- / navbar collapse -->
</header>
<!-- / header -->