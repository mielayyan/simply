{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display: none;"> 
    <span id="error_language">{lang('you_must_select_a language')}</span> 
    <span id="error_main_matter1">{lang('you_must_enter_main_matter')}</span> 
    <span id="error_terms_and_condition">{lang('you_must_enter_terms_and_conditions')}</span>
    <span id="validate_mail_content">{lang('you_must_enter_mail_content')}</span>
    <span id="validate_subject">{lang('you_must_enter_subject')}</span>
</div>

<main>
    <div class="tabsy">
        {* Registration Verification *}
        <input type="radio" id="tab1" name="tab" {if $tab1} checked {/if}>
        <label class="tabButton" for="tab1">{lang('registration_verification_email')}</label>
        <div class="tab">
            {include file="admin/configuration/registration_verification_mail.tpl"  name=""}
        </div>

        {* Registration Welocome mail *}
        <input type="radio" id="tab3" name="tab" {if $tab3} checked {/if}>
        <label class="tabButton" for="tab3">{lang('registration_email')}</label>
        <div class="tab">
            {include file="admin/configuration/registration_mail.tpl"  name=""}
        </div>

        {* Payout Release *}
        <input type="radio" id="tab4" name="tab" {if $tab4} checked {/if}>
        <label class="tabButton" for="tab4">{lang('payout_release_mail')}</label>
        <div class="tab">
            {include file="admin/configuration/payout_release_mail.tpl"  name=""}
        </div>

        {* Change Password *}
        <input type="radio" id="tab5" name="tab" {if $tab5} checked {/if}>
        <label class="tabButton" for="tab5">{lang('change_password')}</label>
        <div class="tab">
            {include file="admin/configuration/change_password_mail.tpl"  name=""}
        </div>

        {* Change Transactions Password *}
        <input type="radio" id="tab6" name="tab" {if $tab6} checked {/if}>
        <label class="tabButton" for="tab6">{lang('change_transaction_password')}</label>
        <div class="tab">
            {include file="admin/configuration/change_transactional_password_mail.tpl"  name=""}
        </div>

        {* Payout Request *}
        <input type="radio" id="tab7" name="tab" {if $tab7} checked {/if}>
        <label class="tabButton" for="tab7">{lang('payout_request')}</label>
        <div class="tab">
            {include file="admin/configuration/payout_request.tpl"  name=""}
        </div>

        {* Forgot Password *}
        <input type="radio" id="tab8" name="tab" {if $tab8} checked {/if}>
        <label class="tabButton" for="tab8">{lang('forgot_password')}</label>
        <div class="tab">
            {include file="admin/configuration/forgot_password.tpl"  name=""}
        </div>

        {* reset_google_auth *}
        <input type="radio" id="tab9" name="tab" {if $tab9} checked {/if}>
        <label class="tabButton" for="tab9">{lang('reset_google_auth')}</label>
        <div class="tab">
            {include file="admin/configuration/reset_google_auth.tpl"  name=""}
        </div>

        {* forgot_transaction_passsword *}
        <input type="radio" id="tab10" name="tab" {if $tab10} checked {/if}>
        <label class="tabButton" for="tab10">{lang('forgot_transaction_passsword')}</label>
        <div class="tab">
            {include file="admin/configuration/forgot_transaction_password.tpl"}
        </div>

        {* external_email *}
        <input type="radio" id="tab11" name="tab" {if $tab11} checked {/if}>
        <label class="tabButton" for="tab11">{lang('external_email')}</label>
        <div class="tab">
            {include file="admin/configuration/external_mail.tpl"}
        </div>
    </div>
</main>

<script type="text/javascript">
    function set_language_id(new_lang_id, type) {
        document.getElementById('lang_id').value = new_lang_id;
        var base_url = document.getElementById('base_url').value;
        if (type == 'payout_release')
            document.location.href = base_url + "admin/configuration/mail_content/" + new_lang_id + "/tabs-2";
        else if (type == 'registration')
            document.location.href = base_url + "admin/configuration/mail_content/" + new_lang_id + "/tabs-1";

    }
</script>
{/block}