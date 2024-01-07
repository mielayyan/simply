<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-sm-12">
                <div class="user_account-user-details-container">
                    <div class="user_account-user-img b b-3x">
                            <img src="{$SITE_URL}/uploads/images/profile_picture/{$user_image}" class="">
                    </div>
                    <div class="user_account-user-details">
                        <div class="user_account-user-details1">
                            <span>{lang('user_name')} </span> : &nbsp; 
                            {$user_name}
                        </div>
                        <div class="user_account-user-details1">
                            <span>{lang('name')} </span> : &nbsp; 
                            {$full_name}
                        </div>
                    </div>                            
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="button-container">
            <div class="col-sm-2 m-b-sm padding_both">
                {form_open('admin/profile_view', 'method="post"')}
                    <input type="hidden" name="user_name" value="{$user_name}" />
                    <input type="hidden" name="from_page" id="from_page" value="user_account" />
                    <input type="hidden" name="overview_disp" id="overview_disp" value="yes" />
                    <button class="btn-block btn-link user_button">
                        <div class="card_1 table-card">
                            <div class="row-table">
                                <div class="col-auto theme-bg text-white p-t-50 p-b-50">
                                    <i class="fa fa-address-book-o f-30"></i>
                                </div>
                                <div class="text-center text-center-width">
                                    <h4 class="f-w-300 m-t-sm m-b-sm">{lang('profile')}</h4>
                                </div>
                            </div>
                        </div>
                    </button>
                {form_close()}
            </div>
            <div class="col-sm-2 m-b-sm padding_both_small">
                {form_open('admin/income', 'method="post"')}
                    <input type="hidden" name="user_name" value="{$user_name}" />
                    <input type="hidden" name="from_page" id="from_page" value="user_account" />
                    <input type="hidden" name="overview_disp" id="overview_disp" value="yes" />
                    <button class="btn-block btn-link user_button">
                        <div class="card_1 table-card">
                            <div class="row-table">
                                <div class="col-auto theme-bg text-white p-t-50 p-b-50">
                                    <i class="fa fa-money f-30"></i>
                                </div>
                                <div class="text-center text-center-width">
                                    <h4 class="f-w-300 m-t-sm m-b-sm">{lang('income_details')}</h4>
                                </div>
                            </div>
                        </div>
                    </button>
                {form_close()}
            </div>
            <div class="col-sm-2 m-b-sm padding_both_small">
                {form_open('admin/my_referal', 'method="post"')}
                    <input type="hidden" name="user_name" value="{$user_name}" />
                    <input type="hidden" name="from_page" id="from_page" value="user_account" />
                    <input type="hidden" name="overview_disp" id="overview_disp" value="yes" />
                    <button class="btn-block btn-link user_button">
                        <div class="card_1 table-card">
                            <div class="row-table">
                                <div class="col-auto theme-bg text-white p-t-50 p-b-50">
                                    <i class="fa fa-user-circle-o f-30"></i>
                                </div>
                                <div class="text-center text-center-width">
                                    <h4 class="f-w-300 m-t-sm m-b-sm">{lang('refferal_details')}</h4>
                                </div>
                            </div>
                        </div>
                    </button>
                {form_close()}
            </div>
            {if $MLM_PLAN == "Binary"}
                <div class="col-sm-2 m-b-sm padding_both_small">
                    {form_open('admin/view_leg_count', 'method="post"')}
                        <input type="hidden" name="user_name" value="{$user_name}" />
                        <input type="hidden" name="from_page" id="from_page" value="user_account" />
                        <input type="hidden" name="overview_disp" id="overview_disp" value="yes" />
                        <button class="btn-block btn-link user_button">
                            <div class="card_1 table-card">
                                <div class="row-table">
                                    <div class="col-auto theme-bg text-white p-t-50 p-b-50">
                                        <i class="fa fa-sitemap f-30"></i>
                                    </div>
                                    <div class="text-center text-center-width">
                                        <h4 class="f-w-300 m-t-sm m-b-sm">{lang('binary_details')}</h4>
                                    </div>
                                </div>
                            </div>
                        </button>
                    {form_close()}
                </div>
            {/if}
            {if $MODULE_STATUS['pin_status']=="yes"}
                <div class="col-sm-2 m-b-sm padding_both_small">
                    {form_open('admin/view_pin_user', 'method="post"')}
                        <input type="hidden" name="user_name" value="{$user_name}" />
                        <input type="hidden" name="from_page" id="from_page" value="user_account" />
                        <input type="hidden" name="overview_disp" id="overview_disp" value="yes" />
                        <button class="btn-block btn-link user_button">
                            <div class="card_1 table-card">
                                <div class="row-table">
                                    <div class="col-auto theme-bg text-white p-t-50 p-b-50">
                                        <i class="fa fa-bookmark-o f-30"></i>
                                    </div>
                                    <div class="text-center text-center-width">
                                        <h4 class="f-w-300 m-t-sm m-b-sm">{lang('user_epin')}</h4>
                                    </div>
                                </div>
                            </div>
                        </button>
                    {form_close()}
                </div>
            {/if}
            <div class="col-sm-2 m-b-sm padding_both_small">
                {form_open('admin/my_income', 'method="post"')}
                    <input type="hidden" name="user_name" value="{$user_name}" />
                    <input type="hidden" name="from_page" id="from_page" value="user_account" />
                    <input type="hidden" name="overview_disp" id="overview_disp" value="yes" />
                    <button class="btn-block btn-link user_button">
                        <div class="card_1 table-card">
                            <div class="row-table">
                                <div class="col-auto theme-bg text-white p-t-50 p-b-50">
                                    <i class="fa fa-money f-30"></i>
                                </div>
                                <div class="text-center text-center-width">
                                    <h4 class="f-w-300 m-t-sm m-b-sm">{lang('income_statement')}</h4>
                                </div>
                            </div>
                        </div>
                    </button>
                {form_close()}
            </div>
            {if $MLM_PLAN == "Binary"}
                <div class="col-sm-2 m-b-sm padding_both_small">
                    {form_open('admin/business_volume', 'method="post"')}
                        <input type="hidden" name="user_name" value="{$user_name}" />
                        <input type="hidden" name="from_page" id="from_page" value="user_account" />
                        <input type="hidden" name="overview_disp" id="overview_disp" value="yes" />
                        <button class="btn-block btn-link user_button">
                            <div class="card_1 table-card">
                                <div class="row-table">
                                    <div class="col-auto theme-bg text-white p-t-50 p-b-50">
                                        <i class="fa fa-tint f-30"></i>
                                    </div>
                                    <div class="text-center text-center-width">
                                        <h4 class="f-w-300 m-t-sm m-b-sm">{lang('business_volume')}</h4>
                                    </div>
                                </div>
                            </div>
                        </button>
                    {form_close()}
                </div>
            {/if}
            </div>
        </div>
    </div>
</div>