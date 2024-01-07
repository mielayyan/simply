{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display:none;">

    <span id="confirm_msg_inactivate">{lang('sure_you_want_to_inactivate_this_promo')}</span>
    <span id="confirm_msg_edit">{lang('sure_you_want_to_edit_this_promo_there_is_no_undo')}</span>
    <span id="confirm_msg_activate">{lang('sure_you_want_to_activate_this_promo')}</span>
    <span id="confirm_msg_delete">{lang('sure_you_want_to_delete_this_promo_there_is_no_undo')}</span>
</div>

    {include file="admin/configuration/system_setting_common.tpl"}

    <div class="panel panel-default">
        <div class="panel-body">
            <legend><span class="fieldset-legend">{lang('rank_promo')}</span>
                <a class="btn m-b-xs btn-sm btn-primary btn-addon pull-right" href="{$BASE_URL}admin/add_new_rank_promo" id="add_rank"><i class="fa fa-plus"></i> {lang('add_new')}</a>
            </legend>
            <div>
            <div class="row">
                {form_open('admin/configuration/rank_promo','role="form" class="" method="post" name="commision_form" id="commision_form" onsubmit="return validation()"')}
        <div class="col-sm-2">
            <div class="form-group">
                <label>{lang('Promo Start Date')}</label>
                <input autocomplete="off" class="form-control date-picker custom-date" name="from_date" id="from_date" type="text" value="{$promo['promo_start_date']}">
            </div>
        </div>
        <div class="col-sm-2 ">
            <div class="form-group">
                <label>{lang('Promo End Date')}</label>
                <input autocomplete="off" class="form-control date-picker custom-date" name="to_date" id="to_date" type="text" value="{$promo['promo_end_date']}">
            </div>
        </div>
        <div class="col-sm-3 ">
        <div class="form-group credit_debit_button">
            <button class="btn btn-primary" name="commision" type="submit" value="{lang('submit')}">
                {lang('Update')}</button>
        </div>
        </div>
        {form_close()}
           </div>
           <div class="row">
               {form_open('admin/configuration/reset_rank_promo','role="form" class="" method="post" name="reset_form" id="reset_form" onsubmit="return confirm_submit()"')}
               <div class="col-sm-3 ">
                <div class="form-group credit_debit_button">
                    <button class="btn btn-primary" name="reset" type="submit" value="{lang('submit')}">
                        {lang('Reset User\'s Promo')}</button>
                </div>
                </div>
                {form_close()}
           </div>
                <div class="panel panel-default table-responsive">
                    <table st-table="rowCollectionBasic" class="table table-striped">
                        <thead>
                            <tr>
                                <th>{lang('sl_no')}</th>
                                <th>{lang('rank_name')}</th>
                                <th>{lang('direct_legs')}</th>
                                <th>{lang('group_pv')}</th>
                                <th>{lang('group_pv_per_leg')}</th>
                                <th>{lang('bonus')}</th>
                                <th>{lang('travel_voucher')}</th>
                                <th>{lang('action')}</th>
                               
                            </tr>
                        </thead>
                        {if count($ranks) > 0}
                        <tbody>
                        {assign var="path" value="{$BASE_URL}admin/"}
                        {foreach from=$ranks item=v}
                                <tr>
                                    <td>{counter}</td>
                                    <td>{$v.rank_name}</td>
                                    <td>{$v.direct}</td>
                                    <td>{$v.group_pv}</td>
                                    <td>{$v.group_pv_percent}%</td>
                                    <td>{$v.bonus}</td>
                                    <td>{$v.voucher}</td>
                                    <td class="ipad_button_table">
                                        {if $v.status=="active"}
                                            <button class="btn btn-light-grey btn-xs text-black" onclick="edit_rank_promo({$v.id}, '{$path}')" title="{lang('edit')}"> <i class="fa fa-edit"></i></button>
                                            <button class="btn btn-light-grey btn-xs text-black inactivate_membership_package" onclick="inactivate_rank_rank({$v.id}, '{$path}')" title="{lang('inactivate')}"><i class="fa fa-ban"></i></button>
                                        {else}
                                            <button class="btn btn-light-grey btn-xs text-black" onclick="activate_rank_promo({$v.id}, '{$path}')" title="{lang('activate')}"><i class="icon-check"></i></button>
                                        {/if}
                                    </td>
                                </tr>
                        {/foreach}
                        </tbody>
                        {else}
                        <tbody>
                            <tr id="tr-empty"><td align="center"><h4 align="center">{lang('no_product_found')}</h4></td></tr>
                        </tbody>
                        {/if}
                    </table>
                </div>
            </div>
        </div>
    </div>


{/block}

{block name=style}
    {$smarty.block.parent}
    <style>
    .rank_col {
        display: inline;
        padding: .2em .6em .3em;
        font-size: 100%;
        font-weight: 700;
        line-height: 1;
        color: #fff;
        text-align: center;
        white-space: nowrap;
        vertical-align: baseline;
        border-radius: .25em;
    }
    </style>
{/block}

{block name=script}
     {$smarty.block.parent}
     <script src="{$PUBLIC_URL}/plugins/bootstrap-colorpicker/dist/js/bootstrap-colorpicker.js"></script>
      <script src="{$PUBLIC_URL}javascript/rank_configuration.js" type="text/javascript" ></script> 
     <link href="{$PUBLIC_URL}/plugins/bootstrap-colorpicker/dist/css/bootstrap-colorpicker.css" rel="stylesheet">
     <script>
         function edit_rank_promo(id, root) {
            var confirm_msg = $('#confirm_msg_edit').html();
            if(confirm(confirm_msg)){
                document.location.href = root + 'configuration/add_new_rank_promo/edit/' + id;
            }
        }
         function inactivate_rank_rank(id, root) {
            var confirm_msg = $('#confirm_msg_inactivate').html();
            if(confirm(confirm_msg)){
                document.location.href = root + 'configuration/activate_inactivate_promo/inactivate/' + id;
            }
        }
        function confirm_submit(){
           
              if (confirm("Are you sure to reset user's rank Promo?.This action will reset all users current Promo")) {
                return true
              } else {
                return false;
              }
        }
     </script>
{/block}