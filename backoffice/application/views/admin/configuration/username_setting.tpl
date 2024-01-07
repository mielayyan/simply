{extends file=$BASE_TEMPLATE}
{block name = "style"}
<style type="text/css">
.slider-selection {
    position: absolute;
    background-color: #4cc0c1;
    border: 1px solid #4cc0c1;
    border-radius: 5px;
}
.slider-track {
    position: absolute !important;
    cursor: pointer !important;
    background-color: #fff !important;
    border: 6px solid #eee !important;
    border-radius: 5px;
}
.slider.slider-horizontal .slider-track {
  height: 6px;
  width: 100%;
  margin-top: -6px;
  top: 50%;
  left: 0;
}
.slider-handle {
  background-image: linear-gradient(to bottom,#fff 0,#fff 100%);
  border: 1px solid #ccc;

}
  
</style>
{/block}
{block name=$CONTENT_BLOCK}

<div id="span_js_messages" style="display: none;">
    <span id="validate_msg1">{lang('you_must_enter_sender_id')}</span>
    <span id="validate_msg2">{lang('you_must_enter_user_name')}</span>
    <span id="validate_msg3">{lang('you_must_enter_password')}</span>
</div>

{include file="admin/configuration/advanced_settings.tpl"}



<div class="panel panel-default">
  <div class="panel-body">
    <legend><span class="fieldset-legend">{lang('username_setting')}</span></legend>
    {form_open('admin/username_setting', 'role="form" class="form" method="post"  name="signup_settings_form" id="signup_settings_form"')}
    <div class="form-group">
      <label class="required">{lang('username_type')}</label>
      <select class="form-control" id="user_name_type" name="user_name_type">
        <option value="static" {if $username_config["type"]=='static'} selected {/if}>{lang('Static')}</option>
        <option value="dynamic" {if $username_config["type"]=='dynamic'} selected {/if}>{lang('Dynamic')}</option>
      </select>
      {form_error('user_name_type')} </div>
    <!--<div class="form-group" id="length_div" {if $username_config["type"] == "static"} style="display: none;" {/if}>
      <label class="required">{lang('user_name_length')}</label>
      <input type="text" class="form-control" name="length" id="length" value="{$username_config["length"]}">
      {form_error('length')} 
    </div>--> 
    <div class="form-group">
      <label class="required">{lang('user_name_length')}</label><br><br>
      <input id="ex2" type="text" name="length" class="span2" value="" data-slider-min="6" data-slider-max="20" data-slider-step="1" data-slider-value="[{$userNameRange['min']},{$userNameRange['max']}]"/>
      {form_error('length')} 
    </div> 

    <div class="form-group" id="prefix_status_div">
      <div class="checkbox" id="prefix_checkbox">
        <label class="i-checks">
        <input type="checkbox" name="prefix_status" {if $username_config["prefix_status"] == 'yes'} checked {/if}><i></i> {lang('enable_username_prefix')}
        </label>
    </div>
    
    <div class="form-group" id="prefix_div" {if $username_config["type"] == "static" || $username_config["prefix_status"] == "no"} style="display: none;" {/if}>
    <label class="required">{lang('username_prefix')}</label>
    <input type="text" class="form-control" name="prefix" id="prefix" value="{$username_config["prefix"]}" maxlength="8">
    {form_error('prefix')} </div><br>
  <div class="form-group">
    <button type="submit" class="btn btn-sm btn-primary" value="update" name="update" id="update">{lang('update')}</button>
  </div>
  {form_close()} </div>
</div>
</div>

{/block}

{block name=script}

     {$smarty.block.parent}
     <script src="{$PUBLIC_URL}javascript/signup_settings.js"></script>
     <script src="{$PUBLIC_URL}javascript/validate_username_config.js"></script>
     <script src="{$PUBLIC_URL}javascript/jquery-1.10.3ui.min.js" type="text/javascript" ></script>
     <script src="{$PUBLIC_URL}javascript/bootstrap-slider.js" type="text/javascript" ></script>
     <script src="{$PUBLIC_URL}javascript/bootstrap-slider.min.js" type="text/javascript" ></script>
     <script type="text/javascript">
      
     $("#ex2").bootstrapSlider(); 

      $( ".row_position" ).sortable({
          delay: 150,
          stop: function() {
              var selectedData = new Array();
              $('.row_position>tr').each(function() {
                  selectedData.push($(this).attr("id"));
              });
              $('#row_order').val(selectedData);

          }
      });
</script>
{/block}