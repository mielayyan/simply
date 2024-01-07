{extends file="newui/layout/app.tpl"}

{block name=script}
    <script src="{$PUBLIC_URL}javascript/timer.js" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}javascript/auto_timeout.js" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}javascript/currency.js" type="text/javascript" ></script>
{/block}

{block name=header}
    {include file="newui/layout/admin_header.tpl"}
{/block}

{block name=sidebar}
    {include file="newui/layout/sidebar.tpl"}
{/block}

{block name=content}
    <!-- content -->
    <div id="content" class="app-content" role="main">
        <div class="app-content-body ">
            <div class="hbox hbox-auto-xs hbox-auto-sm">
                <div class="wrapper-md">
                    {block name=main}{/block}
                </div>
            </div>
        </div>
    </div>
    <!-- /content -->
{/block}

{block name=footer}
    {include file="newui/layout/footer.tpl"}
{/block}
{block name=theme_setting}
        <style>
            .settings {
                right : -245px;
                top : 98px;
            }
            @media (min-width : 1280px) {
                .settings {
                    top : 167px;
                }
            }
        </style>
         {include file="layout/theme_setting.tpl"} 
{/block}
