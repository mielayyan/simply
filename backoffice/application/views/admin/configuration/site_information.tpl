{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<style>
    .form-control-static-2 {
        padding-top: 0px;
        background-color: #d9edf7;
        border: 1px solid #bce8f1;
        border-radius: 0px !important;
        color: #bce8f1;
        font-family: inherit;
        font-size: 14px;
        line-height: 1.6;
        padding: 5px 10px;
        transition-duration: 0.1s;
        box-shadow: none;
        overflow: hidden;
    }
    input[type="file"] {
        z-index: -1;
    }
</style>    
<div id="span_js_messages" style="display: none;"> 
    <span id="validate_msg1">{lang('you_must_enter_company_name')}</span>
    <span id="validate_msg2">{lang('non_valid_file')}</span>
    <span id="validate_msg3">{lang('only_png_jpg')}</span>
    <span id="validate_msg4">{lang('you_must_enter_email')}</span>
    <span id="validate_msg5">{lang('you_must_enter_valid_email')}</span>
    <span id="validate_msg15">{lang('you_must_enter_valid_url')}</span>
    <span id="validate_msg6">{lang('you_must_enter_phone')}</span>
    <span id="validate_msg7">{lang('you_must_enter_valid_phone')}</span>
    <span id="validate_msg8">{lang('you_must_company_address')}</span>
    <span id="validate_msg9">{lang('your_email_address_must_be_in_the_format_of_name@domain.com')}</span>
    <span id="validate_msg10">{lang('your_url_address_must_be_in_the_format')}</span>
    <span id="validate_msg11">{lang('digits_only')}</span>
    <span id="validate_msg12">{lang('facebook_url_is_required')}</span>
    <span id="validate_msg13">{lang('twitter_url_is_required')}</span>
    <span id="validate_msg14">{lang('instagram_url_is_required')}</span>
    <span id="validate_msg20">{lang('google_plus_url_is_required')}</span>
    <span id="validate_msg16">{lang('facebook_url_is_not_valid')}</span>
    <span id="validate_msg17">{lang('twitter_url_is_not_valid')}</span>
    <span id="validate_msg18">{lang('instagram_url_is_not_valid')}</span>
    <span id="validate_msg19">{lang('google_plus_url_is_not_valid')}</span>
    <span id="validate_msg21">{lang('max_10_digit_allowed')}</span>
    <span id="validate_msg22">{lang('max_100_char_allowed')}</span>
    <span id="validate_msg23">{lang('please_enter_atleast_5_digits')}</span>
    <span id="validate_msg24">{lang('you_must_enter_facebook_followers_count')}</span>
    <span id="validate_msg25">{lang('you_must_enter_twitter_followers_count')}</span>
    <span id="validate_msg26">{lang('you_must_enter_instagram_followers_count')}</span>
    <span id="validate_msg27">{lang('you_must_enter_google_followers_count')}</span>
    <span id="validate_msg28">{lang('you_must_enter_facebook_url')}</span>
    <span id="validate_msg29">{lang('you_must_enter_twitter_url')}</span>
    <span id="validate_msg30">{lang('you_must_enter_instagram_url')}</span>
    <span id="validate_msg31">{lang('you_must_enter_google_url')}</span>
</div>

<main>
  <div class="tabsy">
    <input type="radio" id="tab1" name="tab" {if $tab1} checked {/if}>
    <label class="tabButton" for="tab1">{lang('site_information')}</label>
        <div class="tab">
            <div class="content">
                {form_open_multipart('admin/configuration/site_information','role="form" class="" method="post"  name="site_config" id="site_config"')}
                {include file="layout/error_box.tpl"}
            
                <div class="form-group">
                    <label class="control-label required" for="co_name">{lang('company_name')}</label>
                      <input type="text" class="form-control" name="co_name" id="co_name" autocomplete="Off"  value="{$site_info_arr["co_name"]}">
                      {form_error('co_name')}
                </div>
            
                <div class="form-group">
                    <label class="control-label required" for="company_address">{lang('company_address')}</label>
                      <textarea class="form-control required" name="company_address" id="company_address"  rows="" cols="30"   autocomplete="Off" >{$site_info_arr["company_address"]}</textarea>
                      {form_error('company_address')}
                </div>
                
                <input type="hidden" name="def_lan" id="def_lan" value="{$default_lang}" />
            
                <div class="form-group">
                    <label class="control-label required" for="email">{lang('email')}</label>
                    
                    <div class="input-group m-b">
                    <span class="input-group-addon"><i class="glyphicon glyphicon-envelope"></i></span>
                    <input class="form-control" type="text"  name="email" id="email"   autocomplete="Off"  value="{$site_info_arr["email"]}">
                     {form_error('email')}
                    </div>
                    
                      
                </div>
            
                <div class="form-group">
                    <label class="control-label required" for="img_logo">{lang('phone')}</label>
                    
                    <div class="input-group m-b">
                    <span class="input-group-addon"><i class="fa fa-phone"></i></span>
                    <input class="form-control" type="text"  name="phone" id="phone" autocomplete="Off" value="{$site_info_arr["phone"]}">
                    <span id="errmsg1"></span>
                    {form_error('phone')}
                </div>      
             </div>
            
          <hr class="new_line" />
             <div class="col-sm-6 padding_both">
                     <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="fileupload fileupload-new " data-provides="fileupload" >
                                <div class="thumb pull-right m-l m-t-xs avatar">
                                     <div class="fileupload-new thumbnail"><img src="{$SITE_URL}/uploads/images/logos/{$site_info_arr["login_logo"]}" alt="" value="{$site_info_arr["login_logo"]}">
                                       </div>
                                     <div class="fileupload-preview fileupload-exists thumbnail" ></div>
                                </div>
                                <div class="user-edit-image-buttons">
                                    <div class="clear"> <a href="" class="text-info block m-b-xs">{lang('logo_for_light_background')} <i class="icon-twitter"></i></a>
                                        <span class="btn btn-light-grey-new btn-file"><span class="fileupload-new"><button type="button" class="btn btn-addon btn-info"> <i class="fa fa-arrow-circle-o-up"></i> {lang('upload')} </button></span><span class="btn fileupload-exists btn-warning"><i class="fa fa-picture"></i> {lang('Change')}</span>                                           
                                            <input type="file" id="login_logo" name="login_logo"  value="{$site_info_arr["login_logo"]}">
                                        </span>
                                        <a href="#" class="btn fileupload-exists btn-info" data-dismiss="fileupload">
                                            <i class="fa fa-times"></i>{lang('Remove')}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </div>
                </div>
          
            <div class="col-sm-6 padding_both_small">
                 <div class="panel panel-info">
                     <div class="panel-body">                             
                             <div class="fileupload fileupload-new" data-provides="fileupload" >
                                 <div class="thumb pull-right m-l m-t-xs avatar">
                                     <div class="fileupload-new thumbnail" style=""><img src="{$SITE_URL}/uploads/images/logos/{$site_info_arr["logo"]}" alt="" value="{$site_info_arr["logo"]}" alt="...">
                                     </div>
                                     <div class="fileupload-preview fileupload-exists thumbnail" ></div>
                                 </div>
                                 <div class="user-edit-image-buttons">
                                     <div class="clear"> <a href="" class="text-info block m-b-xs">{lang('logo_for_dark_background')} <i class="icon-twitter"></i></a>
                                     <span class="btn btn-light-grey-new btn-file">
                                         <span class="fileupload-new"><button type="button" class="btn btn-addon btn-info"> <i class="fa fa-arrow-circle-o-up"></i> {lang('upload')} </button></span>
                                         <span class="btn fileupload-exists btn-warning"><i class="fa fa-picture"></i> {lang('Change')}</span>
                                         <input type="file" id="img_logo" name="img_logo"  value="{$site_info_arr["logo"]}">
                                     </span>
                                     <a href="#" class="btn fileupload-exists btn-info" data-dismiss="fileupload">
                                         <i class="fa fa-times"></i>{lang('Remove')}
                                     </a>
                                 </div>
                                 </div>
                         </div>             
                     </div>
                 </div>
                </div> 
         <div class="col-sm-6 padding_both">
                 <div class="panel panel-info">
                     <div class="panel-body">                             
                             <div class="fileupload fileupload-new" data-provides="fileupload" >
                                 <div class="thumb pull-right m-l m-t-xs avatar">
                                     <div class="fileupload-new thumbnail" style=""><img src="{$SITE_URL}/uploads/images/logos/{$site_info_arr["shrink_logo"]}" alt="" value="{$site_info_arr["shrink_logo"]}" alt="...">
                                     </div>
                                     <div class="fileupload-preview fileupload-exists thumbnail" ></div>
                                 </div>
                                 <div class="user-edit-image-buttons">
                                     <div class="clear"> <a href="" class="text-info block m-b-xs">{lang('logo_for_collapsed_sidebar')} <i class="icon-twitter"></i></a>
                                     <span class="btn btn-light-grey-new btn-file">
                                         <span class="fileupload-new"><button type="button" class="btn btn-addon btn-info"> <i class="fa fa-arrow-circle-o-up"></i> {lang('upload')} </button></span>
                                         <span class="btn fileupload-exists btn-warning"><i class="fa fa-picture"></i> {lang('Change')}</span>
                                         <input type="file" id="shrink_logo" name="shrink_logo"  value="{$site_info_arr["shrink_logo"]}">
                                     </span>
                                     <a href="#" class="btn fileupload-exists btn-info" data-dismiss="fileupload">
                                         <i class="fa fa-times"></i>{lang('Remove')}
                                     </a>
                                 </div>
                                 </div>
                         </div>             
                     </div>
                 </div>
           </div> 
            <div class="col-sm-6 padding_both_small">
                     <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="fileupload fileupload-new " data-provides="fileupload" >
                                <div class="thumb pull-right m-l m-t-xs avatar">
                                     <div class="fileupload-new thumbnail"><img src="{$SITE_URL}/uploads/images/logos/{$site_info_arr["favicon"]}" alt="" value="{$site_info_arr["favicon"]}">
                                       </div>
                                     <div class="fileupload-preview fileupload-exists thumbnail" ></div>
                                </div>
                                <div class="user-edit-image-buttons">
                                  <div class="clear"> <a href="" class="text-info block m-b-xs">{lang('favicon')} <i class="icon-twitter"></i></a>
                                        <span class="btn btn-light-grey-new btn-file"><span class="fileupload-new"><button type="button" class="btn btn-addon btn-info"> <i class="fa fa-arrow-circle-o-up"></i> {lang('upload')} </button></span><span class="btn fileupload-exists btn-warning"><i class="fa fa-picture"></i> {lang('Change')}</span>                                           
                                            <input type="file" id="favicon" name="favicon"  value="{$site_info_arr["favicon"]}">
                                        </span>
                                        <a href="#" class="btn fileupload-exists btn-info" data-dismiss="fileupload">
                                            <i class="fa fa-times"></i>{lang('Remove')}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </div>
                </div>
                <div>
                 <p id="2" style="color: #31708f;" class="ext form-control-static-2 m-t-xs">{lang(max_size)|replace:'%s':'20MB'}<br>
                 {lang(allowed_type)}  png | jpeg | jpg | gif | ico<br>
                 {lang(ideal_imagesize_light)|replace:'%s':'240x40 pixel'}<br>
                 {lang(ideal_imagesize_dark)|replace:'%s':'242x50 pixel'}<br>
                 {lang(ideal_imagesize_collapsed)|replace:'%s':'16 x 16 , 32 x 32, 48 x 48 pixels'}<br>
                 {lang(ideal_imagesize_favicon)|replace:'%s':'16 x 16 , 32 x 32, 48 x 48 pixels'}<br>
                 </p>
                </div>
                <div class="form-group">
                    <button class="btn btn-sm btn-primary" name="site" id="site" value="{lang('update')}">{lang('update')}</button>
                </div>
                {form_close()}
            </div>
        </div>
        </div>
</main>

{/block}

{block name='script'}
    {$smarty.block.parent}
    <script>
        $('.fileupload-new').on('click', function() {
          $(this).closest('.clear').find('input').trigger('click');
        })
    </script>
{/block}