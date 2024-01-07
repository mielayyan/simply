{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<style type="text/css">
    .form-group-left
    {
       float: left;
       padding: 0px 2px !important; 
    }
    .row-flex
    {
        display: flex;
        flex-wrap: wrap;
    }
    @media (max-width: 769px)
    {
        .form-group-left
        {
            float: unset;
        }
        #searchkyc
        {
            width: 100%;
        }
    }
</style>
{if str_contains($coming_from, 'profile_view')}
    <div class="back-btn" style="padding-right: 10px; text-align: right;">
        <a href="{BASE_URL}/admin/profile_view?user_name={$ci->input->get('user')}" class="btn m-b-xs btn-sm btn-info btn-addon" style="height: 32px"><i class="fa fa-backward"></i> {lang('back')}</a>
    </div>
{/if}
    <div id="span_js_messages" style="display:none;">
        <span id="u_err">{lang('must_select_username')}</span>
        <span id="type_err">{lang('must_select_type')}</span>
        <span id="status_err">{lang('something_wrong')}</span>
    </div>
    <div class="modal" style="margin-top: 100px" id="reason_modal" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" >
                        &times;
                    </button>
                    <h4 class="modal-title">{lang('kyc_status')}</h4>
                </div>
                <div class="modal-body">
                    <table cellpadding="0" cellspacing="0" align="center">
                        <tr>
                            <td>
                                <textarea name="reason" id='reason'></textarea>
                            </td>
                        </tr>
                        <tr></tr>
                        <tr></tr>
                    </table>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    <div class="hidden" id="Kyc_alert"></div>
    <div class="panel panel-default">
        <div class="row-flex panel-body">
            {form_open('','role="form" class="" method="get"  name="searchkyc" id="searchkyc"')}
            <div class="form-group-left padding_both ">
                <div class="form-group text-center-width">
                    <label>{lang('select_category')}</label>
                    <select class="form-control" name="type" id='type'>
                        <option value=''>{lang('any')}</option>
                        {foreach from=$kyc_catg item=v}
                            <option value="{$v.id}" {set_select('type', {$v.id}, false)}>{$v.category}</option>
                        {/foreach}
                    </select>
                </div>
            </div>
            <div class="form-group-left padding_both_small">
                <div class="form-group text-center-width">
                    <label>{lang('status')}</label>
                    <select class="form-control" name="status" id='status'>
                        <option value='any'>{lang('any')}</option>
                        <option value='Pending' {set_select('status', 'Pending', ($status == 'Pending'))}>{lang('Pending')}</option>
                        <option value="Rejected" {set_select('status', 'Rejected', ($status == 'Rejected'))}>{lang('Rejected')}</option>
                        <option value="Approved" {set_select('status', 'Approved', ($status == 'Approved'))}>{lang('Approved')}</option>
                    </select>
                </div>
            </div>
            <div class="form-group-left padding_both_small">
                <div class="form-group">
                    <label>{lang('select_user_name')}</label>
                    <input name="user" class="form-control user_autolist" id="user_name" type="text" autocomplete="off" value="{$uname}">
                </div>
            </div>
            <div class="form-group-left padding_both_small">
                <div class="form-group mark_paid">
                    <button class="btn btn-sm btn-primary" type="submit" id="view_kyc" value="{lang('view')}"> {lang('search')}</button>
                    <a class="btn btn-sm btn-info" href="{$BASE_URL}admin/kyc">
                    {lang('reset')} </a>
                </div>
            </div>
            {form_close()}
            <div class="col-sm-12 padding_both_small">
                <p> <u><a href="{$BASE_URL}admin/kyc_settings" class="text-info">{lang('manage_kyc_configuration')}?</a> </u></p>
            </div>
        </div>
    </div>
    {if $show_table eq 'yes' || count($kyc_list) > 0}
        <div class="panel panel-default">
        {* <div classs="panel-body"> *}
            {form_open('','role="form" class=""   name="from_to_form" id="from_to_form" method="post" target="_blank"')}
            {if count($kyc_list) > 0}
            <div class="table-responsive">
                <table st-table="rowCollectionBasic" class="table table-striped">
                    <thead>
                        <tr class="th">
                            <th>{lang('member_name')}</th>
                            <th>{lang('category')}</th>
                            <th>{lang('status')}</th>
                            <th>{lang('view')}</th>
                            <th>{lang('action')}</th>
                        </tr>
                    </thead>
                    <tbody>
                        {assign var="i" value=1}
                        {foreach from = $kyc_list key = k item = v}
                            <tr id="row_id{$i}">
                                <td>
                                    <div class="table-avatar-details">
                                    <div>
                                        <img class="thumb-table" title="{if $v.active =='yes'} {lang('active')} {else} {lang('blocked')} {/if}" src="{profile_image_path($v.user_photo)}"/>
                                        {if $v.active =='yes'}
                                        <i class="on b-white bottom" style="background-color: #27c24c;"></i> 
                                        {else}
                                        <i class="on b-white bottom" style="background-color: red;"></i> 
                                        {/if}
                                    </div>
                                    <div>
                                        <div class="table-av-name">{$v.full_name}</div>
                                        <a class="table-av-package" href="{$BASE_URL}admin/profile/profile_view?user_name={$v.user_name}">{$v.user_name}</a>
                                    </div>                                    
                                </div>
                                </td>
                                <td>{$v['category']}</td>
                                <td><span class="label label-{$v['font_class']}"> {lang($v['status'])}</span>
                                </td>
                                <td>
                                    {foreach from=$v['file_name'] item=f}
                                        {if $f|pathinfo:$smarty.const.PATHINFO_EXTENSION == 'pdf'}
                                            <a href="{$SITE_URL}/uploads/images/document/kyc/{$f}" class="btn btn-info" data-placement="top" data-original-title="" title="{lang('download')}" target="_blank">
                                                <i class="fa fa-download" data-toggle="tooltip" title="Download"></i></a>
                                            {else}
                                            <a class="btn btn-primary thumbs" href="javascript:mym('{$SITE_URL}/uploads/images/document/kyc/{$f}')"> <img id="borderimg1" width="30" height="20" src="{$SITE_URL}/uploads/images/document/kyc/{$f}" scrolling="no"></a>
                                            {/if}
                                    {/foreach}

                                </td>
                                <td>
                                    {if $v['status'] eq 'pending'}
                                        <input style="color: #00CC00" type="button" id="verify_button{$i}" name='verify_button{$i}' onclick="verify('{$v["user_name"]}',{$v['type']}, this, 'row_id{$i}')" value="Approve" class="btn btn-primary"/>
                                        <input type="button" style="color: #CC0000" id="reject_button{$i}" name='reject_button{$i}' onclick="reject('{$v["user_name"]}',{$v['type']}, this, 'row_id{$i}')" value="Reject" class="btn btn-primary"/>
                                    {else if $v['status'] eq 'rejected'}
                                        <button class="btn-link text-info" type="button" style="margin-top: 0px !important;" id="reject_button{$i}" name='reject_button{$i}' onclick="veiwreason('{$v['reason']|replace:'\\':'\\\\'}')" value="Reason" title="{lang('view_reason')}"> <i class="fa fa-eye"></i></button>
                                        {else}
                                            {lang('na')}
                                        {/if}
                                </td>
                            </tr>
                            {$i = $i + 1}
                        {/foreach}
                    </tbody>
                </table>
                </div>
                {* </div> *}
            {else}
                <h4 class="text-center">{lang('no_records_found')}</h4>
            {/if}
            {form_close()}
        </div>
        <div class="m-b pink-gradient">
            <div class="card-body ">
                <div class="media">
                    <figure class="avatar-50"><i class="glyphicon glyphicon-book"></i></figure>
                    <div class="media-body">
                        <h6 class="my-0">{lang('you_can_manage_kyc_category_from_above_link')}</h6>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="EnSureModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                                        <div class="modal-header"  style="height: 46px;">
                        <button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body" style="text-align:center;width:100%">
                        <img id="im" src="">
                    </div>

                </div>
            </div>
        </div>
        <div class="modal fade" id="EnSureModal" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="bootbox-close-button close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h4 class="modal-title">{lang('enter_reason')}</h4>
                    </div>
                    <div class="modal-body" style="text-align:center;width:100%">

                        <form class="bootbox-form">
                            <input class="bootbox-input bootbox-input-text form-control" autocomplete="off" type="text">
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button data-bb-handler="confirm" type="button" class="btn btn-primary">OK</button>
                    </div>
                </div>
            </div>
        </div>
    {/if}
{/block}
{block name="script"}
    <script>
        // let timerId = setInterval(function(){
        //     $(".alert").fadeTo(2000, 500).slideUp(500, function(){
        //         $(".alert").slideUp(500);
        //         $('#Kyc_alert').addClass('hidden');
        //         window.top.location = window.top.location;
        //         // clearInterval(timerId);
        //     });
        //  },5000);
        jQuery(document).ready(function () {
            
        {*        Main.init();*}
            //DatePicker.init();
        {*        ValidateKYC.init();*}
        });

        function mym(image) {
            document.getElementById("im").src = image;
            $("#EnSureModal").modal();
        }

        function verify(uname, type, button, rowid) {
            $.post('{$BASE_URL}admin/profile/ajaxVerify', {literal}{
                user_name: uname,
                type: type
            },{/literal}
                    function (data) { //alert(data);
                        hidealert();
                        $('#Kyc_alert').removeClass('hidden');
                        if (data === 'no') { //if username not avaiable
                            $('#Kyc_alert').html(`
                                <div class="alert alert-danger alert-dismissible">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                  <strong>Failed!</strong> {lang('something_wrong')}
                                </div>
                                `);
                        } else {
                            $('#Kyc_alert').html(`
                                <div class="alert alert-success alert-dismissible">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                  <strong>Success!</strong> KYC {lang('approved')}
                                </div>
                                `);
                            $('#' + rowid).remove();
                            button.disabled = true;
                            button.value = '{lang('approved')}';
                        }
                    });
        }

        function reject(uname, type, button, rowid) {
            bootbox.prompt('{lang('enter_reason')}', function (reason) {
                if (reason != "" && reason != null) {
                    $("#reason_filed").html('');
                    $.post('{$BASE_URL}admin/profile/ajaxReject', {literal}{
                        user_name: uname,
                        type: type,
                        reason: reason
                    },{/literal}
                            function (data) { //alert(data);
                                $('#Kyc_alert').removeClass('hidden');
                                hidealert();
                                if (data === 'no') { //if username not avaiable
                                    // alert('');
                                    $('#Kyc_alert').html(`
                                        <div class="alert alert-danger alert-dismissible">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                          <strong>Failed!</strong> {lang('something_wrong')}
                                        </div>
                                        `);
                                } else {
                                    $('#Kyc_alert').html(`
                                        <div class="alert alert-success alert-dismissible">
                                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                          <strong>Success!</strong> KYC {lang('rejected')}
                                        </div>
                                        `);
                                    $('#' + rowid).remove();
                                    button.disabled = true;
                                    button.value = '{lang('rejected')}';
                                }
                            });
                } else {
                    if ($('#reason_filed').length == 0) {
                        $(".bootbox-input").after('<span id="reason_filed" class="text-danger">{lang('required')}</span>');
                    } else {
                        $("#reason_filed").html('{lang('required')}');
                    }
                    return false;
                }
            });
        }

        function veiwreason(msg) {
            bootbox.alert("{lang('reason')} : " + "<h4>"+msg+"</h4>");
        }

        $(".btn-dis").click(function () {
            $(this).button('loading');
        });

        //hide alert message 
        function hidealert(){
            setTimeout(function(){
            $(".alert").fadeTo(2000, 500).slideUp(500, function(){
                $(".alert").slideUp(500);
                $('#Kyc_alert').addClass('hidden');
                window.top.location = window.top.location
            });
         },5000);
        }
    </script>
    <style>
    .modal-footer {
    padding-bottom: 10px;
    padding-top: 10px;
    padding-right: 10px;
     }
    .modal-footer .btn-default {
     display:none;
     margin-left: 10px;
     }
</style>
{/block}
