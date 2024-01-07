{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display:none;">
    <span id="error_msg1">{lang('you_must_enter_message_here')}   </span>        
    <span id="error_msg3">{lang('you_must_select_user')}</span>        
    <span id="error_msg2">{lang('you_must_enter_subject_here')}</span>                  

</div>  

    <div class="hbox hbox-auto-xs hbox-auto-sm" ng-controller="MailCtrl">
      {include file="admin/auto_responder/mail_header.tpl"  name=""}
        <div class="col">
            <div> 
             <!-- list -->
                <div ui-view="" class="ng-scope" style="">
                  <div ng-controller="MailDetailCtrl" class="ng-scope">
                    <!-- header -->
                    <div class="wrapper bg-light lter b-b"> 
                        <a ui-sref="app.mail.list" class="btn btn-sm btn-default w-xxs m-r-sm" tooltip="Back to Inbox" href="{$BASE_URL}admin/auto_responder/auto_responder_details"><i class="fa fa-long-arrow-left"></i></a>
                    </div>
                  <!-- / header -->
                    <div class="wrapper b-b">
                        <h2 class="font-thin m-n ng-binding" style="word-wrap: break-word;">{$sub}</h2>
                    </div>
                    <div class="wrapper ng-binding">
                        <h5>{lang('mail_send_date')}: {$current_date}
                        <span class="mailbox-read-time pull-right"></span></h5>
                        <div class="mailbox-read-message" style="word-wrap: break-word;">
                            {$mail_details}
                        </div>
                    </div>
                  
                  </div>     
                </div>
            </div>
            <!-- / list --> 
        </div>
    </div>
{/block}
                                                