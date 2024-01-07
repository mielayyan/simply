<div class="wrapper_index">
<div class="region  panel setting_margin  setting_margin_top">
    <div id="block-block-12" class="block block-block contextual-links-region clearfix">
    {$class_setting = ""}
    {if $CURRENT_URL == "home/index"}
        {$class_setting = "features-quick-access-home"}
    {/if}
        <div class="{$class_setting} features-quick-access">
            <div class="hbox text-center b-light text-sm bg-white">
                
                <a href="{$BASE_URL}admin/profile_setting" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/profile_setting'} setting-selected {/if}">
                    <i class="fa fa-male block m-b-xs fa-2x"></i>
                    <span>{lang('profile')}</span>
                </a>

               {if $MODULE_STATUS['multy_currency_status'] == 'yes'}
                 <a href="{$BASE_URL}admin/currency/currency_management" class="col padder-v text-muted {if $CURRENT_URL == 'currency/currency_management'} setting-selected {/if}">
                    <i class="fa fa-money block m-b-xs fa-2x"></i>
                    <span>{lang('currency')}</span>
                 </a>
                {/if}

                {if $MODULE_STATUS['lang_status'] == 'yes'}
                    <a href="{$BASE_URL}admin/language_settings" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/language_settings'} setting-selected {/if}">
                        <i class="fa fa-language block m-b-xs fa-2x"></i>
                        <span>{lang('language')}</span>
                    </a>
                {/if}

                {if $MODULE_STATUS['pin_status'] == 'yes'}
                <a href="{$BASE_URL}admin/pin_config" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/pin_config'} setting-selected {/if}">
                    <i class="fa fa-tags block m-b-xs fa-2x"></i>
                    <span>{lang('epin')}</span>
                </a>
                {/if}

                {if $MODULE_STATUS['signup_config'] == 'yes'}
                    <a href="{$BASE_URL}admin/custome_field" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/custome_field'} setting-selected {/if}">
                        <i class="fa fa-plus-square block m-b-xs fa-2x"></i>
                        <span>{lang('custome_field')}</span>
                    </a>
                {/if}
                
                <a href="{$BASE_URL}admin/user_dashboard" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/user_dashboard'} setting-selected {/if}">
                    <i class="fa fa-tachometer block m-b-xs fa-2x"></i>
                    <span>{lang('user_dashboard')}</span>
                </a>
                
                <a href="{$BASE_URL}admin/tooltip_settings" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/tooltip_settings'} setting-selected {/if}">
                    <i class="fa fa-arrows block m-b-xs fa-2x"></i>
                    <span>{lang('tree')}</span>
                </a>
            </div>
        </div>
    </div>
</div>
</div>
<style>
a {
    word-break: normal;
}
</style>
