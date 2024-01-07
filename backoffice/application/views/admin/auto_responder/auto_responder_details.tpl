{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display: none;"> 
    <span id="row_msg">{lang('rows')}</span>
    <span id="show_msg">{lang('shows')}</span>
    <span id="validate_msg1">{lang('enter_subject')}</span>
    <span id="validate_msg2">{lang('enter_mail_content')}</span>
    <span id="validate_msg3">{lang('enter_mail_number')}</span>
    <span id="validate_msg4">{lang('enter_mail_send_date')}</span>
    <span id="validate_msg5">{lang('do_you_want_to_edit_this')}</span>
    <span id="validate_msg6">{lang('do_you_want_to_delete_this')}</span>
</div>

    <div class="hbox hbox-auto-xs hbox-auto-sm" ng-controller="MailCtrl">
      {include file="admin/auto_responder/mail_header.tpl"  name=""}
      <input type="hidden" id="path_root" name="path_root" value="{$PATH_TO_ROOT_DOMAIN}admin/">
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
            <ul class="list-group list-group-lg no-radius m-b-none m-t-n-xxs">
                {assign var=i value=0}
                {assign var=clr value=""}
                {assign var=id value=""}
                {assign var=msg_id value=""}
                {assign var=user_name value=""}
                {if $count>0}
                    {foreach from=$mail_data item=v}
                        <li class="list-group-item clearfix b-l-3x b-l-info">
                            <span class="avatar thumb pull-left m-r"><img src="{$SITE_URL}/uploads/images/profile_picture/mail_pro.png"> </span>
                          <div class="pull-right text-sm text-muted">
                              <button class="btn-link h5  text-info" href="" onclick='edit_auto_respnder("{$v.mail_number_encrypt}");'  data-toggle="tooltip" title="Edit" data-placement="bottom"><i class="fa fa-edit "></i></button>
                              <a class="btn-link h5 text-danger" onclick='delete_auto_respnder("{$v.mail_number_encrypt}");' data-toggle="tooltip" title="Delete" data-placement="bottom"><i class="fa fa-trash-o"></i></a> 
                          </div>
                          <div class="clear">
                              <div><a class="text-md" onclick='auto_respnder_details("{$v.mail_number_encrypt}");' title="Details">{$v.subject|truncate:100}</a></div>
                            <div class="text-ellipsis m-t-xs ">{$v.date_to_send}    ({lang('mail_send_date')})</div>
                          </div>      
                        </li>
                      {$i=$i+1} 
                    {/foreach}
                {else}
                    <li class="list-group-item clearfix b-l-3x b-l-info">
                        <center>
                            {lang('No_Auto_responder_Detail_Found')}
                        </center>
                    </li>
                {/if}
            </ul>
            <div class="wrapper-sm">
                {include file="common/notes.tpl" notes=lang('note_autoresponder')}
            </div>
            <!-- / list -->
        </div>
    </div>
</div>
{/block}