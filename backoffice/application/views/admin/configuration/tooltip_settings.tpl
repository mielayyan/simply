{extends file=$BASE_TEMPLATE}
{block name= "script"}
 <script src="{$PUBLIC_URL}/plugins/bootstrap-fileupload/bootstrap-fileupload.min.js"></script>
 <script>
    
    $(document).ready(function() {
     
     var tree_based_on = '{$tree_based_on}';
     if(tree_based_on == 'member_pack'){
       $('.rank_div').addClass('hidden');
       $('.package_div').removeClass('hidden');
       $('.memberstatus_div').addClass('hidden');

    }else if(tree_based_on == 'rank'){

       $('.rank_div').removeClass('hidden');
       $('.package_div').addClass('hidden');
       $('.memberstatus_div').addClass('hidden');


    }else if(tree_based_on == 'member_status'){

         $('.memberstatus_div').removeClass('hidden');
         $('.package_div').addClass('hidden');
         $('.rank_div').addClass('hidden');

    }
    else{
         
         $('.package_div').addClass('hidden');
         $('.rank_div').addClass('hidden');
         $('.memberstatus_div').addClass('hidden');
     }
    
    });
    
    function uploadtype(a){
        
        if(a == 'member_pack'){
           $('.rank_div').addClass('hidden');
           $('.package_div').removeClass('hidden');
           $('.memberstatus_div').addClass('hidden');

        }else if(a == 'rank'){
           
           $('.rank_div').removeClass('hidden');
           $('.package_div').addClass('hidden');
           $('.memberstatus_div').addClass('hidden');

        }else if(a == 'member_status'){

         $('.package_div').addClass('hidden');
         $('.rank_div').addClass('hidden');
         $('.memberstatus_div').removeClass('hidden');

        }else{
         
         $('.package_div').addClass('hidden');
         $('.rank_div').addClass('hidden');
         $('.memberstatus_div').addClass('hidden');

        }
    }
            
 </script>-->   

{/block}
{block name=$CONTENT_BLOCK}
<link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}/plugins/bootstrap-fileupload/bootstrap-fileupload.min.css">


{include file="admin/configuration/advanced_settings.tpl"}
<div class="panel panel-default">
        <div class="panel-body">
         {form_open_multipart('admin/configuration/treeIconConfig', 'role="form" class="form" method="post"  name="subscription_settings_form" id="subscription_settings_form"')}   
           <legend>{lang('tree_icon')}</legend>  
           <div class="form-group">
             <label class="required">{lang('tree_icon')} {lang('based_on')}</label>
             <select class="form-control" id="tree_criteria" name="tree_criteria" onchange="uploadtype(this.value);">
                <option value = "member_status" {if $tree_based_on == 'member_status'} selected {/if}>{lang('member_status')}</option>
                <option value = "profile_image" {if $tree_based_on == 'profile_image'} selected {/if}>{lang('profile_image')}</option>
                <option value = "member_pack" {if $tree_based_on == 'member_pack'} selected{/if}>{lang('membership_pack')}</option>
                {if $MODULE_STATUS['rank_status'] == 'yes'}
                <option value = "rank" {if $tree_based_on == 'rank'} selected {/if}>{lang('rank')}</option>
                {/if}
             </select>
           </div>
           <div class="form-group file_upload_section rank_div row">

            {foreach from=$rank_details item=v}
                
            <div class="upload_div col-sm-4">
                     <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="fileupload fileupload-new " data-provides="fileupload" >
                                <div class="thumb pull-right m-l m-t-xs avatar">
                                     <div class="fileupload-new thumbnail"><img id = "thumbnail" src="{$SITE_URL}/uploads/images/tree/{$v.tree_icon}" alt="" value="">
                                       </div>
                                     <div class="fileupload-preview fileupload-exists thumbnail" ></div>
                                </div>
                                <div class="user-edit-image-buttons">
                                    <div class="clear"> <a href="" class="text-info block m-b-xs"><b class="image_user">{$v.rank_name} </b><i class="icon-twitter"></i></a>
                                        <span class="btn btn-light-grey-new btn-file"><span class="fileupload-new"><button type="button" class="btn btn-addon btn-info"> <i class="fa fa-arrow-circle-o-up"></i> {lang('upload')} </button></span><span class="btn fileupload-exists btn-warning"><i class="fa fa-picture"></i> {lang('Change')}</span>                                           
                                            <input type="file" id="tree_icon_rank" name="tree_icon_rank{$v.rank_id}"  value="">
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

               {/foreach} 
               
           </div>
           <div class = "form-group file_upload_section package_div row">

            {foreach from=$membership_package item=v}
             <div class="upload_div col-sm-4">
                     <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="fileupload fileupload-new " data-provides="fileupload" >
                                <div class="thumb pull-right m-l m-t-xs avatar">
                                     <div class="fileupload-new thumbnail"><img id = "thumbnail" src="{$SITE_URL}/uploads/images/tree/{$v.tree_icon}" alt="" value="">
                                       </div>
                                     <div class="fileupload-preview fileupload-exists thumbnail" ></div>
                                </div>
                                <div class="user-edit-image-buttons">
                                    <div class="clear"> <a href="" class="text-info block m-b-xs"><b class="image_user">{$v.product_name}</b><i class="icon-twitter"></i></a>
                                        <span class="btn btn-light-grey-new btn-file"><span class="fileupload-new"><button type="button" class="btn btn-addon btn-info"> <i class="fa fa-arrow-circle-o-up"></i> {lang('upload')} </button></span><span class="btn fileupload-exists btn-warning"><i class="fa fa-picture"></i> {lang('Change')}</span>                                           
                                            <input type="file" id="tree_icon_mem" name="tree_icon_mem{$v.product_id}"  value="">
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
            {/foreach}

           </div>
           <div class = "form-group file_upload_section memberstatus_div row">

            {foreach from=$member_status item=v}
             <div class="upload_div col-sm-4">
                     <div class="panel panel-info">
                        <div class="panel-body">
                            <div class="fileupload fileupload-new " data-provides="fileupload" >
                                <div class="thumb pull-right m-l m-t-xs avatar">
                                     <div class="fileupload-new thumbnail"><img id = "thumbnail" src="{$SITE_URL}/uploads/images/tree/{$v.tree_icon}" alt="" value="">
                                       </div>
                                     <div class="fileupload-preview fileupload-exists thumbnail" ></div>
                                </div>
                                <div class="user-edit-image-buttons">
                                    <div class="clear"> <a href="" class="text-info block m-b-xs"><b class="image_user">{$v.status_name}</b><i class="icon-twitter"></i></a>
                                        <span class="btn btn-light-grey-new btn-file"><span class="fileupload-new"><button type="button" class="btn btn-addon btn-info"> <i class="fa fa-arrow-circle-o-up"></i> {lang('upload')} </button></span><span class="btn fileupload-exists btn-warning"><i class="fa fa-picture"></i> {lang('Change')}</span>                                           
                                            <input type="file" id="tree_icon" name="tree_icon{$v.id}"  value="">
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
            {/foreach}

           </div>
            <div class="form-group">
             <button type="submit" class="btn btn-sm btn-primary" value="update_tree_icon" name="update_tree_icon" id="update_tree_icon">{lang('update')}</button>
            </div>
            
         {form_close()}
        </div>
 </div>

{form_open('', 'role="form" class="" method="post" name="tooltip_settings" id="tooltip_settings"')}
    <legend>{lang('tooltip_details')}</legend>
    <div class="panel panel-default">
    <div class=" table-responsive">
    {* <legend>
    <span class="fieldset-legend">
        {lang('tooltip_settings')}
    </span>
</legend> *}
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>
                        <div class="checkbox">
                            <label class="i-checks">
                                <input type="checkbox" name="" id="" class="select-checkbox-all">
                                <i></i>
                            </label>
                        </div>
                    </th>
                    {* <th>{lang('sl_no')}</th> *}
                    <th>{lang('label')}</th>
                    {* <th width="20%">{lang('check/uncheck')}</th> *}
                </tr>
            </thead>
            <tbody>
                {foreach from=$tooltip item=v}
                    {if $v.label == 'rank_status' && $MODULE_STATUS['rank_status'] == "no"}
                        {{continue}}
                    {/if}
                    <tr>
                        {* <td>{counter}</td> *}
                        <td class="">
                            <div class="checkbox">
                                <label class="i-checks">
                                    <input class="select-checkbox-single" type="checkbox" name="{$v.id}" id="inlineCheckbox1-{$v.id}" {if $v.status=='yes'} checked {/if} value="{$v.id}" id="{$v.id}"><i></i>
                                </label>
                            </div>
                        </td>
                        <td>{lang($v.label)}</td>
                    </tr>
                {/foreach}
            </tbody>
        </table>
        </div>
        <div class="panel-footer">
         <button class="btn btn-sm btn-primary" name="update" type="submit" value="update">{lang('update')}</button>
         </div>
    </div>
     
{form_close()}

{/block}
