{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}
<div class="button_back">
    <a onClick="print_report(); return false;"><button class="btn m-b-xs btn-sm btn-primary btn-addon hidden-xs hidden-sm hidden-md"><i class="icon-printer"></i>{lang('click_here_to_print')}</button>
    </a>
</div>
<div class=" panel">
<div class="panel-body" id="print_area">
<div class="img"> <img src="{$SITE_URL}/uploads/images/logos/{$site_info["login_logo"]}" /> </div>
<div class="row">
    <div class="col-xs-6">
        <h4>{$site_info["company_name"]}</h4>
        <p>{$site_info["company_address"]}</p>
        <p> {lang('phone')}: {$site_info["phone"]}<br> {lang('email')}:{$site_info["email"]} </p>
    </div>
    <div class="col-xs-6 text-right">
        <p class="h4">{$invoice_number}</p>
        {foreach from=$payout_details item=v}
        <h5>{date("j F, Y",strtotime($v.paid_date))}</h5>
        {/foreach}
    </div>
</div>
 {* 
<h3 class="text-center">{$report_name}</h3>
{if $report_date}
<p class="text-center">{$report_date}</p>
{/if}
<br> *}
<div class="row">
    {* <div class="col-sm-6">
        <h4>{lang('company')} :</h4>
        <div class="well">
            <address>
            <strong>{$site_info['company_name']}.</strong>
            <br>{$site_info['company_address']}
            <br>
            <abbr title="Phone">Phone:</abbr>{$site_info['phone']}
        </address>
            <address>
            <strong>{lang('email')}</strong>
            <br>
            <a href="javascript:void()">{$site_info['email']}
            </a>
        </address>
        </div>
    </div> *}
    <div class="col-sm-6">
        <h4>{lang('user_details')} :</h4>
        <div class="well"><address>
            {foreach from=$user_details item=u}
            <strong>{$u.user_detail_name}&nbsp;{$u.user_detail_second_name}</strong>
            <br>{$u.user_detail_address}
            {$u.user_detail_pin}
            {$u.user_detail_city}
            <br>
            <abbr title="Phone">Phone:</abbr>{$u.user_detail_mobile}
            {/foreach}
   {*  </address>
            <address style="visibility: hidden;">
            <strong>{lang('email')}</strong>
            <br>
            <a href="javascript:void()">NA
            </a>
        </address> *}
        </div>
    </div>
    <br></div>

<div class="panel panel-default table-responsive ng-scope">
    <table st-table="rowCollectionBasic" class="table table-bordered table-striped">
        <tbody>
            <thead>
                <tr>
                    <th>{(lang('sl_no'))}</th>
                    <th> {lang('item')} </th>
                    <th class=""> {lang('paid_date')} </th>
                    {* <th class="hidden-480"> {lang('unit_cost')} </th> *}
                    <th> {lang('Total')} </th>
                </tr>
            </thead>
            <tbody>
                {assign var="total_amount" value=0} {foreach from=$payout_details item=v}
                <tr>
                    <td> {counter} </td>
                    <td> {lang("payout_released")} </td>
                    <td class=""> {date("j F, Y",strtotime($v.paid_date))}</td>
                    <td class="hidden-480">{format_currency($v.paid_amount)}
                    </td>
                    {* <td>{format_currency($v.amount*$v.quantity )}
                     </td> *}
                {* </tr>
                {$total_amount = $total_amount + ($v.amount * $v.quantity)} 
                <tr>
                  <td class="text-right" colspan="4" class="bold-text-center">
                   <b>{lang('Total')}</b>
                  </td>
                 <td colspan="2"><b>{$DEFAULT_SYMBOL_LEFT}
                   {number_format($total_amount * $DEFAULT_CURRENCY_VALUE,$PRECISION)}
                 {$DEFAULT_SYMBOL_RIGHT}</b></td> *} 
                </tr>
                {/foreach}
            </tbody>
    </table>
</div>
</div>
</div>
{/block}