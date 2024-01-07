{extends file=$BASE_TEMPLATE}{block name=$CONTENT_BLOCK}
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-body">

        {assign var=desc1 value=lang('note_excel_registration_1')}
        {assign var=desc2 value=lang('note_excel_registration_2')}
        {include file="common/notes.tpl" notes="<p>`$desc1`</p> <p>`$desc2`</p>"}
        
                {form_open_multipart('','role="form" class="" name="excel_form" id="excel_form"')}
                <div class="col-sm-3 padding_both form-group">
                    <span class="control-fileupload">
                        <label class="required" for="fileInput">{lang('select_file')}</label>
                        <input class="form-control form-control-static" type="file" id="fileInput" name="register_doc">
                    </span>
                </div>
                <div class="col-sm-3 padding_both_small">
                    <div class="form-group mark_paid">
                        <button class="btn btn-primary" name="excel_reg" id="excel_reg" type="submit" value="excel_reg">
                            {lang('register')}
                        </button>
                    </div>
                </div> {form_close()}
                <div class="col-sm-3 padding_both_small pull-right">
                    <div class="form-group mark_paid pull-right">
                    {if $MODULE_STATUS['mlm_plan'] == 'Binary'}
                        <a href="{$SITE_URL}/uploads/images/document/excel_reg/sample_binary.xls" class="btn m-b-xs btn-sm btn-primary btn-addon" title="{lang('download_sample_file')}">
                    {else}
                        <a href="{$SITE_URL}/uploads/images/document/excel_reg/sample.xls" class="btn m-b-xs btn-sm btn-primary btn-addon" title="{lang('download_sample_file')}">
                    {/if}
                    <i class="fa fa-download"></i> {lang('download_sample_file')}</a>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>{/block}