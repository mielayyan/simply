{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}

    <input type="hidden" id="responsive_tree" value="{if $MLM_PLAN == 'Binary'}1{else}0{/if}">

    <p class="text-right">
        <a href="{BASE_URL}/user/binary_history?from_tree=1" class="btn btn-sm btn-info btn-addon"><i class="fa fa-forward"></i>{lang('downline_list')}</a>
    </p>
    
    {include file="layout/search_tree.tpl" search_url="user/genology_tree"}
    
    <div id="summary" class="tree_main" style="overflow: hidden; position: relative;">
        {include file="user/tree/tree_view.tpl" search_url="user/genology_tree"}
    </div>
    
    {if $tree_based_on == 'member_status'}
    <div class="tree-icon-section">
    <h3>{lang("$tree_based_on")}</h3>
    {foreach from=$member_status item=v}
        <div class="tree-icon-container">
        <img src="{$SITE_URL}/uploads/images/tree/{$v.tree_icon}">
        <h3>{$v.status_name}</h3>
      </div>
    {/foreach}
    </div>
    {else if $tree_based_on == 'member_pack'}

    <div class="tree-icon-section">
    <h3>{lang("$tree_based_on")}</h3>
    {foreach from=$membership_package item=v}
        <div class="tree-icon-container">
        <img src="{$SITE_URL}/uploads/images/tree/{$v.tree_icon}">
        <h3>{$v.product_name}</h3>
      </div>
    {/foreach}

    {else if $tree_based_on == 'rank'}

    <div class="tree-icon-section">
    <h3>{lang("$tree_based_on")}</h3>
    {foreach from=$rank_details item=v}
        <div class="tree-icon-container">
        <img src="{$SITE_URL}/uploads/images/tree/{$v.tree_icon}">
        <h3>{$v.rank_name}</h3>
      </div>
    {/foreach}

    {/if}

    <div class="panel panel-default m-t hidden">
        <div class="panel-body">
            <div class="col-lg-9 col-sm-12 col-md-9">
                <div class="m-b m-t-sm tree_img">
                    <img src="{$PUBLIC_URL}images/tree/active.png">
                    <p>{lang('active')}</p>
                    <img src="{$PUBLIC_URL}images/tree/inactive.png">
                    <p>{lang('inactive')}</p>
                    {if $MLM_PLAN == 'Binary'}
                        <img src="{$PUBLIC_URL}images/tree/add_disabled.png">
                        <p>{lang('disabled')}</p>
                    {/if}
                    <img src="{$PUBLIC_URL}images/tree/add.png">
                    <p>{lang('vacant')}</p>
                </div>
            </div>
            <div class="col-lg-3 col-sm-12 col-md-3 m-t-md">
                <div class=" pull-right">
                    <button class="btn m-b-xs btn-primary zoom-in"><i class="glyphicon glyphicon-zoom-in"></i></button>
                    <button class="btn m-b-xs btn-info zoom-out"><i class="glyphicon glyphicon-zoom-out"></i></button>
                    <button class="btn m-b-xs btn-primary zoom-reset"><i class="icon-power"></i></button>
                </div>
    
            </div>
        </div>
    </div>
    <input id="root_user_name" value="{$user_name}" type="hidden">
    <input id="tree_url" value="{$BASE_URL}user/tree/tree_view" type="hidden">
{/block}

{block name=style}
    {$smarty.block.parent}
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/tree.css" type="text/css"/>
    {if $MLM_PLAN=="Binary"}
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/tree_binary.css" type="text/css"/>
    {/if}
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/tree_tooltip.css" type="text/css"/>
{/block}
{block name=script}
    {$smarty.block.parent}
    <script src="{$PUBLIC_URL}theme/libs/jquery/panzoom/jquery.panzoom.min.js"></script>
    <script src="{$PUBLIC_URL}javascript/tree/jquery.tree.js"></script>
    <script src="{$PUBLIC_URL}javascript/tree/genealogy.js"></script>
{/block}
