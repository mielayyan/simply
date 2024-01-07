{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<style type="text/css">
    .news_title{
        font-size: 15px;
        color: black;
        font-weight: 500;
    }
</style>
<div class="col-lg-12">
    {if count($news_details)!=0}
    {assign var="path" value="{$BASE_URL}admin/"}
    {assign var="i" value=0}
    
  <div class="row">
    
    <div class="col-sm-12">
      
      
      
      <div class="">
        {foreach from=$news_details item=v}
        {assign var="news_id" value="{$v.news_id}"}
        {assign var="date" value="{$v.news_date|date_format:"%D"}"}
        {assign var="time" value="{$v.news_date|date_format:"%r"}"}
        <div class="col-lg-3 col-md-4 col-sm-6">
          
        <div class="panel b-b wrapper-lg">
          <a href="{$BASE_URL}user/view_news/{$v.news_id}" class="pull-left thumb thumb-wrapper m-r">
            <img src="{image_path('news',$v.news_image,'default.jpg')}">
          </a>
          <div class="clear">                        
            <a href="{$BASE_URL}user/view_news/{$v.news_id}" class="font-semibold text-ellipsis news_title">{$v.news_title}</a>
            <div class="text-xs block m-t-xs">
              <i class="fa fa-clock-o text-muted "></i> {$date}
               
            </div>
          </div>
          <div style="clear: both;"></div>
          <div class="newsreadmore"><a class="read_full_news" href="{$BASE_URL}user/view_news/{$v.news_id}">{lang('read_more')} </a></div>
        </div>
        </div>
       
       {/foreach}
        {else}
        <div>
         <h4 align="center">{lang('no_news_found')}</h4>
        </div>
        {/if}
      </div>
    </div>
  </div>
</div>

{$ci->pagination->create_links()}
{/block}
{block name=script}
    {$smarty.block.parent}
   
    
{/block}
 