{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display: none;">
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
    <span id="error_msg22">{lang('only_alpha_space')}</span>
    <span id="error_msg23">{lang('atleast_3_char')}</span>
    <div id="span_js_messages" style="display:none;">

    <span id="confirm_msg_inactivate">{lang('sure_you_want_to_inactivate_this_agent')}</span>
    <span id="confirm_msg_delete">{lang('Are you sure to delete this agent? There is no undo!!')}</span>
    <span id="confirm_msg_edit">{lang('sure_you_want_to_edit_this_agent_there_is_no_undo')}</span>
    <span id="confirm_msg_activate">{lang('sure_you_want_to_activate_this_agent')}</span>
    <span id="confirm_msg_delete">{lang('sure_you_want_to_delete_this_agent_there_is_no_undo')}</span>
</div>
</div>
    {* <cdash-inner> 
        <legend style="display:{$visible};">
            <span class="fieldset-legend">{lang('edit_employee')}</span>
        </legend>
            <div class="panel panel-default" style="display:{$visible};">
                <div class="panel-body">
                    {form_open_multipart('', 'role="form" class="smart-wizard" method="post" id="edit_form" name="edit_form"  style="display: {$visibility}"')}
                        {include file="layout/error_box.tpl"} 
                        {foreach from=$editdetails item=v}

                            <div class="form-group">
                                <label class="control-label required" for="full_name">{lang('first_name')}</label>
                                    <input  type="text" class="form-control" name="first_name" id="first_name" autocomplete="Off" value="{$v.user_detail_name}" minlength="3" tabindex="1">
                                    <span id="username_box" style="display:none;"></span>
                                    <span class='val-error' id="err_first_name">{form_error('first_name')}</span>
                            </div>

                            <div class="form-group">
                                <label class="control-label required" for="full_name">{lang('last_name')}</label>
                                    <input  type="text" class="form-control" name="last_name" id="last_name" autocomplete="Off" value="{$v.user_detail_second_name}" minlength="3" tabindex="2">
                                    <span id="username_box" style="display:none;"></span>
                                    <span class='val-error' id="err_last_name">{form_error('last_name')}</span>
                            </div>

                            <div class="form-group">
                                <label class="control-label required" for="mobile">{lang('mob_no_10_digit')}</label>
                                    <input name="mobile" class="form-control" id="mobile" type="text" maxlength="10" autocomplete="Off" tabindex="3" value="{$v.user_detail_mobile}">
                                    <span id="username_box" style="display:none;"></span>
                                    <span id="errmsg3"></span>
                                    <span class='val-error' id="err_mobile">{form_error('mobile')}</span>
                            </div>

                            <div class="form-group">
                                <label class="control-label required" for="email">{lang('email')}</label>
                                    <input name="email" class="form-control" id="email" type="text"  autocomplete="Off" value="{$v.user_detail_email}" tabindex="4">
                                    <span id="username_box" style="display:none;"></span>
                                    <span class='val-error' >{form_error('email')}</span>
                            </div>
                        {/foreach}
                        
                        <div class="form-group">
                            <button class="btn btn-sm btn-primary" value="Update" name="update" id="update" tabindex="5">{lang('update')}</button>
                        </div>
                    {form_close()}
                </div>
            </div>
    </cdash-inner> *}

    
        <div class="panel panel-body panel-default ng-scope">
        <div lcass="">
        <div class="table-responsive ">
            <table st-table="rowCollectionBasic" class="table table-bordered table-striped">
                        {assign var="i" value=1}
                    <thead>
                        <tr class="th">
                            <th>{lang('sl_no')}</th>
                            <th>{lang('user_name')}</th>
                            <th>{lang('member_name')}</th>
                            <th>{lang('Country')}</th>
                            <th>{lang('total_wallet_amount')}</th>
                            <th>{lang('date')}</th>                               
                            <th>{lang('action')}</th>
                        </tr>
                    </thead>
                    {if $count>0} 
                        {assign var="path" value="{$BASE_URL}admin/"}
                        {* {var_dump($agent_detail)} *}
                        <tbody>
                            {foreach from=$agent_detail item=v}
                             {$i=$i++}
                                {assign var="id" value="{$v.user_id}"}
                                <tr>
                                    <td>{$ci->input->get('offset')+$i}</td>
                                    {* <td>{$ci->input->get('offset')+$i}</td> *}
                                    <td>{$v.user_name}</td>
                                    <td>{$v.full_name}</td>
                                    <td>{$v.country}</td>
                                    <td>{format_currency($v.wallet_total)}</td>
                                    <td>{$v.date_added}</td>

                                    <td>
                                    {if $v.status =='yes'}     
                                        <a href="#" onclick="edit_agent({$v.user_id}, '{$path}')" title="Edit" class="btn-link text-info btn_size" data-placement="top" data-original-title="{lang('edit')}"><i class="fa fa-edit"></i></a>
                                        <button class="btn btn-light-grey btn-xs text-black inactivate_membership_package" onclick="inactivate_agent({$v.user_id}, '{$path}')" title="{lang('inactivate')}"><i class="fa fa-ban"></i></button>
                                        <button class="btn btn-danger btn-xs text-black delete_agent" onclick="delete_agent({$v.user_id}, '{$path}')" title="{lang('delete')}"><i class="fa fa-trash-o"></i></button>
                                    {else}    
                                         
                                        <button class="btn btn-light-grey btn-xs text-black" onclick="activate_agent({$v.user_id}, '{$path}')" title="{lang('activate')}"><i class="icon-check"></i></button>
                                        <button class="btn btn-danger btn-xs text-black delete_agent" onclick="delete_agent({$v.user_id}, '{$path}')" title="{lang('delete')}"><i class="fa fa-trash-o"></i></button>
                                    {/if}         
                                    </td> 
                                </tr>
                                {$i=$i+1}
                            {/foreach}
                        </tbody>
                    {else}                   
                        <tbody>
                            <tr><td colspan="12" align="center"><h4>{lang('No_User_Found')}</h4></td></tr>
                        </tbody> 
                    {/if}
            </table>
            {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
        </div>
        </div>
        </div>
{/block}

{block name=script}
     {$smarty.block.parent}
    
     <script type="text/javascript">
        function inactivate_agent(id, root)
        {
            var confirm_msg = $('#confirm_msg_inactivate').html();
            if (confirm(confirm_msg))
            {
                document.location.href = root + 'member/inactivate_agent/' + id;
            }
        }
        function activate_agent(id, root)
        {
            var confirm_msg = $('#confirm_msg_activate').html();
            if (confirm(confirm_msg))
            {
                document.location.href = root + 'member/activate_agent/' + id;
            }
        }

        function edit_agent(id, root) {
            var confirm_msg = $('#confirm_msg_edit').html();
            document.location.href = root + 'member/agent_creation/edit/' + id;
        }
        function delete_agent(id, root)
        {
            var confirm_msg = $('#confirm_msg_delete').html();
            if (confirm(confirm_msg))
            {
                document.location.href = root + 'member/delete_agent/' + id;
            }
        }
     </script>
{/block}