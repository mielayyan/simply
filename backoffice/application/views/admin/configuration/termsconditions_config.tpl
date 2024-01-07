
        <div class="content">
            {form_open_multipart('admin/configuration/content_management','role="form" class="" name= "terms_config" id= "terms_config"')}
                
                  <div class="panel panel-default table-responsive">
                    <div class="panel-body">
                        <div id="overall" class="table-responsive hide show">
                            <table st-table="rowCollectionBasic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>{lang('slno')}</th>
                                        <th>{lang('language')}</th>
                                        <th>{lang('Content')}</th>
                                        <th>{lang('action')}</th>
                                </thead>
                                {assign var="i" value=1}
                                <tbody>
                                     {foreach from=$terms item=v}
                                    <tr>
                                        <td>{$i++}</td>
                                        <td>{ucfirst($v.language)}</td>
                                        <td>{substr($v.terms_conditions,0,100)}</td>
                                        <td> <a href ="{$BASE_URL}admin/configuration/edit_terms/{$v.lang_id}" class="btn-link btn_size has-tooltip text-info" title="edit"><i class="fa fa-edit"></i></a> </td>
                                    </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>  
                    </div>
                </div>
            {form_close()}
        </div>