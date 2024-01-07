<!-- Modal Epin Transfer -->
<div class="modal right fade" id="epin_transfer_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog" role="document"> 
        <div class="modal-content">
            <div class="modal-header modal-ewallet-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="modal-ewallet-area">
                    <h3>{lang('transfer_epin')}</h3>
                </div>
                <div class="popup-input">
                    {form_open('','id="epin_transfer_form"')}
                    <div class="row">
                        
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                            <label class="required"> {lang('to_username')} </label>
                                {* <input class="form-control user-search" name="to_user_name" id="to_user_name" type="text" placeholder="{lang('to_username')}"> *}
                                <input class="form-control user_autolist autocomplete-off" type="text" id="to_user_name" name="to_user_name" autocomplete="Off" placeholder="{lang('to_username')}"/> 
                               
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                            <label class="required"> {lang('epin')} </label>
                                <select name="epin" id="epin" class="form-control m-b">
                                    <option value="">{lang('select_epin')}</option>
                                       {assign var=i value=0}
                                       {foreach from=$epin_details item=v}
                                    <option value="{$v.pin_id}">{$v.pin_numbers}</option>
                                       {$i = $i+1}
                                       {/foreach}

                                </select>
                                
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group ">
                                <button class="btn btn-primary" id="epin_transfer_btn" type="submit">{lang('transfer')}</button>
                            </div>
                        </div>
                    </div>
                    {form_close()}
                </div>
            </div>
        </div>
        <!-- modal-content -->
    </div>
    <!-- modal-dialog -->
</div>
<!-- modal Epin Transfer -->