{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
  <div class="button_back">
        <a href="{BASE_URL}/admin/content_management" class="btn m-b-xs btn-sm btn-info btn-addon"><i class="fa fa-backward"></i> {lang('back')}</a>
    </div>
<div class="panel panel-default">
  <div class="panel-body">
      <div class="panel panel-default">
        <a data-toggle="collapse" data-parent="#accordion" href="#tab-default" aria-expanded="true">
          <div class="panel-heading">
            <h4 class="panel-title">{lang('default_content_replica')}
                <span class="pull-right panel-collapse-clickable" data-toggle="collapse" data-parent="#accordion" href="#tab-3"> <i class="glyphicon glyphicon-chevron-down"></i> </span>
            </h4>
          </div>
          <div id="tab-default" class="panel-collapse panel-collapse collapse in" aria-expanded="true">
            <div class="panel-body">
                {form_open_multipart('admin/configuration/content_management','role="form" class="" name= "content_form"  id="content_form"')}
                
                <input type="hidden" name="lang_id" id="lang_id" value="{$lang_id}"/>
                    {include file="layout/error_box.tpl"}
                        <div class="form-group">
                          <label class="control-label" for="replica_content_main">{lang('title1')}</label>
                           <input type="text" class="form-control" name="home_title1" id="home_title1" value="{$replica_default_content['home_title1']}">
                        </div>
                        <div class="form-group">
                          <label class="control-label" for="replica_content_main">{lang('title2')}</label>
                           <input type="text" class="form-control" name="home_title2" id="home_title2" value="{$replica_default_content['home_title2']}">
                        </div>
                        
                        <div class="form-group">
                          <label class="control-label" for="replica_content_plan">{lang('our_plan')}</label>
                            <textarea class="ckeditor form-control"  id="plan"  name="plan" rows="6">
                              {if isset($replica_default_content['plan'])}
                                {$replica_default_content['plan']}
                              {/if}
                            </textarea>
                            {form_error('plan')}
                        </div>
                        <div class="form-group">
                          <label class="control-label" for="replica_content_contact">{lang('phone')}</label>
                           <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="{$replica_default_content['contact_phone']}">
                        </div>
                        <div class="form-group">
                          <label class="control-label" for="replica_content_contact">{lang('mail')}</label>
                           <input type="text" class="form-control" name="contact_mail" id="contact_mail" value="{$replica_default_content['contact_mail']}">
                        </div>
                        <div class="form-group">
                          <label class="control-label" for="replica_content_contact">{lang('address')}</label>
                           <textarea class="form-control textarea_height_fix" name="contact_address" id="contact_address" value="">{$replica_default_content['contact_address']}</textarea>
                        </div>
                         <div class="form-group">
                          <label class="control-label" for="replica_content_policy">{lang('policy')}</label>
                            <textarea class="ckeditor form-control"  id="policy"  name="policy" title=""  rows="6">
                            {$replica_default_content['policy']}
                            </textarea>
                            {form_error('policy')}
                        </div>
                         <div class="form-group">
                          <label class="control-label" for="replica_content_terms">{lang('terms')}</label>
                            <textarea class="ckeditor form-control"  id="terms"  name="terms" title=""  rows="6">{$replica_default_content['terms']}</textarea>
                            {form_error('terms')}
                        </div>

                        <div class="form-group">
                          <label class="control-label" for="txtDefaultHtmlArea">{lang('about_us')}</label>
                          <textarea class="ckeditor form-control"  id="about"  name="about" title="{lang('main_matter')}"  rows="6">{$replica_default_content['about']}</textarea>
                                {form_error('content_terms')}
                        </div>

                         <div class="form-group">
                            <button class="btn btn-sm btn-primary"  name="default_replica_content" id="default_replica_content" type="submit" value="{lang('update')}" > {lang('update')}</button>
                        </div>
                    {form_close()}
                </div>  
            </div>
            </a>
  </div>
      <div class="panel panel-default">
        <a data-toggle="collapse" data-parent="#accordion" href="#tab-3" aria-expanded="true">
          <div class="panel-heading">
            <h4 class="panel-title">{lang('user_content')}
                <span class="pull-right panel-collapse-clickable" data-toggle="collapse" data-parent="#accordion" href="#tab-3"> <i class="glyphicon glyphicon-chevron-down"></i> </span>
            </h4>
          </div>
          <div id="tab-3" class="panel-collapse panel-collapse collapse in" aria-expanded="true">
            <div class="panel-body">
                {form_open_multipart('admin/configuration/content_management','role="form" class="" name= "content_form"  id="content_form"')}
                
                <input type="hidden" name="lang_id" id="lang_id" value="{$lang_id}"/>
                    {include file="layout/error_box.tpl"}
                        <div class="form-group">
                          <label class="control-label" for="replica_content_main">{lang('title1')}</label>
                           <input type="text" class="form-control" name="home_title1" id="home_title1" value="{$replica['home_title1']}">
                        </div>
                        <div class="form-group">
                          <label class="control-label" for="replica_content_main">{lang('title2')}</label>
                           <input type="text" class="form-control" name="home_title2" id="home_title2" value="{$replica['home_title2']}">
                        </div>
                        
                        <div class="form-group">
                          <label class="control-label" for="replica_content_plan">{lang('our_plan')}</label>
                            <textarea class="ckeditor form-control"  id="plan"  name="plan" rows="6">
                              {if isset($replica['plan'])}
                                {$replica['plan']}
                              {/if}
                            </textarea>
                            {form_error('plan')}
                        </div>
                        <div class="form-group">
                          <label class="control-label" for="replica_content_contact">{lang('phone')}</label>
                           <input type="text" class="form-control" name="contact_phone" id="contact_phone" value="{$replica['contact_phone']}">
                        </div>
                        <div class="form-group">
                          <label class="control-label" for="replica_content_contact">{lang('mail')}</label>
                           <input type="text" class="form-control" name="contact_mail" id="contact_mail" value="{$replica['contact_mail']}">
                        </div>
                        <div class="form-group">
                          <label class="control-label" for="replica_content_contact">{lang('address')}</label>
                           <textarea class="form-control textarea_height_fix" name="contact_address" id="contact_address" value="">{$replica['contact_address']}</textarea>
                        </div>
                         <div class="form-group">
                          <label class="control-label" for="replica_content_policy">{lang('policy')}</label>
                            <textarea class="ckeditor form-control"  id="policy"  name="policy" title=""  rows="6">
                            {$replica['policy']}
                            </textarea>
                            {form_error('policy')}
                        </div>
                         <div class="form-group">
                          <label class="control-label" for="replica_content_terms">{lang('terms')}</label>
                            <textarea class="ckeditor form-control"  id="terms"  name="terms" title=""  rows="6">{$replica['terms']}</textarea>
                            {form_error('terms')}
                        </div>

                        <div class="form-group">
                          <label class="control-label" for="txtDefaultHtmlArea">{lang('about_us')}</label>
                          <textarea class="ckeditor form-control"  id="about"  name="about" title="{lang('main_matter')}"  rows="6">{$replica['about']}</textarea>
                                {form_error('content_terms')}
                        </div>

                         <div class="form-group">
                            <button class="btn btn-sm btn-primary"  name="replica_content" id="replica_content" type="submit" value="{lang('update')}" > {lang('update')}</button>
                        </div>
                    {form_close()}
                </div>  
            </div>
            </a>
  </div>
        </div>
</div>

{/block}