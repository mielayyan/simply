{extends file="layout/app.tpl"}

{block name=user_style}
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/user_theme.css" type="text/css" />
{/block}

{block name=script}
    <script src="{$PUBLIC_URL}javascript/timer.js" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}javascript/auto_timeout.js" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}javascript/currency.js" type="text/javascript" ></script>
    <style type="text/css">
        .margin-zero
        {
            margin-left: unset !important;
        }
    </style>
{/block}
{if $CURRENT_URL != "login/reset_tran_password"}
{block name=header}
    {include file="layout/user_header.tpl"}
{/block}

{block name=sidebar}
    {include file="layout/sidebar_user.tpl"}
{/block}
{/if}

{block name=content}
    <!-- content -->
    <div id="content"  class="app-content {if $CURRENT_URL == 'login/reset_tran_password'} margin-zero {/if}"  role="main">
        <div class="app-content-body ">
            <div class="hbox hbox-auto-xs hbox-auto-sm">
                
                  {block name=page_header}
                        <!-- main header -->
                        {if $HEADER_LANG['page_top_header']}
                            <div class="bg-light lter b-b wrapper-md new-title-section">
                                <!-- <h1 class="m-n font-thin h3">{$HEADER_LANG['page_top_header']}</h1>
                                -->
                                <div class="header-user-title"><h1 class="m-n font-thin h3">{$HEADER_LANG['page_top_header']}</h1></div>
                                {block name=overview} {/block}
                               
                            </div>
                        {/if}
                        <!-- / main header -->
                    {/block}
                    <div class="wrapper-md">
                    {if $CURRENT_URL != 'mail/mail_management'&& $CURRENT_URL != 'mail/mail_sent' && $CURRENT_URL != 'mail/compose_mail'}
                        {include file="layout/alert_box.tpl"}
                    {/if}
                    {block name=main}{/block}
                
            </div>
        {block name=right_content}{/block}
    </div>
    {if $CURRENT_URL !="home/index" && $CURRENT_URL !="mail/mail_management"&& $CURRENT_URL !="mail/compose_mail" && $CURRENT_URL !="mail/read_mail" && $CURRENT_URL !="mail/reply_mail" && $CURRENT_URL !="mail/mail_sent"}
        {include file="layout/demo_footer.tpl"}
    {/if}     
</div>

</div>
<!-- /content -->
{/block}
{if $CURRENT_URL != "login/reset_tran_password"}
{block name=footer}
    {block name=home_wrapper_out}{/block}
    {include file="layout/footer.tpl"}
{/block}

    {*{block name=theme_setting}
             {include file="layout/theme_setting.tpl"} 
        {/block}*}
{/if}
