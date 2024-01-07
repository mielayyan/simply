{extends file="newui/layout/app.tpl"}

{block name=user_style}
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/user_theme.css" type="text/css" />
{/block}

{block name=script}
    <script src="{$PUBLIC_URL}javascript/timer.js" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}javascript/auto_timeout.js" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}javascript/currency.js" type="text/javascript" ></script>
{/block}

{block name=header}
    {include file="newui/layout/user_header.tpl"}
{/block}

{block name=sidebar}
    {include file="newui/layout/sidebar_user.tpl"}
{/block}

{block name=content}
    <!-- content -->
    <div id="content" class="app-content" role="main">
        <div class="app-content-body ">
            <div class="hbox hbox-auto-xs hbox-auto-sm">
                    <div class="wrapper-md">
                    {block name=main}{/block}
                
            </div>
        {block name=right_content}{/block}
    </div>
</div>

</div>
<!-- /content -->
{/block}

{block name=footer}
    {block name=home_wrapper_out}{/block}
    {include file="newui/layout/footer.tpl"}
{/block}

    {block name=theme_setting}
         {include file="layout/theme_setting.tpl"} 
    {/block}
