<!----demo-->
{if DEMO_STATUS == "yes" && $LOG_USER_TYPE=='admin' }
<div class="panel-body setting_margin  demo_margin_top" id="demo_footer">
  <div class="m-b-xxl">
    <div class="panel no-border">
      <div class="wrapper-md">
        <div class="row">
          {if $is_preset_demo}               
            <div class="col-md-6">
              <p>
                <i class="fa fa-check-circle"></i>
                {lang('you_are_viewing_shared_demo_multiple_users_may_try_this_demo_simultaneously')}
              </p>
              <p>
                <i class="fa fa-check-circle"></i> 
                {lang('try')}
                <a class="text-primary" href="https://infinitemlmsoftware.com/register.php" target="_blank">
                  {lang('custom_demo')}
                </a>
                {lang('as_per_your_configurations')}
              </p>
            </div>
          {else} 
            <div class="col-md-6">
              <p>
                <i class="fa fa-check-circle"></i> 
                {lang('custom_demo_will_be_automatically_deleted_after_48_hours_unless_upgraded')}
              </p>
              <p>
                <i class="fa fa-check-circle"></i> 
                {lang('you_can_upgrade_custom_demo_to_one_month_or_can_purchase_the_software')}
              </p>
            </div>   
          {/if}
          <div class="col-md-6 b-l">
            <p>
              <i class="fa fa-check-circle"></i>
              {lang('once_the_demo_is_ready_you_can_simply_move_the_demo_to_your_own_domain_name')}
              </p>
              {if $LOG_USER_TYPE=='admin'}
                <p>
                  <i class="fa fa-check-circle"></i> 
                  {lang('click_here_to_place_a')}
                  <a class="text-primary" href="{$BASE_URL}admin/revamp/send_feedback" target="_blank">
                    {lang('feedback_for_support')}
                  </a>
                </p>
            {/if}
          </div>
          {if !$is_preset_demo && $LOG_USER_TYPE=='admin'}
           <div class="col-md-12 text-center m-t-sm">
            <a class="btn m-b-xs btn-sm btn-primary btn-addon" href="{$BASE_URL}admin/revamp/revamp_update_plan"><i class="fa fa-plus"></i> {lang('upgrade_now')}</a>
            </div>
            {/if}
        </div>
        <hr>
        
                 <div class="row">
         <div class="col-md-3">
         <ul class="social">
            <li><a href="#">  <i class="fa fa-newspaper-o"></i> </a> </li>
           <span class="m-t-sm">{if $is_app} <a class="font-bold " href="https://blog.infinitemlmsoftware.com" target="_blank">Infinite MLM Blog</a>
           {else}<div class="font-bold " href="" target="_blank">Infinite MLM Blog</div>{/if}</span>
          </ul>
         </div>
         <div class="col-md-3">
         <ul class="social">
            <li><a href="#">  <i class="fa fa-skype"></i> </a> </li>
           <span class="m-t-sm"> <div class="font-bold " href="" target="_blank">infinitemlm</div></span>
          </ul>
         </div>
         <div class="col-md-3">
         <ul class="social">
            <li><a href="#">  <i class="fa fa-whatsapp"></i> </a> </li>
           <span class="m-t-sm"> <div class="font-bold " href="" target="_blank">+91 9562-941-055</div></span>
          </ul>
         </div>
         <div class="col-md-3">
         <ul class="social">
            <li><a href="#">  <i class="fa fa-envelope"></i> </a> </li>
           <span class="m-t-sm"> <div class="font-bold " href="" target="_blank">support@ioss.in</div></span>
          </ul>
         </div>
         </div>
        
      </div>
    </div>
  </div>
</div>
{/if}

<!--end demo--> 


 
