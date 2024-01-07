<!DOCTYPE html>
<html lang="en" class="">

<head>
    <title>{$title}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale = 1.0, minimum-scale = 1.0, maximum-scale = 5.0, user-scalable = yes">

    {*!-- start: THEME STYLES --*}
    <link rel="shortcut icon" type="image/png" href="{$SITE_URL}/uploads/images/logos/{$site_info["favicon"]}" />
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/libs/assets/animate.css/animate.css" type="text/css" />
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/libs/assets/font-awesome/css/font-awesome.min.css" type="text/css" />
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/libs/assets/simple-line-icons/css/simple-line-icons.css" type="text/css" />
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/libs/jquery/bootstrap/dist/css/bootstrap.css" type="text/css" />
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/font.css" type="text/css" />
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/app.css" type="text/css" />
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/custom.css" type="text/css" />
    {*!-- end: THEME STYLES --*}

    {block name=style}{/block}
</head>

<body>
    {*!-- start: HIDDEN INPUTS --*}
    <input type = "hidden" name = "base_url" id = "base_url" value = "{$BASE_URL}" />
    <input type = "hidden" name = "img_src_path" id="img_src_path" value="{$PUBLIC_URL}"/>
    <input type = "hidden" name = "current_url" id = "current_url" value = "{$CURRENT_URL}" />
    <input type = "hidden" name = "current_url_full" id = "current_url_full" value = "{$CURRENT_URL_FULL}" />
    <input type = "hidden" name = "DEFAULT_CURRENCY_VALUE" id="DEFAULT_CURRENCY_VALUE" value="{$DEFAULT_CURRENCY_VALUE}"/>
    <input type = "hidden" name = "DEFAULT_CURRENCY_CODE" id="DEFAULT_CURRENCY_CODE" value="{$DEFAULT_CURRENCY_CODE}"/>
    <input type = "hidden" name = "DEFAULT_SYMBOL_LEFT" id="DEFAULT_SYMBOL_LEFT" value="{$DEFAULT_SYMBOL_LEFT}"/>
    <input type = "hidden" name = "DEFAULT_SYMBOL_RIGHT" id="DEFAULT_SYMBOL_RIGHT" value="{$DEFAULT_SYMBOL_RIGHT}"/>
     <input type = "hidden" name = "DEFAULT_PRECISION" id="DEFAULT_PRECISION" value="{$PRECISION}"/>
    {if $LOG_USER_ID}
    <input type = "hidden" name = "logout_time" id="logout_time" value="{$Logout_time}"/>
    {/if}

    {$left_symbol = NULL}
    {$right_symbol = NULL}
    {$input_group_hide = "input-group-hide"}
    {if $DEFAULT_SYMBOL_LEFT}
        {$input_group_hide = ""}
        {$left_symbol = "<span class='input-group-addon'>$DEFAULT_SYMBOL_LEFT</span>"}
    {/if}
    {if $DEFAULT_SYMBOL_RIGHT}
        {$input_group_hide = ""}
        {$right_symbol = "<span class='input-group-addon'>$DEFAULT_SYMBOL_RIGHT</span>"}
    {/if}
    <input type="hidden" name="input_group_hide" id="input_group_hide" value="{$input_group_hide}">

    <input type="hidden" id="system_start_date" value="{$system_start_date}"/>
    <input type="hidden" name="data_table_language" id="data_table_language" value='{$data_table_language}'/>
    <input type="hidden" name="daterangepicker_language" id="daterangepicker_language" value='{$daterangepicker_language}'/>
    <input type="hidden" name="daterangepicker_ranges_language" id="daterangepicker_ranges_language" value='{$daterangepicker_ranges_language}'/>
    {*!-- end: HIDDEN INPUTS --*}

    <div class="app app-header-fixed ">
        {block name=header}{/block}
        {block name=sidebar}{/block}
        {block name=content}{/block}
        {block name=footer}{/block}
        {block name=theme_setting}{/block}
    </div>

    {*!-- start: THEME SCRIPTS --*}
    <script>
        {if isset($validations)}
            window.translations = {json_encode($validations)};
        {/if}
    </script>
    <script src="{$PUBLIC_URL}theme/newui/js/jquery.min.js"></script>
    <script>
        $(function() {
            $.ajaxSetup({
                data: {
                    {$CSRF_TOKEN_NAME}: "{$CSRF_TOKEN_VALUE}"
                }
            });
            themeSettingData = {$THEME_SETTING};
        });
    </script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{$PUBLIC_URL}theme/libs/jquery/bootstrap/dist/js/bootstrap.js"></script>
    <script src="{$PUBLIC_URL}theme/js/ui-load.js"></script>
    <script src="{$PUBLIC_URL}theme/js/ui-jp.config.js"></script>
    <script src="{$PUBLIC_URL}theme/js/ui-jp.js"></script>
    <script src="{$PUBLIC_URL}theme/js/ui-nav.js"></script>
    <script src="{$PUBLIC_URL}theme/js/ui-toggle.js"></script>
    <script src="{$PUBLIC_URL}theme/js/ui-client.js"></script>
    <script src="{$PUBLIC_URL}theme/js/theme-setting.js" type="text/javascript"></script>
    <script src="{$PUBLIC_URL}theme/js/custom.js" type="text/javascript"></script>
    {*!-- end: THEME SCRIPTS --*}
    <script src="{$PUBLIC_URL}plugins/jquery-validation/dist/jquery.validate.min.js"></script>
    <script src="{$PUBLIC_URL}javascript/newui/main.js"></script>
    
    {block name=script}{/block}

</body>

</html>
