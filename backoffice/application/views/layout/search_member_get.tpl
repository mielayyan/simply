<div class="panel panel-default">
    <div class="panel-body">
        {form_open({$search_url|default:$SHORT_URL},'role="form" class="" name="search_member_get" id="search_member_get"
        action="" method="get"')}
    
        <div class="col-sm-2 padding_both">
            <div class="form-group">
                <label class="" for="user_name">{lang('user_name')}</label>
                <input class="form-control user_autolist" type="text" id="user_name" name="user_name" autocomplete="Off" value="{$user_name|default:''}">
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