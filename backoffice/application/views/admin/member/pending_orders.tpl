{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<div id="span_js_messages" style="display: none;">
    <span id="msg1">{lang('pending_repurchase_order_confirm')}</span>
    <span id="error_msg">{lang('please_select_at_least_one_checkbox')}</span>
</div>
     {include file="layout/search_member_get.tpl" search_url="admin/pending_orders"}

    <div class="panel panel-default">
    {* <div class="panel-body"> *}
        {form_open('admin/approve_order', 'name="pending_order" class="" id="pending_order" method="post"')}


    {if count($pending_order_list)}
    <div class="table-responsive">
        <table st-table="rowCollectionBasic" class="table table-striped">
            <thead>
                <tr class="th">

                    <th>{* {lang('check')}/<a class="cursor" type="submit" name="check_all" value="Check All" id="check_all" >{lang('check_all')}</a> *}
                      <div class="checkbox">
                            <label class="i-checks">
                                <input type="checkbox" name="check_all" class="select-checkbox-all" id="check_all" value=""/><i> </i>
                            </label>
                        </div>

                    </th>
                    <th>{lang('slno')}</th>
                    <th>{lang('invoice_no')}</th>
                    <th>{lang('member_name')}</th>
                    {* <th>{lang('full_name')}</th> *}
                    <th>{lang('total_amount')}</th>
                    <th>{lang("payment_method")}</th>
                   
                    <th>{lang('order_date')}</th>
                    {* <th>{lang('view_reciept')}</th> *}
                    <th>{lang('action')}</th>
                </tr>
            </thead>
            <tbody>
                {assign var="i" value=1}
            {assign var="total_quantity" value=0}
                {foreach from = $pending_order_list key = k item = v}
                    <tr>

                         <td>
                        <div class="checkbox">
                        <label class="i-checks">
                            <input type="checkbox" name="approval[]" id="approval{$i}" class="approval" value="{$v['encrypt_order_id']}"/><i> </i>
                        </label>
                        </div>
                       </td>
                        <td>{$page_id + $i}</td>

                         <td>
                            <!--<a href="../repurchase/repurchase_invoice/{$v['encrypt_order_id']}" target="_blank" class="btn-link text-primary" title="">
                                {* <i class="fa fa-eye"></i> *}{$v['invoice_no']}
                            </a>-->
                            <a href='#' onclick='getInvoice("{$v.encrypt_order_id}")'>{$v.invoice_no}</a>    
                         </td>
                        {* <td>{$v['invoice_no']}</td> *}
                        {* <td>{$v['full_name']}&nbsp;({$v.user_name}) *}
                        <td>{user_with_name($v.user_name, $v['full_name'], true)}</td>
                        {* <td>{$v['full_name']}</td> *}
                        <td style="text-align: center;">{format_currency($v.amount)}</td>
                        <td>{lang($v['payment_method'])}</td>
                        <td>{$v['order_date']|date_format:"d M Y - h:i:s A"}</td>
                
                
                        <td><a title="{lang('view_reciept')}" class="btn btn-light-grey btn-xs text-black" href="javascript:mym('{SITE_URL}/uploads/images/reciepts/{$v['reciept']}')"><i class="fa fa-eye"></i></a></td>
                               
            
                    </tr>
                    {$i = $i + 1}
                {/foreach}
            </tbody>
        </table>
        </div>
        <div class="panel-footer">
            <button class="btn btn-sm btn-primary approve" name="confirm_order" id="confirm_registr" type="submit" value="confirm_order"> {lang('approve')} </button>
            {$ci->pagination->create_links()}
        </div>
    {else}
        <h4 class="text-center">{lang('no_data')}</h4>
    {/if}                                
    {form_close()}
    
    <div class="modal fade" id="EnSureModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">

                </div>
                <div class="modal-body" style="text-align:center;width:100%">
                    <img id="im" src="">
                    <!--                <p style="text-align:left;"id="des"></p>-->
                </div>
                <div class="form-group m-l">
                 
                    <button type="button" class="btn btn-primary" data-dismiss="modal">{lang('close')}</button>
                 
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="myModal" role="dialog">
    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">{lang('Invoice')}</h4>
        </div>
        <div class="modal-body invoice_shopping">

        
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        </div>
      </div>
      
    </div>
  </div>

</div>

   
{/block}
{block name = "style"}
<style>
.modal-dialog {
    width: 786px !important;
}
</style>
{/block}

 {block name=script}
  {$smarty.block.parent}
    <script src="{$PUBLIC_URL}/javascript/pending_repurchase.js"></script>
    <script>

    function getInvoice(invoice_id){
         
         var url = '{$BASE_URL}' + "repurchase/getRepurchaseInvoiceDetails";

         $.ajax({
                'url': url,
                'type': "POST",
                'data': {
                  invoice_id : invoice_id
                },
                'dataType': 'text',
                'async': false,
                success: function(data) {

                    //alert(data);
                    $('.invoice_shopping').empty();
                    $('.invoice_shopping').append(data);
                    // Display Modal
                    $('#myModal').modal('show');
                    
                },
                error: function(error) {

                  console.log(error);
                }
         });

    }

    function print_invoice_report() {
    var myPrintContent = document.getElementById('print_invoice_area');
    var myPrintWindow = window.open("", "Print Report", 'left=300,top=100,width=700,height=500', '_blank');
    myPrintWindow.document.write(myPrintContent.innerHTML);
    myPrintWindow.document.close();
    myPrintWindow.focus();
    myPrintWindow.print();
    myPrintWindow.close();
    return false;
    }     
   

 </script>
 {/block}