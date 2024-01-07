{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
    <link rel="stylesheet" href="{$PUBLIC_URL}css/replica_configuration.css">
    <div class="m-b">
        {include file="common/notes.tpl" notes=lang('note_replication_configuration_page')}
    </div>

    <div class="panel-body panel panel-default"> 
        <h4 class="">{lang('current_top_banner')}</h4>     
        <div class="upload-container current_top_banner">
            <img src="{$SITE_URL}/uploads/images/banners/{$banners}">          
        </div>          
        {form_open_multipart('user/replica_configuration','role="form" class="" name="upload_materials" id="upload_materials1"')}
            <div class="upload-container">
              <div class="row">
                <div class="col-md-12">
                  <div class="form-group">                                        
                    <div class="preview-zone hidden">
                      <div class="box box-solid">
                        <div class="box-header with-border">
                          <div>{lang('preview')}</div>
                          <div class="box-tools pull-right">
                            <button type="button" class="btn btn-danger btn-xs remove-preview">
                              <i class="fa fa-times"></i>{lang('reset')}
                            </button>
                          </div>
                        </div>
                        <div class="box-body"></div>
                      </div>
                    </div>
                    <label class="control-label">{lang('upload_top_banner')}</label>
                    <div class="dropzone-wrapper">
                      <div class="dropzone-desc">
                        <i class="glyphicon glyphicon-download-alt"></i>
                        <p>{lang('choose_an_image_file_or_drag_it_here')}.</p>
                        <div class="dropzone-desc2">
                            <span class="">{lang('please_choose_a_png_file.')}</span>
                            <span class="">{lang('Max size 20MB')}</span>
                        </div>
                      </div>
                      <input type="file" name="banner_image" id="banner_image" class="dropzone" >
                    </div>
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-md-12">
                  <button type="submit" name="submit_image" value="submit" class="btn btn-primary">{lang('upload')}</button>
                </div>
              </div>
            </div>
        </form>                            
    </div>
{/block}

{block name=script}
    {$smarty.block.parent}
    <script src="{$PUBLIC_URL}javascript/replica_configuration.js"></script>
{/block}