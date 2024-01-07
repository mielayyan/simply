{* model for change password *}
                        <div class="modal fade change_user_password" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-dialog mt-6" role="document">
                              <div class="modal-content border-0">
                                 <div class="gradient-bar"></div>
                                <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                                    <div class="text-center hiddden_on_success">
                                    <h3><i class="fa fa-lock fa-3x"></i></h3>
                                    <h2 class="text-center">{lang('change_password')}?</h2>
                                    
                                  </div>
                                  {* <button class="btn-close btn-close-white position-absolute top-0 right-0 mt-2 mr-2" data-dismiss="modal" aria-label="Close"></button> *}
                                </div>
                                <div class="modal-body py-8 px-8 text-left hidden-password-form">
                                  {form_open('', 'role="form" class="" id="change_login_user_pass" name="change_pass_admin" novalidate="false"')}

                                  <div class="modal-body py-8 px-8">
                                      <div class="mb-3">
                                        <label class="form-label" for="modal-auth-name">{lang('current_password')}
                                        </label>
                                        <input class="form-control" name="current_pwd_user" type="password" id="current_pwd_user"  autocomplete="Off" required="required">
                                      </div>
                          
                                      <div class="mb-3 ">
                                        <label class="form-label" for="modal-auth-password">{lang('new_password')}
                                        </label>
                                        <input class="form-control act-pswd-popover" name="new_pwd_user" type="password" id="new_pwd_user" size="20"  autocomplete="Off">
                                      </div>
                                      <div class="mb-3 ">
                                        <label class="form-label" for="modal-auth-confirm-password">{lang('confirm_password')}
                                        </label>
                                        <input class="form-control" name="confirm_pwd_user" type="password" id="confirm_pwd_user" size="20"  autocomplete="Off">
                                        <span id='message'></span>
                                      </div>
                                    
                                  </div>


                                   <div class="modal-body py-8 px-8">
                                    <div class="mb-3 text-center">
                                        <button class="btn button-no d-block w-100 mt-3 " id="cancel_change_login_passcod" data-dismiss="modal">{lang('cancel')}</button>
                                        {* <button  class="btn btn-primary d-block w-100 mt-3" type="submit" id="user_login_pass_change" name="submit">{lang('change_password')}</button> *}
                                        <button  class="btn btn-primary d-block w-100 mt-3" type="submit" id="user_login_pass_change" name="submit">{lang('change_password')}</button>
                                      </div>
                                   </div>
                                   
                                   {form_close()}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        {* end of change password model *}

                        {* user password success alert *}
                        <div class="modal fade user_password_success_msg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-dialog mt-6" role="document">
                              <div class="modal-content border-0">
                                 <div class="gradient-bar"></div>
                                <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                                    <div class="text-center ">
                                    <h3><i class="fa fa-check fa-3x sucess_msg" ></i></h3>
                                    <h2 class="text-center">{lang('success')}</h2>
                                    <p>{lang('password_updated_successfully')}</p>
                                  </div>
                                </div>
                                <div class="modal-body py-8 px-8 text-left ">
                                  

                                   <div class="modal-body py-8 px-8">
                                    <div class="mb-3 text-center">
                                         <button class="btn button-no d-block w-100 mt-3 " data-dismiss="modal">{lang('close')}</button>
                                        {* <a href="{BASE_URL}login/logout"  class="btn btn-primary d-block w-100 mt-3" >{lang('logout')}</a> *}

                                      </div>
                                   </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        {* end of user password success alert *}


                        {* change transaction password *}
                        {* <a href="{BASE_URL}/user/change_passcode" class="pswRest">{lang('change_transaction_password')}</a> *}
                       

                        {* model for change transaction password *}
                        <div class="modal fade change_transaction_password" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-dialog mt-6" role="document">
                              <div class="modal-content border-0">
                                 <div class="gradient-bar"></div>
                                <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                                    <div class="text-center">
                                    <h3><i class="fa fa-key fa-3x"></i></h3>
                                    <h2 class="text-center">{lang('change_transaction_password')}?</h2>
                                    
                                  </div>
                                  {* <button class="btn-close btn-close-white position-absolute top-0 right-0 mt-2 mr-2" data-dismiss="modal" aria-label="Close"></button> *}
                                </div>
                                <div class="modal-body py-8 px-8 text-left hidden-password-form">
                                {form_open('', 'role="form" class="" id="change_user_trans_pass" name="change_user_trans_pass" novalidate="false"')}
                                <div class="modal-body py-8 px-8">
                                      <div class="mb-3">
                                        <label class="form-label" for="modal-auth-name">{lang('current_password')}
                                        </label>
                                        <input class="form-control" name="current_tarns_pwd_user" type="password" id="current_tarns_pwd_user"  autocomplete="Off" required="required">
                                      </div>
                          
                                      <div class="mb-3 ">
                                        <label class="form-label" for="modal-auth-password">{lang('new_password')}
                                        </label>
                                        <input class="form-control act-pswd-popover" name="new_tarns_pwd_user" type="password" id="new_tarns_pwd_user" size="20"  autocomplete="Off">
                                      </div>
                                      <div class="mb-3 ">
                                        <label class="form-label" for="modal-auth-confirm-password">{lang('confirm_password')}
                                        </label>
                                        <input class="form-control" name="confirm_tarns_pwd_user" type="password" id="confirm_tarns_pwd_user" size="20"  autocomplete="Off">
                                        <span id='message'></span>
                                      </div>
                                    
                                  </div>
                                 <div class="modal-body py-8 px-8">
                                    <div class="mb-3 text-center">
                                      <button class="btn button-no d-block w-100 mt-3 " id="cancel_change_trans_passcod" data-dismiss="modal">{lang('cancel')}</button>
                                      <button id="user_trans_pass_change" class="btn btn-primary d-block w-100 mt-3" type="submit" name="submit">{lang('change_password')}</button></div>
                                      {* forget password *}
                                      <div class="modal-body text-center text-danger"><a  data-dismiss="modal" data-toggle="modal" data-backdrop="static" data-keyboard="false" data-target=".forget_transaction_pass" ><h4>{lang('forgot_transaction_password')} ?</h4></a></div>
                                 </div>
                                 {form_close()}
                              </div>

                            </div>
                            </div>
                          </div>
                        </div>
                        {* end of change transaction password model *}


                         {* Forget transaction password model *}
                          <div class="modal fade forget_transaction_pass" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-dialog mt-6" role="document">
                              <div class="modal-content border-0">
                                 <div class="gradient-bar"></div>
                                <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                                    <div class="text-center ">
                                    
                                      <h3><i class="fa fa-envelope-open fa-3x" ></i></h3>
                                    <h2 class="text-center">{lang('forgot_transaction_password')}</h2>
                                    <p>{lang('mail_is_send_and_follow_instruction')}</p>
                                  </div>
                                </div>
                                <div class="modal-body py-8 px-8 text-left ">
                                  {form_open('', 'class="" id="forgot_trans_password" name="forgot_trans_password" method="post" onload="onloadCaptcha();"')}
                                    <div class="form-group img_wdth_capcha">
                                      <img style="border-radius:0" src="{$BASE_URL}captcha/load_captcha/admin" id="captcha" />
                                    </div>
                                    <div class="form-group">
                                       <a href="#" onclick="
                                          document.getElementById('captcha').src = '{$BASE_URL}captcha/load_captcha/admin/' + Math.random();
                                          document.getElementById('captcha-form').focus();"
                                          id="change-image" class="color">{lang('not_readable')}</a> 
                                       <input type="text" class="form-control" style="width:100%;" name="captcha_form" id="captcha_form" autocomplete="off" tabindex="3" />
                                       <font color="red">{form_error('captcha')}</font>
                                    </div>

                                   <div class="modal-body py-8 px-8">
                                    <div class="mb-3 text-center">
                                         <button class="btn button-no d-block w-100 mt-3 " id="cancel_forgot_passcod" data-dismiss="modal">{lang('cancel')}</button>
                                         <button type="submit" class="btn btn-primary" name="forgot_password_submit"  id="forgot_password_submit"  tabindex="4" value="{lang('send_request')}" tabindex="4">{lang('send_request')}</button>

                                      </div>
                                   </div>
                                   {form_close()}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      {* end of forget transaction password modal *}