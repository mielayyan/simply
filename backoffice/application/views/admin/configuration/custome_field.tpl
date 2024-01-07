{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display: none;">
    <span id="validate_msg1">{lang('you_must_enter_sender_id')}</span>
    <span id="validate_msg2">{lang('you_must_enter_user_name')}</span>
    <span id="validate_msg3">{lang('you_must_enter_password')}</span>
    <span id="confirm_msg_delete">{lang('Sure_you_want_to_Delete_this_Field_There_is_NO_undo')}</span>
</div>

{include file="admin/configuration/advanced_settings.tpl"}

{if $MODULE_STATUS['opencart_status'] != "yes"}
  {form_open('admin/configuration/update_custome_fields', 'name="signup_field_form" id="signup_field_form" method="post"')}
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
                  <input type="checkbox" {if $v.status == 'yes'} checked {/if} data-id="{$v.id}" data-status="{$v.status}" value="{$v.id}" name="status[]" class="switch-input signup_field">
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
                    <input type="checkbox" {if $v.required == 'yes'} checked {/if} data-id="{$v.id}" data-status="{$v.required}" value="{$v.id}" name="required[]" class="switch-input signup_field">
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
        {form_open('admin/custome_field', 'role="form" class="form" method="post"  name="custom_field_form" id="custom_field_form"')}

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

  {/if}



{/block}

{block name=script}

     {$smarty.block.parent}
     <script src="{$PUBLIC_URL}javascript/signup_settings.js"></script>
     <script src="{$PUBLIC_URL}javascript/validate_username_config.js"></script>
     <script src="{$PUBLIC_URL}javascript/jquery-1.10.3ui.min.js" type="text/javascript" ></script>
{/block}