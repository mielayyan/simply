<div class="panel-body tree-search">
        {form_open({$search_url|default:$SHORT_URL},'role="form" class="" name="search_member_get" id="search_member_get"
        action="" method="get"')}
    <div class="tree-search-input">
        <div class="form-group">
            <label class="tree-search-label" for="user_name">{lang('user_name')}</label>
            <input class="form-control {if $LOG_USER_TYPE=='admin' || $LOG_USER_TYPE=='employee'}user_autolist{/if}" type="text" id="user_name" name="user_name" autocomplete="Off" value="{$user_name|default:''}">
        </div>
    </div>    
    <div class="form-group tree-search-btn">
        <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">
                    {lang('search')}
        </button>
        <a class="btn btn-sm btn-info" href="{$BASE_URL}{$search_url|default:$SHORT_URL}">
                    {lang('reset')} </a>
    </div>    
        {form_close()}
</div>
