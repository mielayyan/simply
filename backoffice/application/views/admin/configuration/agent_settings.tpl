 {extends file=$BASE_TEMPLATE}

{block name=$CONTENT_BLOCK}
{include file="admin/configuration/system_setting_common.tpl"}
       

<div class="panel panel-default">
  <div class="panel-body ">
    
        {form_open('admin/agent_settings','role="form" class="" method="post"  name="search_mem" id=""')}
          

        
	        <div class="col-sm-4 padding_both">
  	        <div class="form-group">
  	            <label>{lang('max_agent')}</label>
                <input class="form-control" type="text" value="{$max_agent}" name="max_agent" id="max_agent" autocomplete="Off" >
  	            {* {if empty($max_agent)}
  	            <input class="form-control" value="" type="text" name="max_agent" id="max_agent" autocomplete="Off">
  	            {else}
  	             <input class="form-control" type="text" value="{$max_agent}" name="max_agent" id="max_agent" autocomplete="Off" readonly="true">
  	            {/if} *}
  	        </div>
	        </div>
        
        <div class="row">
          <div class="col-sm-12">
            
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
