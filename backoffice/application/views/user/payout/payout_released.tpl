{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
                
                    <div class="panel panel-default">
                    
                        {assign var=i value="0"} 
                        {assign var=total value="0"} 
                        {assign var=class value=""}
                        <div class="panel-header">
                            <ul class="list-group m-b-none list-group-overview text-right">
                                <li class="list-group-item">
                                    <span class="text-md">{lang('total_payout_released')}:</span>
                                    <span class="text-md text-primary">{format_currency($total_payout_released)}</span>
                                </li>
                            </ul>
                        </div>
                        <div class=" table-responsive">
                        <table st-table="rowCollectionBasic" class="table table-striped">
                            <thead class="">
                                <tr class="th">
                                    <th>{lang('slno')}</th>
                                    <th>{lang('invoice_number')}</th>
                                    <th>{lang('paid_amount')}</th>
                                    <th>{lang('status')}</th>
                                    <th>{lang('paid_date')}</th>
                                </tr>
                            </thead>
                            {if $count > 0}
                              <tbody>
                            {foreach from=$binary item=v}
                            <tr>

                                <td>{$i + 1 + $page} </td>
                                <td><a href="./payout/payout_invoice/{$v.paid_id}" target="_blank">
                                    {"PR000"}{$v.paid_id}
                                    </a>
                                </td>
                                <td>{format_currency($v.paid_amount)}</td>
                                <td>{if $v.paid_status=='yes'}{lang('paid')}{/if}</td>
                                <td>{$v.paid_date|date_format:"d M Y - h:i:s A"}</td>
                            </tr>
                            {$i=$i+1} {/foreach}
                             
                             </tbody> 
                            {else}    
                            <tbody>
                                <tr>
                                    <td colspan="12" align="center">
                                        <h4>{lang('no_income_details_were_found')}</h4></td>
                                </tr>
                            </tbody>
                            {/if}

                        </table>
                        </div>
                {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
                </div>
{/block}


          