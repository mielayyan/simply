{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

<div class="container loginContainer">
    <div class="loginwsection">
<div class="navbar-brand_login block m-t"> <img src="{$SITE_URL}/uploads/images/logos/{$site_info["login_logo"]}" /> </div>
    {if $DEMO_STATUS == 'yes' || $MODULE_STATUS['lang_status'] == 'yes'}
        <div class="login-lang-btn">
            <ul class="nav">
                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" class="dropdown-toggle width_flag">
                        {foreach from=$LANG_ARR item=v}
                        {if $selected_language_id == $v.lang_id}
                        <img src="{$PUBLIC_URL}images/flags/{$v.lang_code}.png" />
                        {/if}
                        {/foreach}
                        <b class="caret"></b>
                    </a>
                    <!-- dropdown -->
                    <ul class="dropdown-menu animated fadeInRight">
                        {foreach from=$LANG_ARR item=v}
                        <li>
                            <a href="javascript:changeDefaultLanguageCommon('{$v.lang_id}');">
                                <img src="{$PUBLIC_URL}images/flags/{$v.lang_code}.png" /> {$v.lang_name}
                            </a>
                        </li>
                        {/foreach}
                    </ul>
                    <!-- / dropdown -->
                </li>
            </ul>
        </div>
    {/if}
          
  
        <div class="loginforms">
            {block name=CONTENT_INNER}
                
            {/block}
        </div>
    </div>



        <div class="text-center m-t-sm"> <small class="text-muted ">{include file="layout/login_footer.tpl"}</small> </div>

    </div>
        
    

    <style type="text/css">
        .app:before {
        background-color: #fff;
        }
        body {
    background: #f3f3f3 ;

    /* background-image: url(http://localhost/mlm_design/uploads/images/logos/loginbg.png);
    background-size: cover; */
}
.list-group-item {
    background-color: transparent;

}
.app-header-fixed {
    padding-top: 0px;
    display: grid;
    height: 100%;
    align-items: center;
}
    </style>

    <script>
        function changeDefaultLanguageCommon(language_id) {
            $.ajax({
                url: base_url + 'login/change_default_language',
                data: { language: language_id },
                type: 'post',
                success: function(data) {
                    if (data == 'yes') {
                        location.reload();
                    }
                },
                error: function(error) {
                    console.log(error);
                }
            });
        }
    </script>

{/block}