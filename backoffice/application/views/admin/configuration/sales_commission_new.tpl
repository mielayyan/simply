{extends file=$BASE_TEMPLATE}

{block name=style}
     {$smarty.block.parent}
     <style>
        table.table thead {
         background-color: white;
        }
        table > tbody > tr > td {
            border: none !important;
            text-align: left !important;
            }
     </style>
{/block}

{block name=script}
     {$smarty.block.parent}
     <script src="{$PUBLIC_URL}javascript/validate_sales_config.js" type="text/javascript" ></script>
{/block}

{block name=$CONTENT_BLOCK}
    
    <div class="button_back">
        <a href="{BASE_URL}/admin/compensation_settings" class="btn m-b-xs btn-sm btn-info btn-addon"><i class="fa fa-backward"></i> {lang('back')}</a>
    </div>


    <div class="panel panel-default">
        <div class="panel-body">
            {form_open('admin/configuration/update_sales_commission_common','role="form" class="" name="form_setting1" id="form_setting1"')}

                <div class="form-group">
                    <label class="required control-label">{lang('sales_commission_criteria')}</label>
                    <select class="form-control" name="commission_criteria"  id="commission_criteria">
                        <option value="cv" {if $active_criteria == 'cv'} selected="true"{/if}>{lang('sales_commission_cv')}</option>
                        <option value="sp" {if $active_criteria == 'sp'} selected="true"{/if}>{lang('sales_commission_sales_price')}</option>
                    </select>
                    {form_error("commission_criteria")}
                </div>

                <div class="form-group">
                    <label class="required control-label">{lang('sales_commission_distribution')}</label>
                    <select class="form-control" name="sales_type"  id="sales_type">
                        <option value="genealogy" {if $active_type == 'genealogy'} selected="true"{/if}>{lang('distribution_genealogy_level')|strtolower|ucfirst}</option>
                        <option value="package" {if $active_type == 'package'} selected="true"{/if}>{lang('distribution_upline_package')|strtolower|ucfirst}</option>
                        <option value="rank" {if $active_type == 'rank'} selected="true"{/if}>{lang('distribution_rank')|strtolower|ucfirst}</option>
                    </select>
                    {form_error("sales_type")}
                </div>

                <div class="form-group">
                    <label class="required control-label">{lang('distribution_upto_level')}</label>
                    <input type="text" maxlength="5" class="form-control" name="commission_upto_level" data-lang="{lang('you_must_enter')} {lang('distribution_upto_level')|strtolower|ucfirst}" id="commission_upto_level" min="0" value="{$active_level}">
                    {form_error("commission_upto_level")}
                </div>

                <div class="form-group">
                    <button class="btn btn-sm btn-primary" type="submit" value="{lang('update')}" name="sales_commission_common" id="sales_commission_common">{lang('update')}</button>
                </div>
            {form_close()}
        </div>
    </div>
{/block}