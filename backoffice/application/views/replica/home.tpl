{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
<style>
	.val-error {
		color: #a94442;
		float: left;
		margin-bottom: 13px;
	}
	.error {
		color: #a94442;
		float: left;
		margin-bottom: 13px;	
	}
	.help-block {
		margin: 0;
	}
	#contact_form input {
		margin-top: 15px;
	}
</style>
   <section id="home" class="hero_section" style='background-image: url("{$SITE_URL}/uploads/images/banners/{$banners}");'>
		    
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-sm-12">
				<hr>
				<h3><span class="bold"> 
					{if isset($content['home_title1'])}
						{$content['home_title1']}
					{/if}
				</h3>
				<h1 class="heading"> 
					{if isset($content['home_title2'])}
						{$content['home_title2']}
					{/if}	
				</h1>
				<a href="{SITE_URL}/replica_register"  target="_blank" class="smoothScroll btn btn-default">{lang(join_us)}</a>
			</div>
		</div>
	</div>		
</section>



<section id="plan">
	<div class="container">
		<table>
			<tr>
				<td>
					{if isset($content['plan'])}
						{$content['plan']}
					{/if}
				</td>
			</tr>
		</table>
	</div>
</section>


<section id="about">
	<div class="container">
		<table>
			<tr>
				<td>
					{if isset($content['about'])}
						{$content['about']}
					{/if}
				</td>
			</tr>
		</table>
</div>
</section>


<section id="contact">
	<div class="container">
		<div class="row">
			<div class="col-md-12 col-sm-12 text-center">
				<h2 class="heading">{strtoupper(lang(contact_us))}</h2>
			</div>
		</div>
		<div class="row">
			<div class="contact-info-box col-md-4 col-sm-4 col-xs-12 wow fadeInUp" data-wow-delay="0.6s">
				<i class="fa fa-phone"></i>
				{if isset($content['contact_phone'])}
					<h3>{$content['contact_phone']}</h3>
				{/if}
			</div>
			<div class="contact-info-box col-md-4 col-sm-4 col-xs-12 wow fadeInUp" data-wow-delay="0.8s">
				<i class="fa fa-envelope-o"></i>
				{if isset($content['contact_mail'])}
					<h3>{$content['contact_mail']}</h3>
				{/if}
			</div>
			<div class="contact-info-box col-md-4 col-sm-4 col-xs-12 wow fadeInUp" data-wow-delay="1s">
				<i class="fa fa-map-marker"></i>
				{if isset($content['contact_address'])}
					<h3>{$content['contact_address']}</h3>
				{/if}
			</div>
		</div>
		<div class="row">
			<div class="col-md-12" style="margin-top: 37px">
				{include file="replica/error_box.tpl" title="" name=""}
			</div>
			<div class="col-md-12 col-sm-12">
				{form_open('replica/home',' role="form" id="contact_form" method="post"
                        name="contact_form" class="form-horizontal footer_form"')}
					<div class="col-md-6 col-sm-6">
						<input name="name" type="text" class="form-control" id="name" placeholder="{lang('Name')}" value="{if isset($contact_post_array['name'])}{$contact_post_array['name']}{/if}" />
                                <span class="help-block"></span>
                                {if isset($contact_error['name'])}
                                	<span class='val-error'>{$contact_error['name']}</span>
                            	{/if}
				  	  	<input name="email" type="email" class="form-control" id="email" placeholder="{lang('Email')}" value="{if isset($contact_post_array['email'])}{$contact_post_array['email']}{/if}">
                                <span class="help-block"></span>{if isset($contact_error['email'])}<span class='val-error'>{$contact_error['email']}
                                </span>{/if}
						<input name="phone" type="text" class="form-control" id="phone" placeholder="{lang('Phone_Number')}" value="{if isset($contact_post_array['phone'])}{$contact_post_array['phone']}{/if}">
						  <span class="help-block"></span>{if isset($contact_error['phone'])}<span class='val-error'>{$contact_error['phone']}
                                </span>{/if}
						<input type="text" name="address" id="address" class="form-control" placeholder="{lang('address')}" value="{if isset($contact_post_array['address'])}{$contact_post_array['address']}{/if}">
                                <span class="help-block"></span>{if isset($contact_error['address'])}<span class='val-error'>{$contact_error['address']}
                                </span>{/if}
					</div>
					<div class="col-md-6 col-sm-6">
						<textarea name="message" rows="7" class="form-control" id="message" placeholder="Message">{if isset($contact_post_array['message'])}{$contact_post_array['message']}{/if}</textarea>
                                {if isset($contact_error['message'])}<span class='val-error'>{$contact_error['message']}
                                </span>{/if}
					</div>
					<div class="col-md-offset-4 col-md-4 col-sm-offset-4 col-sm-6">
						<input name="submit" type="submit" class="form-control" id="submit" value="{strtoupper(lang('send_email'))}">
					</div>
				{form_close()}
			</div>
		</div>
	</div>
</section>

{/block}
