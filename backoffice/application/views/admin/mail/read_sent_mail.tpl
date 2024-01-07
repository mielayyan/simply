{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
    
<div id="span_js_messages" style="display:none;">
    <span id="error_msg1">{lang('you_must_enter_message_here')}   </span>        
    <span id="error_msg3">{lang('you_must_select_user')}</span>        
    <span id="error_msg2">{lang('you_must_enter_subject_here')}</span>                  

</div>

    <div class="hbox hbox-auto-xs hbox-auto-sm" >
        {include file="admin/mail/mail_header.tpl"  name=""}
        <div class="col">
            <div>
            <!-- list -->
            <div ui-view="" class="ng-scope" style="">
            <div ng-controller="MailDetailCtrl" >
            <!-- header -->
            <div class="wrapper bg-light lter b-b"> 
                <a  class="btn btn-sm btn-default w-xxs m-r-sm" tooltip="Back to Inbox" href="{BASE_URL}/admin/mail/mail_sent"><i class="fa fa-long-arrow-left"></i></a>
            </div>
 
          <!-- / header -->
        <div class="wrapper b-b ">
            <h4 class="font-thin m-n ">{$mail_details[0]['mailtoussub']}</h4>
        </div>
            {foreach from=$mail_details item=v}
                <div class="wrapper b-b ">

                    <div  class="wrapper m-b-lg">
                      <div class="col-md-1">
                        <img class="thumb-xs m-r-sm" src="{$SITE_URL}/uploads/images/profile_picture/mail_pro.png">
                      </div>
                      <div class="col-md-11">
                        <!-- {lang('from')}: --><a class="h4">
                          {$v.fullname}({$mail_details[0]['to']})
                      </a><br>
                        <a><!-- {lang('on')} -->{$v.mailtousdate}</a> 
                      </div>
                    </div>

                <div class="wrapper break_all">
                   <div class="wrapper more panel" style="margin-bottom: 0;padding: 40px 20px ">{if preg_match('/(<img[^>]+>)/i', $v.msg)}{$v.msg}{elseif preg_match('/(<a[^>]+>)/i', $v.msg)}{$v.msg}{else}{$v.msg}{/if}</div>
                </div>           
                {/foreach} 
        </div>
    </div>
      <!-- / list --> 
    </div>
  </div>
</div>
{/block}

{block name=script}
  {$smarty.block.parent}
    <script src="{$PUBLIC_URL}javascript/MailBox.js" type="text/javascript" ></script>
{/block}