{extends file="login/layout.tpl"} 

{block name=script} {$smarty.block.parent}
<script src="{$PUBLIC_URL}javascript/login_user.js" type="text/javascript"></script>
{/block} 

{block name=CONTENT_INNER}

    {include file="layout/alert_box.tpl"}
            {form_open('login/reset_google_auth_key','id="" name="" class="" onload=""')}
            <h3 class="text-center">{lang('enter_otp')}</h3>
            {if $goc_status!='verified'}
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
            <input type="hidden" name="reset" value="{$resetkey}">
            <input type="hidden" name="random" value="{$random_key}">
            <div class="form-group">
                <input type="submit" class="btn btn-sm btn-primary" id="" name="verify" value="{lang('reset')}" />
            </div>
            <!-- <div class="form-group">
                <a href="{$BASE_URL}login/backup_authentication">{lang('more_options')}</a>
            </div> -->
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