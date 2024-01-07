{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display:none;">

    <span id="confirm_msg_inactivate">{lang('sure_you_want_to_inactivate_this_rank_there_is_no_undo')}</span>
    <span id="confirm_msg_edit">{lang('sure_you_want_to_edit_this_rank_there_is_no_undo')}</span>
    <span id="confirm_msg_activate">{lang('sure_you_want_to_activate_this_rank_there_is_no_undo')}</span>
    <span id="confirm_msg_delete">{lang('sure_you_want_to_delete_this_rank_there_is_no_undo')}</span>
    <span id="row_msg">{lang('rows')}</span>
    <span id="show_msg">{lang('shows')}</span>
    <span id="error_msg1">{lang('you_must_enter_rank_name')}</span>
    <span id="error_msg2">{lang('you_must_enter_referal_count')}</span>
    <span id="error_msg3">{lang('you_must_enter_rank_achivers_bonus')}</span>
    <span id="error_msg4">{lang('alpha_digit_only')}</span>
    <span id="error_msg5">{lang('digits_only')}</span>
    <span id="error_msg6">{lang('are_you_sure_you_want_to_Delete_There_is_NO_undo')}</span>
    <span id="error_msg7">{lang('are_you_sure_you_want_to_add')}</span>
    <span id="error_msg8">{lang('digit_limit_5')}</span>
    <span id="error_msg9">{lang('field_required')}</span>
    <span id="error_msg10">{lang('rank_name_required')}</span>
    <span id="error_msg11">{lang('referral_count_required')}</span>
    <span id="error_msg12">{lang('personal_pv_required')}</span>
    <span id="error_msg13">{lang('group_pv_required')}</span>
    <span id="error_msg14">{lang('digit_limit_10')}</span>
    <span id="error_msg15">{lang('greater_than_zero')}</span>
    <span id="error_msg16">{lang('rank_name_should_be_unique')}</span>
    <span id="error_msg17">{lang('downline_member_count_required')}</span>
    <span id="error_msg18">{lang('you_must_enter')} {lang('downline_count')|lower}</span>
    <span id="error_msg19">{lang('package_count_required')}</span>
    <span id="error_msg20">{lang('digit_greater_than_0')}</span>
    <span id="error_msg21">{lang('referral_commission_required')}</span>
    <span id="error_msg22">{lang('you_must_enter')} {lang('team_member')|lower}</span>
    <span id="error_msg23">{lang('you_must_enter')} {lang('rank_color')} </span>
</div>

<legend>
    <span class="fieldset-legend">
        {if $action == 'edit'}{lang('edit_promo')}{else}{lang('add_new_rank_promo')}{/if}
    </span>
    <a href="{$BASE_URL}admin/rank_promo" class="btn btn-addon btn-sm btn-info pull-right">
        <i class="fa fa-backward"></i>
        {lang('back')}
    </a>
</legend>

<div class="panel panel-default">
    <div class="panel-body">
        {form_open('', 'role="form" class="" method="post" name="rank_form" id="rank_form"')}
            {include file="layout/error_box.tpl"}
                <div class="form-group">
                    <label class="required">{lang('rank_name')}</label>
                    <input type="text" class="form-control" name="rank_name" id="rank_name"  {if $action == 'edit'} value="{$rank_name}" {else} value="{set_value('rank_name')}" {/if}>

                    {form_error('rank_name')}
                </div>
                <div class="form-group">
                    <label class="required">{lang('group_pv')}</label>
                    <input type="text" class="form-control" name="group_pv" id="group_pv"  {if $action == 'edit'} value="{$group_pv}" {else} value="{set_value('group_pv')}" {/if}>
                    {form_error('group_pv')}
                </div>
                <div class="form-group">
                    <label class="required">{lang('group_pv_per_leg')}(%)</label>
                    <input type="text" class="form-control" name="group_pv_per_leg" id="group_pv_per_leg"  {if $action == 'edit'} value="{$group_pv_percent}" {else} value="{set_value('group_pv_percent')}" {/if}>
                    {form_error('group_pv_per_leg')}
                </div>
                <div class="form-group">
                    <label class="required">{lang('direct_legs')}</label>
                    <input type="text" class="form-control" name="direct" id="direct"  {if $action == 'edit'} value="{$direct}" {else} value="{set_value('direct')}" {/if}>
                    {form_error('direct')}
                </div>
                <div class="form-group">
                    <label class="required">{lang('bonus')}</label>
                    <input type="text" class="form-control" name="bonus" id="bonus"  {if $action == 'edit'} value="{$bonus}" {else} value="{set_value('bonus')}" {/if}>
                    {form_error('bonus')}
                </div>
                <div class="form-group">
                    <label class="required">{lang('voucher')}</label>
                    <input type="text" class="form-control" name="voucher" id="voucher"  {if $action == 'edit'} value="{$voucher}" {else} value="{set_value('voucher')}" {/if}>
                    {form_error('voucher')}
                </div>

            <div class="form-group">
                {if $edit_id==""}
                    <button class="btn btn-sm btn-primary" name="rank_submit" type="submit" value="Submit">{lang('submit')}</button>
                {else}
                    <button class="btn btn-sm btn-primary" name="rank_update" type="submit" value="Update">{lang('update')}</button>
                    <input name="rank_id" id="rank_id" type="hidden" value="{$edit_id}" />
                {/if}
                <input type="hidden" id="path_temp" name="path_temp" value="{$PUBLIC_URL}">
            </div>
        {form_close()}
    </div>
</div>
{/block}

{block name=script}
     {$smarty.block.parent}
     <script src="{$PUBLIC_URL}/plugins/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.js"></script>
     <link href="{$PUBLIC_URL}/plugins/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.css" rel="stylesheet">
    <script>
        $(function() {
            $('.colorpik').colorpicker();
        });
    </script>
{/block}
