<div class="modal right fade" id="fund_transfer_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-ewallet-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="modal-ewallet-area">
                    <h3>{lang('fund_transfer')}</h3>
                </div>
                {form_open('','id="fund_transfer_form"')}
                    <div class="popup-input">
                        <div class="row">
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group"> 
                                    <label class="required"> {lang('user_name')} </label>
                                    <input class="form-control user_autolist autocomplete-off" type="text" id="user_name" name="user_name" autocomplete="Off" placeholder="{lang('user_name')}"/> 
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12 no-display">
                                <div class="form-group">
                                    <label> {lang('ewallet_balance')} </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">{$left_symbol} </span>
                                        <input type="text" disabled class="form-control" id="user_balance" placeholder="{lang('ewallet_balance')}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="required"> {lang('transfer_to')} </label>
                                    <input class="form-control user_autolist autocomplete-off" type="text" id="to_user_name" name="to_user_name" autocomplete="Off"/ placeholder="{lang('transfer_to')}"> 
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label> {lang('amount')} </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">{$left_symbol} </span>
                                        <input type="text" class="form-control" id="amount" name="amount" placeholder="{lang('amount')}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="required">{lang('transaction_note')}</label>
                                    <textarea class="form-control" name="transaction_note" style="height: 120px;"id="transaction_note" cols="30" rows="10" placeholder="{lang('transaction_note')}"></textarea>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label> {lang('transaction_fee')} </label>
                                    <div class="input-group">
                                        <span class="input-group-addon">{$left_symbol} </span>
                                        <input type="text" disabled class="form-control" id="transaction_fee" placeholder="{lang('transaction_fee')}">
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <label class="required">{lang('transaction_password')}</label>
                                    <input type="password" id="pswd" class="form-control" name="pswd" placeholder="{lang('transaction_password')}"/> 
                                </div>
                            </div>
                            <div class="col-sm-12 col-xs-12">
                                <div class="form-group">
                                    <button type="submit" id="transfer" name="transfer" class="btn btn-primary">{lang('submit')}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                {form_close()}
            </div>
        </div>
    </div>
</div>