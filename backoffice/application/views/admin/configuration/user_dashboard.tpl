{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display: none;">
    <span id="validate_msg1">{lang('you_must_enter_sender_id')}</span>
    <span id="validate_msg2">{lang('you_must_enter_user_name')}</span>
    <span id="validate_msg3">{lang('you_must_enter_password')}</span>
</div>

{include file="admin/configuration/advanced_settings.tpl"}



    <div class="panel panel-default">
        <div class="panel-body"> 
          <legend><span class="fieldset-legend">{lang('user_dashboard')}</span></legend>
            {form_open('','role="form" method="post"  name="set_permission_form" id="set_permission_form"')}
                <div class="content">
                        {foreach from=$dashboardConfig item=v}
                                    <div class="checkbox checkbox-parent">
                                        <label class="i-checks">
                                            <input type="checkbox" class="master_item_box" data-checkbox="icheckbox_square-blue" name="{$v.item}" id="{$v.id}" {if $v.status == 1}checked="yes"{/if} data-on-color="success" data-off-color="danger" value="{$v.status}">
                                            <i></i>
                                            {lang($v.title)}
                                            </label>

                                        <div class="m-t-sm"></div>
                                        {foreach from=$v.sub_items item=u}
                                            <div class="checkbox checkbox-child">
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                &nbsp;&nbsp;&nbsp;&nbsp;
                                                <label class="i-checks">
                                                    <input type="checkbox" data-checkbox="icheckbox_square-blue" class="sub-of-master-{$v.id} child_item_box" name="{$u.item}" id="{$u.id}" {if $u.status == 1}checked="yes"{/if} data-on-color="success" data-off-color="danger" value="{$u.status}">
                                                <i></i>{lang($u.item)}
                                                </label>
                                                <div class="m-t-sm"></div>
                                            </div>
                                     {/foreach}
                                    </div>
                                   
                        {/foreach}
                </div>
                <div class="form-group">
                    <button class='btn btn-sm btn-primary' type='submit' align='center' name="update" id='permission' value='Set Permission'>
                      {lang('update')}
                    </button>
                </div>
            {form_close()}        
        </div>
    </div>
{/block}

{block name=script}
  {$smarty.block.parent}
    <script src="{$PUBLIC_URL}javascript/validate_user_dashboard.js" type="text/javascript"></script>
{/block}