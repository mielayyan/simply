{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display:none;">
    <span id="validate_msg1">{lang('enter_subject')}</span>
    <span id="validate_msg2">{lang('enter_message')}</span>    
    <input type = "hidden" name = "logo" id = "logo" value = "{$SITE_URL}images/logos/{$site_info["logo"]}" >
</div>

<main>
    <div class="tabsy">
        <input type="radio" id="tab1" name="tab"  {if $active_tab == 'text_invites'} checked {/if} >
          <label class="tabButton" for="tab1">{lang('add_text_invite')}</label>
            <div class="tab">{include file="admin/member/text_invite_configuration.tpl"  name=""} </div>
                
        <input type="radio" id="tab2" name="tab" {if $active_tab == 'banner_invites'} checked {/if}>
          <label class="tabButton" for="tab2" >{lang('banner')}</label>
            <div class="tab">{include file="admin/member/invite_banner_config.tpl"  name=""}
            </div>

       
        <input type="radio" id="tab3" name="tab" {if $active_tab == 'social_invites'} checked {/if}>
          <label class="tabButton" for="tab3">{lang('social_invites')}</label>
            <div class="tab">{include file="admin/member/invite_wallpost_config.tpl" name=""}
            </div>

      
    </div>
</main>

{/block}