{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<style type="text/css">
    .blog-img{
        height: 260px;
    }
</style>
<div class="col-lg-12">
    {if count($latest_news)!=0}
    {assign var="path" value="{$BASE_URL}admin/"}
    {assign var="i" value=0}
    
  <div class="row">
    <div class="col-sm-9">
    {foreach from=$latest_news item=l}
    {assign var="date" value="{$l.news_date|date_format:"%D"}"}
      <div class="blog-post">                   
        <div class="panel">
          <div>
            <img class="blog-img" src="{image_path('news',$l.news_image,'default.jpg')}" class="img-full">
          </div>
          <div class="wrapper-lg">
            <h2 class="m-t-none text-center">{$l.news_title}</h2>
            <div>
              <p>{$l.news_desc}</p>
            </div>
            <div class="line line-lg b-b b-light"></div>
            <div class="text-muted">
              <i class="fa fa-user text-muted"></i> by <a href="" class="m-r-sm">Admin</a>
              <i class="fa fa-clock-o text-muted "></i> {$date}
            </div>
          </div>
        </div>
        
      </div>
      {/foreach}
      
      
      
      
      
    </div>
    <div class="col-sm-3">
      
      
      
      <h5 class="font-bold">{lang('recent_news')}</h5>
      <div>
        {if count($news_details)!=0}
        {foreach from=$news_details item=v}
        {assign var="news_id" value="{$v.news_id}"}
        {assign var="date" value="{$v.news_date|date_format:"%D"}"}
        {assign var="time" value="{$v.news_date|date_format:"%r"}"}
        <div class="panel panel b-b wrapper-lg">
          <a href="{$BASE_URL}user/view_news/{$v.news_id}" class="pull-left thumb thumb-wrapper m-r">
            <img src="{image_path('news',$v.news_image,'default.jpg')}">
          </a>
          <div class="clear">                        
            <a href="{$BASE_URL}user/view_news/{$v.news_id}" class="font-semibold text-ellipsis">{$v.news_title}</a>
            <div class="text-xs block m-t-xs"><i class="fa fa-clock-o text-muted "> </i> {$date}</div>
          </div>
        </div>
       <div class="line line-lg b-b b-light"></div>
       {/foreach}
       {else}
        <div>
         <h4 align="center">{lang('no_news_found')} </h4>
        </div>
       {/if}
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
 