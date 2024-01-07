{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display: none;"> 
    <span id="row_msg">{lang('rows')}</span>
    <span id="show_msg">{lang('shows')}</span>
    <span id="validate_msg1">{lang('enter_subject')}</span>
    <span id="validate_msg2">{lang('enter_mail_content')}</span>
    <span id="validate_msg3">{lang('enter_mail_number')}</span>
    <span id="validate_msg4">{lang('enter_mail_send_date')}</span>
    <span id="validate_msg5">{lang('do_you_want_to_edit_this')}</span>
    <span id="validate_msg6">{lang('do_you_want_to_delete_this')}</span>
</div>

<div class="hbox hbox-auto-xs hbox-auto-sm" ng-controller="MailCtrl">
       {include file="admin/auto_responder/mail_header.tpl"  name=""}
        <div class="col">
            <div>
                <ul class="list-group list-group-lg no-radius m-b-none m-t-n-xxs">
                <li class="list-group-item clearfix b-l-3x b-l-info">
                    <div class="tab">
                        <div class="content">
                       {form_open('admin/auto_responder_settings' , 'role="form" class="" method="post"  name="mail_auto_settings" id="mail_auto_settings"')}
                        {include file="layout/error_box.tpl"} 
                        {assign var="tabindexvalue" value="4"}

                        <div class="form-group" id="user_div">
                            <input  type='text' class='form-control' name='subject' id='subject' value="{if $edit == 'true'}{$mail_details['subject']}{else}{$sub}{/if}" placeholder='{lang('subject')}'autocomplete="Off"/>
                            <input class="form-control"  type="hidden"  name ="mail_number" id ="mail_number"value="{if $edit == 'true'}{$mail_details['mail_number']} {else}NA{/if}" autocomplete="Off" >
                            {form_error('subject')}
                        </div>
                        {form_error('user_id')}
                        {$tabindexvalue = 5}

                        <div class="form-group">
                            <select  class="form-control" id="date_to_send" name="date_to_send" style ="width:200px">
                                <option value="" selected>{lang('mail_send_date')}</option>
                                {if $edit == 'true'}
                                    <option value="{$current_date}" selected>{$current_date}</option>
                                {/if} 
                                {if $send_date != ''}
                                    <option value="{$send_date}" selected> {$send_date} </option>
                                {/if} 
                                {for $date=1 to 31}<option value="{$date}">{$date}</option>{/for}
                            </select>
                            {form_error('date_to_send')}
                        </div>
 
                    <div class="form-group">                                                
                        <textarea class="textarea textfixed" name='mail_content' id='message1' placeholder="{lang('mail_content')}" style="width: 100%; height: 125px; font-size: 14px; line-height: 18px; border: 1px solid #dddddd; padding: 10px;">
                            {if $edit == 'true'}{$mail_details['content']}{else}{$mail_cond}{/if}
                        </textarea>
                        <span class='val-error' id="err_mail_content">{form_error('mail_content')}</span>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit" name="update"  id="update" value="Update">{if $edit == 'true'}{lang('update')}{else}{lang('Add')}{/if}</button>
                        {if $edit == 'true'}
                        <a class="btn btn-sm btn-primary" href="../../../auto_responder/auto_responder_details">{lang('Back')}</a>
                        {/if}
                    </div>
                    
                    <input type="hidden" id="path_temp" name="path_temp" value="{$PUBLIC_URL}">

                {form_close()}
                        </div>     
                    </div>     
                </li>
                </ul>
                <div class="wrapper-sm">
                 {include file="common/notes.tpl" notes=lang('note_autoresponder')}
                 </div>
            </div>
        </div>
    </div>
        <div>
       
        </div>


{/block}

{block name=script}
  {$smarty.block.parent}
    <script src="{$PUBLIC_URL}javascript/validate_auto_responder.js" type="text/javascript" ></script>
    <script src="{$PUBLIC_URL}javascript/bootstrap3-wysihtml5.all.min.js" type="text/javascript" ></script>
{/block}

{block name=style}
  {$smarty.block.parent}
      <link rel="stylesheet" href="{$PUBLIC_URL}css/bootstrap3-wysihtml5.min.css" type="text/css" />
{/block}
