{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display:none;">
    <span id="confirm_msg">{lang('Sure_you_want_to_Delete_There_is_NO_undo')}</span>
</div>

    <div class="hbox hbox-auto-xs hbox-auto-sm" ng-controller="MailCtrl">
        {include file="user/mail/mail_header.tpl"  name=""}
        <div class="col">
            <div class="m-l-sm">
                {include file="layout/alert_box.tpl"}
                </div>
          <div>
            <!-- header -->
            <div class="wrapper bg-light lter b-b">
                <div class="btn-group pull-left">
                      <a href="" class="btn btn-sm btn-bg btn-default panel-refresh" data-toggle="tooltip" data-placement="bottom" data-title="Refresh" data-original-title="" title=""><i class="fa fa-refresh"></i></a>
                </div>
                <div class="btn-toolbar">
                </div>
            </div>  
            <!-- / header -->
          <!-- list -->
            <ul class="list-group list-group-lg no-radius m-t-n-xxs">
                {assign var=i value=0}
                {assign var=i value=1}
                {assign var=clr value=""}
                {assign var=id value=""}
                {assign var=msg_id value=""}
                {assign var=user_name value=""}
                {assign var=msg_tid value=""}
                {if $cnt_mails > 0}
                    {foreach from=$row item=v}
                        {$id = $v.mailadid}     

                        {$user_name = $v.user_name}  
                        {if $v.type == 'contact' || $v.type == 'ext_mail_user'}
                            {$msg_tid = $v.mailtousid}
                        {else}
                            {$msg_tid = $v.thread}
                        {/if}

                        <li class="list-group-item clearfix b-l-3x b-l-info">
                            <span class="avatar thumb pull-left m-r">
                              <img src="{$SITE_URL}/uploads/images/profile_picture/mail_pro.png">
                            </span>
                            <div class="pull-right text-sm text-muted" style="padding-top: 3%;">
                              <span class="hidden-xs ">{$v.mailadiddate}</span>
                                {$msg_id=$v.mailadid}
                                  {if $v.type == "ext_mail_user"}
                                      <button type="button" class="btn-link text-danger btn-md" onclick="javascript:deleteSentMessage('{$msg_id}', this.parentNode.parentNode.rowIndex, '{$v.type}', '{$BASE_URL}user')" data-original-title="Delete"><i class="fa fa-trash-o" ></i></button>
                                  {else}
                                      <button type="button" class="btn-link text-danger btn-md" onclick="javascript:deleteSentMessage('{$v.thread}', this.parentNode.parentNode.rowIndex, '{$v.type}', '{$BASE_URL}user')" data-original-title="Delete"><i class="fa fa-trash-o" ></i></button>
                                  {/if}
                            </div>
                            <div class="clear">
                              <div><span  class="text-md ">{$v.fullname}({$user_name})</span></div>
                                    {if $v.type == "ext_mail_user"}
                                        <div class="text-ellipsis m-t-xs "><a href="{$BASE_URL}user/mail/read_sent_mail/{$v.mail_enc_id}/{$v.mail_enc_type}">{$v.mailadsubject}</a></div>
                                    {else}
                                        <div class="text-ellipsis m-t-xs "><a href="{$BASE_URL}user/mail/read_sent_mail/{$v.mail_enc_id}/{$v.mail_enc_type}">{$v.mailadsubject}</a></div>  
                                    {/if}
                            </div>      
                        </li>
                        {$i=$i+1} 
                    {/foreach}
                {else}
                    <li class="list-group-item clearfix b-l-3x b-l-info">
                        <center>
                            {lang('You_have_no_mails_in_sent')}
                        </center>
                    </li>
                {/if}
          </ul>
           {$ci->pagination->create_links()}     
          <!-- / list -->
        </div>

      </div>
    </div>
     <style>
 ul.pagination {
    margin-top: 0px;
    float: right !important;
    margin-bottom: 51px;
}
</style>
{/block}