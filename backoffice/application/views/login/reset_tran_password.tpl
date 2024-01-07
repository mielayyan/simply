{extends file="login/layout.tpl"} 

{block name=CONTENT_INNER}

<div id="span_js_messages" style="display:none;">
    <span id="validate_msg15">{lang('you_must_enter_password')}</span>
    <span id="validate_msg18">{lang('password_miss_match')}</span>
    <span id="validate_msg16">{lang('minimum_six_characters_required')}</span>
    <span id="validate_msg17">{lang('you_must_enter_your_password_again')}</span>

</div>

{form_open('', 'id="reset_password_form" name="reset_password_form" method="post"')}
            <div class="text-danger wrapper text-center" ng-show="authError">

            </div>
            {include file="layout/error_box.tpl"}
            <input type="hidden" id="key" name="key" value="{$key}">
            <input type="hidden" id="user_name" name="user_name" value="{$user_name}">
            <div class="list-group login-input user-login-input">
            <div class="list-group">
                <div class="list-group-item">
                    <input type="password" class="form-control no-border" id="pass" name="pass" placeholder="{lang('new_password')}">{form_error('pass')}
                </div>

                <div class="list-group-item">
                    <input type="password" class="form-control no-border" id="confirm_pass" name="confirm_pass" placeholder="{lang('confirm_password')}">{form_error('confirm_pass')}
                </div>
                <div class="list-group-item forget_pass">
                    <img src="{$BASE_URL}captcha/load_captcha/admin" id="captcha" />
                    <a class="pull-right" href="#" onclick="document.getElementById('captcha').src = '{$BASE_URL}captcha/load_captcha/admin/' + Math.random();document.getElementById('captcha-form').focus();" id="change-image"> {lang('not_readable_change_text')}</a>
                </div>
                <div class="list-group-item">
                    <input type="text" placeholder="{lang('enter_cpacha')}" name="captcha" class="form-control no-border" id="captcha-form" autocomplete="off" /> {form_error('captcha')}
                </div>
            </div>
            </div>
            <div class="login-btn">
            <input type="submit" id="reset_password_submit" class="btn btn-lg btn-primary btn-block" name="reset_password_submit" value="{lang('reset_password')}" /> 
            </div>
            {form_close()}

{/block}