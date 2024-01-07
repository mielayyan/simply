{extends file=$BASE_TEMPLATE}

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
    <div class="panel panel-default">
        <div class="panel-body">
        {form_open('admin/member/registration_action', 'name="pending_reg" class="" id="pending_reg" method="post"')}
        <div class="alert alert-danger errorHandler no-display">
            <a href="#" class="close" data-dismiss="alert" aria-label="close">×</a>
            {lang('please_select_at_least_one_checkbox')}
        </div>
    <input type="hidden" id="details" value='{json_encode($pending_registration_list)}'>
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
                    <th>{lang('name')}</th>
                    <th>{lang('sponsor')}</th>
                    {if $MODULE_STATUS['product_status'] == 'yes'}
                        <th>{lang('package')}</th>
                    {/if}
                    <th>{lang('total_amount')}</th>
                    <th>{lang('payment_method')}</th>
                    <th>{lang('action')}</th>
                </tr>
            </thead>
            <tbody>
                {if count($pending_registration_list)}
                {assign var="i" value=1}
                {foreach from = $pending_registration_list key = k item = v}
                    {if !isset($v.last_name)}
                        {assign "last_name" ''}
                    {else}
                        {assign "last_name" $v.last_name}
                    {/if}
                    <tr>
                        <td>
                            <div class="checkbox">
                                <label class="i-checks i-checks-sm">
                                    <input type="checkbox" name="release[]" id="release{$i}" class="release" value="{$v['user_name']}"/><i> </i>
                                </label>
                            </div>
                        </td>
                        <td>{user_with_name($v.user_name, "`$v.first_name` `$last_name`", false, null)}</td>
                        <td>{$v['sponsor_user_name']}</td>
                        {if $MODULE_STATUS['product_status'] == 'yes'}
                            <td>{$v['package_name']} ({lang('amount')}: {format_currency($v['product_amount'])})</td>
                        {/if}
                        <td>{$DEFAULT_SYMBOL_LEFT}{($v['total_amount']*$DEFAULT_CURRENCY_VALUE)|round:$PRECISION}{$DEFAULT_SYMBOL_RIGHT}</td>
                        <td>{lang($v['payment_method'])}</td>
                        <td>
                            <a class="btn btn-light-grey btn-xs text-black" title="{lang('more_info')}" data-toggle="modal" data-key="{$k}" data-target="#user_detail_modal">
                                <i class="fa fa-eye"></i>
                            </a>
                            {if $v['payment_method'] == 'bank_transfer'}
                                <a class="btn btn-light-grey btn-xs text-black" title="{lang('view_bank_receipt')}" href="javascript:mym('{SITE_URL}/uploads/images/reciepts/{$v['reciept']}')"><i class="fa fa-file-text-o"></i></a>
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
        {if count($pending_registration_list)}
        <div class="panel-footer">
        <button class="btn btn-sm btn-primary approve" name="confirm_registr" id="confirm_registr" type="submit" value="confirm_registr"> {lang('approve')} </button>
        <button class="btn btn-sm btn-primary"  name="reject_registr" id="reject_registr" type="submit" value="{$BASE_URL}admin/member/reject_registration_action"> {lang('reject')} </button>

        
        {$ci->pagination->create_links()}
        </div>
        {/if}
            
    {form_close()}
     
    </div>
     
    
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
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-condensed table-hover">
                                <thead>
                                    <tr>
                                        <th colspan="2">{lang('contact_info')}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {if search_array($signup_fields, 'field_name', 'first_name', 'required', "yes")}
                                        <tr>
                                            <td>{lang('first_name')}:</td>
                                            <td class="first_name"></td>
                                        </tr>
                                    {/if}
                                    {if search_array($signup_fields, 'field_name', 'last_name', 'status', "yes")}
                                        <tr>
                                            <td>{lang('last_name')}:</td>
                                            <td class="last_name"></td>
                                        </tr>
                                    {/if}
                                    {if search_array($signup_fields, 'field_name', 'gender', 'status', "yes")}
                                        <tr>
                                            <td>{lang('gender')}:</td>
                                            <td class="gender"></td>
                                        </tr>
                                    {/if}
                                    {if search_array($signup_fields, 'field_name', 'date_of_birth', 'status', "yes")}
                                        <tr>
                                            <td>{lang('date_of_birth')}:</td>
                                            <td class="date_of_birth"></td>
                                        </tr>
                                    {/if}

                                    {if search_array($signup_fields, 'field_name', 'adress_line1', 'status', "yes")}
                                        <tr>
                                            <td>{lang('address_line1')}:</td>
                                            <td class="address_line1"></td>
                                        </tr>
                                    {/if}
                                    {if search_array($signup_fields, 'field_name', 'adress_line2', 'status', "yes")}
                                        <tr>
                                            <td>{lang('address_line2')}:</td>
                                            <td class="address_line2"></td>
                                        </tr>
                                    {/if}
                                    {if search_array($signup_fields, 'field_name', 'country', 'status', "yes")}
                                        <tr>
                                            <td>{lang('country')}:</td>
                                            <td class="country"></td>
                                        </tr>
                                    {/if}
                                    {if search_array($signup_fields, 'field_name', 'state', 'status', "yes")}
                                        <tr>
                                            <td>{lang('state')}:</td>
                                            <td class="state"></td>
                                        </tr>
                                    {/if}
                                    {if search_array($signup_fields, 'field_name', 'city', 'status', "yes")}
                                        <tr>
                                            <td>{lang('city')}:</td>
                                            <td class="city"></td>
                                        </tr>
                                    {/if}
                                    {if search_array($signup_fields, 'field_name', 'pin', 'status', "yes")}
                                        <tr>
                                            <td>{lang('zip_code')}:</td>
                                            <td class="zip_code"></td>
                                        </tr>
                                    {/if}
                                    {if search_array($signup_fields, 'field_name', 'email', 'status', "yes")}
                                        <tr>
                                            <td>{lang('email')}:</td>
                                            <td class="email"></td>
                                        </tr>
                                    {/if}
                                    {if search_array($signup_fields, 'field_name', 'mobile', 'status', "yes")}
                                        <tr>
                                            <td>{lang('mobile')}:</td>
                                            <td class="mobile"></td>
                                        </tr>
                                    {/if}
                                    {if search_array($signup_fields, 'field_name', 'land_line', 'status', "yes")}
                                        <tr>
                                            <td>{lang('landline')}:</td>
                                            <td class="landline"></td>
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

    <div class="modal fade" id="EnSureModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                </div>
                <div class="modal-body" style="text-align:center;width:100%">
                    <img id="im" src="">
                    <!--                <p style="text-align:left;"id="des"></p>-->
                </div>
                <div class="form-group m-l">
                 
                    <button type="button" class="btn btn-primary" data-dismiss="modal">{lang('close')}</button>
                 
                </div>
            </div>
        </div>
    </div>
    

</div>

   
{/block}