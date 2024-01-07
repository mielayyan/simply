{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<div class="panel panel-default">
    <div class="panel-body">
            <div class="content">
                {form_open('admin/profile/change_username','role="form" class="" id="" name="" method="post"')}
                <div class="col-sm-3 padding_both">
                    <div class="form-group">
                        <label>{lang('user_name')}</label>
                        <input class="form-control user_autolist autocomplete-off" type="text" id="user_name_common" name="user_name" value="" autocomplete="Off"><span id="referral_box" style="display:none;"></span> 
                        <span class="text-danger">{form_error('user_name')}</span>
                    </div>
                    
                </div>
                <div class="col-sm-3 padding_both_small">
                    <div class="form-group">
                        <label>{lang('new_user_name')}</label>
                        <input class="form-control act-pswd-popover" name="new_username" type="text" id="new_username" autocomplete="Off"/>
                        <span class="text-danger">{form_error('new_username')}</span>
                    </div>
                    
                </div>
                
                <input type="hidden" name="base_url" id="base_url" value="{$BASE_URL}admin/">
                <div class="col-sm-3 padding_both_small">
                    <div class="form-group mark_paid">
                        <button class="btn btn-sm btn-primary"  type="submit" name="change_username"  id="change_username" value="{lang('change_username')}" >{lang('update')}</button>
                    </div>
                </div>
                <input type="hidden" id="path_temp" name="path_temp" value="{$PUBLIC_URL}">
                {form_close()}
            </div>
    </div>
</div>
        
{/block}