{extends file="login/layout.tpl"}  

{block name=CONTENT_INNER}

<div id="span_js_messages" style="display:none;">
    <span id="error_msg4">{lang('please_enter_password')}</span>
    <span id="error_msg3">{lang('please_enter_captcha')}</span>
</div>

{form_open('login/verify_employee_login', 'id="login_form" name="login_form" class="form-validation" ')}
            <div class="text-danger wrapper text-center" ng-show="authError">
            </div>
            {include file="layout/alert_box.tpl"}
            <div class="list-group form-group login-input">
            <div class="list-group">
                <div class="list-group-item">
                    <input type="text" name="user_username" id="employee_username" placeholder="{lang('privileged_user_name')}" class="form-control no-border" value="{$employee_username}" />
                </div>
                <div class="list-group-item">
                    <input type="password" name="user_password" id="employee_password" placeholder="{lang('password')}" class="form-control no-border">
                </div>
            </div>
        </div>
        <div class="login-btn">
            <input type="submit" class="btn btn-lg btn-primary btn-block" id="user_login" name="user_login" value="{lang('login')}" />
            
        </div>
            <div class="line line-dashed"></div> 
            {form_close()}
        

{/block}