<div class="panel panel-default">
    <div class="panel-body">
        {form_open({$search_url|default:$SHORT_URL},'role="form" class="" name="search_member_get" id="search_member_get"
        action="" method="get"')}
    
       <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label class="" for="daterange">{lang('daterange')}</label>
                <select name="daterange" id="daterange" class="form-control">
                    <option value="all">{lang('overall')}</option>
                    <option value="today">{lang('today')}</option>
                    <option value="month">{lang('this_month')}</option>
                    <option value="year">{lang('this_year')}</option>
                    <option value="custom">{lang('custom')}</option>
                </select>
            </div>
        </div>

        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label>{lang('from_date')}</label>
                <input autocomplete="off" class="form-control date-picker custom-date" name="from_date" id="from_date" type="text" value="">
            </div>
        </div>
        <div class="col-sm-2 padding_both_small">
            <div class="form-group">
                <label>{lang('to_date')}</label>
                <input autocomplete="off" class="form-control date-picker custom-date" name="to_date" id="to_date" type="text" value="">
            </div>
        </div> 
        
        <div class="col-sm-2 padding_both_small">
            <div class="form-group mark_paid search-member-btns">
                <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">
                    {lang('search')}
                </button>
                <a class="btn btn-sm btn-info" href="{$BASE_URL}{$search_url|default:$SHORT_URL}">
                    {lang('reset')} </a>
            </div>
        </div>
        {form_close()}
    </div>
</div>