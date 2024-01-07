{extends file="login/layout.tpl"} 

{block name=script} {$smarty.block.parent}
<script src="{$PUBLIC_URL}javascript/login_user.js" type="text/javascript"></script>
{/block} 

{block name=CONTENT_INNER}

{form_open('login/verify_one_time_password','id="" name="" class="" onload=""')}
            <h3 class="text-center">{lang('enter_otp')}</h3>
            {if $goc_status!='verified'}
            <div class="alert alert-warning">
                    {lang('google_auth_app_info')}
                </div>
            <div class="col-sm-12 col-sm-offset-2 padding_both img_width_otp">
                <p class="text-left">{lang('scan_qr_to_get_otp')}</p>
                <img src="{$qr_code}">
            </div>
            <p class="text-center">{lang('authentication_key')} : {$secret_key}</p>
            <p class="text-center">{lang('store_your_secret_code_safely')}</p>
            {/if}
            <div class="form-group">
                <input type="password" autocomplete="off" class="form-control" name="one_time_password" id="one_time_password" placeholder="{lang('enter_otp')}" maxlength="32" placeholder="{lang('enter_otp')}">
            </div>
            <div class="form-group">
                <input type="submit" class="btn btn-sm btn-primary" id="" name="verify" value="{lang('verify')}" />
            </div>
            <div class="text-center m-t-md"><a href="{$BASE_URL}login/Reset_google_authentication">{lang('reset_google_authentication')}?</a></div>
            <div class="form-group">
                <a href="{$BASE_URL}login/backup_authentication">{lang('more_options')}</a>
            </div>
            {form_close()}
            
<style>
    .img_width_otp img {
        width: 245px;
    }
    
    a {
        color: #337ab7;
    }
</style>
{/block}