{extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
<div class="wrapper_index">
    <div class="region  panel setting_margin  setting_margin_top">
        <div id="block-block-12" class="block block-block contextual-links-region clearfix">
            <div class=" features-quick-access">
                <div class="hbox text-center b-light text-sm bg-white" style="width: 100px;">
                    <a href="http://localhost/WC/10.0.2/backoffice/user/configuration" class="col padder-v text-muted  setting-selected ">
                        <i class="fa fa-desktop block m-b-xs fa-2x"></i>
                        <span>{lang('general')}</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="panel panel-default">
    <div class="panel-body">
        <legend><span class="fieldset-legend">{lang('general_settings')}</span></legend>
        <div class="table-responsive">
            {if $MODULE_STATUS['google_auth_status'] == 'yes'}
                {form_open('','role="form" class="" name="form_general_setting" id="form_general_setting"')}
                    <div class="form-group">
                        <div class="checkbox">
                          <label class="i-checks">
                            <input type="checkbox" name="google_auth_status" {if $user_google_auth_status == 'yes'} checked {/if}><i></i> 
                            {lang('google_auth_status')}
                          </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <button class="btn btn-sm btn-primary" type="submit" value="Update" name="setting" id="setting">Update</button>
                    </div>
                    <div class="m-b pink-gradient">
                        <div class="card-body ">
                            <div class="media">
                                <figure class=" avatar-50 "><i class="glyphicon glyphicon-book"></i></figure>
                                <h6 class="my-0"><p>For each commission calculation, service charge and TDS will be deducted.</p><p>Transaction fee will be added for every E-wallet transaction.</p></h6>
                            </div>
                        </div>
                    </div>
                 {form_close()}
            {/if}
        </div>
    </div>
</div>
{/block}