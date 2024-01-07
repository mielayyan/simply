{extends file=$BASE_TEMPLATE}
{block name=script}
  {$smarty.block.parent}
  <script src="{$PUBLIC_URL}javascript/Epinvalidation.js" type="text/javascript" ></script>
  <script>
  $(function(){
      ValidateUser.init();
  });
  </script>
{/block}
{block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display:none;">
    <span id="error_msg6">{lang('please_select_at_least_one_checkbox')}</span>
    <span id="row_msg">{lang('rows')}</span>
    <span id="show_msg">{lang('shows')}</span>
    <span id="confirm_msg">{lang('are_you_sure_want_delete')}</span>
    <span id="err_msg1">{lang('non_zero_digits_only')}</span>
    <span id="err_msg2">{lang('count_field_is_required')}</span>
</div>
{include file="layout/search_member_get.tpl" search_url="admin/view_epin_request"}

<div class="panel panel-default">
 {form_open('admin/epin/allocate_delete_epin_request','role="form" method="post"  name="view_request_form" id="view_request_form2"')}
  <div class="table-responsive">
    <table class="table table-striped">
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
          <th>{lang('name')}</th>
          <th>{lang('requested_pin_count')}</th>
          <th>{lang('count')}</th>
          <th>{lang('amount')}</th>
          <th>{lang('requested_date')}</th>
          <th>{lang('expiry_date')}</th>
        </tr>
      </thead>
      <tbody>
        {if !empty($epin_requests)}
          {foreach from=$epin_requests item=request key=key}
              <tr>
                <td>
                  <div class="checkbox">
                      <label class="i-checks">
                          <input type="checkbox" name="request_id[]" class="select-checkbox-single" value="{$request['req_id']}">
                          <i></i>
                      </label>
                  </div>
                </td>
                <td>{user_with_name($request.user_name, $request.full_name, true)}</td>
                <td>{$request.req_pin_count}</td>
                <td>
                  <input name='count[{$request['req_id']}]' type='number' min="1" max="{$request.req_rec_pin_count}" class="count"  size='4' maxlength='50'  value='{$request.req_pin_count}' style="text-align:  center;"/>
                  <input type="hidden" name="allocate_user[{$request['req_id']}]" value="{$request.req_user_id}">
                  <input type="hidden" name="remaining_epin_count[{$request['req_id']}]" value="{$request.req_rec_pin_count}">
                  <input type="hidden" name="epin_expiry_date[{$request['req_id']}]" value="{$request.pin_expiry_date}">
                  <input type="hidden" name="epin_amount[{$request['req_id']}]" value="{$request.pin_amount}">
                </td>
                <td>{format_currency($request.pin_amount)}</td>
                <td>{$request.req_date|date_format:"%Y-%m-%d"}</td>
                <td>{$request.pin_expiry_date|date_format:"%Y-%m-%d"}</td>
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
  <div class="panel-footer">
    <button type="submit" class="btn btn-primary" name="action" value="allocate">{lang('allocate')}</button>
    <button type="submit" class="btn btn-danger" name="action" value="delete">{lang('delete')}</button>
    {$ci->pagination->create_links()}
  </div>
  {form_close()}
</div>
{/block}