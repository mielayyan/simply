{extends file="login/layout.tpl"}  

{block name=CONTENT_INNER}

<div id="span_js_messages" style="display:none;"> <span id="error_msg1">{lang('please_enter_username')}</span> <span id="error_msg2">{lang('please_enter_password')}</span> <span id="error_msg3">{lang('please_enter_captcha')}</span> </div>

{form_open('login/verifylogin','class="" id="login_form" name="login_form" autocomplete="off"')}
        {include file="layout/alert_box.tpl"}
        <input type="password" style="display:none">
        <input type="text" style="display:none">
        <div class="text-danger wrapper text-center" ng-show="authError"> </div>
        <div class="list-group form-group login-input">
        <div class="list-group form-group">
            <div class="list-group-item">
                <input type="text" name="user_username" id="user_username" autocomplete="Off" placeholder="{lang('user_name')}" value="{$url_user_name}" class="form-control no-border">
            </div>
            <div class="list-group-item form-group">
                <input type="password" name="user_password" id="user_password" placeholder="{lang('password')}" class="form-control no-border password">
            </div>
            {if $CAPTCHA_STATUS=='yes'}
            <div class="list-group-item forget_pass">
                <img src="{$BASE_URL}captcha/load_captcha/user" id="captcha" />
                <a class="pull-right" href="#" onclick="document.getElementById('captcha').src = '{$BASE_URL}captcha/load_captcha/user/' + Math.random();document.getElementById('captcha_user').focus();" id="change-image"> {lang('not_readable_change_text')}</a>
            </div>
            <div class="list-group-item">
                <input type="text" placeholder="{lang('enter_cpacha')}" class="form-control no-border" name="captcha_user" id="captcha_user" autocomplete="off" /> {form_error('captcha')}
            </div>
            {/if}
        </div>
    </div>
    <div class="forgotpassword">          
          <div class="text-left"><a href="{$BASE_URL}login/forgot_password">{lang('forgot_password')}?</a></div>
        </div>
        <div class="login-btn">
            <input type="submit" id="user_login" name="user_login" value="{lang('login')}" class="btn btn-lg btn-primary btn-block" /><span id="loginmsg" style="display:none"></span>
        </div>
        {form_close()}
        <div class="line line-dashed"></div>
        <p class="text-center link-fade"><span>{lang('dont_have_an_account')}? </span> <a class="text-info" href="{$BASE_URL}register/user_register" class="register">
                                {lang('sign_up_now')}
                            </a></p>
        
    
{/block}