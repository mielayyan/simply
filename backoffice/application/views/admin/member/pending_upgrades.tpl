{extends file=$BASE_TEMPLATE}
{block name = "script"}
    <script>
        function mym(image){
            document.getElementById("image_view").src = image;
            $("#EnSureModal").modal();
        }
        $('#check_all').change(function () {
            $(".release").prop('checked', $(this).is(':checked'));
        });
    </script>
    <div class="modal fade" id="EnSureModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                </div>
                <div class="modal-body" style="text-align:center;width:100%">
                    <img id="image_view" src="">
                </div>
                <div class="form-group m-l">
                 
                    <button type="button" class="btn btn-primary" data-dismiss="modal">{lang('close')}</button>
                 
                </div>
            </div>
        </div>
</div>
{/block}

{block name=$CONTENT_BLOCK}
    <div id="span_js_messages" style="display: none;">
        <span id="msg1">{lang('pending_registration_approval_confirm')}</span>
        <span id="error_msg">{lang('please_select_at_least_one_checkbox')}</span>   
    </div>
     <div class="m-b pink-gradient">
        <div class="card-body ">
            <div class="media">
                <figure class=" avatar-50 "> <i class="glyphicon glyphicon-book"></i> </figure>
                <h6 class="my-0">{lang('note_signup_approval')}</h6>
            </div>
        </div>
    </div>
    <div class="button_back">
        <a href="{BASE_URL}/admin/search_member"> <button class="btn m-b-xs btn-sm btn-info btn-addon"><i class="fa fa-backward"></i>{lang('back')}</button></a>
    </div>
    <div class="panel panel-default">
        {form_open('admin/member/approve_pending_package_upgrades', 'name="pending_reg" class="" id="pending_reg" method="post"')}
    <input type="hidden" id="details" value='{json_encode($pending_upgrade_list)}'>
    <div class="table-responsive">
        <table class="table table-striped m-b-none">
            <thead>
                <tr>
                    <th>
                        <div class="checkbox">
                            <label class="i-checks i-checks-sm">
                                <input type="checkbox" name="check_all" id="check_all" class="" value=""/><i> </i>
                            </label>
                        </div>
                    </th>
                    <th>{lang('user_name')}</th>
                    <th>{lang('current_package')}</th>
                    <th>{lang('new_package')}</th>
                    <th>{lang('total_amount')}</th>
                    <th>{lang('payment_method')}</th>
                    <th>{lang('action')}</th>
                </tr>
            </thead>
            <tbody>
                {if count($pending_upgrade_list)}
                {assign var="i" value=1}
                {foreach from = $pending_upgrade_list key = k item = v}

                    <tr>
                        <td>
                            <div class="checkbox">
                                <label class="i-checks i-checks-sm">
                                    <input type="checkbox" name="request_id[]" id="release{$i}" class="release" value="{$v['id']}"/><i> </i>
                                </label>
                            </div>
                        </td>
                        <td>{$v['user_name']}</td>
                        <td>{$v['current_package']}</td>
                        <td>{$v['new_package']}</td>
                        <td>{$DEFAULT_SYMBOL_LEFT}{($v['payment_amount']*$DEFAULT_CURRENCY_VALUE)|round:$PRECISION}{$DEFAULT_SYMBOL_RIGHT}</td>
                        <td>{lang($v['payment_type'])}</td>
                        <td>
                            <!--<a class="btn btn-light-grey btn-xs text-black" title="{lang('more_info')}" data-toggle="modal" data-key="{$k}" data-target="#user_detail_modal">
                                <i class="fa fa-eye"></i>
                            </a>-->
                            {if $v['payment_type'] == 'bank_transfer'}
                                <a class="btn btn-light-grey btn-xs text-black" title="{lang('view_bank_receipt')}" href="javascript:mym('{SITE_URL}/uploads/images/reciepts/{$v['payment_receipt']}')"><i class="fa fa-file-text-o"></i></a>
                            {/if}
                        </td>
                    </tr>
                    {$i = $i + 1}
                {/foreach}
                {else}
                    <tr>
                        <td colspan="7"><h4 class="text-center">{lang('no_data')}</h4></td>
                    </tr>
                {/if}
            </tbody>
        </table>
        </div>
        {if count($pending_upgrade_list)}
        <div class="panel-footer">
        <button class="btn btn-sm btn-primary approve" name="confirm_registr" id="confirm_registr" type="submit" value="confirm_registr"> {lang('approve')} </button>
        
        {$ci->pagination->create_links()}
        </div>
        {/if}
            
    {form_close()}
     
     
     
    
     </div>
    
    <div class="panel-body">
    <div id="user_detail_modal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">{lang('user_info')}: <span class="user_name"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="2">{lang('sponsor_package_info')}</th>
                                    </tr>
                                </thead>
                                <tbody class="user-info-tbody">
                                    <tr>
                                        <td>{lang('sponsor')}:</td>
                                        <td class="modal-sponsor"></td>
                                    </tr>
                                    <tr class="reg_from_tree hidden">
                                        <td>{lang('placement')}:</td>
                                        <td class="placement_user"></td>
                                    </tr>
                                    {if $MODULE_STATUS['product_status'] == 'yes'}
                                        <tr>
                                            <td>{lang('package')}:</td>
                                            <td class="package"></td>
                                        </tr>
                                    {/if}
                                </tbody>
                            </table>
                        </div>
                    </div>
            </div>
        </div>
    </div>

    
    

</div>

   
{/block}