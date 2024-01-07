{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display:none;">
    <span id="confirm_msg_inactivate">{lang('Sure_you_want_to_inactivate_this_Product')}</span>
    <span id="confirm_msg_edit">{lang('Sure_you_want_to_edit_this_Product_There_is_NO_undo')}</span>
    <span id="confirm_msg_delete">{lang('Sure_you_want_to_Delete_this_Product_There_is_NO_undo')}</span>
    <span id="confirm_msg_activate">{lang('Sure_you_want_to_Activate_this_Product')}</span>
</div>

    <p class="text-right" style="margin-bottom: 44px">
        <a href="{$BASE_URL}admin/add_repurchase_package" class="btn btn-sm btn-primary btn-addon pull-right" name="add_prod" id="add_prod" value="add product" ><i class="fa fa-plus"></i>{lang('add_new_product')}</a>
        <a href="{$BASE_URL}admin/repurchase_category" class="btn btn-sm btn-primary btn-addon pull-right m-r-xs" name="category" id="category" value="category" ><i class="fa fa-plus"></i>{lang('manage_category')}</a>
        <a href="{$BASE_URL}admin/add_repurchase_category" class="btn btn-sm btn-primary btn-addon pull-right m-r-xs" name="category" id="category" value="category" ><i class="fa fa-plus"></i>{lang('add_category')}</a>
    </p>
    <div class="panel panel-default">
        <div class="panel-body">
            <form action="repurchase_package">
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
                        <button class="btn btn-sm btn-primary" type="submit">
                            {lang('search')}
                        </button>
                        <a class="btn btn-info btn-sm" href="repurchase_package">
                            {lang('reset')}
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="panel panel-default">
        {form_open('admin/repurchase_package_action', 'method="POST"')}
        <div class="table-responsive">
            <table class="table table-striped m-b-none">
                <thead>
                    <tr>
                        <th>
                            <div class="checkbox">
                                <label class="i-checks">
                                    <input type="checkbox" class="select-checkbox-all">
                                    <i></i>
                                </label>
                            </div>
                        </th>
                        <th>{lang('id')}</th>
                        <th>{lang('Product_img')}</th>
                        <th>{lang('package_name')}</th>
                        <th>{lang('category')}</th>
                        <th>{lang('amount')}</th>
                        {if $pv_visible == 'yes'}<th>{lang('product_pv')}</th>{/if}
                        {if $bv_visible == 'yes'}
                            <th>{lang('bv_value')}</th>
                        {/if}
                        <th>{lang('status')}</th>
                        <th>{lang('action')}</th>
                    </tr>
                </thead>
                {if count($packages) > 0}
                    <tbody>
                    {$i = 0}
                    {foreach from=$packages key=key item=$package}
                        {assign var="name" value="{$package.product_name}"}
                        {assign var="category" value="{$package.category_name}"}
                        {assign var="active" value="{$package.active}"}
                        {assign var="date" value="{$package.date_of_insertion}"}
                        {assign var="prod_value" value="{$package.product_value}"}
                        {assign var="bv_value" value="{$package.bv_value}"}
                        {assign var="pair_value" value="{$package.pair_value}"}
                        {assign var="type_of_package" value="{$package.type_of_package}"}
                        {assign var="package_id" value="{$package.prod_id}"}
                        <tr>
                            <td>
                                <div class="checkbox">
                                    <label class="i-checks">
                                        <input type="checkbox" name="package_id[]" class="select-checkbox-single" value="{$package.product_id}">
                                        <i></i>
                                    </label>
                                </div>
                            </td>
                            <td>{$package_id}</td>
                            <td>
                                <div class="checkout-image">
                                    {if $package['prod_img'] != '' && $package['prod_img']!="no"}
                                    <img src="{$SITE_URL}/uploads/images/product_img/{$package.prod_img}"   alt="a"   /> {else}
                                    <img src="{$SITE_URL}/uploads/images/product_img/cart.jpg"  /> {/if}
                                </div>
                            </td>
                            <td>{$name}</td>
                            <td>{$category}</td>
                            <td>{format_currency($prod_value)}</td>
                            {if $pv_visible == 'yes'}
                                <td>{$pair_value}</td>
                            {/if}
                            {if $bv_visible == 'yes'}
                                <td>{$bv_value}</td>
                            {/if}
                            <td>{if $status=="yes"}{lang('active')}{else}{lang('blocked')}{/if}</td>
                            <td>
                                <a href="javascript:edit_repurchase_package({$package.product_id})" title="{lang('edit')}"  class="btn btn-light-grey btn-xs text-black" data-placement="top" data-original-title="{lang('edit')}"><i class="fa fa-edit"></i></a>
                            </td>
                        </tr>

                        {$i = $i + 1}
                    {/foreach}
                    </tbody>
                {else}
                    <tbody>
                        <tr id="tr-empty"><td align="center"><h4 align="center">{lang('no_records_found')}</h4></td></tr>
                    </tbody>
                {/if}
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