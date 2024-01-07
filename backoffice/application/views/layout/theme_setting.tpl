<!-- theme settings -->
{if $LOG_USER_TYPE=='admin' || $LOG_USER_TYPE=='employee'}
<div class="settings panel panel-default">
    <button class="btn btn-default no-shadow pos-abt" ui-toggle-class="active" target=".settings">
        <i class="fa fa-spin fa-gear"></i>
    </button>
    
    <div class="panel-heading">
        <span>{lang('Settings')}</span>
        <div class="btn-group dropdown open">
          <button class="btn btn-xs" data-action_value="admin" id="theme" data-toggle="dropdown" aria-expanded="true">{ucfirst(lang('admin'))} <span class="caret"></span></button>
          <ul class="dropdown-menu">
            <li data-theme_value="admin" class="theme-value"><a href="#">{ucfirst(lang('admin'))}</a></li>
            <li data-theme_value="user" class="theme-value"><a href="#">{ucfirst(lang('user'))}</a></li>
          </ul>
        </div>

         <div class="m-b-sm">
      </div>
    </div>
   
    <div class="panel-body">
        <div class="m-b-sm">
            <label class="i-switch bg-info pull-right">
                <input type="checkbox" name="headerFixed" class="ng-pristine ng-untouched ng-valid ng-empty"
                    aria-invalid="false">
                <i></i>
            </label>
            {lang('Fixed_header')}
        </div>
        <div class="m-b-sm">
            <label class="i-switch bg-info pull-right">
                <input type="checkbox" name="asideFixed" class="ng-pristine ng-untouched ng-valid ng-empty"
                    aria-invalid="false">
                <i></i>
            </label>
            {lang('Fixed_aside')}
        </div>
        <div class="m-b-sm">
            <label class="i-switch bg-info pull-right">
                <input type="checkbox" name="asideFolded" class="ng-pristine ng-untouched ng-valid ng-empty"
                    aria-invalid="false">
                <i></i>
            </label>
           {lang('Folded_aside')}
        </div>
        <div class="m-b-sm">
            <label class="i-switch bg-info pull-right">
                <input type="checkbox" name="asideDock" class="ng-pristine ng-untouched ng-valid ng-empty"
                    aria-invalid="false">
                <i></i>
            </label>
           {lang('Dock_aside')}
        </div>
        <div>
            <label class="i-switch bg-info pull-right">
                <input type="checkbox" name="container" class="ng-pristine ng-untouched ng-valid ng-empty"
                    aria-invalid="false">
                <i></i>
            </label>
            {lang('Boxed_layout')}
        </div>
    </div>
    <div class="wrapper b-t b-light bg-light lter r-b">
        <div class="row row-sm" id="theme_selection">
            <div class="col-xs-6">
                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="1" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-black header"></b>
                        <b class="bg-white header"></b>
                        <b class="bg-black"></b>
                    </span>
                </label>

                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="13" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-dark header"></b>
                        <b class="bg-white header"></b>
                        <b class="bg-dark"></b>
                    </span>
                </label>

                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="2" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                    aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-white header"></b>
                        <b class="bg-white header"></b>
                        <b class="bg-black"></b>
                    </span>
                </label>

                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="3" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-primary header"></b>
                        <b class="bg-white header"></b>
                        <b class="bg-dark"></b>
                    </span>
                </label>

                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="4" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-info header"></b>
                        <b class="bg-white header"></b>
                        <b class="bg-black"></b>
                    </span>
                </label>

                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="5" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-success header"></b>
                        <b class="bg-white header"></b>
                        <b class="bg-dark"></b>
                    </span>
                </label>

                <label class="i-checks block" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="6" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-danger header"></b>
                        <b class="bg-white header"></b>
                        <b class="bg-dark"></b>
                    </span>
                </label>
            </div>
            <div class="col-xs-6">
                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="7" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-black header"></b>
                        <b class="bg-black header"></b>
                        <b class="bg-white"></b>
                    </span>
                </label>

                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="14" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-dark header"></b>
                        <b class="bg-dark header"></b>
                        <b class="bg-light"></b>
                    </span>
                </label>

                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="8" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-info dker header"></b>
                        <b class="bg-info dker header"></b>
                        <b class="bg-light dker"></b>
                    </span>
                </label>

                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="9" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-primary header"></b>
                        <b class="bg-primary header"></b>
                        <b class="bg-dark"></b>
                    </span>
                </label>

                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="10" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-info dker header"></b>
                        <b class="bg-info dk header"></b>
                        <b class="bg-black"></b>
                    </span>
                </label>

                <label class="i-checks block m-b-sm" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="11" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-success header"></b>
                        <b class="bg-success header"></b>
                        <b class="bg-dark"></b>
                    </span>
                </label>

                <label class="i-checks block" role="button" tabindex="0">
                    <input type="radio" name="themeID" value="12" class="ng-pristine ng-untouched ng-valid ng-not-empty"
                        aria-invalid="false">
                    <span class="block bg-light clearfix pos-rlt">
                        <span class="active pos-abt w-full h-full bg-black-opacity text-center">
                            <i class="glyphicon glyphicon-ok text-white m-t-xs"></i>
                        </span>
                        <b class="bg-danger dker header"></b>
                        <b class="bg-danger dker header"></b>
                        <b class="bg-dark"></b>
                    </span>
                </label>
            </div>
        </div>
    </div>
</div>
{/if}

<!-- / theme settings -->