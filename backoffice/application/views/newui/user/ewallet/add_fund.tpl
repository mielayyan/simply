<div class="modal right fade" id="add_fund_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel2">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header modal-ewallet-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <div class="col-sm-12 col-xs-12">
                
                <div class="modal-ewallet-area">
                    <h3>{lang('add_purchase_wallet_fund')}</h3>
                </div>
                </div>
                {form_open('user/add_purchase_wallet_amount','role="form" id="purchase_wallet" name="purchase_wallet" method="post"')}
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        <label class="required">{lang('amount')}</label>
                        <input type="number" class="form-control" name="amount" id="amount" maxlength="10" value="" required="required">
                        {form_error('amount')}
                    </div>
                </div>
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group credit_debit_button" style="margin: 0">
                        <button class="btn btn-sm btn-primary btn-addon" type="submit" id="add_fund" value="add_fund" name="add_fund">
                            <i class="fa fa-paypal"></i>
                            {lang('pay_with_paypal')}
                        </button>
                    </div>
                </div>
                {form_close()}
            </div>
        </div>
    </div>
</div>