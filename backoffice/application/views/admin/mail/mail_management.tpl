{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display:none;">
    <span id="confirm_msg">{lang('Sure_you_want_to_Delete_There_is_NO_undo')}</span>
</div>

    <input type="hidden" id="inbox_form" name="inbox_form" value="{$BASE_URL}" />
    <div class="hbox hbox-auto-xs hbox-auto-sm" ng-controller="MailCtrl">
        {include file="admin/mail/mail_header.tpl"  name=""}
          <div class="col">
            <div class="m-l-sm m-t-sm">
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
            <ul class="list-group list-group-lg no-radius   m-t-n-xxs">
              {assign var=i value=0}
              {assign var=clr value=""}
              {assign var=id value=""}
              {assign var=msg_id value=""}
              {assign var=user_name value=""}
              {assign var=msg_tid value=""}
              {if $cnt_adminmsgs > 0}
              {foreach from=$adminmsgs item=v}
                      {if $v.type == 'contact'}
                          {$msg_tid = $v.id}
                      {else}
                          {$msg_tid = $v.thread}
                      {/if}
                          {$id = $v.id}     
                          {$user_name = $v.user_name}  
                      {if $v.type == 'contact'}
                      {$url = "`$BASE_URL`admin/mail/read_mail/`$v['id']`/`$v['type']`"}
                      {else}
                      {$url = "`$BASE_URL`admin/mail/read_mail/`$v['thread']`/`$v['type']`"}
                      {/if}
                      <li class="list-group-item clearfix b-l-3x {if $v.read_msg=='no'} b-l-success {else}  b-l-info  {/if}">
                        <a href="{$url}">
                          <span class="avatar thumb pull-left m-r"> 
                            <img src="{$SITE_URL}/uploads/images/profile_picture/mail_pro.png">
                          </span>
                         </a>   
                          {$msg_id=$v.id}
                          <div class="pull-right text-sm text-muted" style="padding-top: 3%">
                            <a href="{$url}">
                            <span class="hidden-xs ">{$v.mailadiddate}</span>
                            </a>
                            <button type="button" class="btn-link text-danger" onclick="javascript:deleteMessage({$msg_id},this.parentNode.parentNode.rowIndex, '{$v.type}', '{$BASE_URL}admin')"><i class="fa fa-trash-o" ></i></button>
                          </div>
                        <a href="{$url}">
                          <div class="clear">
                            <div><span  class="text-md ">{$v.fullname} ({$user_name})</span>
                              {if $v.read_msg=='no'}
                              <span class="label bg-primary m-l-sm ">{lang('new')}</span>
                              {/if}
                            </div>
                            <div class="text-ellipsis m-t-xs ">
                              {substr($v.mailadsubject,0,86)} 
                              {if strlen($v.mailadsubject)>86 }...{/if}

                            </div>
                          </div>  
                         </a>    
                      </li>
                      {$i=$i+1}
                      {/foreach}
                      {else}
                        <li class="list-group-item clearfix b-l-3x b-l-info">
                          <center>
                            {lang('You_have_no_mails_in_inbox')}  
                          </center>
                        </li>
                      {/if}
                  </ul>
            <!-- / list -->
          {$ci->pagination->create_links()}
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
