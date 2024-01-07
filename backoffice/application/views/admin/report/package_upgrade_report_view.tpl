{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}{assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_package_upgrade_report/{$user_name}?product_id1={$product_id1}"} {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_package_upgrade_report/{$user_name}?product_id1={$product_id1}"}
 {assign var="report_name" value="{lang('package_upgrade_report')}"}
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/package_upgrade_report','role="form" class="" method="get" name="commision_form" id="commision_form" onsubmit="return validation()"')}
        <div class="col-sm-3 padding_both">
        <div class="form-group">
            <label>{lang('user_name')}</label>
            <input type="text" class="form-control user_autolist" id="user_name" name="user_name" autocomplete="Off" value="{$ci->input->get('user_name')}">
        </div>
        </div>
        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label class="" for="package">{lang('package_name')}</label>
                <select name="package_name" id="package_name" class="form-control">
                    <option value="all">{lang('any')}</option>
                    {foreach from=$package_names item=v}
                      <option value="{$v.product_id}" {if $v.product_id == $ci->input->get('package_name')}selected{/if}>{$v.product_name}</option>
                    {/foreach}
                </select>
            </div>
        </div>
        <div class="col-sm-3 padding_both_small">
        <div class="form-group credit_debit_button">
            <button class="btn btn-primary" name="upgrade"  type="submit" value="">{lang('submit')}</button>
            <button class="btn btn-sm btn-info search_clear" type="button">{lang('reset')}</button>
        </div>
        </div>
        


        {form_close()}

  </div>
  </div>  
 {include file="admin/report/report_nav.tpl" name=""}
 <div id="print_area" class="img panel-body panel">
  {include file="admin/report/header.tpl" name=""}
  {if $packageString != ""}<h4 align="center"><b>{$packageString}</b></h4>{/if}
  {if $userFilter != ""}<h4 align="center"><b>{$userFilter}</b></h4>{/if}

 <div class="panel panel-default  ng-scope">
  <div class=" table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">
      {if count($package_details)>0}
        <thead>
        <tr>
          <th>{lang('sl_no')}</th>
            {if $userFilter == ""}<th>{lang('member_name')}</th>{/if}
            <th>{lang('old_package')}</th>
            <th>{lang('upgraded_package')}</th>
            <th>{lang('amount')}</th>
            <th>{lang('payment_method')}</th>
            <th>{lang('upgraded_date')}</th>
        </tr>
        </thead>
        <tbody>
          
          {assign var="i" value=1}
          {foreach from=$package_details item=v}
              <tr>
                  <td>{$ci->input->get('offset')+$i}</td>
                  {if $userFilter == ""}
                    <td>
                      {if $v.delete_status == "active"}
                        {$v.full_name}({$v.user_name})
                      {else}
                        {$v.user_name}
                      {/if}
                    </td>
                  {/if}
                  <td>{$v.current_package}</td>
                  <td>{$v.new_package}</td>
                  <td>{format_currency($v.payment_amount)}</td>
                  <td>{if $v.payment_type == "free_upgrade" && $v.payment_amount == 0}    
                        {lang('manualy_by_admin')}
                       
                       {else if $v.payment_type == "free_upgrade" && $v.payment_amount !=0}
                          {lang('free_upgrade')}
                        {else}
                        {lang($v.payment_type)}
                       {/if}

                  </td> 
                  <td>{date("d M Y - h:i:s A", strtotime($v.date_added))}</td>
               </tr>   
             {$i=$i+1}
           {/foreach}       
          {assign var="colspan" value=4}
            {if $userFilter != ""}{$colspan = $colspan - 1}{/if}

            <th colspan="{$colspan}" style="text-align:center;">{lang('total')}</th>
            <th>{format_currency($total_amount)}</th>
            <th></th>
        </tbody>
     {else}
        <h4 align="center">
            <font>{lang('no_data')}</font>
        </h4>
        {/if}
    </table>
    </div>
  </div>
  {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
  </div>
  {/block}    
  {block name=script}
  {$smarty.block.parent}
  <script>
    $('.search_clear').on('click', function () {
      $('#user_name').val('');
      $('#package_name').val('all').trigger('change');
    });
  </script>
{/block}
