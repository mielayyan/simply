{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
<div class="panel panel-default">
    <div class="panel-body">
        <legend>
            <span class="fieldset-legend">{lang('bank_configuration')}</span>
            <a href="{$BASE_URL}admin/configuration/payment_view" class="btn btn-addon btn-sm btn-info pull-right">
                <i class="fa fa-backward"></i>
                {lang('back')}
            </a>
        </legend>


    {form_open('', 'role="form" class="" method="post" name="bank_info_form" id="bank_info_form"')}
         <div class="col-sm-6">
            <div class="form-group">
                <label class="required">{lang('bank_details')}</label>
                <textarea class="form-control" style="height:200px;" name="bank_info" id="bank_info">{$bank_details['account_info']}</textarea>
            </div>
         </div>
         <div class="form-group col-sm-8">
            <button class="btn btn-sm btn-primary" name="update_bank" type="submit" value="update">{lang('update')}</button>
         </div>
    {form_close()}
    </div>
</div>

{/block}