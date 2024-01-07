{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display: none;">
    <span id="validate_msg1">{lang('you_must_enter_sender_id')}</span>
    <span id="validate_msg2">{lang('you_must_enter_user_name')}</span>
    <span id="validate_msg3">{lang('you_must_enter_password')}</span>
</div>

{include file="admin/configuration/advanced_settings.tpl"}



<div class="panel panel-default">
    <div class="panel-body">
        {form_open('', 'role="form" class="" method="post" name="password_policy_form" id="password_policy_form"')}
            {include file="layout/error_box.tpl"}
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
            <div class="form-group">
                <button class="btn btn-sm btn-primary" name="submit" type="submit" value="submit">{lang('submit')}</button>
            </div>
        {form_close()}
    </div>
</div>

{/block}

{block name='script'}
    {$smarty.block.parent}
    <script type="text/javascript" src="{$PUBLIC_URL}/javascript/validate_password_policy.js"></script>
{/block}