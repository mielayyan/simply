 {extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
{include file="admin/configuration/system_setting_common.tpl"}
       
  {if empty($api_key)}
	  <div class="alert alert-info">
		 {lang('You_have_not_configured_api_key')}
	   </div>
  {/if}

<div class="panel panel-default">
  <div class="panel-body ">
    <p>
      <b>{lang('api_base_url')}:</b> <span class="bg-light">{$BASE_URL}api</span>
    </p>
    <p>
      <b>{lang('api_doc_link')}:</b> <a class="bg-light" target="_blank" href="https://infinitemlmsoftware.com/docs/integration/rest-api">https://infinitemlmsoftware.com/docs/integration/rest-api</a>
    </p>
    


        {form_open('admin/save_api_key','role="form" class="" method="post"  name="search_mem" id=""')}
          

        
	        <div class="col-sm-4 padding_both">
	        <div class="form-group">
	            
	            <label>{lang('Api_key')}</label>
	            {if empty($api_key)}
	            <input class="form-control" value="" type="text" name="apikey" id="apikey" autocomplete="Off">
	            {else}
	             <input class="form-control" type="text" value="{$api_key}" name="apikey" id="apikey" autocomplete="Off" readonly="true">
	            {/if}
	        </div>
	        </div>
	        <div class="col-sm-2 padding_both_small">
	            <div class="form-group mark_paid">
	                <button class="btn btn-sm btn-info" type="button" name="copy" id="copy" onclick="copyApiKey()" value="{lang('copy')}"><i class="fa fa-clipboard" aria-hidden="true"></i>
</button>
	            </div>
	        </div>
        
        <div class="row">
          <div class="col-sm-12">
            <button class="btn btn-sm btn-primary" type="button" name="generate_key" id="generate_key" value="{lang('Generate')}">{lang('Generate')}</button>
            <button class="btn btn-sm btn-primary" type="submit" name="save_key" id="save_key" value="{lang('Save')}">{lang('Save')}</button>

          </div>
        	
        </div>
        {form_close()}
    </div>
</div>

{/block}

{block name=script}
   <script src="{$PUBLIC_URL}javascript/generate_api_key.js" type="text/javascript" ></script> 
   <script>
function copyApiKey() {
  var copyText = document.getElementById("apikey");
  copyText.select();
  copyText.setSelectionRange(0, 99999)
  document.execCommand("copy");
  // $('#apikey').val('');
}
</script>
{/block}
