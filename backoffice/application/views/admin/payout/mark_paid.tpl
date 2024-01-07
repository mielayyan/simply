{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
<div class="button_back">
  <a href="{BASE_URL}/admin/payout_release"> 
    <button class="btn m-b-xs btn-sm btn-info btn-addon"><i class="fa fa-backward"></i>{lang('back')}</button>
  </a>
</div>

<div id="span_js_messages" style="display: none;">
    <span id="error_msg">{lang('please_select_at_least_one_checkbox')}</span>
    <span id="errmsg1">{lang('You_must_select_a_date')}</span>
    <span id="errmsg2">{lang('You_must_select_from_date')}</span>
    <span id="errmsg3">{lang('You_must_select_to_date')}</span>
    <span id="errmsg4">{lang('You_must_Select_From_To_Date_Correctly')}</span>
    <span id="row_msg">{lang('rows')}</span>
    <span id="show_msg">{lang('shows')}</span>
    <span id="msg"> {lang('from_date_greater_than_to_date')}</span>
</div>
 
  <div class="m-b pink-gradient">
  <div class="card-body ">
    <div class="media">
      <figure class=" avatar-50 "> <i class="glyphicon glyphicon-book"></i> </figure>
      <div class="media-body">
        <h6 class="my-0">{lang('note_payout_confirm_bank_transfer')}</h6>
      </div>
    </div>
  </div>
</div>

<div class="panel panel-default">
  <div class="panel-body">
    {form_open('', 'role="form" class="" name="date_submit" id="date_submit" method="get" ')}
      <div class="col-sm-2 padding_both">
        <div class="form-group">
            <label class="" for="user_name">{lang('user_name')}</label>
            <input class="form-control user_autolist" type="text" id="user_name" name="user_name" autocomplete="Off" value="{$user_name}">
        </div>
      </div>
      <div class="col-sm-2 padding_both_small">
        <div class="form-group">
          <label class="" for="daterange">{lang('daterange')}</label>
          <select name="daterange" id="daterange" class="form-control">
            <option value="all" {if $daterange=="all"} selected {/if}>{lang('overall')}</option>
            <option value="today" {if $daterange=="today"} selected {/if}>{lang('today')}</option>
            <option value="month" {if $daterange=="month"} selected {/if}>{lang('this_month')}</option>
            <option value="year" {if $daterange=="year"} selected {/if}>{lang('this_year')}</option>
            <option value="custom" {if $daterange=="custom"} selected {/if}>{lang('custom')}</option>
          </select>
        </div>
      </div>
      <div class="col-sm-2 padding_both_small">
        <div class="form-group">
            <label>{lang('from_date')}</label>
            <input autocomplete="off" class="form-control date-picker custom-date" name="from_date" id="from_date" type="text" value="{$from_date}">
        </div>
      </div>
      <div class="col-sm-2 padding_both_small">
          <div class="form-group">
              <label>{lang('to_date')}</label>
              <input autocomplete="off" class="form-control date-picker custom-date" name="to_date" id="to_date" type="text" value="{$to_date}">
          </div>
      </div>
      <div class="col-sm-2 padding_both_small">
          <div class="form-group credit_debit_button">
              <button class="btn btn-primary"  id="submit" type="submit" value="{lang('search')}"> {lang('search')} </button>
              <a class="btn btn-info" href="{$BASE_URL}admin/mark_paid">
                  {lang('reset')} </a>
          </div>
      </div>
    {form_close()}
  </div>
</div>
{form_open('admin/confirm_mark_paid', 'role="form" name="mark_payout" id="mark_payout" method="post"')}
  <div class="panel panel-default">
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <th>
            <div class="checkbox">
              <label class="i-checks">
                <input type="checkbox" name="pay_all" id="pay_all" class="pay_all">
                <i></i>
              </label>
            </div>
          </th>
          <th>{lang('member_name')}</th>
          <th>{lang('amount')}</th>
          <th>{lang('approved_date')}</th>
        </thead>
        
        <tbody>
          {if !empty($payout_details)}
            {foreach from=$payout_details key=key item=item }
              <tr>
                <td>
                  <div class="checkbox">
                    <label class="i-checks">
                        <input type="checkbox" name="payout_paid_id[]" class="payout-checkbox release" value="{$item['paid_id']}">
                        <i></i>
                    </label>
                  </div>
                </td>
                <td>{user_with_name($item.user_name, $item.full_name, true)}</td>
                <td>{format_currency($item.paid_amount)}</td>
                <td>{$item.paid_date|date_format:"d M Y - h:i:s A"}</td>
              </tr>
            {/foreach}
          {else}
            <tr>
              <td colspan="4" align="center"><h4>{lang('no_records_found')} </h4></td>
            </tr>
          {/if}
        </tbody>
      </table>
      
      <div class="panel-footer">
        {if !empty($payout_details)}
          <button type="submit" class="btn btn-sm btn-primary" name="marksw" id="marksw" value="marked">{lang('Confirm')}</button>
          {$ci->pagination->create_links()}
        {/if}
      </div>
    </div>
  </div>
  {form_close()}
{/block}