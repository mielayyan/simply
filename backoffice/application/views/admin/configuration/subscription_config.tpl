{extends file=$BASE_TEMPLATE}

{block name=style}
      <style type="text/css">
      	.w3-container, .w3-panel {
		    padding: 0.01em 16px;
		}
		.w3-border-blue, .w3-hover-border-blue:hover {
		    border-color: #2196F3 !important;
		}
		.w3-pale-blue, .w3-hover-pale-blue:hover {
		    color: #000 !important;
		    background-color:#ddffff !important;
		}
		.w3-rightbar {
		    border-right: 6px solid #ccc !important;
		}
		.w3-leftbar {
		    border-left: 6px solid #ccc !important;
		}
		.w3-panel {
		    margin-top: 25px;
		    margin-bottom: 16px;
		}
		.w3-container, .w3-panel {
		    padding: 5px 2px;
		}
      </style>
     {$smarty.block.parent}
{/block}

{block name=script}
     {$smarty.block.parent}
     <script src="{$PUBLIC_URL}javascript/validate_binary_bonus.js"></script>
     <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
     <script>
      
      $(document).ready(function(){
         
         var criteria = document.getElementById("subscription_criteria").value;
         if(criteria == 'member_package'){
      	
      	$(".amount_wise").addClass('hidden');
      	$(".package_wise").fadeIn("slow");
        }else if(criteria == 'amount_based'){
        	$(".amount_wise").removeClass('hidden');
        	$(".package_wise").fadeOut("slow");
        }
         
      });
      
      function subscriptionCriteria(a){
      	
      	if(a == 'member_package'){
      	
      	$(".amount_wise").addClass('hidden');
      	$(".package_wise").fadeIn("slow");
        }else if(a == 'amount_based'){
        	$(".amount_wise").removeClass('hidden');
        	$(".package_wise").fadeOut("slow");
        }
        	
      }	
     	
     </script>
{/block}

{block name=$CONTENT_BLOCK}

 {include file="admin/configuration/system_setting_common.tpl"}

 
 <div class="panel panel-default">
        <div class="panel-body">
         {form_open('', 'role="form" class="form" method="post"  name="subscription_settings_form" id="subscription_settings_form"')}	
           <legend>{lang('subscription')}</legend>	
           <div class="form-group">
             <label class="required">{lang('subscription')} {lang('based_on')}</label>
             <select class="form-control" id="subscription_criteria" name="subscription_criteria" onchange="subscriptionCriteria(this.value);">
             	<option value = "member_package" {if $subscription_config['based_on'] == 'member_package'} selected {/if}>Membership Package</option>
             	<option value = "amount_based" {if $subscription_config['based_on'] == 'amount_based'} selected {/if}>Fixed Amount</option>
             </select>
          
           </div>
           <div class="package_wise" style="margin-top: 30px">
	           <!--<div class="form-group">
	           	<div class="w3-container">
				  <div class="w3-panel w3-pale-blue w3-leftbar w3-rightbar w3-border-blue">
				    <p>Subscription Amount is same as that of package Amount</p>
				  </div>
	            </div>
	           </div>-->
	           <div class="panel panel-default table-responsive">
			    <table class="table table-striped">
			      <thead>
			        <tr>
			          <th>{lang('sl_no')}</th>
			          <th>{lang('package')}</th>
			          <th>{lang('amount')}</th>
			          <th>{lang('subscription')} {lang('amount')}</th>
			          <th>{lang('period')} ({lang('monthly')})</th>
			        </tr>
			      </thead>
			      <tbody>
			      {foreach from = $membership_package item = v}
			      <tr>
			        <td>{counter}</td>
			        <td>{$v.product_name}</td>
			        <td>{format_currency($v.product_value)}</td>
			        <td><div class="form-group">
			        	<div class="input-group">
			        	{$left_symbol}
                        <input type="text" class="form-control" name="subscription_value[{$v.product_id}]" id='subscription_value' value="{round($v.subscription_value*$DEFAULT_CURRENCY_VALUE,$PRECISION)}" >
                        {$right_symbol}
                    </div>
                    </div>
                    </td>
			        <td class="col-sm-2">
			        	<input type="number" class="form-control" value="{$v.subscription_period}" name="subscription_period[{$v.product_id}]" maxlength="4" min="1">
			        </td>
			      </tr>
			      {/foreach}
			        </tbody>

			    </table>
              </div>
          </div>
           
           <div class="form-group amount_wise hidden">
           	  <label>{lang('subscription')} {lang('amount')}</label>
           	  <input class="form-control" name="fixed_amount" id="fixed_amount"  maxlength="5" value="{$subscription_config['fixed_amount']}">
           	  {form_error('fixed_amount')}

           	  <label>{lang('subscription')} {lang('period')}</label>
           	  <input type = "number" class="form-control" name="fixed_subscription" id="fixed_subscription"  maxlength="5" min="1" value="{if $subscription_config['subscription_period'] == 0}1{else}{$subscription_config['subscription_period']}{/if}">
           	
           </div>

	        <div class="form-group">
	            <div class="checkbox">
	                <label class="i-checks">
	                <input type="checkbox" name="registration" {if $subscription_config['reg_status'] == 'yes'} checked {/if}><i></i> {lang('disable_registration')}
	                </label>
	            </div>
	        </div>
	        <div class="form-group">
	            <div class="checkbox">
	                <label class="i-checks">
	                <input type="checkbox" name="payout" {if $subscription_config['payout_status'] == 'yes'} checked {/if}><i></i> {lang('disable_payout')}
	                </label>
	            </div>
	        </div>

	        <div class="form-group">
             <button type="submit" class="btn btn-sm btn-primary" value="update" name="update" id="update">{lang('update')}</button>
            </div>
           	
         {form_close()}
        </div>
 </div>        	


{/block}