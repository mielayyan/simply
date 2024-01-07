<div class='panel panel-default'>
    <div class='panel-body'>
        <div class='button_back'>
            <a onClick='print_report(); return false;'>
                <button class='btn m-b-xs btn-sm btn-primary btn-addon'><i class='icon-printer'></i><?= lang('Print') ?></button>
            </a>
        </div>
        
        <div class='panel panel-default table-responsive'  id='print_area'>
            <table border='0' width='700' height='100' align='center'>
                <tbody>
                    <?php foreach($order_details as $v): ?>
                        <tr>
                            <td colspan='2'>
                                <h3>
                                    <b> <?= lang('invoice_no') ?>:</b><?= $v['invoice_no'] ?></td>

                                    </b>
                                </h3>
                            </td>
                        </tr>
                        <tr>
                            <td colspan='2'><hr></td>
                        </tr>
                        <tr>
                            <td colspan='2'><b><?= lang('date_added') ?>:  </b> <?= $v['date_submission'] ?></td>
                        </tr>

                        <tr>
                            <td colspan='2'><b><?= lang('payment_method') ?>:  </b><?= lang($v['payment_method']) ?></td>
                        </tr>
     
                        <tr>
                            <td colspan='2'><h2><b><?= lang('order_products') ?></b></h2>
                                <table class='table table table-bordered table-striped table-hover'>
                                    <thead>
                                        <tr>
                                            <td><b><?= lang('product') ?></b></td>
                                            
                                            <td><b> <?= lang('price') ?></b></td>
                                            
                                        </tr>
                                    </thead>
                                    <tbody>
                                            <tr>                                      
                                                <td><?= $v['product_name'] ?></td>                           
                                               <td> <?= format_currency($v['amount']) ?></td>
                                            </tr>
                                        <hr>
                              
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br> 
        </div>
    </div>
</div>