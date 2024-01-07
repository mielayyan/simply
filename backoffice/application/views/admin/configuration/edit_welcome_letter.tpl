{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
  <div class="button_back">
        <a href="{BASE_URL}/admin/content_management" class="btn m-b-xs btn-sm btn-info btn-addon"><i class="fa fa-backward"></i> {lang('back')}</a>
    </div>
 <div class="panel panel-default">
   <div class="panel-body">
     {form_open_multipart('admin/configuration/content_management','role="form" class="" name= "letter_config"  id="letter_config"')}
             <div class="form-group">
                <label class="control-label" for="txtDefaultHtmlArea">{lang('main_matter')}</label>
                   <textarea class="ckeditor form-control"  id="txtDefaultHtmlArea"  name="txtDefaultHtmlArea" title="{lang('main_matter')}" >
                    {$letter_arr["main_matter"]}
                    </textarea>
                    {form_error('txtDefaultHtmlArea')}
              </div>
              <input type="hidden" name="lang_id" value="{$lang_id}">
               <div class="form-group">
                   <button class="btn btn-sm btn-primary" name="setting" id="setting" type="submit" value="{lang('update')}" > {lang('update')}</button>
                </div>

      {form_close()}
    </div>
 </div>

{/block}
