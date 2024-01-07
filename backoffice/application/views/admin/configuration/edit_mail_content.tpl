{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
  <div class="button_back">
        <a href="{BASE_URL}/admin/mail_content" class="btn m-b-xs btn-sm btn-info btn-addon"><i class="fa fa-backward"></i> {lang('back')}</a>
    </div>
 <div class="panel panel-default">
   <div class="panel-body">
    {form_open('admin/configuration/update_mail_content','role="form" class="" name="payout_mail_settings" id="payout_mail_settings"')}
        {include file="layout/error_box.tpl"} 
            <div class="form-group">
                <label class=" control-label required" >{lang('subject')}</label>
                    <input class="form-control"  type="text"  name ="subject1" id ="subject1" value="{$content['subject']}" autocomplete="Off">
                    <span>{form_error('subject1')}</span>
            </div>
                        
            <div class="form-group">
                <label class="control-label required" for="mail_content1">
                    {lang('mail_content')}
                </label>
                <textarea id="mail_content1"  name="mail_content1" class="ckeditor form-control" rows='10'>
                    {$content['content']}
                </textarea>
                <span>{form_error('mail_content1')}</span>
            </div>
                        
            <div class="form-group">
                <label class="control-label"></label>
                <label> <span class="symbol required"></span>{lang('mail_msg')}</label> 
            </div>
                        
            <div class="form-group">
                <label class=" control-label"></label>
                <p class="m-b">
                    <label>{lang('other_variables_that_you_can_use')}</label> <br>
                        <code>A</code>{literal}{fullname}{/literal}<br>
                        <code>B</code>{literal}{company_name}{/literal}<br>
                        <code>C</code>{literal}{company_address}{/literal}<br>
                </p>
            </div>

            <div class="form-group">
                <input type="hidden" name="content_id" value="{$content['id']}">
                <input type="hidden" name="content_type" value="{$content['mail_type']}">
                <button class="btn btn-sm btn-primary" type="submit" id="payout_release" >
                    {lang('update')}
                </button>
            </div>  
        {form_close()}
    </div>
 </div>
{/block}
