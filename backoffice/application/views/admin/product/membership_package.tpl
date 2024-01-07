{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
    <div id="span_js_messages" style="display:none;">
        <span id="confirm_msg_inactivate">{lang('Sure_you_want_to_inactivate_this_Product')}</span>
        <span id="confirm_msg_edit">{lang('Sure_you_want_to_edit_this_Product_There_is_NO_undo')}</span>
        <span id="confirm_msg_delete">{lang('Sure_you_want_to_Delete_this_Product_There_is_NO_undo')}</span>
        <span id="confirm_msg_activate">{lang('Sure_you_want_to_Activate_this_Product')}</span>
    </div>
    
    <p class="text-right" style="margin-bottom: 44px">
        <a href="{$BASE_URL}admin/add_membership_package" class="btn btn-sm btn-primary btn-addon pull-right" name="add_prod" id="add_prod" value="add product" ><i class="fa fa-plus"></i>{lang('add_new_product')}</a>
    </p>

    <div class="panel panel-default">
        <div class="panel-body">
            <form action="membership_package">
                <div class="col-sm-2 padding_both_small">
                    <div class="form-group">
                        <label>{lang('status')}</label>
                        <div>
                            <select class="form-control m-b" name="status">
                                <option value="yes" {if $status=="yes"}selected{/if}>{lang('active')}</option>
                                <option value="no" {if $status=="no"}selected{/if}>{lang('blocked')}</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="col-sm-2 padding_both_small">
                    <div class="form-group mark_paid">
                        <button class="btn btn-sm btn-primary" type="submit">{lang('search')}</button>
                        <a class="btn btn-info btn-sm" href="membership_package">{lang('reset')}</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    <div class="panel panel-default">
        {form_open('admin/membership_package_action', 'method="POST"')}
            <div class="table-responsive">
                <table class="table table-striped m-b-none">
                    <thead>
                        <tr class="th">
                            <th>
                                <div class="checkbox">
                                    <label class="i-checks">
                                        <input type="checkbox" class="select-checkbox-all">
                                        <i></i>
                                    </label>
                                </div>
                            </th>
                            <th>{lang('id')}</th>
                            <th>{lang('package_name')}</th>
                            <th>{lang('package_type')}</th>
                            <th>{lang('amount')}</th>
                            {if $pv_visible == 'yes'}
                                <th>{lang('pv')}</th>
                            {/if}
                            {if $bv_visible == 'yes'}
                                <th>{lang('bv_value')}</th>
                            {/if}
                            {if $MODULE_STATUS['subscription_status']=="yes"}
                                <th>{lang('validity_in_months')}</th>
                            {/if}
                            <th>{lang('Simply Url')}</th>
                            <th>{lang('Support Board System')}</th>
                            <th>{lang('Support Services System')}</th>
                            <th>{lang('Support Tourism System')}</th>
                            <th>{lang('status')}</th>
                            <th>{lang('action')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {if !empty($packages)}
                            {foreach from=$packages item=$package key=key}
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label class="i-checks">
                                                <input type="checkbox" name="package_id[]" class="select-checkbox-single" value="{$package.product_id}">
                                                <i></i>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{$package.prod_id}</td>
                                    <td>{$package.product_name}</td>
                                    <td>{lang($package.pck_type)}</td>
                                    <td>{format_currency($package.product_value)}</td>
                                    {if $pv_visible == 'yes'}
                                        <td>{$package.pair_value}</td>
                                    {/if}
                                    {if $bv_visible == "yes"}
                                        <td>{$package.bv_value}</td>
                                    {/if}
                                    {if $MODULE_STATUS['subscription_status']=="yes"}
                                        <td>{$package.subscription_period}</td>
                                    {/if}
                                    <td>{ucfirst(lang($package.simply_url_status))}</td>
                                    <td>{ucfirst(lang($package.board_system))}</td>
                                    <td>{ucfirst(lang($package.services_system))}</td>
                                    <td>{ucfirst(lang($package.tourism_system))}</td>
                                    <td>{if $package.active == "yes"}{lang('active')}{else}{lang('blocked')}{/if}</td>
                                    <td>
                                        <a href="javascript:edit_membership_package({$package.product_id})" title="{lang('edit')}" class="btn btn-light-grey btn-xs text-black" data-placement="top" data-original-title="{lang('edit')}"><i class="fa fa-edit"></i></a>
                                    </td>
                                </tr>
                            {/foreach}
                        {else}
                            <tr>
                                <td colspan="7">
                                    <h4 class="text-center">{lang('no_records_found')}</h4>
                                </td>
                            </tr>
                        {/if}
                    </tbody>
                </table>
            </div>

            {if !empty($packages)}
                <div class="panel-footer">
                    {if $status == "yes"}
                        <button class="btn btn-primary" type="submit" name="action" value="deactivate_package">{lang('block')}</button>
                    {elseif $status == "no"}
                        <button class="btn btn-primary" type="submit" name="action" value="activate_package">{lang('activate')}</button>
                    {/if}
                    {$ci->pagination->create_links()}
                </div>
            {/if}
        {form_close()}
    </div>
{/block}