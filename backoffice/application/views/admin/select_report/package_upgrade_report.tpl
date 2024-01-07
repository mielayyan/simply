{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/package_upgrade_report_view','role="form" class="" method="get" name="commision_form" id="commision_form" target="_blank" onsubmit="return validation()"')}
        <div class="col-sm-3 padding_both">
        <div class="form-group">
            <label>{lang('user_name')}</label>
            <input type="text" class="form-control user_autolist" id="user_name" name="user_name" autocomplete="Off">
        </div>
        </div>
        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label class="" for="package">{lang('package_name')}</label>
                <select name="package_name" id="package_name" class="form-control">
                    <option value="all">{lang('any')}</option>
                    {foreach from=$package_names item=v}
                    <option value="{$v.product_id}">{$v.product_name}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="col-sm-3 padding_both_small">
        <div class="form-group credit_debit_button">
            <button class="btn btn-primary" name="upgrade"  type="submit" value="">
            {lang('submit')}</button>
        </div>
        </div>
        


        {form_close()}

  </div>
  </div>  

   {/block}    