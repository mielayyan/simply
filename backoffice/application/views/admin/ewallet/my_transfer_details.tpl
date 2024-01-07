{extends file=$BASE_TEMPLATE} 
{block name=$CONTENT_BLOCK}
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('','role="form" method="get"  name="transfer_history_filter"')} 
            {include file="layout/error_box.tpl"}
            <div class="col-sm-2 padding_both">
                <div class="form-group">
                    <label>{lang('given_user_name')}</label>
                    <input type="text" id="user_name" name="user_name" class="form-control user_autolist" autocomplete="Off" value="{$ci->input->get('user_name')}">
                </div>
            </div>

            <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label>{lang('recieved_user_name')}</label>
                    <input type="text" id="recieved_user_name" name="recieved_user_name" class="form-control user_autolist" autocomplete="Off" value="{$ci->input->get('recieved_user_name')}">
                </div>
            </div>
            
            <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label class="" for="daterange">{lang('daterange')}</label>
                    <select name="daterange" id="daterange" class="form-control">
                        <option value="all" {if $daterange=="all"} selected {/if}>{lang('overall')}</option>
                        <option value="today" {if $daterange=="today"} selected {/if}>{lang('today')}</option>
                        <option value="month" {if $daterange=="month"} selected {/if}>{lang('this_month')}</option>
                        <option value="year" {if $daterange=="year"} selected {/if}>{lang('this_year')}</option>
                        <option value="custom" {if $daterange=="custom"} selected {/if}>{lang('custom')}</option>
                    </select>
                </div>
            </div>

             <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label>{lang('from_date')}</label>
                    <input autocomplete="off" class="form-control date-picker custom-date" name="from_date" id="from_date" type="text" value="{$ci->input->get('from_date')}">
                </div>
            </div>
            <div class="col-sm-2 padding_both_small">
                <div class="form-group">
                    <label>{lang('to_date')}</label>
                    <input autocomplete="off" class="form-control date-picker custom-date" name="to_date" id="to_date" type="text" value="{$ci->input->get('to_date')}">
                </div>
            </div>
            
            <div class="col-sm-2 padding_both_small">
                <div class="form-group credit_debit_button">
                    <button class="btn btn-primary" id="submit" type="submit" value="Search">
                        {lang('search')} </button>
                    <a class="btn btn-info" href="{base_url()}admin/my_transfer_details">
                        {lang('reset')} </a>
                </div>
            </div>
        {form_close()}
    </div>
</div>
 
<div class="panel panel-default">
    
        <div class="table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped">
            <thead>
                <tr class="th">
                    <th>{lang('slno')}</th>
                    <th>{lang('given_user_name')}</th>
                    <th>{lang('recieved_user_name')}</th>
                    <th>{lang('amount')}</th>
                    <th>{lang('transaction_fee')}</th>
                    <th>{lang('date')}</th>
                    <th>{lang('notes')}</th>
                </tr>
            </thead>
            {if !empty($fund_transfer_details)}
                <tbody>
                    {foreach from=$fund_transfer_details key=key item=item}
                        <tr>
                            <td>{$ci->input->get('offset')+$key+1}</td>
                            <td>
                                {if $item.from_user_delete_status == "active"}
                                    {user_with_name($item.from_username, $item.from_user_fullname, true)}
                                {else}
                                    {$item.from_username}
                                {/if}
                            </td>
                            <td>
                                {if $item.to_user_delete_status == "active"}
                                    {user_with_name($item.to_username, $item.to_user_fullname, true)}
                                {else}
                                    {$item.to_username}
                                {/if}
                            </td>
                            <td>{format_currency($item.amount)}</td>
                            <td>{format_currency($item.trans_fee)}</td>
                            <td>{$item.date|date_format:"d M Y - h:i:s A"}</td>
                            <td>{$item.transaction_concept}</td>
                        </tr>
                    {/foreach}
                </tbody>
            {else}
                <tbody>
                    <tr>
                        <td colspan="7">
                            <h4 class="text-center">{lang('No_Details_Found')}</h4>
                        </td>
                    </tr>
                </tbody>
            {/if}
        </table>
    </div>
    {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
</div>

{/block}
