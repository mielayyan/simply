{extends file=$BASE_TEMPLATE}
{block name=script}
{$smarty.block.parent}
<script src="{$PUBLIC_URL}plugins/clipboard.min.js" type="text/javascript"></script>
<script src="{$PUBLIC_URL}javascript/chart/chart.min.js"></script>
<script src="{$PUBLIC_URL}javascript/ajax-dynamic-dashboard-user.js" type="text/javascript"></script>
<script src="{$PUBLIC_URL}javascript/user_dashboard.js"></script>
<script src="{$PUBLIC_URL}javascript/user_dashboard.js"></script>
<link rel="stylesheet" href="{$PUBLIC_URL}css/owl.carousel.min.css"/>
<script src="{$PUBLIC_URL}javascript/owl.carousel.js"></script>
<script>
  country_map_data = {$map_data};
</script>
{/block}

{* {block name=overview}

  <div class="header-user-details">
    {$Profit = $amount-$expense }
    <div class="header-user-cnt">
      <h4 class="text-info">{lang('turnover')}</h4>
      <p>
        {format_currency($amount)}
      </p>
    </div>
     <div class="header-user-cnt">
        <h4 class="text-danger">{lang('expense')}</h4>
        <p>{format_currency($expense)}</p>
    </div>
    <div class="header-user-cnt">
      <h4 class="text-success">{lang('profit')}</h4>
      <p>{format_currency($Profit)}</p>
    </div>
  </div>
{/block} *}
{block name=style}
<style type="text/css">
body{
  /*margin-top: auto;
    //background-color: #f1f1f1;*/
  }
  .border{
    border-bottom:1px solid #F1F1F1;
    margin-bottom:10px;
  }
  .main-secction{
    box-shadow: 10px 10px 10px;
  }
  .image-section{
    padding: 0px;
  }
  .image-section img{
    width: 100%;
    height:250px;
    position: relative;
  }
  .user-image{
    position: absolute;
    margin-top:-50px;
  }
  .user-left-part{
    margin: 0px;
  }
  .user-image img{
    width:100px;
    height:100px;
  }
  .user-profil-part{
    padding-bottom:30px;
    background-color:#FAFAFA;
  }
  .follow{    
    margin-top:70px;   
  }
  .user-detail-row{
    margin:0px; 
  }
  .user-detail-section2 p{
    font-size:12px;
    padding: 0px;
    margin: 0px;
  }
  .user-detail-section2{
    margin-top:10px;
  }
  .user-detail-section2 span{
    color:#7CBBC3;
    font-size: 20px;
  }
  .user-detail-section2 small{
    font-size:12px;
    color:#D3A86A;
  }
  .profile-right-section{
    padding: 20px 0px 10px 15px;
    background-color: #FFFFFF;  
  }
  .profile-right-section-row{
    margin: 0px;
  }
  .profile-header-section1 h1{
    font-size: 25px;
    margin: 0px;
  }
  .profile-header-section1 h5{
    color: #0062cc;
  }
  .req-btn{
    height:30px;
    font-size:12px;
  }
  .profile-tag{
    padding: 10px;
    border:1px solid #F6F6F6;
  }
  .profile-tag p{
    font-size: 12px;
    color:black;
  }
  .profile-tag i{
    color:#ADADAD;
    font-size: 20px;
  }
  .image-right-part{
    background-color: #FCFCFC;
    margin: 0px;
    padding: 5px;
  }
  .img-main-rightPart{
    background-color: #FCFCFC;
    margin-top: auto;
  }
  .image-right-detail{
    padding: 0px;
  }
  .image-right-detail p{
    font-size: 12px;
  }
  .image-right-detail a:hover{
    text-decoration: none;
  }
  .image-right img{
    width: 100%;
  }
  .image-right-detail-section2{
    margin: 0px;
  }
  .image-right-detail-section2 p{
    color:#38ACDF;
    margin:0px;
  }
  .image-right-detail-section2 span{
    color:#7F7F7F;
  }

  .nav-link{
    font-size: 1.2em;    
  }
  
</style>
{/block}
{block name=$CONTENT_BLOCK}


<div class="panel-body">
            <div class="col-md-12 col-sm-12 col-xs-12 image-section">
                <img src="{$SITE_URL}/uploads/images/banners/banner-tchnoly.jpg">
            </div>
            <div class="row user-left-part">
                <div class="col-md-3 col-sm-3 col-xs-12 user-profil-part pull-left">
                    <div class="row ">
                        <div class="col-md-12 col-md-12-sm-12 col-xs-12 user-image text-center">
                            <img src="{$SITE_URL}/uploads/images/profile_picture/{$profile_pic}" class="rounded-circle">
                        </div>
                        <!--<div class="col-md-12 col-sm-12 col-xs-12 user-detail-section1 text-center">
                            <button id="btn-contact" (click)="clearModal()" data-toggle="modal" data-target="#contact" class="btn btn-success btn-block follow">Contactarme</button> 
                            <button class="btn btn-warning btn-block">Descargar Curriculum</button>                               
                        </div>-->
                        <div class="col-md-12 col-sm-12 col-xs-12 user-detail-section1" style="margin-top: 50px">
                            <div class="col-md-12 col-sm-12 user-detail-section2 pull-left">
                                <div class="border"></div>
 
                                {if $MODULE_STATUS['opencart_status_demo'] =="no" && $MODULE_STATUS['opencart_status'] =="no"}
                                <p style="font-size: 2em">{lang('product')}</p>
                                <span>{$user_details['data']['product_name']}</span>
                                <p style="font-size: 1em">{lang('payment')}</p>
                                <span>{$user_details['data']['by_using']}</span>
                                <p style="font-size: 1em">{lang('status')}</p>
                                <span>{lang('pending')}</span>
                                {else}



                                <p style="font-size: 1em">{lang('status')}</p>
                                <span>{lang('pending')}</span>
                                {/if}
                              
                            </div>                           
                        </div>
                       
                    </div>
                </div>
                <div class="col-md-9 col-sm-9 col-xs-12 pull-right profile-right-section">
                    <div class="row profile-right-section-row">
                        <div class="col-md-12 profile-header">
                            <div class="row">
                                <div class="col-md-8 col-sm-6 col-xs-6 profile-header-section1 pull-left">
                                    <h1>{$user_details['user_name']}</h1>
                                    {if $MODULE_STATUS['opencart_status_demo'] =="no" && $MODULE_STATUS['opencart_status'] =="no"}
                                    <h5>{lang('date_of_joining')} : {$user_details['data']['joining_date']}</h5>
                                    {/if}
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="row">
                                <div class="col-md-8">
                                        <ul class="nav nav-tabs" role="tablist">
                                                <li class="nav-item">
                                                  <a class="nav-link active" href="#profile" role="tab" data-toggle="tab">{lang('personal_info')}</a>
                                                </li>                                               
                                              </ul>
                                              
                                              <!-- Tab panes -->
                                              <div class="tab-content">
                                                <div role="tabpanel" class="tab-pane fade show active in" id="profile">
                                                    <br>
                                                   <div class="row">
                                                        <div class="col-md-4">
                                                            <label>{lang('first_name')}</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p>{$user_details['data']['first_name']}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>{lang('last_name')}</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p>{$user_details['data']['last_name']}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>{lang('dob')}</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p>{$user_details['data']['date_of_birth']}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>{lang('email')}</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p>{$user_details['data']['email']}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>{lang('mob_no_10_digit')}</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p>{$user_details['data']['mobile']}</p>
                                                        </div>
                                                    </div>      
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>{lang('sponsor')}</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p>{$user_details['data']['sponsor_user_name']}</p>
                                                        </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-4">
                                                            <label>{lang('sponsor_full_name')}</label>
                                                        </div>
                                                        <div class="col-md-4">
                                                            <p>{$user_details['data']['sponsor_full_name']}</p>
                                                        </div>
                                                    </div>
                                                </div>
                                              </div>
                          
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
    </div>
  {/block}
  {block name=home_wrapper_out}
    {if !empty($news)}
      <div class="panel-body setting_margin news home-news news-fixed" style="display: none;" id="news_div">
        <div class="panel wrapper">
          <button class="news-hide"><i class="fa fa-angle-down" aria-hidden="true"></i>  {lang('hide')}</button>
          <h4 class="font-thin m-t-none m-b-none text-primary-lt">{lang('latest_news')}</h4>
          <div id="news_carousel" class="owl-carousel owl-theme news-carousel">
            {foreach from=$news item=$news_item}
              <div class="item">
                <div class="rela-blog-img">
                  <a href="{$BASE_URL}user/view_news">
                    <img src="{$SITE_URL}/uploads/images/news/{$news_item.news_image}" alt="{$news_item.news_title}">
                  </a>
                </div>
                <div class="rela-blog-cnt">
                  <h5 class="rela-blog-cnt-title"><a href="{$BASE_URL}user/view_news">{$news_item.news_title|truncate:64}</a></h5>
                </div>
              </div>
            {/foreach}
          </div>
        </div>
      </div>
    {/if}

  {/block}



