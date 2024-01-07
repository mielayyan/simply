<div class="modal right fade" id="fund_credit_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-ewallet-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="modal-ewallet-area">
                    <h3>{lang('fund_credit')}</h3>
                </div>
                {form_open('','id="fund_credit_form"')}
                    <div class="popup-input">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="required">{lang('user_name')}</label>
                                    <input class="form-control user_autolist" type="text" id="user_name" name="user_name" autocomplete="Off" placeholder="{lang('user_name')}"/>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="required">{lang('amount')}</label>
                                    <div class="input-group {$input_group_hide}">
                                        {$left_symbol} {$right_symbol}
                                        <input type="text" class="form-control" id="amount" name="amount" placeholder="{lang('amount')}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="required">{lang('transaction_note')}</label>
                                    <textarea class="form-control" name="tran_concept" style="height: 120px;"rows="10" id="tran_concept" placeholder="{lang('transaction_note')}"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <button type="submit" id="credit" name="credit" class="btn btn-primary">{lang('submit')}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                {form_close()}
            </div>
        </div>
    </div>
</div>