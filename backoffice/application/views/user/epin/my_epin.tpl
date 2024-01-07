{extends file=$BASE_TEMPLATE} 
{block name=script} {$smarty.block.parent}
    <script src="{$PUBLIC_URL}javascript/copy_to_clip_board.js" type="text/javascript"></script>
{/block} 
{block name=$CONTENT_BLOCK}

<div class="panel panel-default">
    <div class="panel-body">
        {form_open('user/my_epin', 'role="form" class="smart-wizard" id="search_epin" name="search_epin" method="get"')}
            <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label>{lang('search_epin')}</label>
                    <input class="form-control epin_autolist" type="text" name="epin" value="{$epin}" autocomplete="off" />
                </div>
            </div>

            <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label>{lang('amount')}</label>
                    <div>
                        <select class="form-control m-b" name="amount" id="amount">
                            <option value="">{lang('select_bal_amount')}</option>
                            {foreach from=$amount_details item=v}
                                <option value="{$v.amount}" {if $v.amount == $amount}selected{/if}>
                                    {format_currency($v.amount)}
                                </option>
                            {/foreach}
                            <span class="val-error">{form_error('amount')}</span>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label>{lang('status')}</label>
                    <div>
                        <select class="form-control m-b" name="status">
                            <option value="active" {if $status=="active"}selected{/if}>{lang('active')}</option>
                            <option value="blocked" {if $status=="blocked"}selected{/if}>{lang('blocked')}</option>
                            <option value="used_expired" {if $status=="used_expired"}selected{/if}>{lang('used_expired')}</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="col-sm-2 padding_both_small">
                <div class="form-group mark_paid">
                    <button class="btn btn-sm btn-primary" type="submit">
                        {lang('search')}
                    </button>
                    <a class="btn btn-info btn-sm" href="{base_url('user/my_epin')}">
                        {lang('reset')} 
                    </a>
                </div>
            </div>
        {form_close()}
    </div>
</div>
    
<div class="panel panel-default">
    <div class="table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped m-b-none">
            <thead>
                <tr class="th">
                    <th>{lang('e-pin')|ucfirst}</th>
                    <th>{lang('amount')|ucfirst}</th>
                    <th>{lang('bal_amount')}</th>
                    <th>{lang('status')}</th>
                    <th>{lang('expiry_date')}</th>
                    <th>{lang('Action')}</th>
                </tr>
            </thead>
            <tbody>
                {if !empty($epins)}
                {assign var="root" value="{$BASE_URL}user/"}
                    {foreach from=$epins item=epin key=key}
                        <tr>
                            <td>
                                <span class="btn-light-gray m-b-xs w-xs">
                                    {$epin.pin_numbers}
                                </span>
                            </td>
                            <td>{format_currency($epin.pin_amount)}</td>
                            <td>{format_currency($epin.pin_balance_amount)}</td>
                            <td>
                                {lang($epin.status_name)}
                            </td>
                            <td>{$epin['pin_expiry_date']|date_format:"d M Y"}</td>
                            <td> 

                            
                       {if $epin.status == "yes" && $epin.purchase_status =="yes" && ($epin.pin_balance_amount > 0)}
                    <!--refund option-->
                        {if DEMO_STATUS == 'yes' && $MODULE_STATUS['basic_demo_status'] == 'yes' && $is_preset_demo}
                        {else} 
                            <a class='btn-link btn_size has-tooltip text-danger' onclick="javascript:refund_pin({$epin.pin_id}, '{$root}')" title="{lang(refund)}"><i class="icon-reload"></i></a>
                        {/if}
                        {else}
                        {lang('na')}
                        {/if}

                            </td>
                        </tr>
                    {/foreach}
                {else}
                    <tr>
                        <td colspan="7">
                            <h4 class="text-center">{lang('No_Details_Found')}</h4>
                        </td>
                    </tr>
                {/if}
            </tbody>
        </table>
    </div>
    {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
</div>
{/block}
{block name=script} 
    {$smarty.block.parent}
    <script src="{$PUBLIC_URL}javascript/validate_epin.js"></script>
    <script src="{$PUBLIC_URL}javascript/misc.js"></script>
{/block}