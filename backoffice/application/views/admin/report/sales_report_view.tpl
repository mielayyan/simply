{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}{assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_sales_report/{$product_type}?from_date={$from_date}&&to_date={$to_date}&&package={$package_id}"} {assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_sales_report/{$product_type}?from_date={$from_date}&&to_date={$to_date}&&package={$package_id}"}
{assign var="report_name" value="{lang('sales_report')}"}{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
{include file="admin/report/header.tpl" name=""}
  <div class="panel panel-defaultng-scope">
  <div class="table-responsive">
    <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
      <tbody>
      <thead>
        <tr>
            <th>{lang('sl_no')}</th>
            <th>{lang('invoice_no')}</th>
            <th>{lang('prod_name')}</th>
            <th>{lang('user_name')}</th>
            <th>{lang('payment_method')}</th>
            <th>{lang('amount')}</th>
        </tr>
        </thead>
        {assign var="i" value=0}
        {foreach from=$report_arr item=v}
        {$i=$i+1}
        <tr class="">
            <td>{counter}</td>
            <td>{$v.invoice_no}</td>
            <td>{$v.prod_id}</td>
            <td>{$v.user_id} {if $v.pending_id}<span>(pending)</span>{/if}</td>

            <td>{lang($v.payment_method)}</td>
            <td>{format_currency($v.amount)}</td>
        </tr>
        {/foreach}
      </tbody>
      {else}
            <h4 align="center">
                <font>{lang('no_data')}</font>
            </h4>
            {/if}
    </table>
  </div>
</div>
</div>
{/block}