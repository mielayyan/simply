
<div class="panel panel-default table-responsive">
<div class="panel-body">
    {assign var="count" value = count($binary)}
    <table st-table="rowCollectionBasic" class="table table-striped">
        <thead>
            <th>{lang('slno')}</th>
            <th>{lang('paid_date')}</th>
            <th>{lang('paid_amount')}</th>
             <th>{lang('status')}</th>
        </thead>
        {if $count>0}
            {assign var="i" value = 0} 
            {assign var="status" value = ""} 
            {assign var="class" value = ""}
        <tbody>
            {foreach from=$binary item=v}
            <tr>
                <td>{$i + 1 + $page} </td>
                <td>{$v.paid_date}</td>
                <td>{format_currency($v.paid_amount)}</td>
                <td>{lang($v.paid_type)}</td>
            </tr>
            {$i=$i+1} {/foreach}
        </tbody>
    </table>
    
            {else}
    <tbody>
        <tr>
            <td colspan="8" align="center">
                <h4 align="center"> {lang('no_income_found')}</h4></td>
        </tr>
    </tbody>
    </table>
    </div>
    {/if}

</div>
{$ci->pagination->create_links()}
