{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}
{assign var="report_name" value="{lang('subscription_report')}"}
{assign var="excel_url" value="{$BASE_URL}admin/excel/create_excel_voucher_report?username={$userName}"}
{assign var="csv_url" value="{$BASE_URL}admin/excel/create_csv_voucher_report?username={$userName}"}

<div class="panel panel-default">
  <div class="panel-body">
  {form_open('admin/voucher_report','role="form" class="" method="get"  name="searchform" id="searchform"')}
      {include file="layout/error_box.tpl"}
      <div class="col-sm-3 padding_both">
      <div class="form-group">
        <label class="required">{lang('user_name')}</label>
        <input type="text" class="form-control user_autolist" autocomplete="Off" id="user_name" name="user_name" value="{$ci->input->get('user_name')}">
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

{include file="admin/report/report_nav.tpl" name=""}
<div id="print_area" class="img panel-body panel">
    {include file="admin/report/header.tpl" name=""}
    <h4 align="center"><b>{$filterUserString}</b></h4>
    <div class="panel panel-default  ng-scope">
    <div class="table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
            <tbody>
                <thead>
                    <tr class="th">
                        <th>{lang('sl_no')}</th>
                        <th>{lang('member_name')}</th>
                        <th>{lang('voucher_amount')}</th>
                        <th>{lang('rank')}</th>
                        <th>{lang('date')}</th>
                    </tr>
                </thead>
                {assign var="i" value=0}{assign var="total" value=0} {foreach from=$details item=v} {$i=$i+1}
                <tr>
                    <td>{$ci->input->get('offset')+$i}</td>
                    <td>{$v.user_name}</td>
                    <td>{format_currency($v.voucher)}</td>
                    <td>{lang($v.rank_name)}</td>
                    <td>{date("d M Y - h:i:s A", strtotime($v.date))}</td>
                </tr>
                {$total = $total + $v.voucher}
                {/foreach}
                <tr>
                    <th colspan="2" style="text-align:center;">{lang('total')}</th>
                    <th>{format_currency($total)}</th>
                    <th></th>
                    <th></th>
                </tr>
                
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
    });
  </script>
{/block}