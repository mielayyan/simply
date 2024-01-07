{extends file=$BASE_TEMPLATE}
{block name=script}
  {$smarty.block.parent}
<script>
    jQuery(document).ready(function () {
        ValidateUser.init();
    });
</script>
{/block}
{block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display:none;">
    <span id="error_msg">{lang('search_member_error')}</span>
    <span id="error_msg2">{lang('search_member_error')}</span>
    <span id="error_msg3">{lang('you_must_enter_count')}</span>
    <span id="error_msg9">{lang('digits_only')}</span>
    <span id="error_msg4">{lang('you_must_enter_count_from')}</span>
    <span id="error_msg5">{lang('you_must_enter_count')}</span>
    <span id="error_msg6">{lang('values_greater_than_0')}</span>
    <span id="error_msg7">{lang('invalid_username')}</span>
</div>
<div class="panel panel-default">
  <div class="panel-body">
  {form_open('admin/report/subscription_report_view','role="form" class="" method="get"  name="searchform" id="searchform" target="_blank"')}
      {include file="layout/error_box.tpl"}
      <div class="col-sm-3 padding_both">
      <div class="form-group">
        <label class="required">{lang('user_name')}</label>
        <input type="text" class="form-control user_autolist" autocomplete="Off" id="user_name" name="user_name">
      </div>
         {if $error_count && isset($error_array['user_name'])}{$error_array['user_name']}{/if}
      </div>
      <div class="col-sm-3 padding_both_small">
      <div class="form-group credit_debit_button">
        <button type="submit" class="btn btn-primary" name="" value="">{lang('view')}</button>
      </div>
      </div>
    {form_close()}
  </div>
</div>
{/block}