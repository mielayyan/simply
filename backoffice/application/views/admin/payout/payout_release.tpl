{extends file=$BASE_TEMPLATE} {block name=$CONTENT_BLOCK}

    <div id="span_js_messages" style="display: none;">
        <span id="error_msg">{lang('please_select_at_least_one_checkbox')}</span>
        <span id="row_msg">{lang('rows')}</span>
        <span id="show_msg">{lang('shows')}</span>
        <span id="show_msg1">{lang('are_you_sure_you_want_to_Delete_There_is_NO_undo')}</span>
        <span id="show_msg2">{lang('digits_only')}</span>
        <span id="err_msg1">{lang('main_password_required')}</span>
        <span id="err_msg2">{lang('second_password_required')}</span>
        <span id="err_msg3">{lang('wallet_id_required')}</span>
        <span id="err_msg4">{lang('passphrase_required')}</span>
        <span id="err_msg5">{lang('wallet_name_required')}</span>
        <span id="err_msg6">{lang('wallet_password_required')}</span>
        <span id="otp_err1">{lang('you_must_enter_otp')} </span>
        <span id="otp_err2">{lang('otp_is_numeric')} </span>
    </div>
    <style>
        .input_width {
            margin-top: -6px;
        }
    </style>
    <p class="text-right">
        <a href="{$BASE_URL}admin/mark_paid" class="btn btn-sm btn-primary">{lang('process_payment')}</a>
    </p>
    <div class="panel panel-default">
        <div class="panel-body">
            {form_open('', 'name="" class="" id="search_filter" method="get"')}
                <div class="col-sm-2 padding_both">
                    <label class="" for="user_name">{lang('user_name')}</label>
                    <input class="form-control user_autolist" type="text" id="user_name" name="user_name" autocomplete="Off" size="100" value="{$ci->input->get('user_name')}">
                </div>
                <div class="col-sm-2 padding_both_small">
                    <div class="form-group">
                        <label class="control-label">{lang('payout_method')}</label>
                        <select class="form-control" name="payment_method">
                            <option value="bank">{lang('bank')}</option>
                            {if count($gateway_list) >0}
                            {foreach from=$gateway_list item="v"}
                                <option {if $payment_type==$v.gateway_name} selected="selected" {/if} value="{$v.gateway_name}">
                                    {if $v.gateway_name=="Bitcoin"}
                                        {lang('blocktrail')}
                                    {else}
                                        {$v.gateway_name}
                                    {/if}
                                </option>
                            {/foreach}
                            {/if}
                        </select>
                    </div>
                </div>

                <div class="col-sm-2 padding_both_small">
                    <div class="form-group">
                        <label class="control-label">{lang('payout_type')}</label>
                        <select name="payout_type" class="form-control">
                            {if $payout_release == 'both'}
                            <option value="admin" {if $payout_type == "admin"} selected="selected" {/if}>{lang('manual')}</option>
                            <option value="user" {if $payout_type == "user"} selected="selected" {/if}>{lang('user_request')}</option>
                            {else if $payout_release == 'from_ewallet'}
                            <option value="admin" {if $payout_type == "admin"} selected="selected" {/if}>{lang('manual')}</option>                         
                            {else if $payout_release == 'ewallet_request'}
                            <option value="user" {if $payout_type == "user"} selected="selected" {/if}>{lang('user_request')}</option>
                            {else}
                            {/if}
                        </select>
                    </div>
                </div>

                <div class="col-sm-2 padding_both_small">
                    <div class="form-group mark_paid">
                        <button class="btn btn-sm btn-primary" type="submit">
                            {lang('search')}
                        </button>
                        <a class="btn btn-info btn-sm" href="{base_url('admin/payout_release')}">
                            {lang('reset')} 
                        </a>
                    </div>
                </div>
            {form_close()}
        </div>
    </div>
    
    <div class="panel panel-default">
        {form_open('admin/payout/release_or_delete_payout_requests', 'name="" class="" id="ewallet_form_det" method="post"')}
            <div class="table-responsive">
                <table st-table="rowCollectionBasic" class="table table-striped m-b-none">
                    <thead>
                        <tr class="th">
                            <th>
                                <div class="checkbox">
                                    <label class="i-checks">
                                        <input type="checkbox" name="release_all" id="release_all" class="release_requests_all">
                                        <i></i>
                                    </label>
                                </div>
                            </th>
                            <th>{lang('member_name')}</th>
                            <th>{lang('payout_amount')}</th>
                            <th>{lang('payout_method')}</th>
                            <th>{lang('payout_type')}</th>
                            {if $payout_type == "admin"}
                                <th>{lang('ewallet_balance')}</th>
                            {elseif $payout_type == "user"}
                                <th>{lang('requested_date')}</th>
                            {/if}
                        </tr>
                    </thead>
                    {if !empty($payout_requests)}
                        <tbody>
                            {foreach from=$payout_requests key=key item=item}
                                <tr>
                                    <td>
                                        <div class="checkbox">
                                            <label class="i-checks">
                                                <input type="checkbox" name="payout_request_id[{$item.req_id}]" class="payout-checkbox release" value="{$item.req_id}" >
                                                <i></i>
                                            </label>
                                        </div>
                                    </td>
                                    <td>{user_with_name($item.user_name, $item.full_name, true)}</td>
                                    <td>
                                        {if $payout_type == "admin"}
                                        <div class="input_width">
                                            <div class="input-group"> {if $DEFAULT_SYMBOL_LEFT}<span class="input-group-addon">{$DEFAULT_SYMBOL_LEFT}</span>{/if}
                                                <input type="text" class="payout_amount form-control" name="payout_amount[{$item.req_id}]" id="payout_amount{$key}" value="{round($item.payout_amount*$DEFAULT_CURRENCY_VALUE,$PRECISION)}" />
                                            </div>
                                        </div>
                                        <span id="errmsg1"></span>
                                        {/if}
                                        {if $payout_type == "user"}
                                            {format_currency($item.payout_amount)}
                                        {/if}

                                    </td>
                                    <td>{$item.payout_type|ucfirst}</td>
                                    <td>
                                        {if $payout_type == "admin"}
                                            {lang('manual')}
                                        {elseif $payout_type == "user"}
                                            {lang('user_request')}

                                        {/if}
                                    </td>
                                    <td>
                                        {if $payout_type == "admin"}
                                            {$DEFAULT_SYMBOL_LEFT}{number_format($item.balance_amount*$DEFAULT_CURRENCY_VALUE,$PRECISION, '.', '')}{$DEFAULT_SYMBOL_RIGHT}
                                        {elseif $payout_type == "user"}
                                            {$item.requested_date|date_format:"d M Y - h:i:s A"}
                                        {/if}
                                    </td>
                                </tr>
                            {/foreach}
                        </tbody>
                    {else}
                        <tbody>
                            <tr>
                                <td colspan="7">
                                    <h4 class="text-center">{lang('No_Details_Found')}</h4>
                                </td>
                            </tr>
                        </tbody>
                    {/if}
                </table>
            </div>
            {if !empty($payout_requests)}
                <div class="panel-footer">
                    {if !in_array($payment_type, ['Blockchain', 'Bitgo'])}
                        <button class="btn btn-sm btn-primary" name="action" id="release_payout" type="sybmit" value="release_payout"> 
                            {lang('release')} 
                        </button>
                        {if $payout_type == "user"}
                            <button type="submit" class="btn btn-sm btn-danger" name="action" type="button" value="delete_payout" id="delete_payout"> 
                                {lang('delete')} 
                            </button>
                        {/if}
                    {/if}
                    {$ci->pagination->create_links()}
                    {if $payment_type=="Blockchain"}
                        <div class="m-t-xxl">
                            <legend>
                                <span class="fieldset-legend">{lang('account_details')}</span>
                            </legend>
                            <div class="form-group">
                                <div class="">
                                    <label class="control-label required" for="main_password">{lang('main_password')}</label>
                                    <input class="form-control" type="password" name="main_password" id="main_password" value="" title="">
                                    <span id="errmsg3"></span> {form_error('main_password')}
                                </div>
                            </div>
                            <div class=" form-group">
                                <div class="">
                                    <label class="control-label required" for="second_password">{lang('second_password')}</label>
                                    <input class="form-control" type="password" name="second_password" id="second_password" value="" title="">
                                    <span id="errmsg3"></span> {form_error('second_password')}
                                </div>
                            </div>
                        </div>
                    {/if}
                    {if $payment_type=="Bitgo"}
                        <div class="m-t-xxl">
                           <legend><span class="fieldset-legend">{lang('account_details')}</span></legend>
                            <div class="form-group">
                                <div class="">
                                    <label class=" control-label required" for="wallet_id">{lang('wallet_id')}</label>
                                    <input class="form-control" type="text" name="wallet_id" id="wallet_id" value="" title="">
                                    <span id="errmsg3"></span> {form_error('wallet_id')}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="">
                                    <label class="control-label required" for="passphrase">{lang('passphrase')}</label>
                                    <input class="form-control" type="password" name="passphrase" id="passphrase" value="" title="">
                                    <span id="errmsg3"></span> {form_error('passphrase')}
                                </div>
                            </div>
                        </div>
                    {/if}
                    {if in_array($payment_type, ['Blockchain', 'Bitgo'])}
                        <button class="btn btn-sm btn-primary" name="action" id="release_payout" type="sybmit" value="release_payout"> 
                            {lang('release')} 
                        </button>
                        {if $payout_type == "user"}
                            <button type="submit" class="btn btn-sm btn-danger" name="action" type="button" value="delete_payout" id="delete_payout"> 
                                {lang('delete')} 
                            </button>
                        {/if}
                    {/if}
                </div>
            {/if}
        </div>
        <input type="hidden" name="payout_type" value="{$payout_type}"> 
        <input type="hidden" id="payment_method" name="payment_method" value="{$payment_type}">
        {form_close()}
    

{/block}