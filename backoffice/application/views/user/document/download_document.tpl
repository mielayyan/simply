{extends file=$BASE_TEMPLATE}
{block name=$CONTENT_BLOCK}
    <div class="userDownloadSection">
        {if count($file_details)==0}
            <div align="center">
                <h4 align="center"> {lang('no_data')}</h4>
            </div>
        {else}
            {foreach from=$file_details item=v}
                <div class="panel userDownloadContainer">
                    <div class="UD-thumbnail">
                        {* Document  *}
                        {if in_array($v.doc_file_name|pathinfo:$smarty.const.PATHINFO_EXTENSION, ['pdf', 'xlsx', 'word', 'ods', 'docx'])}
                            <img class="" src="{$PUBLIC_URL}images/document/document.svg">
                        {/if}

                        {* Video *}
                        {if in_array($v.doc_file_name|pathinfo:$smarty.const.PATHINFO_EXTENSION, ['mp4' , 'avi' , 'flv' , 'mpg' , 'wmv' , '3gp' , 'rm'])}
                            <img class="" src="{$PUBLIC_URL}images/document/mov.svg">
                        {/if}

                        {* Image *}
                        {if in_array($v.doc_file_name|pathinfo:$smarty.const.PATHINFO_EXTENSION, ['png', 'jpeg', 
                        'svg', 'jpg'])}
                            <img class="" src="{$PUBLIC_URL}images/document/image.svg">
                        {/if}
                    </div> 
                    <div class="UD-details"> 
                        <div class="UD-title">{$v.file_title}</div>  
                        <div class="UD-date">{$v.uploaded_date}</div>  
                        <div class="UD-desc">{$v.doc_desc}</div>
                    </div> 
                    <div class="UD-actions">
                        <a href="{$SITE_URL}/uploads/images/document/{$v.doc_file_name}" class="btn m-b-xs m-t-sm bg-green mov" title="{lang('download')}" download=""><i class="glyphicon glyphicon-download-alt"></i></a>
                    </div>
                </div>
            {/foreach}
        {/if}
    </div>
        {$ci->pagination->create_links()}
{/block}
{block name="style"}
    {$smarty.block.parent}
    <link rel="stylesheet" href="{$PUBLIC_URL}theme/css/document_materials.css">
{/block}