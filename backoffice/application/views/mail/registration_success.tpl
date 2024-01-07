<div style="background: #eee;padding:5px 10px 25px 10px;box-sizing: border-box; display: inline-block; width: inherit">
                <div style=" width:auto; min-width: 250px;box-sizing: border-box; margin:10px 10px ;padding:10px 15px 10px 15px;    background: rgba(255,255,255,0.8);
    border: 1px solid #e6e6e6;
    border-radius: 4px;">
                    <h5 style="font-size: 1.3em;font-weight:500;border-bottom:1px solid #d6d6d6; display: inline-block;padding-bottom: 6px;padding-right: 10px;margin-bottom: 10px;box-sizing: border-box;      margin-top: 0px;  line-height: 1.5;">Login Details</h5>
                    <p style="margin-bottom: 5px;box-sizing: border-box;    margin-top: 0px;    line-height: 1.5;">{lang('Username')} : <span>{$username}</span></p>
                    <p  style="margin-bottom: 5px;box-sizing: border-box;     margin-top: 0px;   line-height: 1.5;">{lang('Password')}  : <span>{$user_password}</span></p>
                    <p  style="margin-bottom: 5px;box-sizing: border-box;    margin-top: 0px;    line-height: 1.5;">{lang('Transaction_Password')} : <span>{$regr["tran_password"]}</span></p>
                
                
                    
                    <p style="margin-bottom: 5px;    margin-top: 0px;">      <a style="text-decoration:none; color: blue; text-decoration: underline;" href="{$ci->PUBLIC_VARS['USER_URL']}/login/index/user/{$ci->ADMIN_USER_NAME}/{$username}" target="_blank">{lang('Login_Link')}</a></p>
                    {if $module_status['replicated_site_status'] == "yes"}
                        <p style="margin-bottom: 5px;box-sizing: border-box;margin-top: 0px;   line-height: 1.5;">
                            <a style="text-decoration:none; color: blue; text-decoration: underline;" href="{$site_url}/replica/{$ci->ADMIN_USER_NAME}/{$username}" target="_blank">
                                {lang('Replicated_Website_Link')}
                            </a>
                        </p>
                    {/if}
                    {if $module_status['lead_capture_status'] == "yes"}
                        <p style="margin-bottom: 5px;margin-top: 0px;box-sizing: border-box; line-height: 1.5;"> 
                            <a style="text-decoration:none; color: blue; text-decoration: underline;" href="{$site_url}/lcp/{$ci->ADMIN_USER_NAME}/{$username}" target="_blank">
                                {lang('LCP_Link')}
                            </a>
                        </p>
                    {/if}
               </div>
            </div>   
