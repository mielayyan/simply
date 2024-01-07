<div class="banner" style="background: url({literal}{banner_img}{/literal});
    height: 58px;
    color: #fff;
    font-size: 21px;
    padding: 43px 20px 20px 40px;">
    {* Your Lead Capture is updated *}
    {lang('your_lead_capture_is_updated')}
</div>
<div class="body_text" style="padding:25px 65px 25px 45px; color:#333333;">
	<h1 style="font-size:18px; color:#333333; font-weight: normal; font-weight: 300;">
		{lang('dear')} 
		<span style="font-weight:bold;">{$first_name} {$last_name}</span>
	</h1>
    {if (isset($admin_comment) && ($admin_comment != ""))}
		<p style="font-size: 14px; line-height: 27px;">&emsp; &emsp; {lang('the_user')} {$ci->LOG_USER_NAME} {lang('commented_as')} {$admin_comment},
		</p>
    {/if}
    {if ($new_status == $lead_status)}
        <p style="font-size: 14px; line-height: 27px;">&emsp; &emsp; {lang('your_leads_status_updated_to')} {$new_status}....</p>
    {/if}
</div>