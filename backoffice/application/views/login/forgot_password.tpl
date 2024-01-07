{extends file="login/layout.tpl"} 

{block name=CONTENT_INNER}

<div id="span_js_messages" style="display:none;">
    <span id="error_msg1">{lang('please_enter_username')}</span>
    <span id="error_msg2">{lang('you_must_enter_email')}</span>
    <span id="error_msg3">{lang('please_enter_captcha')}</span>
</div>

    {form_open('', 'class="login_form form-validation" id="forgot_password" name="forgot_password" method="post" onload="onloadCaptcha();"')}
      <div class="text-danger wrapper text-center" ng-show="authError">
            {include file="layout/error_box.tpl"}
      </div>
      <div class="list-group form-group login-input">
        <div class="list-group-item">
          <input type="text" id="user_name" name="user_name" placeholder="{lang('user_name')}" AUTOCOMPLETE = "OFF" class="form-control no-border" value="{set_value('user_name')}"/>
            {form_error('user_name')}
        </div>

        <div class="list-group-item form-group">
           <input type="email" id="e_mail" name="e_mail" placeholder="{lang('email')}" class="form-control no-border"  value="{set_value('e_mail')}"/>
            {form_error('e_mail')}
        </div>
        <div class="list-group-item forget_pass">
         <img src="{$BASE_URL}captcha/load_captcha/admin" id="captcha">
         <a class="pull-right" href="#" onclick="
                                             document.getElementById('captcha').src = '{$BASE_URL}captcha/load_captcha/admin/' + Math.random();
                                             document.getElementById('captcha-form').focus();"
                                   id="change-image">{lang('not_readable_change_text')}</a>
        </div>
        <div class="list-group-item">
           <input type="text" placeholder="{lang('enter_capcha')}" class="form-control no-border"  name="captcha" id="captcha-form" autocomplete="off" />
           {form_error('captcha')}
        </div>
      </div>
      <div class="login-btn">
        <input type="submit" id="forgot_password_submit" name="forgot_password_submit" class="btn btn-lg btn-primary btn-block" value="{lang('send_request')}" />
      </div>
    {form_close()}
     <div class="text-center text-center m-t-sm"><a href="{$BASE_URL}login" ><i class="fa fa-long-arrow-left" aria-hidden="true"></i> {lang('login')}</a></div>
{/block}