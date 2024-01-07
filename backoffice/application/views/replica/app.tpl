<!DOCTYPE html>
<html lang="en" class="">

<head>
  <meta charset="utf-8">
    <title>{$title}</title>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="keywords" content="">
    <meta name="description" content="">
    <link rel="shortcut icon" type="image/png" href="{$SITE_URL}/uploads/images/logos/{$site_info["favicon"]}"/>
    <!-- STYLESHEET CSS FILES -->
    <link rel="stylesheet" href="{$PUBLIC_URL}replica/theme/css/bootstrap.min.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}replica/theme/css/animate.min.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}replica/theme/css/font-awesome.min.css">
    <link rel="stylesheet" href="{$PUBLIC_URL}replica/theme/fonts/fontawesome-webfont.woff2">
    <!-- <link rel="stylesheet" href="css/nivo-lightbox.css">
    <link rel="stylesheet" href="css/nivo_themes/default/default.css"> -->
    <link rel="stylesheet" href="{$PUBLIC_URL}replica/theme/css/style.css">
    <link href="https://fonts.googleapis.com/css?family=Montserrat:300,400,500,600,700&display=swap" rel="stylesheet">
    {block name=style}{/block}
</head>
<style>
    ul.dropdown-menu.animated.fadeInRight {
        left: auto;
        top: 31px;
    }
    .dropdown-menu.animated li {
        display: list-item;
        font-weight: initial;
        margin: 0;
        text-align: left;
    }
    ul.dropdown-menu.animated.fadeInRight li img {
        width: 16px;
        margin-top: -3px;
    }
</style>

<body data-spy="scroll" data-target="#navbar" data-offset="60">
    <input type="hidden" name="base_url" id="base_url" value="{$BASE_URL}" />
    <input type="hidden" name="current_url" id="current_url" value="{$CURRENT_URL}" />
    <input type="hidden" name="current_url_full" id="current_url_full" value="{$CURRENT_URL_FULL}" />
    <input type="hidden" name="site_url" id="site_url" value="{$SITE_URL}/" />
    {if !$MAINTENANCE_MODE }
    <div id='preloader'>
        <div class='loader'>
           <!--  <img src="{$PUBLIC_URL}replica/theme/img/three-dots.svg" width="60" alt=""> -->
        </div>
    </div><!-- Preloader -->
   <section class="navbar navbar-fixed-top custom-navbar" role="navigation">
    <div class="top">
        <div class="container">
        <ul class="">
                <li>
                 {if $LANG_STATUS == 'yes'}
                <a class="dropdown-toggle lang-a" data-close-others="true" data-hover="dropdown" data-toggle="dropdown"
                    href="#">

                    {foreach from=$LANG_ARR item=v}
                    {if $LANG_ID == $v.lang_id}
                    <img src="{$PUBLIC_URL}images/flags/{$v.lang_code}.png"  style="width: unset;"/>
                    {/if}
                    {/foreach}
                </a>
                <ul class="dropdown-menu animated fadeInRight">
                    {foreach from=$LANG_ARR item=v}
                        <li>
                          <a href="javascript:changeDefaultLanguageInReplica('{$v.lang_id}');">
                              <img src="{$PUBLIC_URL}images/flags/{$v.lang_code}.png" /> {$v.lang_name}
                          </a>
                      </li>
                    {/foreach}
                      
                  </ul>
            {/if}
            </li>
                {if isset($user_details['email']) && $user_details['email']}
                    <li class=""><i class="fa fa-envelope-o" aria-hidden="true"></i>
                        {$user_details['email']}
                    </li>
                {/if}
                {if isset($user_details['phone']) && $user_details['phone']}
                    <li class=""><i class="fa fa-phone" aria-hidden="true"></i>
                        {$user_details['phone']}
                    </li>
                {/if}
                {if isset($user_details['fullname']) && $user_details['fullname']}
                    <li class=""><i class="fa fa-user" aria-hidden="true"></i>
                        {$user_details['fullname']}
                    </li>
                {/if}
            </ul>
        </div>
    </div>

    <div class="container nav-container">
        <div class="row">
            <div class="col-md-3 col-sm-4 col-xs-12">
                <a href="#" class="navbar-brand"><img src="{$BASE_URL}../uploads/images/logos/{$site_info['login_logo']}"/></a>
            </div>
            <div class="col-md-9 col-sm-8 col-xs-12">
                <div class="navbar-header">
            <button class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="icon icon-bar"></span>
                <span class="icon icon-bar"></span>
                <span class="icon icon-bar"></span>
            </button>
            
        </div>
        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav navbar-right">
                <li><a href="{SITE_URL}/replica/home#home" class="smoothScroll">{strtoupper(lang('Home'))}</a></li>
                <li><a href="{SITE_URL}/replica/home#plan" class="smoothScroll">{strtoupper(lang('plan'))}</a></li>
                <li><a href="{SITE_URL}/replica/home#about" class="smoothScroll">{strtoupper(lang('about'))}</a></li>
                <li><a href="{SITE_URL}/replica/home#contact" class="smoothScroll">{strtoupper(lang('contact'))}</a></li>
                <li><a href="{SITE_URL}/replica_register" class="register" target="_blank">{strtoupper(lang('register'))}</a></li>
            </ul>
        </div>
            </div>
        </div>
        
    </div>
</section>

        <div class="app app-header-fixed ">
            {block name=$CONTENT_BLOCK}{/block}
        </div>

        {block name=footer}
           <footer id="copyright">
                <div class="container">
                    <div class="row">
                        <div class="col-md-8 col-sm-8 col-xs-12">
                           <p class="pull-left">
                                {'Y'|date} © 
                                {if isset($site_info['company_name'])} 
                                    {$site_info['company_name']} 
                                {else}  
                                    {* MLM Software 10.0 - Developed by CompanyName *}
                                {/if}
                            </p>
                        </div>
                        <div class="col-md-4 col-sm-4 col-xs-12">
                            <ul class="copy-condition">
                                <li><a href="{$BASE_URL}replica/policy">{lang('privacy_policy')}</a></li>
                                <li><a href="{$BASE_URL}replica/terms">{lang('terms_&_conditions')}</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
                <a href="#home" class="scroll-top smoothScroll fa fa-angle-up"></a>
            </footer>
        {/block}
        <script>
            {if isset($validations)}
                window.translations = {json_encode($validations)};
            {/if}
        </script>
        {* <a data-scroll href="#header" id="scroll-to-top"><i class="fa fa-angle-up" style="font-size: 25px;"></i> </a> *}
        <script src="{$PUBLIC_URL}replica/theme/js/jquery.js"></script>
        <script src="{$PUBLIC_URL}replica/theme/js/bootstrap.min.js"></script>
        {* <script src="{$PUBLIC_URL}replica/theme/js/imagesloaded.min.js"></script> *}
        <script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>
        <script src="{$PUBLIC_URL}replica/theme/js/isotope.js"></script>
        <script src="{$PUBLIC_URL}replica/theme/js/nivo-lightbox.min.js"></script>
        <script src="{$PUBLIC_URL}replica/theme/js/smoothscroll.js"></script>
        <script src="{$PUBLIC_URL}replica/theme/js/wow.min.js"></script>
        <script src="{$PUBLIC_URL}replica/theme/js/custom.js"></script>
        <script src="{$PUBLIC_URL}plugins/jquery-validation/dist/jquery.validate.min.js"></script>
        <script src="{$PUBLIC_URL}/javascript/replica/replica.js"></script>
        <script>
            $(function () {
                $.ajaxSetup({
                    data: {
                    {$CSRF_TOKEN_NAME}: "{$CSRF_TOKEN_VALUE}"
                }
            });
        });
        function changeDefaultLanguageInReplica(language_id) {
            $.ajax({
                url: $("#site_url").val()+'change_replica_language',
                data: { language: language_id },
                type: 'post',
                success: function(data) {
                    if (data == 'yes') {
                        location.reload();
                    }
                },
                error: function(error) {
                    console.log(error);
                },
                complete: function() {
                }
            });
        }
        </script>
        {block name=script}{/block}
        {foreach from = $ARR_SCRIPT item=v}
        {assign var="type" value=$v.type}
        {assign var="loc" value=$v.loc}
        {if $type == "js"}
        <script src="{$PUBLIC_URL}replica/js/{$v.name}" type="text/javascript"></script>
        {elseif $type == "css"}
        <link href="{$PUBLIC_URL}replica/css/{$v.name}" rel="stylesheet" type="text/css" />
        {elseif $type == "plugins/js"}
        <script src="{$PUBLIC_URL}replica/plugins/{$v.name}" type="text/javascript"></script>
        {elseif $type == "plugins/css"}
        <link href="{$PUBLIC_URL}replica/plugins/{$v.name}" rel="stylesheet" type="text/css" />
        {/if}
        {/foreach}
</body>
</html>
{/if}










