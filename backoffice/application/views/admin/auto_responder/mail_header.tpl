     
        <div class="col w-md bg-light dk b-r bg-auto">
            <div class="wrapper b-b bg">
                <button class="btn btn-sm btn-default pull-right visible-sm visible-xs" ui-toggle="show" target="#email-menu"><i class="fa fa-bars"></i></button>
                <a href="{$BASE_URL}admin/auto_responder/auto_responder_settings" class="btn btn-sm btn-primary w-xs font-bold">{lang('Add')}</a>
            </div>
            <div class="wrapper hidden-sm hidden-xs" id="email-menu">
                <ul class="nav nav-pills nav-stacked nav-sm">
                    <li {if $CURRENT_URL=="auto_responder/auto_responder_details"}class="active"{/if}>
                        <a tabindex="3" href="{$BASE_URL}admin/auto_responder/auto_responder_details"><i class="fa fa-envelope-o"></i> {lang('Details')}
                            <span class="label label-primary pull-right">{if $unread_mail>0}{$unread_mail}{else}0{/if}</span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>