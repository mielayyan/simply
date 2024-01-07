{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
    <style>
        p > img {
            display: block;
            width: 28%;
        }
        .p-15
        {
            padding-top: 15px;
        }
    </style>
<div id="span_js_messages" style="display:none;">
    <span id="error_msg">{lang('you_must_enter_news_title')}</span>
    <span id="error_msg1">{lang('you_must_enter_news')}</span>
    <span id="confirm_msg1">{lang('sure_you_want_to_edit_this_news_there_is_no_undo')}</span>
    <span id="confirm_msg2">{lang('sure_you_want_to_delete_this_news_there_is_no_undo')}</span>
</div>

<div class="m-b">
    {include file="common/notes.tpl" notes=lang('note_news')}
  </div>
   
    <div class="form-group text-right">
        {form_open('admin/add_new_news','role="form" class=""')}
            <button class="btn btn-sm btn-primary btn-addon" id="add_new_news" name="add_new_news" type="submit" value="{lang('add_news')}"> <i class="fa fa-plus"></i>{lang('add_news')} </button>
        {form_close()}
    </div>

        {if $arr_count!=0}
        {assign var="path" value="{$BASE_URL}admin/"}
        {assign var="i" value=0}
        {foreach from=$news_details item=v}
            {assign var="news_id" value="{$v.news_id}"}
            {assign var="date" value="{$v.news_date|date_format:"%D"}"}
            {assign var="time" value="{$v.news_date|date_format:"%r"}"}
        <!---NEW NEWS--->
        <div class="col-lg-4 col-md-4 col-sm-6">
          
        <div class="panel b-b wrapper-lg">
          <a href="{$BASE_URL}user/view_news/{$v.news_id}" class="pull-left thumb thumb-wrapper m-r">
            <img src="{image_path('news',$v.news_image,'default.jpg')}">
          </a>
          <div class="clear">                        
            <a href="{$BASE_URL}user/view_news/{$v.news_id}" class="font-semibold text-ellipsis news_title">{$v.news_title}</a>
            <div class="text-xs block m-t-xs">
              <i class="fa fa-calendar"></i> {$date}
               
            </div>
          </div>
          
          <div style="clear: both;"></div>
          <p class="p-content" title="{$v.news_desc}">{$v.news_desc|truncate:35:"...":true}</p>
          <div class="p-15">
              <a href="javascript:edit_news({$news_id},'{$path}')"  title="Edit" class="btn m-b-xs btn-info"><i class="fa fa-edit"></i></a>
                        {form_open('admin/news/delete_news', 'class="inline-form-button" method="post" onsubmit="return confirmAction(\'confirm_msg2\')"')}
             <input type="hidden" name="id" value="{$news_id}">
             <button class="btn m-b-xs btn-danger" title="Delete"><i class="fa fa-trash-o"></i></button>
            {form_close()}
        
          </div>
        </div>
        
        </div>
        <!--END NEW NEWS--->
     
        {/foreach}  
        {else}
        <h4 align="center">{lang('no_news_found')}</h4>
        {/if}

    {$ci->pagination->create_links()}
       
{/block}
