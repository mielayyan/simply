{extends file=$BASE_TEMPLATE}
{block name="script"} {$smarty.block.parent}
    <script src="{$PUBLIC_URL}javascript/validate_edit_profile.js"></script>
{/block}
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
    </style> 
    <div class="panel panel-default">
        <div class="panel-body">
            {form_open_multipart('user/profile/update_profileimg_banner','role="form" class="" name= "edit_profile"  id="edit_profile"')}
            <div class="col-sm-3 padding_both">
                <div class="form-group">
                    <label class="control-label  pro-label">{lang('upload_banner')}</label>
                    <div data-provides="fileupload" class="bg_file_upload pro_file_upload">
                        <input name="file2" id="file2" type="file">
                    </div>
                    <label id="fileLabel1" class="upload-filename">{lang('select_file')}</label>
                </div>
            </div>
            <div class="col-sm-3 padding_both_small">
                <div class="form-group">
                    <label class="control-label  pro-label">{lang('upload_profile_photo')}</label>
                    <div data-provides="fileupload" class="bg_file_upload pro_file_upload">
                        <input name="file1" id="file1" type="file">
                    </div>
                    <label id="fileLabel2" class="upload-filename">{lang('select_file')}</label>
                </div>
            </div>
            <div class="col-sm-3 padding_both_small">
                <div class="form-group mark_paid">
                    <button type="submit" class="btn btn-sm btn-primary">{lang('update_profile')}</button>
                </div>
            </div>
            {form_close()}
        </div>
    </div>
    <div>
                 <p id="2" style="color: #31708f;" class="ext form-control-static-2 m-t-xs">{lang(max_size)|replace:'%s':'2MB'}<br>
                 {lang(allowed_type)}  png | jpeg | jpg | gif <br> 
                 {lang(ideal_imagesize_banner)|replace:'%s':'1000 x 300 pixel'} <br>
                 {lang(ideal_imagesize_profile)|replace:'%s':'242 x 50 pixel'} <br>
                 </p>
                </div>
{/block}