{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
  <div id="span_js_messages" style="display:none;"> 
    <span id="user_name_length">{lang('user_name_length_required')}</span>
    <span id="user_name_prefix">{lang('user_name_prefix_required')}</span>
    <span id="digit_only">{lang('digit_only')}</span> 
    <span id="validate_msg26">{lang('field_is_required')}</span>
    <span id="validate_msg30">{lang('digit_greater_than_0')}</span>
    <span id="lang_age">{lang('age')|strtolower}</span>
    <span id="confirm_msg_delete">{lang('Sure_you_want_to_Delete_this_Field_There_is_NO_undo')}</span>
    <span id="confirm_msg_edit">{lang('Sure_you_want_to_Edit_this_Field_There_is_NO_undo')}</span>
  </div>

{if $MODULE_STATUS['opencart_status'] != "yes"}
  <div class="panel panel-default">
    <div class="panel-body">
      <legend><span class="fieldset-legend">{lang('edit_custom_field')}</span>
        <a href="{$BASE_URL}admin/custome_field" class="btn btn-addon btn-sm btn-info pull-right">
            <i class="fa fa-backward"></i>{lang('back')}
        </a>
      </legend>
        {form_open('admin/edit_custom_settings', 'role="form" class="form" method="post"  name="custom_field_form" id="custom_field_form"')}
        {if $LANG_STATUS=='no'}
          <div class="form-group">
              <label class="required">{lang('field_name')}</label>
              <input type="text" class="form-control" name="field_name" value="{$signup_fields['field_name']}">
              {form_error('field_name')}
          </div>
        {else}
          {foreach from=$signup_fields item=v}
            <div class="form-group">
                <label class="required">{lang('field_name')} - {$v.lang_name_in_english}</label>
                <input type="text" class="form-control field_lang" name="field_name_{$v.lang}" id="field_name_{$v.lang}" value="{$v.field_name}" data-lang="{lang('you_must_enter')} {lang('field_name')|lower}">
                {form_error("field_name_{$v.lang}")}
            </div>
          {/foreach}
        {/if}

          <div class="form-group">
            <label class="required">{lang('enabled')}</label>
              <select class="form-control" name="enabled">
                <option value="yes" {if $signup_status['status'] == 'yes'} selected {/if}>{lang('yes')}</option>
                <option value="no" {if $signup_status['status'] == 'no'} selected {/if}>{lang('no')}</option>
              </select>
            {form_error('enabled')} 
          </div>

          <div class="form-group">
            <label class="required">{lang('required')}</label>
              <select class="form-control" name="mandatory">
                <option value="yes" {if $signup_status['required'] == 'yes'} selected {/if}>{lang('yes')}</option>
                <option value="no" {if $signup_status['required'] == 'no'} selected {/if}>{lang('no')}</option>
              </select>
            {form_error('mandatory')} 
          </div>

          <div class="form-group">
            <button type="submit" class="btn btn-sm btn-primary" value="up_custom" name="up_custom" id="up_custom">{lang('update')}</button>
          </div>
          <input type="hidden" id="field" name="field" value="{$signup_status['field_name']}">
        {form_close()} 
    </div>
  </div>

  {/if}

<div class="alert alert-success alert-dismissable" style="display: none;" id="success-box"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {lang('configuration_success')} </div>
<div class="alert alert-danger alert-dismissable" style="display: none;" id="error-box"> <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a> {lang('configuration_error')} </div>
<input type="hidden" id="base_url" value="{$BASE_URL}">
{/block}

{block name=script}
     {$smarty.block.parent}
     <script src="{$PUBLIC_URL}/javascript/signup_settings.js"></script>
{/block}
