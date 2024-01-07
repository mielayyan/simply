<div id="tooltip_div" style="display:none;">
    {foreach from= $tooltip_array item=v}
        <div id="user_{$v['user_name']|replace:'.':'_'}" class="tree_img_tree">
            <div class="Demo_head_bg">
                {$a = dirname(FCPATH)}
                {$b = '/uploads/images/profile_picture/'}
                {$c = $v['photo']}
                {$d = "{$a}{$b}{$c}"}
                {if file_exists($d)}
                    <img src="{$SITE_URL}/uploads/images/profile_picture/{$v['photo']}"/>
                {else}
                     <img src="{$SITE_URL}/uploads/images/profile_picture/nophoto.jpg" />
                {/if}
                <p>{$v['user_name']}</p>
            </div>
            <div class="body_text_tree">
                {if $tooltip_config['first_name'] == 'yes'}
                    <div class="binary_bg">
                        <p class="text-center">{$v['full_name']}</p>
                    </div>
                {/if}
                <ul class="list-group no-radius">
                    {if $tooltip_config['join_date'] == 'yes'}
                        <li class="list-group-item">
                            <div class="pull-right">:&nbsp;&nbsp;{date('Y/m/d', strtotime($v['join_date']))}</div>
                            <div class="pull-left">{lang('join_date')}</div>
                        </li>
                    {/if}
                    {if $MODULE_STATUS['mlm_plan'] == 'Binary'}
                        {if $tooltip_config['left'] == 'yes'}
                            <li class="list-group-item">
                                <div class="pull-right">:&nbsp;&nbsp;{round($v['left'], 2)}</div>
                                <div class="pull-left">{lang('left')}</div>
                            </li>
                        {/if}
                        {if $tooltip_config['right'] == 'yes'}
                            <li class="list-group-item">
                                <div class="pull-right">:&nbsp;&nbsp;{round($v['right'], 2)}</div>
                                <div class="pull-left">{lang('right')}</div>
                            </li>
                        {/if}
                        {if $tooltip_config['left_carry'] == 'yes'}
                            <li class="list-group-item">
                                <div class="pull-right">:&nbsp;&nbsp;{round($v['left_carry'], 2)}</div>
                                <div class="pull-left">{lang('left_carry')}</div>
                            </li>
                        {/if}
                        {if $tooltip_config['right_carry'] == 'yes'}
                            <li class="list-group-item">
                                <div class="pull-right">:&nbsp;&nbsp;{round($v['right_carry'], 2)}</div>
                                <div class="pull-left">{lang('right_carry')}</div>
                            </li>
                        {/if}
                    {/if}
                    {if $tooltip_config['personal_pv'] == 'yes'}
                        <li class="list-group-item">
                            <div class="pull-right">:&nbsp;&nbsp;{$v['personal_pv']|default:0}</div>
                            <div class="pull-left">{lang('personal_PV')}</div>
                        </li>
                    {/if}
                    {if $tooltip_config['gpv'] == 'yes'}
                        <li class="list-group-item">
                            <div class="pull-right">:&nbsp;&nbsp;{$v['group_pv']|default:0}</div>
                            <div class="pull-left">{lang('group_PV')}</div>
                        </li>
                    {/if}
                    {if $tooltip_config['country'] == 'yes'}
                    <li class="list-group-item">
                        <div class="pull-right">:&nbsp;&nbsp;{$v['country_name']|default:0}</div>
                        <div class="pull-left">{lang('Country')}</div>
                    </li>
                    {/if}
                    {if $tooltip_config['sponsor_name'] == 'yes'}
                    <li class="list-group-item">
                        <div class="pull-right">:&nbsp;&nbsp;{$v['sponsor_full_name']|default:0}</div>
                        <div class="pull-left">{lang('sponsor_name')}</div>
                    </li>
                    {/if}
                    {if isset($v['promo_rank'])}
                     <li class="list-group-item">
                        <div class="pull-right">:&nbsp;&nbsp;{$v['promo_rank']|default:''}</div>
                        <div class="pull-left">{lang('Rank Promo')}</div>
                    </li>
                    {/if}
                    {if $MODULE_STATUS['mlm_plan'] == 'Donation' && $v['donation_level']}
                        {if $tooltip_config['donation_level'] == 'yes'}
                            <li class="list-group-item">
                                <div class="donation_level">{$v['donation_level']}</div>
                            </li>
                        {/if}
                    {/if}
                    {if $MODULE_STATUS['rank_status'] == 'yes' && $v['rank_name']}
                        {if $tooltip_config['rank_status']=="yes"}
                            <div class="tooltip_rank" style="background-color:{$v['rank_color']}">{$v['rank_name']}</div>
                        {/if}
                    {/if}
                </ul>
            </div>
        </div>
    {/foreach}
</div>

{$display_tree}

<div id="tree" class="orgChart"></div>
