<!-- Modal Epin Purchase --> 
<div class="modal right fade" id="epin_purchase_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-ewallet-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="modal-ewallet-area">
                    <h3>{lang('epin_purchase')}</h3>
                </div>
                <div class="popup-input">
                    {form_open('','id="epin_purchase_form"')}
                    <div class="row">
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                            <label class="required"> {lang('user_name')} </label>
                                {* <input class="form-control user-search" id="user_name" type="text" name="user_name" placeholder="{lang('username')}"> *}
                                <input class="form-control user_autolist autocomplete-off" type="text" id="user_name" name="user_name" autocomplete="Off" placeholder="{lang('username')}"/> 
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                            <label class="required"> {lang('amount')} </label>
                                <select name="amount" id="amount" class="form-control m-b">
                                    <option value="" selected="selected">{lang('select_amount')}</option>
                                    {foreach $amounts as $amount}
                                        <option value="{$amount['id']}">{format_currency($amount['amount'])}</option>
                                    {/foreach}
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                            <label class="required"> {lang('epin_count')} </label>
                                <input class="form-control" type="number" name="pin_count" id="pin_count" placeholder="{lang('epin_count')}">
                            </div>
                        </div>
                        <div class="col-lg-12 col-xs-12">
                            <div class="form-group">
                                <label for="create_epin_expiry_date" class="required">{lang('expiry_date')}</label>
                                <input class="form-control" type="date" name="expiry_date" id="expiry_date" placeholder="Expiry Date">
                                
                            </div>
                        </div>
                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group ">
                                <button class="btn btn-primary" id="epin_purchase_btn" type="submit">{lang('epin_purchase')}</button>
                            </div>
                        </div>
                    </div>
                    {form_close()}
                </div>
            </div>
        </div>
    </div>
    <!-- modal-content -->
</div>
<!-- modal-dialog -->
<!-- modal Epin Transfer