<div class="wrapper_index">
<div class="region  panel setting_margin  setting_margin_top">
    <div id="block-block-12" class="block block-block contextual-links-region clearfix">
	{$class_setting = ""}
	{if $CURRENT_URL == "home/index"}
		{$class_setting = "features-quick-access-home"}
	{/if}
        <div class="{$class_setting} features-quick-access">
            <div class="hbox text-center b-light text-sm bg-white">
                <a href="{$BASE_URL}admin/commission_settings" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/commission_settings'} setting-selected {/if}">
                    <i class="fa fa-desktop block m-b-xs fa-2x"></i>
                    <span>{lang('commission')}</span>
                </a>
                <a href="{$BASE_URL}admin/compensation_settings" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/compensation_settings'} setting-selected {/if}">
                    <i class="fa fa-calculator block m-b-xs fa-2x"></i>
                    <span>{lang('compensation')}</span>
                </a>
                {if $MLM_PLAN == 'Board' || $MLM_PLAN == 'Matrix'}
                    <a href="{$BASE_URL}admin/plan_settings" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/plan_settings'} setting-selected {/if}">
                        <i class="fa fa-cogs block m-b-xs fa-2x"></i>
                        <span>
                            {if $MLM_PLAN == 'Matrix'}
                                {lang('matrix')}
                            {elseif $MLM_PLAN == 'Board' && $MODULE_STATUS['table_status'] == 'yes'}
                                {lang('table')}
                            {else}
                                {lang('board')}
                            {/if}
                        </span>
                    </a>
                {/if}
                {if $MLM_PLAN == 'Stair_Step'}
                <a href="{$BASE_URL}admin/stairstep_configuration" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/stairstep_configuration'} setting-selected {/if}">
                    <i class="fa fa-sticky-note block m-b-xs fa-2x"></i>
                    <span>{lang('stairstep')}</span>
                </a>
                {/if}
                {if $MLM_PLAN == 'Donation'}
                <a href="{$BASE_URL}admin/donation_configuration" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/donation_configuration'} setting-selected {/if}">
                    <i class="fa fa-gift block m-b-xs fa-2x"></i>
                    <span>{lang('donation')}</span>
                </a>
                {/if}
                {if $MODULE_STATUS['rank_status'] == 'yes'}
                <a href="{$BASE_URL}admin/rank_configuration" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/rank_configuration'} setting-selected {/if}">
                    <i class="fa fa-trophy block m-b-xs fa-2x"></i>
                    <span>{lang('rank')}</span>
                </a>
                {/if}
                <a href="{$BASE_URL}admin/payout_setting" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/payout_setting'} setting-selected {/if}">
                    <i class="fa fa-history block m-b-xs fa-2x"></i>
                    <span>{lang('payout')}</span>
                </a>
                <a href="{$BASE_URL}admin/payment_view" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/payment_view'} setting-selected {/if}">
                    <i class="fa fa-credit-card block m-b-xs fa-2x"></i>
                    <span>{lang('payment')}</span>
                </a>
                {if $MODULE_STATUS['signup_config'] == 'yes'}
                <a href="{$BASE_URL}admin/signup_settings" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/signup_settings'} setting-selected {/if}">
                    <i class="fa fa-user-plus block m-b-xs fa-2x"></i>
                    <span>{lang('signup')}</span>
                </a>
                {/if}
                {if $MODULE_STATUS['subscription_status'] == 'yes'}
                <a href="{$BASE_URL}admin/subscription_config" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/subscription_config'} setting-selected {/if}">
                    <i class="fa fa-refresh block m-b-xs fa-2x"></i>
                    <span>{lang('subscription')}</span>
                </a>
                {/if}
                
                <a href="{$BASE_URL}admin/mail_settings" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/mail_settings'} setting-selected {/if}">
                    <i class="fa fa-envelope block m-b-xs fa-2x"></i>
                    <span>{lang('mail')}</span>
                </a>
                   <a href="{$BASE_URL}admin/api_credentials" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/generate_api_key'} setting-selected {/if}">
                    <i class="fa fa-key block m-b-xs fa-2x"></i>
                    <span>{lang('api_credential')}</span>
                </a>

                <a href="{$BASE_URL}admin/agent_settings" class="col padder-v text-muted {if $CURRENT_URL == 'configuration/agent_settings'} setting-selected {/if}">
                    <i class="fa fa-group m-b-xs fa-2x"></i></br>
                    <span>{lang('agents')}</span>
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
