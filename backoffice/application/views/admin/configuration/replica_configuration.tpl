<div class="content">
    <div class="panel panel-default">
        <a data-toggle="collapse" data-parent="#accordion" href="#tab_new" aria-expanded="true">
            <div class="panel-heading">
                <h4 class="panel-title"> {lang('top_default_banner')}
                    <span class="pull-right panel-collapse-clickable" data-toggle="collapse" data-parent="#accordion" href="#tab1"> <i class="glyphicon glyphicon-chevron-down"></i> </span>
                </h4>
            </div>
        </a>
        <div id="tab_new" class="panel-collapse panel-collapse collapse in" aria-expanded="true">
            <div class="panel-body">
                {form_open_multipart('admin/configuration/content_management','role="form" class="" name="upload_materials" id="upload_materials1"')}
                    <div class="form-group">
                        <label class="control-label required">{lang('upload_top_banner')}</label>
                        <div data-provides="fileupload" class="bg_file_upload">
                          <input name="banner_image" id="banner_image" type="file">
                        </div>
                        <span class="help-block m-b-none">{lang('please_choose_a_png_file.')}</span>
                        <span class="help-block m-b-none">{lang('Max size 20MB')}
                        </span>    
                      </div>
                      <div class="form-group">
                        <label class="control-label" for="subtitle">{lang('current_top_banner')}</label>
                        {if isset($defualt_banner)}
                            <input class="form-control" type="" pe="text" value="{$defualt_banner}" readonly='true' style="overflow: hidden;white-space: nowrap;">
                        {else}
                            <input class="form-control" type="text" value="banner-tchnoly.jpg" readonly='true' style="overflow: hidden;white-space: nowrap;">
                        {/if}
                      </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" name="submit_default_image" id="submit_default_image" type="submit" value="submit">{lang('upload')}</button>
                    </div>
                {form_close()}            
              </div>                                        
            </div>  
    </div>
    <div class="panel panel-default">
        <a data-toggle="collapse" data-parent="#accordion" href="#tab" aria-expanded="true">
            <div class="panel-heading">
                <h4 class="panel-title"> {lang('top_banner')}
                    <span class="pull-right panel-collapse-clickable" data-toggle="collapse" data-parent="#accordion" href="#tab"> <i class="glyphicon glyphicon-chevron-down"></i> </span>
                </h4>
            </div>
        </a>
        <div id="tab" class="panel-collapse panel-collapse collapse in" aria-expanded="true">
            <div class="panel-body">
                {form_open_multipart('admin/configuration/content_management','role="form" class="" name="upload_materials" id="upload_materials1"')}
                    <div class="form-group">
                        <label class="control-label required">{lang('upload_top_banner')}</label>
                        <div data-provides="fileupload" class="bg_file_upload">
                          <input name="banner_image" id="banner_image" type="file">
                        </div>
                        <span class="help-block m-b-none">{lang('please_choose_a_png_file.')}</span>
                        <span class="help-block m-b-none">{lang('Max size 20MB')}
                        </span>    
                      </div>
                      <div class="form-group">
                        <label class="control-label" for="subtitle">{lang('current_top_banner')}</label>
                        {if isset($banner)}
                            <input class="form-control" type="text" value="{$banner}" readonly='true' style="overflow: hidden;white-space: nowrap;">
                        {else}
                            <input class="form-control" type="text" value="banner-tchnoly.jpg" readonly='true' style="overflow: hidden;white-space: nowrap;">
                        {/if}
                      </div>
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" name="submit_image" id="submit_image" type="submit" value="submit">{lang('upload')}</button>
                    </div>
                {form_close()}            
              </div>                                        
            </div>  
      </div>
                <div class="panel panel-default table-responsive">
                    <div class="panel-body">
                        <div id="overall" class="table-responsive hide show">
                            <table st-table="rowCollectionBasic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{lang('slno')}</th>
                                        <th>{lang('language')}</th>
                                        <th>{lang('action')}</th>
                                </thead>
                                {assign var="i" value=1}
                                <tbody>
                                     {foreach from=$language item=v}
                                    <tr>
                                        <td>{$i++}</td>
                                        <td>{ucfirst($v.lang_name_in_english)}</td>
                                        <td> <a href ="{$BASE_URL}admin/configuration/edit_replica_content/{$v.lang_id}" class="btn-link btn_size has-tooltip text-info" title="edit"><i class="fa fa-edit"></i></a> </td>
                                    </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>  
                    </div>
                </div>
</div>
