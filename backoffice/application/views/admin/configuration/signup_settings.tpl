 {extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}

{include file="admin/configuration/system_setting_common.tpl"}
<div id="span_js_messages" style="display:none;"> 
    <span id="user_name_length">{lang('user_name_length_required')}</span>
    <span id="user_name_prefix">{lang('user_name_prefix_required')}</span>
    <span id="digit_only">{lang('digit_only')}</span> 
    <span id="validate_msg26">{lang('field_is_required')}</span>
    <span id="validate_msg30">{lang('digit_greater_than_0')}</span>
    <span id="lang_age">{lang('age')|strtolower}</span>
    <span id="confirm_msg_delete">{lang('Sure_you_want_to_Delete_this_Field_There_is_NO_undo')}</span>
</div>

<div class="panel panel-default table-responsive">
  <div class="panel-body">
    <legend><span class="fieldset-legend">{lang('signup_settings')}</span></legend>
    {form_open('','role="form" class="" name="signup_form" id="signup_form"')}
        
        {if $MODULE_STATUS['opencart_status'] == "no"}
                <div class="form-group">
                    <label class="required">{lang('registration_amount')}</label>
                    <div class="input-group {$input_group_hide}">
                        {$left_symbol}
                        <input class="form-control" type="text" name="reg_amount" id="reg_amount" value="{set_value('reg_amount' ,round($obj_arr["reg_amount"]*$DEFAULT_CURRENCY_VALUE,$PRECISION))}">
                        {$right_symbol}
                    </div>
                    {form_error('reg_amount')}
                </div>
        {/if}

        <div class="form-group">
            <div class="checkbox">
                <label class="i-checks">
                <input type="checkbox" name="registration_allowed" {if $signup_config['general_signup_config']['registration_allowed'] == 'no'} checked {/if}><i></i> {lang('block_user_registration')}
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <div class="checkbox">
                <label class="i-checks">
                <input type="checkbox" name="mail_notification" {if $signup_config['general_signup_config']['mail_notification'] == 'yes'} checked {/if}><i></i> {lang('enable_mail_notification')}
                </label>
            </div>
        </div>
        
        <div class="form-group">
            <div class="checkbox">
                <label class="i-checks">
                <input type="checkbox" name="approval_free_join"  {if $free_join_status == 1} checked {/if} id="free_join_approval"><i></i> {lang('enable_admin_verification')}
                </label>
            </div>
        </div>
        
        {if $MLM_PLAN == 'Binary'}
            <div class="form-group">
                <div class="checkbox">
                    <label class="i-checks">
                    <input type="checkbox" value="yes" name="binary_leg_status" {if $signup_config['general_signup_config']['binary_leg'] != 'any'} checked {/if}><i></i> {lang('enable_binary_locking')}
                    </label>
                </div>
            </div>
            <div class="form-group">
                <label class="required">{lang('position_to_lock')}</label>
                <select class="form-control" name="binary_leg">
                    <option value="left" {if $signup_config['general_signup_config']['binary_leg'] == 'left'} selected {/if}>{lang('left_leg')}</option>
                    <option value="right" {if $signup_config['general_signup_config']['binary_leg'] == 'right'} selected {/if}>{lang('right_leg')}</option>
              </select>
            </div>
        {/if}
        <!--<div class="form-group">
            <div class="checkbox">
                <label class="i-checks">
                <input type="checkbox" value="yes" name="age_limit_status" {if $signup_config['general_signup_config']['age_limit']} checked {/if} {set_checkbox('age_limit_status', 'yes')}><i></i> {lang('enable_age_restriction')}
                </label>
            </div>
        </div>-->
        <div class="form-group">
            <div class="checkbox">
                <label class="i-checks">
                <input type="checkbox" name="email_verification" {if $signup_config['general_signup_config']['email_verification'] == 'yes'} checked {/if}><i></i> {lang('enable_email_verification')}
                </label>
            </div>
        </div>
        <!--<div class="form-group">
            <div class="checkbox">
                <label class="i-checks">
                <input type="checkbox" name="login_unapproved" {if $signup_config['general_signup_config']['login_unapproved'] == 'yes'} checked {/if}><i></i> {lang('enable_unapproved_user_login')}
                </label>
            </div>
        </div>-->
        <!--<div class="form-group">
            <label class="required">{lang('min_age_required')}</label>
            <input type="text" class="form-control" name="age_limit" maxlength="3" value="{$signup_config['general_signup_config']['age_limit']}">
            {form_error('age_limit')}
        </div>
        {if $country_status =='yes'}
            <div class="form-group">
                <label class="required">{lang('default_country')}</label>
                <select name="country" id="country" class="form-control">{$countries}</select>
            </div>
        {/if}-->
        
        <button type="submit" id="update_signup" value="update" name="update_signup" class="btn btn-sm btn-primary m-b-new">{lang('update')}</button>
    {form_close()}
    
  </div>
</div>

<div class="panel panel-default hidden">
  <div class="panel-body">
    <legend><span class="fieldset-legend">{lang('pending_registrations')}</span></legend>
    <div class="m-b-sm">
    {include file="common/notes.tpl" notes=lang('note_pending_signup_payment_method')}
    </div>
    <div class="panel panel-default table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>{lang('sl_no')}</th>
          <th>{lang('payment_method')}</th>
          <th>{lang('status')}</th>
        </tr>
      </thead>
      <tbody>

      {foreach from = $signup_config['pending_signup_config'] item = v}
      {if $MODULE_STATUS['opencart_status'] == "yes" && $v.payment_method=="Free Joining"}
        {continue}
      {/if}
      {if $v.payment_method=='Bank Transfer'}
      {continue}
      {/if}
      <tr>
        <td>{counter}</td>
        <td>{if $v.payment_method=="Bitcoin"}{lang("blocktrail")}{else}{$v.payment_method}{/if}</td>
        <td  class="payment_width_td"><div class="form-group-button">
            <label class="i-switch bg-primary">
              <input type="checkbox" {if $v.status} checked {/if} data-id="{$v.id}" data-status="{$v.status}" name="pending_status"
                                    id="set_paypal_status" class="switch-input pending_status">
              <i></i> </label>
          </div></td>
      </tr>
      {/foreach}
        </tbody>

    </table>
    </div>
  </div>
</div>
<!--<div class="panel panel-default">
  <div class="panel-body">
    <legend><span class="fieldset-legend">{lang('username_setting')}</span></legend>
    {form_open('admin/signup_settings', 'role="form" class="form" method="post"  name="signup_settings_form" id="signup_settings_form"')}
    <div class="form-group">
      <label class="required">{lang('username_type')}</label>
      <select class="form-control" id="user_name_type" name="user_name_type">
        <option value="static" {if $username_config["type"]=='static'} selected {/if}>{lang('Static')}</option>
        <option value="dynamic" {if $username_config["type"]=='dynamic'} selected {/if}>{lang('Dynamic')}</option>
      </select>
      {form_error('user_name_type')} </div>
    <!--<div class="form-group" id="length_div" {if $username_config["type"] == "static"} style="display: none;" {/if}>
      <label class="required">{lang('user_name_length')}</label>
      <input type="text" class="form-control" name="length" id="length" value="{$username_config["length"]}">
      {form_error('length')} 
    </div>--> 
    <!--<div class="form-group">
      <label class="required">{lang('user_name_length')}</label><br><br>
      <input id="ex2" type="text" name="length" class="span2" value="" data-slider-min="6" data-slider-max="20" data-slider-step="1" data-slider-value="[{$userNameRange['min']},{$userNameRange['max']}]"/>
      {form_error('length')} 
    </div> 

    <div class="form-group" id="prefix_status_div">
      <div class="checkbox" id="prefix_checkbox">
        <label class="i-checks">
        <input type="checkbox" name="prefix_status" {if $username_config["prefix_status"] == 'yes'} checked {/if}><i></i> {lang('enable_username_prefix')}
        </label>
    </div>
    
    <div class="form-group" id="prefix_div" {if $username_config["type"] == "static" || $username_config["prefix_status"] == "no"} style="display: none;" {/if}>
    <label class="required">{lang('username_prefix')}</label>
    <input type="text" class="form-control" name="prefix" id="prefix" value="{$username_config["prefix"]}" maxlength="8">
    {form_error('prefix')} </div><br>
  <div class="form-group">
    <button type="submit" class="btn btn-sm btn-primary" value="update" name="update" id="update">{lang('update')}</button>
  </div>
  {form_close()} </div>
</div>
</div>-->

<!--{if $MODULE_STATUS['opencart_status'] != "yes"}
  {form_open('', 'name="signup_field_form" id="signup_field_form" method="post"')}
    <div class="panel panel-default">
      <div class="panel-body">
         <legend><span class="fieldset-legend">{lang('custom_sign_up_form_field')}</span>
          <button type="button" class="btn m-b-xs btn-sm btn-primary btn-addon pull-right next_button"><i class="fa fa-plus"></i> {lang('add_custom_field')}</button>
        </legend>
        <div class="panel panel-default table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped">
          <thead>
            <tr>
              <th>{lang('sl_no')}</th>
              <th>{lang('name')}</th>
              <th>{lang('sort_order')}</th>
              <th>{lang('enabled')}</th>
              <th>{lang('required')}</th>
              <th>{lang('action')}</th>
            </tr>
          </thead>

        <tbody>
        {assign var="i" value=0}
        {foreach from=$signup_fields item=v}
          {if $v.field_name=="first_name" ||$v.field_name=="email" ||$v.field_name=="mobile" }
            {continue}
          {/if} 
          <tr>
            <td>{assign var="i" value=$i+1}{$i}</td>
            <td>{lang($v.field_name)}</td>
            <td><input class="form-control sort_order" type="text" id="sort_order{$i}" name="sort_order{$i}" value="{$v.sort_order}"><span id="errmsg{$i}" style="color:red;"></span>
                <input type="hidden" id="id" name="id{$i}" value="{$v.id}">
            </td>
            <td> <div class="form-group-button">
                  <label class="i-switch bg-primary">
                  <input type="checkbox" {if $v.status == 'yes'} checked {/if} data-id="{$v.id}" data-status="{$v.status}" name="status" class="switch-input signup_field">
                    <i></i>
                </label>
              </div>
            </td> 
            <td> 
            <div class="form-group-button">
             {if $v.field_name=='country'}
                <font color="#ff0000">{lang('default_data_enabled')}</font> 
             {else}
                    <label class="i-switch bg-primary">
                    <input type="checkbox" {if $v.required == 'yes'} checked {/if} data-id="{$v.id}" data-status="{$v.required}" name="required" class="switch-input signup_field">
                      <i></i>
                    </label> 
                  {/if}</div>  </td>
             <td>
                  {if $v.custom_field}
                    <button type="button" class="btn-link btn_size has-tooltip text-info" onclick="edit_custom({$v.id}, '{$BASE_URL}admin/')" title="{lang('edit')}"> <i class="fa fa-edit"></i></button>
                    <a href="javascript:delete_custom({$v.id})" title="Delete" class="btn-link btn_size has-tooltip text-danger delete_custom" data-placement="top" data-original-title="{lang('delete')}"><i class="fa fa-trash-o"></i></a>                       
                  {/if}
                  </td>     
            <input type="hidden" id="number" name="number" value="{$i}">
          </tr> 
        {/foreach}
        </tbody>
      </table>
      </div>
       <button type="submit" id="save" value="save" name="save" class="btn btn-sm btn-primary m-b-new update_config">{lang('update')}</button>
    </div>
  </div>
      {form_close()}

  <div class="panel panel-default" style="display:none;">
    <div class="panel-body">
      <legend><span class="fieldset-legend">{lang('add_custom_field')}</span></legend>
        {form_open('admin/signup_settings', 'role="form" class="form" method="post"  name="custom_field_form" id="custom_field_form"')}

        {if $LANG_STATUS=='no'}
          <div class="form-group">
              <label class="required">{lang('field_name')}</label>
              <input type="text" class="form-control" name="field_name" value="{set_value('field_name')}">
              {form_error('field_name')}
          </div>
        {else}
          {foreach from=$lang_code item=v}
            <div class="form-group">
                <label class="required">{lang('field_name')} - {$v.lang_eng}</label>
                <input type="text" class="form-control field_lang" name="field_name_{$v.lang_code}" id="field_name_{$v.lang_code}" value="{set_value('field_name_`$v.lang_code`')}" data-lang="{lang('you_must_enter')} {lang('field_name')|lower}">
                {form_error("field_name_{$v.lang_code}")}
            </div>
          {/foreach}
        {/if}

          <div class="form-group">
            <label class="required">{lang('enabled')}</label>
              <select class="form-control" name="enabled">
                <option value="yes" >{lang('yes')}</option>
                <option value="no">{lang('no')}</option>
              </select>
            {form_error('enabled')} 
          </div>

          <div class="form-group">
            <label class="required">{lang('required')}</label>
              <select class="form-control" name="mandatory">
                <option value="yes" >{lang('yes')}</option>
                <option value="no">{lang('no')}</option>
              </select>
            {form_error('mandatory')} 
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary" value="update_custom" name="update_custom" id="update_custom">{lang('submit')}</button>
          <button type="button" class="btn btn-sm btn-primary previous_button">{lang('cancel')}</button>
          </div>
        {form_close()} 
    </div>
  </div>    

  {/if}-->



{* <div class="alert alert-success alert-dismissable" style="display: none;" id="success-box"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {lang('configuration_success')} </div> *}
<div class="alert alert-danger alert-dismissable" style="display: none;" id="error-box"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {lang('configuration_error')} </div>
<input type="hidden" id="base_url" value="{$BASE_URL}">
{/block}

{block name=script}

     {$smarty.block.parent}
     <script src="{$PUBLIC_URL}javascript/signup_settings.js"></script>
     <script src="{$PUBLIC_URL}javascript/jquery-1.10.3ui.min.js" type="text/javascript" ></script>
     <script src="{$PUBLIC_URL}javascript/bootstrap-slider.js" type="text/javascript" ></script>
     <script src="{$PUBLIC_URL}javascript/bootstrap-slider.min.js" type="text/javascript" ></script>
     <script type="text/javascript">
      
     $("#ex2").bootstrapSlider(); 

      $( ".row_position" ).sortable({
          delay: 150,
          stop: function() {
              var selectedData = new Array();
              $('.row_position>tr').each(function() {
                  selectedData.push($(this).attr("id"));
              });
              $('#row_order').val(selectedData);

          }
      });
</script>
{/block}
