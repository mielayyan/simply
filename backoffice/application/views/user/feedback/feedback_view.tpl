{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display:none;">
    <span id="error_msg1">{lang('please_enter_your_company_name')}</span>  
    <span id="error_msg2">{lang('you_must_enter_the_user_name')}</span>        
    <span id="error_msg8">{lang('please_type_your_comments')}</span>            
    <span id="row_msg">{lang('rows')}</span>
    <span id="show_msg">{lang('shows')}</span>    
    <span id="error_msg3">{lang('please_type_your_phone_no')}</span>        
    <span id="error_msg4">{lang('please_type_your_time_to_call')}</span>                  
    <span id="error_msg5">{lang('please_type_your_e_mail_id')}</span>
    <span id="error_msg6">{lang('digits_only')}</span>
    <span id="error_msg9">{lang('phone_number_should_be_atleast_5_digits_long')}</span>
    <span id="error_msg10">{lang('phone_number_cannot_be_longer_than_32_digits')}</span>
    <span id="error_msg11">{lang('email_format_is_incorrect')}</span>
    <span id="error_msg12">{lang('company_name_should_be_atleast_3_characters_long')}</span>
    <span id="error_msg13">{lang('company_name_cannot_be_greater_than_32_characters')}</span>
    <span id="error_msg14">{lang('email_cannot_be_greater_than_50_characters')}</span>
    <span id="confirm_msg">{lang('sure_you_want_to_delete_this_feedback_there_is_no_undo')}</span>
    <span id="digit_msg">{lang('digits_only')}</span>
</div> 
<div class="panel panel-default">
    <div class="panel-body">
        {form_open('user/feedback_view','role="form" class="" method="post" name="feedback_form" id="feedback_form" onSubmit="return validate_feedback()"')}
            {include file="layout/error_box.tpl"}
            <div class="form-group">
                <label class="required">{lang('company')}</label>
                <input type="text" class="form-control" name="company" id="company">
                {form_error('company')}
            </div>
            <div class="form-group">
                <label class="required">{lang('phone_no')}</label>
                <input type="text" class="form-control" name="phone_no" id="phone_no">
                {form_error('phone_no')}
            </div>
            <div class="form-group">
                <label class="required">{lang('time_to_call')}</label>
                <input type="text" class="form-control time-picker" name="time_to_call" id="time_to_call">
                {form_error('time_to_call')}
            </div>
            <div class="form-group">
                <label class="required">{lang('email')}</label>
                <input type="text" class="form-control" name="email" id="email">
                {form_error('email')}
            </div>
            <div class="form-group">
                <label class="required">{lang('comments')}</label>
                <textarea class="form-control" name="comments" id="comments"></textarea>
                {form_error('comments')}
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-sm btn-primary" name="feedback_submit" id="feedback_submit" value="{lang('submit')}">{lang('submit')}</button>
            </div>
        </form>
    </div>
</div>

<legend>
    <span class="fieldset-legend">{lang('feedback_details')}</span>
</legend>
<div class="row">
<div class="col-md-12">
        {assign var=i value="0"}
        {assign var=class value=""}  
        {if count($feedback)!=0}
            {assign var="path" value="{$BASE_URL}user/"}
            {foreach from=$feedback item=v}
                {assign var="feedback_id" value="{$v.feedback_id}"}
                    <!-- <div class="col-sm-6">
                        <div class="col-sm-2 padding-zero">
                            <img src="{$SITE_URL}/uploads/images/document/feed_back.png">
                        </div>
                        <div class="col-sm-10 padding-zero">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="clearfix"> -->
                                  <!-- <div class="pull-left thumb-md avatar b-3x m-r"> <img src="{$SITE_URL}/uploads/images/document/Feedback.png"> </div> -->
                                 <!--  <div class="clear">
                                    <div class="h3 m-t-xs m-b-xs"> {$v.feedback_name}
                                        {form_open('user/feedback/delete_feedback', 'class="inline-form-button pull-right" method="post" onsubmit="return confirmAction(\'confirm_msg\')"')}
                                        <input type="hidden" name="id" value="{$feedback_id}">
                                        <button class="close" title="{lang('delete')}"><span aria-hidden="true">×</span><span class="sr-only">{lang('delete')}</span></button>
                                        {form_close()}
                                    </div> -->
                                    <!-- <small class="text-muted"><i class="glyphicon glyphicon-time"></i> {lang('time_to_call')} - {$v.feedback_time}</small> </div>
                                </div>
                            </div>
                             {* height_feedback *}   
                            <div class="list-group no-radius alt">
                                <div class="list-group-item">
                                    <p style="overflow-y:auto;">{$v.feedback_remark}</p>
                                </div>
                                <a class="list-group-item" href=""> <span class="pull-right"> <i class="fa fa-building-o"></i> {$v.feedback_company} </span> <i class="glyphicon glyphicon-phone-alt"></i> {$v.feedback_phone} l <i class="fa fa-envelope"></i> {$v.feedback_email}</a> 
                            </div>
                        </div>
                        </div>
                    </div> -->
            <div class="col-md-12 panel owl-upload">
            <div class="col-md-2" style="text-align: center;">
                <img class="sm-img-50" src="{$SITE_URL}/uploads/images/document/feed_back.png">
            </div>
             <div class="col-md-3 v_center padding-zero">
                <div class="col-md-12 text-center padding-zero">
                <h4 class=" upload_title">{$v.feedback_name}</h4>
                <span class="text-danger">{lang('time_to_call')} -  {$v.feedback_time}</span>
                <h6>
                <i class="fa fa-phone"></i>
                {$v.feedback_phone}</h6>
                <span><i class="fa fa-envelope-o text-center"></i>
                    {$v.feedback_email}
                </span>
                <div>
                <span>
                <i class="fa fa-thumb-tack" ></i>
                    {$v.feedback_company}
                </span>
                </div>
                </div>
            </div>
            <div class="dot-line-sm"></div>
            <div class="col-md-5 v_center">
                <p style="margin:0;text-align: center;">{$v.feedback_remark}</p>
            </div>
            <div class="dot-line-sm"></div>
             <div class="col-md-2 v_center">
                <div class="action_round" >
                  {form_open('user/feedback/delete_feedback', 'class="inline-form-button pull-right" method="post" onsubmit="return confirmAction(\'confirm_msg\')"')}
                  <input type="hidden" name="id" value="{$feedback_id}">
                    <button class="btn m-b-xs m-t-sm text-danger" title="Delete"><i class="fa fa-trash-o"></i></button>
                 {form_close()}   
                </div>
                    
             </div>
            
        </div>

            {/foreach} 
        </div>
        {else}
            <div align="center"><h4 align="center"> {lang('no_feedback_found')}</h4></div>
        {/if}
    </div>
    {$ci->pagination->create_links()}

{/block}