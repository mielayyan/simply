{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
    {if $from_replica}
        {include file="layout/alert_box.tpl"}
    {/if}
    <style>
        .float-right {
            float: right;
        }
    </style>

    <div class="button_back"> 
        {if $MLM_PLAN != "Board"}
            {if $LOG_USER_ID && !$from_replica}
                <a href="{$PATH_TO_ROOT}{$user_type}/tree/genology_tree" style="text-decoration:none">
                    <button class="btn m-b-xs btn-sm btn-primary">
                        {lang('go_to_tree_view')}
                    </button>
                </a>
            {/if}
        {else}
            {if $LOG_USER_ID && !$from_replica}
                <a href="{$PATH_TO_ROOT}{$user_type}/boardview/view_board_details" style="text-decoration:none">
                    <button class="btn m-b-xs btn-sm btn-primary">
                        {lang('Club_View')}
                    </button>
                </a>
            {/if}
        {/if}
        <a href="" onclick="print_report(); return false;"> 
            <button class="btn m-b-xs btn-sm btn-primary btn-addon"><i class="icon-printer"></i>{lang('Print')}</button>
        </a>
    </div>
    <div class="panel panel-default">
        <div class="panel-body" id="print_area">
            <div class="row">
               {if !$LOG_USER_ID ||$from_replica == TRUE}
                  <ul class="nav navbar-nav">
                      <li class="dropdown">
                          <a href="#" data-toggle="dropdown" class="dropdown-toggle width_flag">
                              {foreach from=$LANG_ARR item=v}
                                  {if $selected_language_id == $v.lang_id}
                                      <img src="{$PUBLIC_URL}images/flags/{$v.lang_code}.png" /> 
                                  {/if}
                              {/foreach}
                              <span class="visible-xs-inline">{lang('change_your_language')}</span>
                              <b class="caret"></b>
                          </a>
                          <!-- dropdown -->
                          <ul class="dropdown-menu animated fadeInRight">
                              {foreach from=$LANG_ARR item=v}
                              <li>
                                  <a href="javascript:changeDefaultLanguageInRegister('{$v.lang_id}');">
                                      <img src="{$PUBLIC_URL}images/flags/{$v.lang_code}.png" /> {$v.lang_name}
                                  </a>
                              </li>
                              {/foreach}
                          </ul>
                          <!-- / dropdown -->
                      </li>
                      </ul>
                  {/if}
                {* Language *}
                <div class="img">
                    <div class="col-sm-6"> <img src="{$SITE_URL}/uploads/images/logos/{$site_info['login_logo']}" alt="" /> </div>
                    {* Language *}
                </div>
                <div class="col-sm-6 text-right float-right">
                    <p> {$site_configuration['company_name']}</p>
                    <p> {$site_configuration['company_address']}</p>
                </div>
                <div class="col-sm-12">
                    <div class="table-responsive">
                        <table st-table="rowCollectionBasic" class="table table-striped ">
                            <tbody>
                                <tr>
                                    <td  class="user_table_width"><strong>{lang('User_Name')}</strong></td>
                                    <td class="mobile_text_left">{$user_registration_details['user_name']} </td>
                                </tr>
                                <tr>
                                    <td><strong>{lang('fullname')}</strong></td>
                                    <td class="mobile_text_left">
                                        {if isset($user_registration_details['first_name'])}
                                            {$user_registration_details['first_name']} 
                                        {/if}
                                        {$user_registration_details['last_name']}
                                    </td>
                                </tr>
                                {if $referal_status == "yes"}
                                    <tr>
                                        <td><strong>{lang('sponsor')}</strong></td>
                                        <td class="mobile_text_left">{$sponsorname}</td>
                                    </tr>
                                {/if}
                                <tr>
                                    <td><strong>{lang('registration_amount')}</strong></td>
                                    <td class="mobile_text_left"> {$DEFAULT_SYMBOL_LEFT}{number_format($user_registration_details['reg_amount']*$DEFAULT_CURRENCY_VALUE,2, '.', '')} {$DEFAULT_SYMBOL_RIGHT}</td>
                                </tr>
                                {if $user_registration_details['reg_amount']>0}
                                {/if}
                                {$total_amount = $user_registration_details['reg_amount']}
                                {if $MODULE_STATUS['product_status'] == "yes"}
                                    {$total_amount = $total_amount + $user_registration_details['product_amount']}
                                    <tr>
                                        <td><strong>{lang('package')}</strong></td>
                                        <td class="mobile_text_left">{$user_registration_details['product_name']}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>{lang('package_amount')}</strong></td>
                                        <td class="mobile_text_left">{format_currency($user_registration_details['product_amount'])}</td>
                                    </tr>
                                {/if}
                                <tr>
                                    <td><strong>{lang('total_amount')}</strong></td>
                                    <td class="mobile_text_left"> {$DEFAULT_SYMBOL_LEFT}{number_format($total_amount*$DEFAULT_CURRENCY_VALUE,2, '.', '')} {$DEFAULT_SYMBOL_RIGHT}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="well bg-light lt"> 
                {$letter_arr['main_matter']}
                <br />
                {lang('winning_regards')},<br />
                <br />
                {lang('admin')}<br />
                <br />
                {$site_configuration['company_name']} <br />
                <br />
                {lang('date')}<br />
                {$date} <br />
            </div>
        </div>
    </div>
    <script>
        function changeDefaultLanguageInRegister(language_id) {
            $.ajax({
                url: base_url + 'register/change_default_language',
                data: { language: language_id },
                type: 'post',
                success: function(data) {
                    if (data == 'yes') {
                        location.reload();
                    }
                },
                error: function(error) {
                    console.log(error);
                },
                complete: function() {
                }
            });
        }
</script>
{/block}