{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
    <div id="span_js_messages" style="display: none;">
      <span id="errmsg">{lang('You_must_enter_keyword_to_search')}</span>
      <span id="row_msg">{lang('rows')}</span>
      <span id="show_msg">{lang('shows')}</span>
    </div>

    <div class="row">
      <div class="col-lg-12">
        <div class="panel panel-default padder-v">
          <div class="panel-body padder-lg padder-v">
            {* <div class="upgrade-profile">
              <div class="profile-avatar">
                <img class="" src="{$SITE_URL}/uploads/images/profile_picture/{$expired_users.user_img}">
              </div>
              <div>
                <h3 class="profile-name full_name" title="{$expired_users.full_name}">{$expired_users.full_name}</h3>
                <h5 class="profile-name2 user_name2">{$expired_users.user_name}</h5>
                <p class="profile-email">{$expired_users.email}</p>
              </div>
            </div> *}

              <div class="upgrade-dec">
                <table>
                  <tbody>
                    {* <tr>
                      <td>{lang('sponser_name')}</td>
                      <td>:</td>
                      <td>{$expired_users.sponsor_name}</td>
                    </tr> *}
                    <tr>
                      <td>{lang('expiry_on')}</td>
                      <td>:</td>
                      <td>{$expired_users.product_validity|date_format:"%B %e, %Y, %r"}</td>
                    </tr>
                    <tr>
                        <td>{lang('renewal_charge')}</td>
                        <td>:</td>
                        <td>{format_currency($product_amount)}</td>
                    </tr>
                  </tbody>
                </table>
              </div>              
            </div>

            <!-- Start Tab section -->

            <div class="tab-section">
              <div class="tab-container">
                {if $MODULE_STATUS['opencart_status'] != 'yes'}  
                  {if count($expired_users) > 0 && !$product_status}
                      <legend><span class="fieldset-legend">{lang('payment_options')}</span></legend>
                      {include file="user/member/payment_tab.tpl" title="Example Smarty Page" name=""} 
                  {/if}
                {/if} 
              </div>
            </div>
            <!----demo-->
          <!--end demo--> 
        </div>
      </div>
    <!-- /content -->
    </div>
    <div id="alert_div" style="display: none;">
        <div id="err_reciept" class="alert alert-dismissable text-left">
            <a href="#" style="display:block !important" class="close" data-dismiss="alert" aria-label="close">&times;</a>
        </div>
    </div>
    <div id="div_pos" style="display: none;">
        <option value="">{lang('select_leg')}</option>
        <option value="L">{lang('left_leg')}</option>
        <option value="R">{lang('right_leg')}</option>
    </div>
{/block} 
{block name=style}
    {$smarty.block.parent}
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/user_tab.css" type="text/css" />
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/upgrade_package_validity.css">
{/block}
{block name=script}
    {$smarty.block.parent} 
    <script src="{$PUBLIC_URL}theme/js/tabs_new.js"></script> 
{/block}