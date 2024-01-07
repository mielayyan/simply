{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

    <div id="span_js_messages" style="display:none;">
        <span id="confirm_msg">{lang('sure_you_want_to_delete_this_feedback_there_is_no_undo')}</span>
        <span id="row_msg">{lang('rows')}</span>
        <span id="show_msg">{lang('shows')}</span>
        <span id="digit_msg">{lang('digits_only')}</span>
    </div> 
    
    <div class="row">
        {assign var=i value="0"}
        {assign var=class value=""}  
        {if count($feedback)!=0}
            {assign var="path" value="{$BASE_URL}admin/"}
            {foreach from=$feedback item=v}
                {assign var="feedback_id" value="{$v.feedback_id}"}
                    <!-- <div class="col-sm-6">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                                <div class="clearfix">
                                  <div class="pull-left thumb-md avatar b-3x m-r"> <img src="{$SITE_URL}/uploads/images/document/Feedback.png"> </div>
                                  <div class="clear">
                                    <div class="h3 m-t-xs m-b-xs"> {$v.feedback_name}
                                        {form_open('admin/feedback/delete_feedback', 'class="inline-form-button pull-right" method="post" onsubmit="return confirmAction(\'confirm_msg\')"')}
                                        <input type="hidden" name="id" value="{$feedback_id}">
                                        <button class="close" title="{lang('delete')}"><span aria-hidden="true">×</span><span class="sr-only">{lang('delete')}</span></button>
                                        {form_close()}
                                    </div>
                                    <small class="text-muted"><i class="glyphicon glyphicon-time"></i> {lang('time_to_call')} - {$v.feedback_time}</small> </div>
                                </div>
                            </div>
                                
                            <div class="list-group no-radius alt">
                                <div class="list-group-item" >
                                    <p style="overflow-y:auto;">{$v.feedback_remark}</p>
                                </div>
                                <a class="list-group-item" href=""> <span class="pull-right"> <i class="fa fa-building-o"></i> {$v.feedback_company} </span> <i class="glyphicon glyphicon-phone-alt"></i> {$v.feedback_phone} l <i class="fa fa-envelope"></i> {$v.feedback_email}</a> 
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
                <p style="margin:0;text-align: center; word-break : break-all;padding-top : 5%;">{$v.feedback_remark}</p>
            </div>
            <div class="dot-line-sm"></div>
             <div class="col-md-2 v_center text-line-break">
                <div class="action_round" >
                  {form_open('user/feedback/delete_feedback', 'class="inline-form-button pull-right" method="post" onsubmit="return confirmAction(\'confirm_msg\')"')}
                  <input type="hidden" name="id" value="{$feedback_id}">
                    <button class="btn m-b-xs m-t-sm text-danger" title="Delete"><i class="fa fa-trash-o"></i></button>
                 {form_close()}   
                </div>
                    
             </div>
            
            </div>
            {/foreach} 
        {else}
            <div align="center"><h4 align="center"> {lang('no_feedback_found')}</h4></div>
        {/if}
    </div>
    {$ci->pagination->create_links()}

{/block}
