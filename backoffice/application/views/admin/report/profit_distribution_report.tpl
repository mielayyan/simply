{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('admin/profit_distribution_report','role="form" class="" method="get" name="weekly_payout" id="weekly_payout" onsubmit="return validation()"')}


        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label class="" for="daterange">{lang('daterange')}</label>
                <select name="daterange" id="daterange" class="form-control">
                    <option value="all" {if $daterange == 'all'} selected {/if}>{lang('overall')}</option>
                    <option value="today" {if $daterange == 'today'} selected {/if}>{lang('today')}</option>
                    <option value="month" {if $daterange == 'month'} selected {/if}>{lang('this_month')}</option>
                    <option value="year" {if $daterange == 'year'} selected {/if}>{lang('this_year')}</option>
                    <option value="custom" {if $daterange == 'custom'} selected {/if}>{lang('custom')}</option>
                </select>
            </div>
        </div>
        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label>{lang('from_date')}</label>
                <input autocomplete="off" class="form-control date-picker custom-date" name="from_date" id="from_date" type="text" value="{$from_date}">
            </div>
        </div>
        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label>{lang('to_date')}</label>
                <input autocomplete="off" class="form-control date-picker custom-date" name="to_date" id="to_date" type="text" value="{$to_date}">
            </div>
        </div>
        <div class="col-sm-3 padding_both_small">
            <div class="form-group credit_debit_button">
                <button class="btn btn-primary" name="weekdate" type="submit" value="{lang('submit')}">
                {lang('submit')}</button>
            </div>
        </div>
        {form_close()}
    </div>
</div>
<div id="print_area" class="img panel-body panel">
    {if $dateRangeString != ""}<h4 align="center"><b>{$dateRangeString}</b></h4>{/if}
    <div class="panel panel-default  ng-scope">
    <div class=" table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped">{if $count >= 1}
            <tbody>
                <thead>
                    <tr>
                        <th>{lang('sl_no')}</th>
                        <th>{lang('date')}</th>
                        <th>{lang('total_profit')}</th>
                        <th>{lang('company')}</th>
                        <th>{lang('founder')}</th>
                        <th>{lang('founder_count')}</th>
                        <th>{lang('pool')}</th>

                    </tr>
                </thead>
                {assign var="i" value=1} {foreach from=$weekly_payout item=v} 
                  
                <tr>
                    <td>{$ci->input->get('offset')+$i}</td>
                    <td>{$v.date}</td>
                    <td>{format_currency($v.total_profit)}</td>
                    <td>{format_currency($v.company)}</td>
                    <td>{format_currency($v.founder)}</td>
                    <td>{$v.founder_count}</td>
                    <td>{format_currency($v.pool)}</td>
                </tr>
                {$i = $i+1}
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
    {$ci->pagination->create_links('<div class="panel-footer panel-footer-pagination text-right">', '</div>')}
</div>
{/block}