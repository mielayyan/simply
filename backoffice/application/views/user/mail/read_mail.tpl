{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
    
<div id="span_js_messages" style="display:none;">
    <span id="confirm_msg">{lang('Sure_you_want_to_Delete_There_is_NO_undo')}</span>
    <span id="error_msg1">{lang('you_must_enter_message_here')}   </span>        
    <span id="error_msg3">{lang('you_must_select_user')}</span>        
    <span id="error_msg2">{lang('you_must_enter_subject_here')}</span>                  
</div> 
    
    <div class="hbox hbox-auto-xs hbox-auto-sm" ng-controller="MailCtrl">
      {include file="user/mail/mail_header.tpl"  name=""}
        <div class="col">
          <div>
            <div ui-view="" class="ng-scope" style="">
              <div ng-controller="MailDetailCtrl" class="ng-scope">
                <div class="wrapper bg-light lter b-b">
                  <a ui-sref="app.mail.list" class="btn btn-sm btn-default w-xxs m-r-sm" tooltip="Back to Inbox" href="{BASE_URL}/user/mail/mail_management"><i class="fa fa-long-arrow-left"></i></a>
                </div>

                {if $mail_type == 'user'}
                    <div class="wrapper b-b ">
                      <h4 class="font-thin m-n ng-binding">{$mail_details[0]['subject']}</h4>
                    </div>
                    <div class="wrapper b-b ">
                    {$i = 0}
                    {$id = ""}
                    {$user_name = ""}
                    {$subject = ""}
                    {foreach from=$mail_details item=v}
                      {if $v.from != {$LOG_USER_ID} }

                        <div class="panel panel-default">
                          <a data-toggle="collapse" data-parent="#accordion" href="#{$v.id}">
                              <div class="panel-heading"> 
                                  <div  class="wrapper m-b-lg">
                                    <div class="col-md-1">
                                      <img class="thumb-xs m-r-sm" src="{$SITE_URL}/uploads/images/profile_picture/mail_pro.png">
                                    </div>
                                    <div class="col-md-11">
                                    <span class="h4">
                                        {if $loged == $v.from}Me
                                        {else}{$v.fullname}({$v.user_name})
                                        {/if}
                                    </span>
                                    {if $loged == $v.from}<span style="color:#fda341">SENT</span>
                                    {else}<span style="color: #c3c1c4">INBOX</span>
                                    {/if}
                                    <br>
                                      <span><!-- {lang('on')} -->{$v.date}</span> 
                                    </div>
                                  </div>
                              </div>
                              <div id="{$v.id}" class="panel-collapse panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="wrapper break_all">
                                      <div>{if preg_match('/(<img[^>]+>)/i', $v.msg)}{$v.msg}{elseif preg_match('/(<a[^>]+>)/i', $v.msg)}{$v.msg}{else}{$v.msg}{/if}</div>
                                  </div>
                                </div>
                              </div>
                          </a>
                        </div>

                      {else}
                        <div class="panel panel-default">
                          <a data-toggle="collapse" data-parent="#accordion" href="#{$v.id}">
                              <div class="panel-heading"> 
                                  <div  class="wrapper m-b-lg">
                                    <div class="col-md-1">
                                      <img class="thumb-xs m-r-sm" src="{$SITE_URL}/uploads/images/profile_picture/mail_pro.png">
                                    </div>
                                    <div class="col-md-11">
                                    <span class="h4">
                                        {if $loged == $v.from}Me
                                        {else}{$v.fullname}({$v.user_name})
                                        {/if}
                                    </span>
                                    {if $loged == $v.from}<span style="color:#fda341">SENT</span>
                                    {else}<span style="color: #c3c1c4">INBOX</span>
                                    {/if}
                                    <br>
                                      <span><!-- {lang('on')} -->{$v.date}</span> 
                                    </div>
                                  </div>
                              </div>
                              <div id="{$v.id}" class="panel-collapse panel-collapse collapse">
                                <div class="panel-body">
                                    <div class="wrapper break_all">
                                      <div>{if preg_match('/(<img[^>]+>)/i', $v.msg)}{$v.msg}{elseif preg_match('/(<a[^>]+>)/i', $v.msg)}{$v.msg}{else}{$v.msg}{/if}</div>
                                  </div>
                                </div>
                              </div>
                          </a>
                        </div>
                      {/if}

                    {if $v.from != {$LOG_USER_ID} }
                       {$id = $v.id}
                       {$user_name = $v.user_name}
                    {/if}
                    {$subject = $v.message}
                    {$i = $i + 1}
                    {/foreach}
                {else}
                    <div class="wrapper b-b">
                       <h2 class="font-thin m-n ng-binding">{$mail_details['contact_name']} Contacted You</h2>
                    </div>
                    <div class="wrapper ">
                    <div class="panel-heading"> 
                            <div  class="wrapper m-b-lg">
                              <div class="col-md-1">
                                <img class="thumb-xs m-r-sm" src="{$SITE_URL}/uploads/images/profile_picture/mail_pro.png">
                              </div>
                              <div class="col-md-11">
                              <span class="h4">
                                  {$mail_details['contact_name']}({$mail_details['contact_email']})
                              </span>
                              
                              <br>
                                <span>{$mail_details['mailadiddate']}</span> 
                              </div>
                            </div>
                        </div>
                        <div  class="panel">
                          <div class="panel-body">
                              <div class="wrapper break_all">
                                <div>
                                <h5>
                                  {lang('Email')}    :
                                  {$mail_details['contact_email']}
                                </h5>
                                <h5>{lang('Address')}:{$mail_details['contact_address']}</h5>
                                <h5>{lang('Phone')}  : {$mail_details['contact_phone']}</h5>
                                <div class="mailbox-read-message" style="word-wrap: break-word;border: 1px solid #eee;padding: 15px;">
                                {$mail_details['contact_info']}
                                </div>
                                </div>
                            </div>
                          </div>
                        </div>
                      </div>

                {/if}

                {if $mail_type == 'user'}
                    <div class="wrapper">
                      <div class="panel b-a reply_mail_fixed">
                        <div class="panel-heading reply_mail_bg" >
                          <div class="" >
                          Click here to<a href="{$BASE_URL}user/mail/reply_mail/{$id}" class="text-danger" ><span onclick="getUsername('{$user_name}', '{$subject}');" > {lang('reply')}</span></a>
                          </div>
                        </div>
                      </div>
                    </div>
               {/if}
            </div>
        </div>
      </div>
    </div>
 </div>
{/block}

{block name=script}
  {$smarty.block.parent}
    <script src="{$PUBLIC_URL}javascript/MailBox.js"></script>
{/block}
