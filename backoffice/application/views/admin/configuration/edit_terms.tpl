{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
  <div class="button_back">
        <a href="{BASE_URL}/admin/content_management" class="btn m-b-xs btn-sm btn-info btn-addon"><i class="fa fa-backward"></i> {lang('back')}</a>
  </div>
 <div class="panel panel-default">
   <div class="panel-body">
       {form_open_multipart('admin/configuration/content_management','role="form" class="" name= "terms_config" id= "terms_config"')}
                
                <div class="form-group">
                    <label class="control-label required" for="txtDefaultHtmlArea1">
                        {lang('terms_and_conditions')}
                    </label>
                        <textarea id="txtDefaultHtmlArea1"  name="txtDefaultHtmlArea1" class="ckeditor form-control">
                            {$terms}
                        </textarea>
                        {form_error('txtDefaultHtmlArea1')}
                </div>
                 <input type="hidden" name="lang_id" value="{$lang_id}">   
                <div class="form-group">
                    <button class="btn btn-sm btn-primary" name="content_submit" id="content_submit" type="submit" value="{lang('update')}" > {lang('update')}</button>
                </div>

      {form_close()}
    </div>
 </div>

{/block}
