{extends file="login/layout.tpl"} 

{block name=script} {$smarty.block.parent}
<script src="{$PUBLIC_URL}javascript/login_user.js" type="text/javascript"></script>
{/block} 

{block name=CONTENT_INNER}

        {include file="layout/alert_box.tpl"}
        {form_open('login/verify_backup_key','id="" name="" class="" onload=""')}
            {include file="layout/error_box.tpl"}
            <h3 class="text-center">{lang('authentication_key')}</h3>
            <div class="form-group">
                <input type="password" autocomplete="off" class="form-control password" name="one_time_password" id="one_time_password" placeholder="{lang('enter_authentication_key')}" maxlength="32"/>
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-sm btn-primary" id="" name="verify" value="{lang('verify')}" />
            </div>
          {form_close()}
{/block}