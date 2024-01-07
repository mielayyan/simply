{extends file='newui/layout/admin.tpl'}

{block name=$CONTENT_BLOCK}
<link rel="stylesheet" href="{$PUBLIC_URL}plugins/select2/dist/css/select2.min.css" type="text/css" />
    <script src="{$PUBLIC_URL}plugins/select2/dist/js/select2.min.js"></script>
<div id="span_js_messages" style="display:none;">
    <span id="error_msg1">{lang('You_must_enter_user_name')}</span>        
    <span id="error_msg2">{lang('you_must_enter_your_password')}</span>        
    <span id="error_msg3">{lang('You_must_enter_your_Password_again')}</span>        
    <span id="error_msg4">{lang('You_must_enter_your_email')}</span>                  
    <span id="error_msg5">{lang('You_must_enter_your_mobile_no')}</span>
    <span id="error_msg6">{lang('mail_id_format')}</span>
    <span id="error_msg7">{lang('You_must_enter_first_name')}</span>
    <span id="error_msg8">{lang('You_must_enter_last_name')}</span>
    <span id="error_msg12">{lang('Invalid_Username')}</span>
    <span id="error_msg13">{lang('checking_username_availability')}</span>
    <span id="error_msg14">{lang('username_validated')}</span>
    <span id="error_msg15">{lang('username_already_exists')}</span>
    <span id="confirm_msg">{lang('sure_you_want_to_delete_this_feedback_there_is_no_undo')}</span>
    <span id="error_msg16">{lang('please_enter_atleast_6_characters')}</span>
    <span id="error_msg17">{lang('digits_only')}</span>
    <span id="error_msg18">{lang('alphabets_only')}</span>
    <span id="error_msg19">{lang('special_characters_are_not_allowed')}</span>
    <span id="error_msg20">{lang('please_select_a_date')}</span>
    <span id="error_msg21">{lang('please_enter_atleast_5_digits')}</span>
    <span id="error_msg22">{lang('please_enter_no_more_than_10_digits')}</span>
    <span id="error_msg23">{lang('you_must_enter_atleast_6_characters')}</span>
    <span id="error_msg24">{lang('password_mismatch')}</span>
</div> 
<input type="hidden" id="passwordPolicyJson" value='{$passwordPolicyJson}'>
        <div class="panel panel-default">
            <div class="panel-body">
                {form_open('','role="form" class="smart-wizard employreglog" method="post"  name="user_register" id="user_register"')}
                    {include file="layout/error_box.tpl"}

                <input type="hidden" id="path_temp" name="path_temp" value="{$PUBLIC_URL}">
                <input type="hidden" id="path_root" name="path_root" value="{$PATH_TO_ROOT_DOMAIN}">
                    
                   
                    
                    <div class="form-group">
                        <label class="control-label required" for="agent_firstname">{lang('first_name')}</label>
                            <input class="form-control" type="text"  name="agent_firstname" id="agent_firstname" required readonly  autocomplete="Off" tabindex="2"  value="{if $edit_id !=''}{$agent_firstname}{/if}">
                            {form_error('agent_firstname')}
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label required" for="agent_secondname" >{lang('last_name')}</label>
                            <input class="form-control"  type="text"  name="agent_secondname" id="agent_secondname" readonly  required autocomplete="Off" tabindex="3"value="{if $edit_id !=''}{$agent_secondname}{/if}">
                            {form_error('agent_secondname')}
                    </div>
                    
                    <div class="form-group">
                        <label class="control-label required" for="agent_email">{lang('email')}</label>
                            <input class="form-control" type="text"  name="agent_email" id="agent_email" required  readonly autocomplete="Off" tabindex="4" value="{if $edit_id !=''}{$agent_email}{/if}">
                            {form_error('agent_email')}
                    </div>

                    <div class="form-group">
                        <label class=" control-label required" for="agent_mobile" >{lang('mobile_no')}</label>
                            <input class="form-control" type="text"  name="agent_mobile" id="agent_mobile" required readonly  autocomplete="Off" tabindex="5" value="{if $edit_id !=''}{$agent_mobile}{/if}">
                            <span id="errmsg1"></span>
                            {form_error('agent_mobile')}
                       
                    </div>

                    
                    
                    <div class="form-group">
                        <label>{lang('country')}</label>
                        
                        <input class="form-control" type="text" id="agent_country"  name="agent_country" class="form-control" disabled="" value="{$countries}"  >

                    </div>

                     <div class="form-group">
                        <label class="control-label required" for="agent_username" >{lang('user_name')}</label>
                            <input class="form-control" type="text" name="agent_username" id="agent_username"   required autocomplete="Off" tabindex="1" {if $edit_id !=''} readonly {/if} value="{if $edit_id !=''}{$agent_username}{/if}">
                            <span id="username_box" style="display:none;"></span>
                            {form_error('agent_username')}
                    </div>

            
               
                {form_close()}
            </div>
        </div>
         {if $edit_id !=''}
        <div class="panel panel-default">
            <div class="panel-body">
                {form_open('','role="form" class="smart-wizard employreglog" method="post"  name="user_register" id="user_register"')}
                    {include file="layout/error_box.tpl"}
                    <div class="form-group">
                        <label class="control-label required" for="agent_password">{lang('New Password')}</label>
                            <input class="form-control act-pswd-popover" type="password"  name="agent_password" id="agent_password" tabindex="6" autocomplete="Off" size="24" maxlength="20" value=""  >
                        {form_error('agent_password')}
                    </div>

                    <div class="form-group">
                        <label class="control-label required" for="cagentpswd"  >{lang('confirm_password')}</label>
                            <input class="form-control" name="cagentpswd" id="cagentpswd" type="password" tabindex="7" autocomplete="Off" size="24" maxlength="20" value="">
                            {form_error('cagentpswd')}
                    </div>
                    <button class="btn btn-sm btn-primary" name="change_password" id="change_password" tabindex="8" value="{lang('Assign Agent')}" >
                     {lang('Change Password')}</button>
                 {form_close()}
            </div>
        </div>
        {/if}
{/block}

{block name=script}
  {$smarty.block.parent}
    <script>
    jQuery(document).ready(function() {
        // ValidateUser.init();
       
    });


    function getAllCountries(country_code) {
        var root = $("#path_root").val();
        var strURL = root + "/party/getCountries/" + country_code;
        var req = getXMLHTTP();

        if (req) {

            req.onreadystatechange = function() {
                if (req.readyState == 4) {
                    if (req.status == 200) {
                        document.getElementById('state').innerHTML = trim(req.responseText);
                        document.getElementById('state').style.display = '';
                    } else {
                        alert("There was a problem while using XMLHTTP:\n" + req.statusText);
                    }
                }
            }
            req.open("GET", strURL, true);
            req.send(null);
        }
    }
    function getAllCountries() {
        var country = $('#country').val();
        $('.countrywise_user_autolist').autocomplete({
        minLength: 1,
        appendMethod: 'replace',
        highlight: false,
        showHint: false,
        visibleLimit: 10,
        filter: function(items, query, source) {
            var results = [],
                value = '';
            for (var i in items) {
                value = items[i][this.valueKey];
                results.push(items[i]);
            }
            return results;
        },
        source: [
            function(q, add) {
                if (q == '/' || q == '.') {
                    var keyword = null;
                } else {
                    var keyword = q;
                }
                if (q == '' || q == null) {
                    add([]);
                } else {
                    $.ajax({
                        method: "POST",
                        url: base_url + 'admin/home/ajax_countrywise_user_autolist/'+country,
                        data: { keyword: keyword, country : country },
                        dataType: 'json',
                        success: function(data) {
                            add(data);
                        }
                    });
                }
            }
        ]
    });
    }
    </script>
{/block}