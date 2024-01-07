{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display:none;">
    <span id="confirm_msg_activate_category">{lang('Sure_you_want_to_Activate_this_category')}</span>
    <span id="confirm_msg_inactivate_category">{lang('Sure_you_want_to_Inactivate_this_category')}</span>
    <span id="confirm_msg_delete_category">{lang('Sure_you_want_to_delete_this_category')}</span>
</div>

<div class="button_back">
  <a href="{$BASE_URL}admin/repurchase_package"> 
    <button class="btn m-b-xs btn-sm btn-info btn-addon"><i class="fa fa-backward"></i>{lang('back')}</button>
  </a>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <form action="{$BASE_URL}admin/repurchase_category" autocomplete="off">
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
                    <a class="btn btn-info btn-sm" href="{$BASE_URL}admin/repurchase_category">
                        {lang('reset')}
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="panel panel-default">
    <div class="table-responsive">
        {form_open('admin/repurchase_category_action', 'method="POST"')}
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
                        <th>{lang('category_name')}</th>
                        <th>{lang('status')}</th>
                        {if $status == "yes"}
                            <th>{lang('action')}</th>
                        {/if}
                    </tr>
                </thead>
                <tbody>
                    {if !empty($categories)}
                        {foreach from=$categories item=$category key=key}
                            <tr>
                                <td>
                                    <div class="checkbox">
                                        <label class="i-checks">
                                            <input type="checkbox" name="category_id[]" class="select-checkbox-single" value="{$category.category_id}">
                                            <i></i>
                                        </label>
                                    </div>
                                </td>
                                <td>{$category.category_name}</td>
                                <td>
                                    {if $category.status == "yes"}
                                        {lang('active')}
                                    {elseif $category.status == "no"}
                                        {lang('blocked')}
                                    {/if}
                                </td>
                                {if $status == "yes"}
                                    <td>
                                        <a href="javascript:edit_repurchase_category({$category.category_id})" title="{lang('edit')}" class="btn btn-light-grey btn-xs text-black" data-placement="top" data-original-title="{lang('edit')}"><i class="fa fa-edit"></i></a>
                                    </td>
                                {/if}
                            </tr>
                        {/foreach}
                    {else}
                        <tr>
                            <td colspan="8">
                                <h4 class="text-center">{lang('no_records_found')}</h4>
                            </td>
                        </tr>
                    {/if}
                </tbody>
            </table>
            {if !empty($categories) && $status == "yes"}
                <div class="panel-footer">
                    <button class="btn btn-primary" type="submit" name="action" value="deactivate_category">{lang('block')}</button>
                    {$ci->pagination->create_links()}
                </div>
            {elseif !empty($categories) && $status == "no"}
                <div class="panel-footer">
                    <button class="btn btn-primary" type="submit" name="action" value="activate_category">{lang('activate')}</button>
                    {$ci->pagination->create_links()}
                </div>
            {elseif !empty($categories)}
                {$ci->pagination->create_links('<div class="panel-footer">', '</div>')}
            {/if}
        {form_close()}
    </div>
</div>
{/block}

