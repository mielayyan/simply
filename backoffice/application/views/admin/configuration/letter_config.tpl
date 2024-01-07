<div class="content">
   
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
                                     {foreach from=$letter_matter item=v}
                                    <tr>
                                        <td>{$i++}</td>
                                        <td>{ucfirst($v.language)}</td>
                                        <td>{substr($v.main_matter,0,100)}</td>
                                        <td> <a href ="{$BASE_URL}admin/configuration/edit_welcome_letter/{$v.lang_id}" class="btn-link btn_size has-tooltip text-info" title="edit"><i class="fa fa-edit"></i></a> </td>
                                    </tr>
                                    {/foreach}
                                </tbody>
                            </table>
                        </div>  
                    </div>
                </div>
</div>