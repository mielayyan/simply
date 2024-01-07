{extends file='newui/layout/user.tpl'}
{block name=$CONTENT_BLOCK}
<div class="main-content-new-dashboard">
  <div class="breadcrumb-header-new-dashboard justify-content-between">
    <div>
        <h4>{lang('repurchase_report')}</h4>
    </div>
  </div>
</div>
<style type="text/css">
  div.dt-buttons
  {
    position: unset;
  }
  .main-content-new-dashboard {
    height: auto;
  }
</style>
<div class="panel panel-default ng-scope">
  <div class="panel-body">
    <div class="filter-new row m-l-none">
      <div class="col-md-8">
        <form action="#"  id="repurchase_report_filter_form">
           <div class="row">
                <div class="padding_both_small">
                    <div class="form-group">
                      <div id="repurchase_daterangepicker" class="date-range-picker">
                        <i class="fa fa-calendar"></i>&nbsp;
                        <span></span> <i class="fa fa-caret-down"></i>
                      </div>
                    </div>
                </div>
                <div class="padding_both" style="padding-left: 5px;">
                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit" id="search_member_get" value="search_member_get">
                        {lang('search')}
                        </button>
                        <button class="btn btn-sm btn-info search_clear" type="button">
                        {lang('reset')}
                        </button>
                    </div>
                </div>
           </div>
        </form>
      </div>
      <div class="col-md-4" id="user_purchase_report_btn"></div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <div class="table-wallet table-payout-summary table-payout-summary-pending new-dashborad-summary ">
            <div class="table-responsive">
              <table id="purchase_report_table" class="">
                <thead>
                  <tr>
                    <th>{lang('invoice_no')}</th>
                    <th>{lang('total_amount')}</th>
                    <th>{lang('payment_method')}</th>
                    <th>{lang('purchase_date')}</th>
                  </tr>
                </thead>
                <tbody>
                </tbody>
              </table>
            </div>
        </div>
      </div>
    </div>
</div>
</div>

<div class="no-display" id="template_amount">
  <span class="badge bg-amount">[amount]</span>
</div>

<div id="print_title" class="hidden">
  {include file="user/report/header.tpl" name=""}
</div>
{/block}

{block name="style"}
    {$smarty.block.parent}
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/datepicker.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.css">
    <link href="{$PUBLIC_URL}theme/newui/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="{$PUBLIC_URL}javascript/toastr/jquery.toast.min.css">
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable_with_btn.min.css">
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/datatable_with_btn.min.css">
    <link rel="stylesheet" type="text/css" href="{$PUBLIC_URL}theme/newui/css/repurchase_report.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/ewallet.css">
{/block}

{block name=script}
    {$smarty.block.parent}
    <script type="text/javascript" src="{$PUBLIC_URL}javascript/toastr/jquery.toast.min.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/select2.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/moment.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/daterangepicker.min.js"></script>
    <script src="{$PUBLIC_URL}theme/libs/jquery/autocomplete/jquery.autocomplete.js"></script>
    <script src="{$PUBLIC_URL}theme/newui/js/toastr.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/datatables_with_btn.min.js"></script>
    <script type="text/javascript" src="{$PUBLIC_URL}theme/newui/js/repurchase_report.js"></script>
{/block}