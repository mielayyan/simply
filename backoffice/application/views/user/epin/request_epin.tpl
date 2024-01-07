{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display: none;">
   <span id="error_msg2">{lang('you_must_select_an_amount')}</span>
   <span id="error_msg1">{lang('you_must_enter_count')}</span>    
   <span id="error_msg3">{lang('digits_only')}</span>         
   <span id="row_msg">{lang('rows')}</span>
   <span id="show_msg">{lang('shows')}</span>
   <span id="error_msg4">{lang('Digit limit is five')}</span>
   <span id="error_msg6">{lang('Digit limit is two')}</span>
</div>
<div class="panel panel-default">
   <div class="panel-body">
      {form_open('user/epin/request_epin','role="form"  method="post" name="upload" id="upload" ')} 
      <div class="col-sm-3 padding_both">
         <div class="form-group">
            <label class="letter_width" for="fb_count">{lang('amount')}<font color="#ff0000">*</font></label>
            <select  class="form-control" name="amount1" id="amount1">
               <option value="">{lang('select_amount')}</option>
               {assign var=i value=0}
               {foreach from=$amount_details item=v}
               <option value="{$v.amount}">{format_currency($v.amount)}</option>
               {$i = $i+1}
               {/foreach}
            </select>
            {form_error('amount1')}
         </div>
      </div>
      <div class="col-sm-3 padding_both_small">
         <div class="form-group">
            <label for="fb_count"> {lang('count')}<font color="#ff0000">*</font></label>
            <input type="number" class="form-control" size="45" name="count"   id="count" type="text" value="" title="{lang('no_of_epin_generated')}" autocomplete="Off" min="0">
         </div>
      </div>
      <div class="col-sm-3 padding_both_small">
         <div class="form-group">
           <label class="required">{lang('expiry_date')}</label>
           <input type="text" class="form-control date-picker" name="date" id="date" type="text" tabindex="4" maxlength="10"  value="" >
           <span for="date" class="help-block"></span>
           {form_error('date')}
         </div>
      </div>
      <div class="col-sm-3 padding_both_small ">
         <button class="btn btn-sm btn-primary mark_paid_1 " name="reqpasscode" id="reqpasscode" value="{lang('request_epin')}" title="{lang('request_epin')}"> {lang('request_epin')}</button>
      </div>
      {form_close()}
   </div>
</div>
{/block}
{block name=script} {$smarty.block.parent}
<script>
   jQuery(document).ready(function() {
       ValidateUser.init();
   });
</script>
{/block}
