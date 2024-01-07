{extends file="login/layout.tpl"} 

{block name=CONTENT_INNER}
<div id="span_js_messages" style="display:none;">
    <span id="error_msg1">{lang('please_enter_username')}</span>
    <span id="error_msg2">{lang('you_must_enter_email')}</span>
    <span id="error_msg3">{lang('please_enter_captcha')}</span>
</div>

    {form_open('', 'class="login_form form-validation" id="reset_google_auth" name="reset_google_auth" method="post"')}
      <div class="text-danger wrapper text-center" ng-show="authError">
            {include file="layout/error_box.tpl"}
      </div>
      <div class="list-group login-input user-login-input">
      <div class="list-group">
        <div class="list-group-item form-group">
          <input type="text" id="user_name" name="user_name" placeholder="{lang('user_name')}" AUTOCOMPLETE = "OFF" class="form-control no-border" />
            {form_error('user_name')}
        </div>

        <div class="list-group-item form-group">
           <input type="email" id="e_mail" name="e_mail" placeholder="{lang('email')}" class="form-control no-border" />
            {form_error('e_mail')}
        </div>
      </div>
      <div class="login-btn"> 
      <input type="submit" id="google_auth_reset_submit" name="google_auth_reset_submit" class="btn btn-lg btn-primary btn-block" value="{lang('reset')}" />
      </div>
</div>
    {form_close()}

  

{/block}