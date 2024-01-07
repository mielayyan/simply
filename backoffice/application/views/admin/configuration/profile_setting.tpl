{extends file=$BASE_TEMPLATE}
{block name = "style"}
<style type="text/css">
    .slider-selection {
        position: absolute;
        background-color: #4cc0c1;
        border: 1px solid #4cc0c1;
        border-radius: 5px;
    }
    .slider-track {
        position: absolute !important;
        cursor: pointer !important;
        background-color: #fff !important;
        border: 6px solid #eee !important;
        border-radius: 5px;
    }
    .slider.slider-horizontal .slider-track {
    height: 6px;
    width: 100%;
    margin-top: -6px;
    top: 50%;
    left: 0;
    }
    .slider-handle {
    background-image: linear-gradient(to bottom,#fff 0,#fff 100%);
    border: 1px solid #ccc;

    }
</style>
{/block}
{block name=$CONTENT_BLOCK}
{include file="admin/configuration/advanced_settings.tpl"}
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/configuration/update_profile_setting','role="form" class="" method="post" name="signup_form" id="profile_form"')}
            {include file="layout/error_box.tpl"}
            <h3>{lang('profile')}</h3>
            <div class="form-group">
                <label class="required">{lang('auto_logout_after')}</label>
                <select type="text" class="form-control m-b" name="logout_time" id="logout_time" value={set_select('logout_time')}>
                    <option {if $prev_time eq "300"} selected {/if} value="300">{5} {lang('n_minutes_of_inactivity')}</option>
                    <option {if $prev_time eq "600"} selected {/if} value="600">{10} {lang('n_minutes_of_inactivity')}</option>
                    <option {if $prev_time eq "900"} selected {/if} value="900">{15} {lang('n_minutes_of_inactivity')}</option>
                    <option {if $prev_time eq "1800"} selected {/if} value="1800">{30} {lang('n_minutes_of_inactivity')}</option>
                    <option {if $prev_time eq "3600"} selected {/if} value="3600">{1} {lang('n_hr_of_inactivity')}</option>
                    <option {if $prev_time eq "7200"} selected {/if} value="7200">{2} {lang('n_hrs_of_inactivity')}</option>
                </select>
                {form_error('logout_time')}
            </div>

            <div class="form-group">
                <div class="checkbox">
                  <label class="i-checks">
                    <input type="checkbox" name="google_auth_status" {if $MODULE_STATUS['google_auth_status'] == 'yes'} checked {/if}><i></i> 
                    {lang('google_auth_status')}
                  </label>
                </div>
            </div>
            
            <div class="form-group">
                <div class="checkbox">
                    <label class="i-checks">
                    <input type="checkbox" value="yes" name="age_limit_status" {if $signup_config['general_signup_config']['age_limit']} checked {/if} {set_checkbox('age_limit_status', 'yes')}><i></i> {lang('enable_age_restriction')}
                    </label>
                </div>
            </div>

            <div class="form-group" style="display:none">
                <label class="required">{lang('min_age_required')}</label>
                <input type="text" class="form-control" name="age_limit" maxlength="3" value="{$signup_config['general_signup_config']['age_limit']}">
                {form_error('age_limit')}
            </div>

            <div class="form-group">
                <div class="checkbox">
                    <label class="i-checks">
                        <input type="checkbox" name="login_unapproved" {if $signup_config['general_signup_config']['login_unapproved'] == 'yes'} checked {/if}><i></i> {lang('enable_unapproved_user_login')}
                    </label>
                </div>
            </div>

            {if $country_status =='yes'}
                <div class="form-group">
                    <label class="required">{lang('default_country')}</label>
                    <select name="country" id="country" class="form-control">{$countries}</select>
                </div>
            {/if}
            {include file="common/notes.tpl" notes=lang('note_tax_transaction_fee')}
            <hr>
        
            {* Username Settings *}
            <h3>{lang('user_name')}</h3>
            <div class="form-group">
                <label class="required">{lang('username_type')}</label>
                <select class="form-control" id="user_name_type" name="user_name_type">
                    <option value="static" {if $username_config["type"]=='static'} selected {/if}>{lang('Static')}</option>
                    <option value="dynamic" {if $username_config["type"]=='dynamic'} selected {/if}>{lang('Dynamic')}</option>
                </select>
                {form_error('user_name_type')} 
            </div>
            
            <div class="form-group">
                <label class="required">{lang('user_name_length')}</label><br><br>
                <input id="ex2" type="text" name="length" class="span2" value="" data-slider-min="6" data-slider-max="20" data-slider-step="1" data-slider-value="[{$userNameRange['min']},{$userNameRange['max']}]"/>
                {form_error('length')} 
            </div> 

            <div class="form-group" id="prefix_status_div">
                <div class="checkbox" id="prefix_checkbox">
                    <label class="i-checks">
                    <input type="checkbox" name="prefix_status" {if $username_config["prefix_status"] == 'yes'} checked {/if}>
                    <i></i> {lang('enable_username_prefix')}
                </label>
            </div>
        
            <div class="form-group" id="prefix_div" {if $username_config["type"] == "static" || $username_config["prefix_status"] == "no"} style="display: none;" {/if}>
                <label class="required">{lang('username_prefix')}</label>
                <input type="text" class="form-control" name="prefix" id="prefix" value="{$username_config["prefix"]}" maxlength="8">
                {form_error('prefix')} 
            </div>
            <br>
            <hr>
            {* ./ Username settings *}
        
            {* Password Settings *}
            <h3>{lang('password')}</h3>
            <div class="form-group">
                <div class="checkbox">
                    <label class="i-checks">
                    <input type="checkbox" name="enable_password_policy" id="enable_password_policy" {if $passwordPolicy['enable_policy'] == 1} checked {/if}><i></i> 
                    {lang('enable_password_policy')}
                    </label>
                </div>
            </div>

            <div id="passwordPolicyDiv" {if $passwordPolicy['enable_policy'] == 0} style="display: none;" {/if}>
                <div class="form-group">
                    <div class="checkbox">
                        <label class="i-checks">
                        <input type="checkbox" name="contain_lowercase" id="contain_lowercase"{if $passwordPolicy['lowercase'] == 1} checked {/if}><i></i> 
                        {lang('contain_lowercase')}
                        </label>
                    </div>
                </div>
                    
                <div class="form-group">
                    <div class="checkbox">
                        <label class="i-checks">
                        <input type="checkbox" name="contain_uppercase" id="contain_uppercase"{if $passwordPolicy['uppercase'] == 1} checked {/if}><i></i> 
                        {lang('contain_uppercase')}
                        </label>
                    </div>
                </div>
                    
                <div class="form-group">
                    <div class="checkbox">
                        <label class="i-checks">
                        <input type="checkbox" name="contain_number" id="contain_number"{if $passwordPolicy['number'] == 1} checked {/if}><i></i> 
                        {lang('contain_number')}
                        </label>
                    </div>
                </div>
                    
                <div class="form-group">
                    <div class="checkbox">
                        <label class="i-checks">
                        <input type="checkbox" name="contain_sp_char" id="contain_sp_char"{if $passwordPolicy['sp_char'] == 1} checked {/if}><i></i> 
                        {lang('contain_sp_char')}
                        </label>
                    </div>
                </div>
                    
                <div class="form-group">
                    <label class="required">{lang('min_password_length')}</label>
                    <input type="text" class="form-control" name="min_password_length" id="min_password_length" value="{$passwordPolicy['min_length']}">
                    {form_error('min_password_length')}
                </div>
            </div>
            {* ./Password Settings *}

            <div class="form-group">
                <button class="btn btn-sm btn-primary" type="submit" value="{lang('update')}" name="setting" id="setting">{lang('update')}</button>
            </div>
        {form_close()}
    </div>
</div>
{/block}

{block name=style}
     {$smarty.block.parent}
{/block}

{block name='script'}
    {$smarty.block.parent}
    <script src="{$PUBLIC_URL}javascript/bootstrap-slider.min.js" type="text/javascript" ></script>
    <script src="{$PUBLIC_URL}javascript/profile_settings.js"></script>
{/block}