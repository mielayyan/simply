{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<input type="hidden" id="passwordPolicyJson" value='{$passwordPolicyJson}'>
<div class="col-md-7 col-md-offset-2 m-b-xxl">
  {form_open_multipart('register/register_submit', 'role="form" method="post"  name="form" id="msform"')}
    {if $from_replica}{include file="layout/alert_box.tpl"}{/if}
      <input type="hidden" name="age_limit" id="age_limit" value="{$signup_settings['age_limit']}"/>
      <input type="hidden" name="mlm_plan" id="mlm_plan" value="{$MLM_PLAN}"/>
      <input type="hidden" name="path" id="path" value="{$PATH_TO_ROOT_DOMAIN}"/>
      <input type="hidden" name="lang_id" id="lang_id" value="{$LANG_ID}"/>
      <input type="hidden" id="path_temp" name="path_temp" value="{$PUBLIC_URL}"/>
      <input type="hidden" id="path_root" name="path_root" value="{$PATH_TO_ROOT_DOMAIN}"/>
      <input type="hidden" id="reg_from_tree" name="reg_from_tree" value="{$reg_from_tree}"/>
      <input type="hidden" id="username_type" name="username_type" value="{$user_name_type}"/>
      <input type="hidden" id="pin_count" name="pin_count" value="{$pin_count}" /> 
      <input type="hidden" id="epin_count" name="epin_count" value="0" /> 
      <input type="hidden" id="ewallet_bal" name="ewallet_bal" value="0"/>
      <input type="hidden" id ="ewallet_cheking_type" name= "ewallet_cheking_type"  value = "register" />
      <input type="hidden" id ="registration_fee" name= "registration_fee"  value = "{$registration_fee}" />
      <input type="hidden" id ="product_amount" name= "product_amount"  value = "{$registration_fee}" />
      <input type="hidden" id ="total_reg_amount" name= "total_reg_amount"  value = "{$registration_fee}" />
      <input type="hidden" id ="total_reg_amount1" name= "total_reg_amount1"  value = "{$registration_fee1}" />
      <input type="hidden" id ="product_status" name= "product_status"  value = "{$MODULE_STATUS['product_status']}" />                          
      <input name="date_of_birth" id="date_of_birth" type="hidden" size="16" maxlength="10"  {if $reg_count>0} value="{$reg_post_array['date_of_birth']}" {/if} />
      <input type="hidden" name="default_country" id="default_country" value="{$signup_settings['default_country']}"/>
      <input type="hidden" id="demo_status" value="{$DEMO_STATUS}" />
      <input type="hidden" id ="pro_id" name= "pro_id" value="0" />
      <input type="hidden" id ="p_type" name= "p_type"/>
                   
        {if !$LOG_USER_ID || uri_string() == 'replica_register'}
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
  
  <!-- progressbar -->
  <ul id="progressbar">
    <li class="active"></li>
    <li></li>
    <li></li>
    {if $MODULE_STATUS['product_status'] == "yes" || $registration_fee > 0}
    <li></li>
    {/if}
  </ul>
  <!-- fieldsets -->
  <fieldset>
    {if $MODULE_STATUS['product_status'] == "yes"}
      <h2 class="fs-title"> {lang('sponsor_and_package_information')}</h2>
    {else}
      <h2 class="fs-title"> {lang('sponsor_information')}</h2>
    {/if}
      <div class="form-group">
          <label class="control-label" for="sponsor_user_name">{lang('sponsor_user_name')}<font color="#ff0000">*</font></label>
          {if $LOG_USER_ID}
            <input name="sponsor_user_name" id="sponsor_user_name" type="text" size="22" autocomplete="Off" value="{set_value('sponsor_user_names', $sponsor_user_name)}"  title="" class="form-control" {if $LOG_USER_TYPE != 'admin' && $LOG_USER_TYPE != 'employee'} readonly="" {/if}/>
          {else}
              <input name="sponsor_user_name" id="sponsor_user_name" type="text" size="22" autocomplete="Off" value="{set_value('sponsor_user_names', $sponsor_user_name)}"  title="" class="form-control"/>    
          {/if}
          {form_error('sponsor_user_name')}
          <span id="referral_box" style="display:none;"></span> 
          <span id="errormsg4"></span>
          {if isset($error['sponsor_user_name'])}
              <span class='val-error' >{$error['sponsor_user_name']} </span>
          {/if} 
      </div>
      <div class="form-group">
          <div class="col-sm-12" id="referal_div"  height="30" class="text" style="display:none; padding: 0px;">
          </div>
          {if isset($error['sponsor_full_name'])}<div class="col-sm-4 height_mode_tranpass "> <span class='val-error' >{$error['sponsor_full_name']} </span> </div>{/if}   
      </div> 


      {$plan_array = array("Unilevel","Stair_Step")}
      {if $reg_from_tree && !in_array($MLM_PLAN, $plan_array)} 
          <div class="form-group" style="display:none">  
              <label class="control-label" for="placement_user_name">{lang('placement_user_name')}<font color="#ff0000">*</font></label>
              <input type="text" name="placement_user_name" id="placement_user_name" size="20" value="{set_value('placement_user_name', $placement_user_name)}" readonly="" autocomplete="Off" title="" class="form-control"/> 
              <span id="username_box" style="display:none;"></span>

          </div>
          <div class="form-group" style="display:none">
              <label class="control-label" for="placement_full_name">{lang('placement_full_name')}</label>
              <input type="text" name="placement_full_name" id="placement_full_name" size="22" value="{set_value('placement_full_name', $placement_full_name)}" readonly="" autocomplete="Off"  class="form-control">
          </div>
      {else}
          <input type="hidden" name="placement_user_name" id="placement_user_name" size="20" value="{$placement_user_name}" readonly="" autocomplete="Off" title="" class="form-control"/> 
          <input type="hidden" name="placement_full_name" id="placement_full_name" size="22" maxlength="50"  value="{set_value('placement_full_name', $placement_full_name)}" readonly="" autocomplete="Off"  class="form-control">
      {/if}

      {if $MLM_PLAN == "Binary"}
          <div class="form-group">
              <label class="control-label" for="position">{lang('position')}<font color="#ff0000">*</font></label>
              <select name="position" id="position" class="form-control">
                  {if $reg_from_tree}
                      {if $position == 'L'}
                          <option value="L" {set_select('position', "L", TRUE)}>{lang('left_leg')}</option>
                      {elseif $position == 'R'}
                          <option value="R" {set_select('position', "R")}>{lang('right_leg')}</option>
                      {/if}
                  {else}
                      <option value="" selected>{lang('select_leg')}</option>
                      <option value="L" {if isset($reg_post_array['position']) && $reg_post_array['position'] == 'L'} selected {/if}>{lang('left_leg')}</option>
                      <option value="R" {if isset($reg_post_array['position']) && $reg_post_array['position'] == 'R'} selected {/if}>{lang('right_leg')}</option>
                  {/if}
              </select>
              {form_error('position')}
              <span id="errormsg2"></span>
              {if isset($error['position'])}<span class='val-error' >{$error['position']} </span>{/if}
          </div>
      {else}
          <input type='hidden' value='{$position}' name='position' id='position' class="form-control">
      {/if}
      {if $MODULE_STATUS['product_status'] == "yes"}
          <div class="form-group">
              <label class="control-label" for="product_id">{lang('product')}<font color="#ff0000">*</font></label> 
              <select name="product_id" id="product_id" class="form-control"> 
                  {$products}
              </select> 
              {form_error('product_id')}
              <span id="error_product"></span>
              {if isset($error['product_id'])}<span class='val-error' >{$error['product_id']} </span>{/if}
          </div>   
      {else}
          <input type='hidden' value='0' name='product_id' id='product_id' class="form-control">
      {/if}
      <div class="form-group" id="down_user_div" style="display:none;">
          <label class="control-label" for="user_position">{lang('downline_user_position')}<font color="#ff0000">*</font></label> 
          <select name="downline_user_position" id="downline_user_position" class="form-control"> 
              <option value="BOTH" {if isset($reg_post_array['downline_user_position']) && $reg_post_array['downline_user_position'] == 'BOTH'} selected {/if}>{lang('both_leg')}</option>
              <option value="L" {if isset($reg_post_array['downline_user_position']) && $reg_post_array['downline_user_position'] == 'L'} selected {/if}>{lang('left_leg')}</option>
              <option value="R" {if isset($reg_post_array['downline_user_position']) && $reg_post_array['downline_user_position'] == 'R'} selected {/if}>{lang('right_leg')}</option>
          </select> 
          {form_error('downline_user_position')}
          <span id="error_downline_user_position"></span>
          {if isset($error['downline_user_position'])}<span class='val-error' >{$error['downline_user_position']} </span>{/if}
      </div>
      <div class="form-group" id="board_div" style="display:none;">
          <label class="control-label" for="user_position">{lang('choose_user_for_gifting')}<font color="#ff0000">*</font></label> 
           <select name="board_downline" id="board_downline" class="form-control"> 
           </select>
          {form_error('select_user_for_gifting')}
          <span id="error_downline_user_position"></span>
          {if isset($error['select_user_for_gifting'])}<span class='val-error' >{$error['select_user_for_gifting']} </span>{/if}
      </div>         
      <input type="button" name="" id="product" class="next action-button" value="{lang('next')}"/>
  </fieldset>
    
    
  <fieldset class="m-b-lg">
          <h2 class="fs-title">{lang('contact_info')}</h2>

            {foreach from=$fields item=v}
                {$name = $v['field_name']}
                {$key_name = $v['key_name']}
                {$required = $v['required']}
                <div class="form-group">
                    <label class="control-label" for="{$name}">{lang($name)} {if $required == 'yes'}<font color="#ff0000">*</font> {/if}</label>
                    {if $name == 'country'}
                        <select name="country" id="country" onChange="getAllStates(this.value, 'admin');" class="form-control {if $required == 'yes'} required {/if}">{$countries}</select>
                    {elseif $name == 'gender'}
                        <select name="gender" id="gender" class="form-control {if $required == 'yes'} required {/if}">
                                <option value="">{lang('select_gender')}</option>
                                <option value='M' {if isset($reg_post_array['gender'])&&$reg_post_array['gender']=="M"}selected{/if} >{lang('male')}</option>
                                <option value='F' {if isset($reg_post_array['gender'])&&$reg_post_array['gender']=="F"}selected{/if} >{lang('female')}</option>
                        </select>
                    {elseif $name == 'state'}
                        <select name="state" id="state" class="form-control {if $required == 'yes'} required {/if}">{$states}</select>
                    {elseif $name == 'email'}
                        <div class="input-group">
                        <input type="text" name="{$key_name}" id="{$key_name}" style="text-transform: lowercase;"   autocomplete="Off"  class="form-control {if $name =='date_of_birth'} date-picker-dob {/if} {if $required == 'yes'} required {/if}" {if isset($reg_post_array[$name])} value="{set_value($name, $reg_post_array[$name])}" {/if} />
                        <span class="input-group-btn">
                            <button class="btn btn-success" onClick="send_otp();return false;" type="button">{lang('send_mail_otp')}</button>
                        </span>
                        </div>
                        <span id="otp_sent"></span>
                    {else}
                        <input type="text" name="{$key_name}" id="{$key_name}"  autocomplete="Off"  class="form-control {if $name =='date_of_birth'} date-picker-dob {/if} {if $required == 'yes'} required {/if}" {if isset($reg_post_array[$name])} value="{set_value($name, $reg_post_array[$name])}" {/if} />
                    {/if}
                    {form_error($name)}
                    {if isset($error[$name])}<span class='val-error' >{$error[$name]} </span>{/if}
                </div>
                {if $name == 'email'}
                 <div class="form-group">
                  <div class="input-group">
                  <input type="text" name="otp" id="otp" placeholder="Enter OTP" class="form-control" />
                  <span class="input-group-btn">
                    <button class="btn btn-success" onClick="verify_otp();return false;" type="button">{lang('verify_otp')}</button>
                </span>
                </div>
                <span id="otp_verify"></span>    
                </div>
                
                {/if}
            {/foreach}

          <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
          <input type="button" name="next" class="next action-button" value="{lang('next')}"/>
      </fieldset>
                            
      <fieldset>
          <h2 class="fs-title">{lang('login_information')}</h2>
          <div class="form-group">
              {if {$user_name_type}!="dynamic"}
                  <label class="control-label" for="user_name_entry">{lang('User_Name')}<font color="#ff0000">*</font></label>
                  <input type="text" name="user_name_entry" style="text-transform: lowercase;" id="user_name_entry" autocomplete="Off" {if $reg_count >0} value="{set_value('user_name_entry', $reg_post_array['user_name_entry'])}" data-value="{set_value('user_name_entry', $reg_post_array['user_name_entry'])}"{/if} class="form-control">
                  {form_error("user_name_entry")}
                  <span id="errormsg3"></span>
                  <span id="errmsg33"></span>
                  {if isset($error['user_name_entry'])}<span class='val-error'>{$error['user_name_entry']} </span>{/if}
              {else}
                  <input type='hidden' value='{$user_name_type}' style="text-transform: lowercase;" name='user_name_entry'  id='user_name_entry' class="form-control" data-value="{$user_name_type}">
              {/if}
          </div>
          <div class="form-group">
              <label class="control-label" for="password">{lang('password')}<font color="#ff0000">*</font></label>
              <input type="password"  name="pswd" id="pswd"  autocomplete="Off"  class="form-control act-pswd-popover" >
              {form_error("pswd")}
              {if isset($error['pswd'])}<span class='val-error' >{$error['pswd']} </span>{/if}
          </div>
          <div class="form-group">
              <label class="control-label" for="cpswd">{lang('confirm_password')}<font color="#ff0000">*</font></label>
              <input name="cpswd" id="cpswd" type="password" autocomplete="Off" class="form-control" >
              {form_error('cpswd')}
              {if isset($error['cpswd'])}<span class='val-error' >{$error['cpswd']} </span>{/if}
          </div>
          
          <div id='log1'>
          
          <h2 class="fs-title">{lang('login_information1')}</h2>
          <div class="form-group">
              {if {$user_name_type}!="dynamic"}
                  <label class="control-label" for="user_name_entry">{lang('User_Name')}<font color="#ff0000">*</font></label>
                  <input type="text" name="user_name_child1" style="text-transform: lowercase;" id="user_name_child1" autocomplete="Off" {if $reg_count >0} value="{set_value('user_name_child1', $reg_post_array['user_name_child1'])}" data-value="{set_value('user_name_child1', $reg_post_array['user_name_child1'])}"{/if} class="form-control">
                  {form_error("user_name_child1")}
                  <span id="errormsg3"></span>
                  <span id="errmsg33"></span>
                  {if isset($error['user_name_child1'])}<span class='val-error'>{$error['user_name_child1']} </span>{/if}
              {else}
                  <input type='hidden' value='{$user_name_type}' style="text-transform: lowercase;" name='user_name_entry' id='user_name_entry' class="form-control" data-value="{$user_name_type}">
              {/if}
          </div>
          <div class="form-group">
              <label class="control-label" for="password">{lang('password')}<font color="#ff0000">*</font></label>
              <input type="password"  name="pswd_child1" id="pswd_child1"  autocomplete="Off"  class="form-control act-pswd-popover" >
              {form_error("pswd_child1")}
              {if isset($error['pswd_child1'])}<span class='val-error' >{$error['pswd_child1']} </span>{/if}
          </div>
          <div class="form-group">
              <label class="control-label" for="cpswd">{lang('confirm_password')}<font color="#ff0000">*</font></label>
              <input name="cpswd_child1" id="cpswd_child1" type="password" autocomplete="Off" class="form-control" >
              {form_error('cpswd_child1')}
              {if isset($error['cpswd_child1'])}<span class='val-error' >{$error['cpswd_child1']} </span>{/if}
          </div>
          
          
          
          <h2 class="fs-title">{lang('login_information2')}</h2>
          <div class="form-group">
              {if {$user_name_type}!="dynamic"}
                  <label class="control-label" for="user_name_entry">{lang('User_Name')}<font color="#ff0000">*</font></label>
                  <input type="text" name="user_name_child2" style="text-transform: lowercase;" id="user_name_child2" autocomplete="Off" {if $reg_count >0} value="{set_value('user_name_child2', $reg_post_array['user_name_child2'])}" data-value="{set_value('user_name_child2', $reg_post_array['user_name_child2'])}"{/if} class="form-control">
                  {form_error("user_name_child2")}
                  <span id="errormsg3"></span>
                  <span id="errmsg33"></span>
                  {if isset($error['user_name_child2'])}<span class='val-error'>{$error['user_name_child2']} </span>{/if}
              {else}
                  <input type='hidden' value='{$user_name_type}' style="text-transform: lowercase;" name='user_name_entry' id='user_name_entry' class="form-control" data-value="{$user_name_type}">
              {/if}
          </div>
          <div class="form-group">
              <label class="control-label" for="password">{lang('password')}<font color="#ff0000">*</font></label>
              <input type="password"  name="pswd_child2" id="pswd_child2"  autocomplete="Off"  class="form-control act-pswd-popover" >
              {form_error("pswd_child2")}
              {if isset($error['pswd_child2'])}<span class='val-error' >{$error['pswd_child2']} </span>{/if}
          </div>
          <div class="form-group">
              <label class="control-label" for="cpswd">{lang('confirm_password')}<font color="#ff0000">*</font></label>
              <input name="cpswd_child2" id="cpswd_child2" type="password" autocomplete="Off" class="form-control" >
              {form_error('cpswd_child2')}
              {if isset($error['cpswd_child2'])}<span class='val-error' >{$error['cpswd_child2']} </span>{/if}
          </div>
          
          </div>
          
          
          <div class="form-group">
              <label > </label>
              <div class="checkbox" align="left">
                  <label class="i-checks">
                      <input name="agree" id="agree"  type="checkbox" "{set_checkbox('agree')}">
                       <i></i> <a class="" data-toggle="modal" href ="#panel-config"  style="text-decoration: none" >
                          {lang('I_ACCEPT_TERMS_AND_CONDITIONS')}
                      </a>
                      <font color="#ff0000">*</font>
                      {form_error('agree')}
                      {if isset($error['agree'])}<span class='val-error' >{$error['agree']} </span>{/if}
                  </label>
              </div>
          </div>
          <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
          {if $MODULE_STATUS['product_status'] == "no" && $registration_fee==0}
           <input type="hidden" name="active_tab" id="active_tab" value="free_join" >
          <input type="submit" name="submit" class="submit action-button sw-btn-finish"  value="{lang('finish')}"/>
          {else}
          <input type="button" name="next" class="next action-button next2" value="{lang('next')}"/>
          {/if}
      </fieldset>
                           
  <fieldset>
    {if $MODULE_STATUS['product_status'] == "yes" || $registration_fee > 0}
  <h2 class="fs-title"> {lang('reg_type')}</h2>
  
  
    <h4>{lang('total_amount')}:  
        <span style="font-family: monospace;height:15px; width:100px" class="total-title" id="total_product_amount">
        {format_currency($registration_fee)}
        </span>
    </h4>
 
  {assign var=total value=''}
  {assign var=active_tab_val value="free_join_tab"}
  
     

    {if $payment_methods_tab}
       
         {$payment_pin_status     = $payment_gateway_array['epin_status']}
         {$free_joining_status    = $payment_gateway_array['freejoin_status']}
        {$payment_ewallet_status = $payment_gateway_array['ewallet_status']}
        {$bank_transfer_status   = $payment_gateway_array['banktransfer_status']}
        {$paypal_status = $payment_gateway_array['paypal_status']}
        {$bitcoin_status = $payment_gateway_array['bitcoin_status']}
        {$authorize_status = $payment_gateway_array['authorize_status']}
        {$blockchain_status = $payment_gateway_array['blockchain_status']}  
        {$bitgo_status = $payment_gateway_array['bitgo_status']}   
        {$payeer_status = $payment_gateway_array['payeer_status']}  
        {$sofort_status = $payment_gateway_array['sofort_status']}  
        {$squareup_status = $payment_gateway_array['squareup_status']}
        {$stripe_status = $payment_gateway_array['stripe']}  
        {if $reg_count}
            {$active_tab_val="{$reg_post_array['active_tab']}"}
        {else}

    {foreach from=$payment_gateway_using_reg_status item=v} 
            
    {if $v.gateway_name== 'E-pin' && $MODULE_STATUS['pin_status'] == "yes" }

    {$active_tab_val="epin_tab"}
    {break}
    {else if $v.gateway_name== 'E-wallet' }
        {$active_tab_val="ewallet_tab"}
        {break}
    {else if $v.gateway_name== 'Paypal' }
        {$active_tab_val="paypal_tab"}
        {break}
    {else if $v.gateway_name=='Authorize.Net'}
        {$active_tab_val="authorize_tab"}
        {break}
    
    {else if $v.gateway_name=='Blockchain'}
        {$active_tab_val="blockchain_tab"}
        {break}
    {else if $v.gateway_name=='Bitgo'}
        {$active_tab_val="bitgo_tab"}
        {break}
    {else if $v.gateway_name=='Sofort'}
        {$active_tab_val="sofort_tab"}
        {break}
    {else if $v.gateway_name=='Payeer'}
        {$active_tab_val="payeer_tab"}
        {break}
    {else if $v.gateway_name=='SquareUp'}
        {$active_tab_val="squareup_tab"}
        {break}
    {else if $v.gateway_name== 'Bank Transfer' }
        {$active_tab_val="bank_transfer"}
        {break}
    {else if $v.gateway_name=='Free Joining' }
        {$active_tab_val="free_join_tab"}
        {break}
    {else if $v.gateway_name== 'Stripe' }
        {$active_tab_val="stripe_tab"}
         {break}
    {/if}
    {/foreach}
    {/if}

    {else}
      {$active_tab_val="free_purchase"}
        
    {/if}

  <div class="col-sm-12 bhoechie-tab-container">
    
    <div class=" col-sm-3 bhoechie-tab-menu">
    <div class="list-group">
        {foreach from=$payment_gateway_using_reg_status item=v} 
        {* {if $LOG_USER_ID} *}
        {if $payment_methods_tab}
        {if $v.gateway_name=="E-pin" && $MODULE_STATUS['pin_status'] == "yes" }
            <a href="#" class="list-group-item text-center {if $active_tab_val=='epin_tab'}active{/if}" onclick="changeActiveTab('epin_tab');">
                <h4 class="tabs_h4"><i class="icon-pin"></i></h4>
                    {lang('epin')} 
            </a> 
        {/if}
       {*  {/if} *}
        {if $v.gateway_name=="E-wallet"}
            <a href="#" class="list-group-item text-center {if $active_tab_val=='ewallet_tab'}active{/if}" onclick="changeActiveTab('ewallet_tab');">
                <h4 class="tabs_h4"><i class="icon-wallet"></i></h4>
                    {lang('ewallet')} 
            </a> 
        {/if}
        {if $v.gateway_name=="Bank Transfer"}
            <a href="#" class="list-group-item text-center {if $active_tab_val=='bank_transfer'}active{/if}" onclick="changeActiveTab('bank_transfer');">
                <h4 class="tabs_h4"><i class="fa fa-bank"></i></h4>
                    {lang('bank_transfer')} 
            </a> 
        {/if}
        {if $v.gateway_name=="Paypal"}
            <a href="#" class="list-group-item text-center {if $active_tab_val=='paypal_tab'}active{/if}" onclick="changeActiveTab('paypal_tab');">
                <h4 class="tabs_h4"><i class="fa fa-paypal"></i></h4>
                    {lang('paypal')} 
            </a> 
        {/if}
        {if $v.gateway_name=="Authorize.Net"}
            <a href="#" class="list-group-item text-center {if $active_tab_val=='authorize_tab'}active{/if}" onclick="changeActiveTab('authorize_tab');">
                <h4 class="tabs_h4"><i class="icon-lock"></i></h4>
                    {lang('authorize')} 
            </a> 
        {/if}
     {if $v.gateway_name=="Stripe"}
                            <a href="#" class="list-group-item text-center {if $active_tab_val=='paypal_tab'}active{/if}"                              onclick="changeActiveTab('stripe_tab');">
                                <h4 class="tabs_h4"><i class="fa fa-paypal"></i></h4>
                                    {lang('stripe')} 
                            </a> 
        {/if}
       
        {if $v.gateway_name=="Blockchain"}
            <a href="#" class="list-group-item text-center {if $active_tab_val=='blockchain_tab'}active{/if}" onclick="changeActiveTab('blockchain_tab');">
                <h4 class="tabs_h4"><i class="fa fa-asterisk"></i></h4>
                    {lang('blockchain')} 
            </a> 
        {/if}        
        {if $v.gateway_name=="Bitgo"}
            <a href="#" class="list-group-item text-center {if $active_tab_val=='bitgo_tab'}active{/if}" onclick="changeActiveTab('bitgo_tab');">
                <h4 class="tabs_h4"><i class="fa fa-btc"></i></h4>
                    {lang('bitgo')} 
            </a> 
        {/if}
        {if $v.gateway_name=="Payeer" }
            <a href="#" class="list-group-item text-center {if $active_tab_val=='payeer_tab'}active{/if}" onclick="changeActiveTab('payeer_tab');">
                <h4 class="tabs_h4"><i class="fa fa-product-hunt"></i></h4>
                    {lang('payeer')} 
            </a> 
        {/if}
        {if $v.gateway_name=="Sofort"}
            <a href="#" class="list-group-item text-center {if $active_tab_val=='sofort_tab'}active{/if}" onclick="changeActiveTab('sofort_tab');">
                <h4 class="tabs_h4"><i class="fa fa-euro"></i></h4>
                    {lang('sofort')} 
            </a>
        {/if}
        {if $v.gateway_name=="SquareUp"}
            <a href="#" class="list-group-item text-center {if $active_tab_val=='squareup_tab'}active{/if}" onclick="changeActiveTab('squareup_tab');">
                <h4 class="tabs_h4"><i class="fa fa-square"></i></h4>
                    {lang('squareup')} 
            </a>
        {/if}
        {if $v.gateway_name=="Free Joining" }
          
            <a href="#" class="list-group-item text-center {if $active_tab_val=='free_join_tab'}active{/if}" onclick="changeActiveTab('free_join_tab');">
                <h4 class="tabs_h4"><i class="fa fa-cog"></i></h4>
                    {lang('free_join')} 
            </a> 
        {/if}
    {else}  
        {$active_tab_val="free_join_tab"} 
        <a href="#" class="list-group-item text-center {if $active_tab_val=='free_join'}active{/if}" onclick="changeActiveTab('free_join_tab');">
            <h4 class="tabs_h4"><i class="fa fa-cog"></i></h4>
                {lang('free_join')} 
        </a>  
    {/if}

    {/foreach}        
    </div>
  </div>
  
  <div class="col-sm-9 bhoechie-tab">
    
    <input type="hidden" name="active_tab" id="active_tab" value="{$active_tab_val}" >
    <input type="hidden" name="free_join_status" id="free_join_status" value="yes" >
    {foreach from=$payment_gateway_using_reg_status item=v}
<!-- Epin section -->
{* {if $LOG_USER_ID} *}
{if $payment_methods_tab}
{if $v.gateway_name=="E-pin"  && $MODULE_STATUS['pin_status'] == "yes"}
      <div class="bhoechie-tab-content {if $active_tab_val=='epin_tab'}active{/if}">
                      <div class="content">
                          <div class="panel panel-default">
                              <table class="table table-striped table-bordered table-hover table-full-width overflow_table" id="p_scents" st-table="rowCollectionBasic">
                                  <thead>
                                      <tr align="center">
                                          <th>{lang('sl_no')}</th>
                                          <th>{lang('epin')} </th> 
                                          <th>{lang('epin_amount')}  </th>
                                          <th>{lang('remain_epin_amount')}  </th> 
                                          <th>{lang('req_epin_amount')} </th> 
                                      </tr>
                                  </thead>
                                  <tbody>
                                      {if $pin_count}
                                          {for $i=1 to $pin_count}
                                              <tr   align="center" >
                                                  <td>{$i}</td>  
                                                  <td>
                                          <input type="text" name="epin{$i}" id="epin{$i}" size="20"   autocomplete="Off"   class="form-control rounded width_table" onblur="check_epin_submit();" value="{$reg_post_array["epin{$i}"]}"/>
                                                      <span id="pin_box_{$i}" style="display:none;"></span>
                                                      {if isset($error["epin$i"])}<span class='val-error' >{$error["epin$i"]}</span>{/if}   
                                                  </td>
                                                  <td> 
                                          {$DEFAULT_SYMBOL_LEFT}<input type="text" name="pin_amount{$i}" id="pin_amount{$i}" size="20"   autocomplete="Off"   class="form-control rounded width_table" readonly value="{$reg_post_array["pin_amount{$i}"]}"/> {$DEFAULT_SYMBOL_RIGHT}
                                                      <span id="pin_amount_span" style="display:none;"></span>
                                                  </td>
                                                  <td>
                                          {$DEFAULT_SYMBOL_LEFT}<input type="text" name="remaining_amount{$i}" id="remaining_amount{$i}" size="20"   autocomplete="Off"   class="form-control rounded width_table" readonly value="{$reg_post_array["remaining_amount{$i}"]}"/> {$DEFAULT_SYMBOL_RIGHT}
                                                      <span id="remain_amount_span" style="display:none;"></span>
                                                  </td>
                                                  <td>
                                          {$DEFAULT_SYMBOL_LEFT}<input type="text" name="$i}" id="balance_amount{$i}" size="19"   autocomplete="Off"   class="form-control rounded width_table" readonly value="{$reg_post_array["balance_amount{$i}"]}"/> {$DEFAULT_SYMBOL_RIGHT}
                                                      <span id="balance_amount_span" style="display:none;"></span>                                                            
                                                  </td>
                                              </tr> 
                                          {/for}
                                      {else}
                              <tr align="center" id="epin_raw1">
                                              <td>1</td>  
                                              <td>
                                                  <p style="margin: 0px 0 0px;">
                                          <input  type="text" name="epin1" id="epin1" size="20"   autocomplete="Off"   class="form-control rounded width_table" onblur="check_epin_submit();"/>  
                                                  </p>
                                                  <span id="pin_box_1" style="display:none;"></span>  
                                              </td>
                                              <td> 
                                      <input type="text" name="pin_amount1" id="pin_amount1" size="20"   autocomplete="Off"   class="form-control rounded width_table" readonly/>  
                                                  <span id="pin_amount_span" style="display:none;"></span>                                                       
                                              </td>
                                              <td>
                                      <input type="text" name="remaining_amount1" id="remaining_amount1" size="20"   autocomplete="Off"   class="form-control rounded width_table" readonly/>  
                                                  <span id="remain_amount_span" style="display:none;"></span>
                                              </td>
                                              <td>
                                      <input type="text" name="balance_amount1" id="balance_amount1" size="19"   autocomplete="Off"   class="form-control rounded width_table" readonly/>  
                                                  <span id="balance_amount_span" style="display:none;"></span>                                                        
                                              </td>
                                          </tr>
                                      {/if}
                                  </tbody>
                              </table>
                          </div>
                      <div class="pull-left">
                          <div class="form-group line_block">
                              <label  class="bg_color_none" >{lang('total_amount')}</label>
                              <input type="text" name="epin_total_amount" id="epin_total_amount" size="20"   autocomplete="Off"   class="form-control"  readonly {if isset($reg_post_array["epin_total_amount"])}value="{$reg_post_array["epin_total_amount"]}"{/if}/>  
                              <span id="epin_total_amount_span" style="display:none;"></span>
                          </div>
                          <div class="form-group line_block"  id="validate_epin_div">
                              <button type="button" class="btn m-b-xs btn-primary validate_e_pin" id ="pin_btn" name= "pin_btn" onclick="validate_epin();">{lang('epin_val')}</button>
                              {if $DEMO_STATUS =="yes"}
                                     <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                                      <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/>
                              {/if}
                          </div>
                      </div>
                  </div> 
        </div>  
{/if}
{* {/if}  *}
<!-- Ewallet section -->
{if $v.gateway_name=="E-wallet"}
    <div class="bhoechie-tab-content {if $active_tab_val=='ewallet_tab'}active{/if}">
        <div class="content">
                <div class="form-group">
                    <label class="bg_color_none" >{lang('User_Name')}<font color="#ff0000">*</font></label>
                    <input type="text" class="form-control" id="user_name_ewallet" name="user_name_ewallet"  title="{lang('User_Name')}" class="form-control" autocomplete="off"/>    
                    <span id="user_name_ewallet_box" style="display:none;"  class="help-block m-b-none"></span>
                    {if isset($error['user_name_ewallet'])}<span class='val-error' >{$error['user_name_ewallet']} </span>{/if}
                </div>

                    <div class="form-group">
                        <label class="bg_color_none">{lang('transaction_password')}<font color="#ff0000">*</font></label>
                        <input type="password" class="form-control" id="tran_pass_ewallet" name="tran_pass_ewallet" title="{lang('transaction_password')}" class="form-control" autocomplete="off"/>  
                        <span id="tran_pass_ewallet_box" style="display:none;" class="help-block m-b-none"></span>
                        {if isset($error['tran_pass_ewallet'])}<span class='val-error' >{$error['tran_pass_ewallet']} </span>{/if}
                    </div>                    
                    <div class="form-group">
                        <div id="check_ewallet_button">
                            <button type="button" id="ewallet_btn" name="ewallet_btn" class="btn btn-primary  update_social_info-submit" onclick="validate_ewallet();">{lang('check_availability')}</button>
                                  {if $DEMO_STATUS =="yes"}
                                     <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                                      <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/>
                              {/if}
                        </div> 
                    </div>
        </div>
    </div>
{/if}  
  
<!-- Bank section -->
{if $v.gateway_name=="Bank Transfer"}
    <div class="bhoechie-tab-content bank {if $active_tab_val=='bank_transfer'}active{/if}">
        <div class="content">
            <div class="form-group">
                <label class="no_bg_clr">{lang('bank_details')}</label>
                <textarea class="" id="bank_info" style="height:150px;" readonly="true" name="bankinfo">{$bank_details['account_info']}</textarea> 
            </div>
            <div class="form-group">
                <label class="no_bg_clr">{lang('select_reciept')} <font color="#ff0000">*</font></label>
                <input class="m-b-xs padding_center" id="userfile" name="userfile" accept="image/*" type="file">  
                <p style="color: #ff0000;">({lang('Allowed_types_are_pg_jpeg_png')})</p>       
                <img class="m-i-xs" id="img_prev" src="#" alt=""/>
                <a href="#" class="btn btn-light-grey btn-file fileupload-exists" data-dismiss="fileupload" id="remove_id" style="display:none;">
                    <i class="fa fa-times"></i> {lang('remove')}
                </a>
            </div>
            <div class="form-group">
                <button type="button" class="btn btn-addon m-b-xs btn-info update_profile_image" id="update_profile_image"> <i class="fa fa-arrow-circle-o-up"></i> {lang('upload')} </button>
                       {if $DEMO_STATUS =="yes"}
                            <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                            <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/>
                       {/if}
            </div>
            </div>
    </div>
{/if}

<!-- Paypal section -->
{if $v.gateway_name=="Paypal"} 
    <div class="bhoechie-tab-content {if $active_tab_val=='paypal_tab'}active{/if}">
        <div class="content">
            <pre class="alert alert-info">{lang('click_finish_continue')}</pre>
                  {if $DEMO_STATUS =="yes"}
                       <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                       <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/>
                  {/if}
        </div>
    </div>
{/if}
{* stripe section *}

 {if $v.gateway_name=="Stripe"} 
                    <div class="bhoechie-tab-content {if $active_tab_val=='stripe_tab'}active{/if}">
                        <div class="content">
                            <pre class="alert alert-info">{lang('click_finish_continue')}</pre>
                                   {* <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                                   <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/> *}
                                
                        </div>
                    </div>
                {/if}
{* end stripe section *}
<!-- Authorize.net section -->
{if $v.gateway_name=="Authorize.Net"} 
    <div class="bhoechie-tab-content {if $active_tab_val=='authorize_tab'}active{/if}">
        <div class="content">
            <pre class="alert alert-info">{lang('click_finish_continue')}</pre>
                  {if $DEMO_STATUS =="yes"}
                      <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                      <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/>
                  {/if}
        </div>
    </div>
{/if}




{if $v.gateway_name=="Blockchain"}                
    <div class="bhoechie-tab-content {if $active_tab_val=='blockchain_tab'}active{/if}">
        <div class="content">
            <pre class="alert alert-info">{lang('blockchain_only_in_live')}</pre>
                  {if $DEMO_STATUS =="yes"}
                      <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                      <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/>
                  {/if}
        </div>
    </div>
{/if}

{if $v.gateway_name=="Bitgo"} 
    <div class="bhoechie-tab-content {if $active_tab_val=='bitgo_tab'}active{/if}">
        <div class="content">
            <pre class="alert alert-info">{lang('click_finish_continue')}</pre>
                  {if $DEMO_STATUS =="yes"}
                     <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                     <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/>
                 {/if}
        </div>
    </div> 
{/if}
{if $v.gateway_name=="Payeer" }
    <div class="bhoechie-tab-content {if $active_tab_val=='payeer_tab'}active{/if}">
        <div class="content">
            <pre class="alert alert-info">{lang('payeer_only_in_live')}</pre>
                  {if $DEMO_STATUS =="yes"}
                        <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                        <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/>
                  {/if}
        </div>
    </div>
{/if}
{if $v.gateway_name=="Sofort"} 
    <div class="bhoechie-tab-content {if $active_tab_val=='sofort_tab'}active{/if}">
        <div class="content">
            <pre class="alert alert-info">{lang('sofort_only_in_live')}</pre>
            {if $DEMO_STATUS == "yes"}
                   <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                   <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/>
            {/if}
        </div>
    </div> 
{/if}
{if $v.gateway_name=="SquareUp"}
    <div class="bhoechie-tab-content {if $active_tab_val=='squareup_tab'}active{/if}">
        <div class="content">
            <pre class="alert alert-info">{lang('click_finish_continue')}</pre>
                  {if $DEMO_STATUS =="yes"}
                      <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                      <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/>
                  {/if}
        </div>
    </div>
{/if}
{if $v.gateway_name=="Free Joining"}
    <div class="bhoechie-tab-content {if $active_tab_val=='free_join_tab'}active{/if}">
        <div class="content">
            <pre class="alert alert-info">{lang('click_finish_continue')}</pre>
                {if $DEMO_STATUS =="yes"}
                    <input type="button" name="previous" class="previous action-button-previous inner-previous-button" value="{lang('previous')}"/>
                    <input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}"/>
                {/if}
        </div>
    </div>
{/if}
{else}
        <div class="bhoechie-tab-content active">
            <div class="content">
                <pre class="alert alert-info">{lang('click_finish_continue')}</pre>
                {if $DEMO_STATUS =="yes"}
                    <input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
                    <input type="submit" name="submit" class="submit action-button sw-btn-finish" disabled="disabled"  value="{lang('finish')}"/>
                {/if}
            </div>
        </div>
{/if}
{/foreach}
</div>

</div>
 {/if}
{if $DEMO_STATUS == "no"}
<input type="button" name="previous" class="previous action-button-previous" value="{lang('previous')}"/>
<input type="submit" name="submit" class="submit action-button sw-btn-finish" value="{lang('finish')}" disabled="disabled"/>
{/if}
</fieldset>
  {form_close()}
<!-- link to designify.me code snippets -->
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

<div class="modal terms" id="panel-config" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
      <div class="modal-content">
          <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >
                  &times;
              </button>
              <h4 class="modal-title" style="font-size: 16px;">{lang('terms_conditions')}</h4>
          </div>
          <div class="modal-body">
              <table cellpadding="0" cellspacing="0" align="center">
                  <tr>
                      <td width="80%">
                          {$termsconditions}
                      </td>
                  </tr>
              </table>
          </div>

      </div>
  </div>
</div> 
{* {/if} *}
{/block}

 {block name=style}
  {$smarty.block.parent}
<link rel="stylesheet" href="{$PUBLIC_URL}theme/css/user_tab.css" type="text/css" />
{/block}
 {block name=script}
  {$smarty.block.parent} 
<script src="{$PUBLIC_URL}theme/js/tabs_new.js"></script>
<script src="{$PUBLIC_URL}javascript/epin_register.js" type="text/javascript" ></script>
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
                // $('#update_language_info').attr('disabled', false);
            }
        });
}

</script>
{/block}
