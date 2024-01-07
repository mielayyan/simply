                  {* model for change password *}
                        <div class="modal fade update_pv_profile" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                          <div class="modal-dialog modal-lg">
                            <div class="modal-dialog mt-6" role="document">
                              <div class="modal-content border-0">
                                 <div class="gradient-bar"></div>
                                <div class="modal-header px-5 position-relative modal-shape-header bg-shape">
                                    <div class="text-center hiddden_on_success">
                                    <h3><i class="fa fa-pencil-square-o fa-3x"></i></h3>
                                    <h2 class="text-center">{lang('update_pv')}</h2>
                                   
                                  </div>
                                  {* <button class="btn-close btn-close-white position-absolute top-0 right-0 mt-2 mr-2" data-dismiss="modal" aria-label="Close"></button> *}
                                </div>
                                {if $user_type=='user'}﻿
                                <div class="text-center mt-6">
                                        <label class="form-label" for="modal-auth-name">{lang('user_name')} : {$user_name}
                                        </label>
                                </div>
                                {/if}
                                <div class="modal-body py-8 px-8 text-left hidden-password-form" style="padding-top: 0px">
                                  {form_open('', 'role="form" class="" id="update_pv_profile" name="update_pv_profile" novalidate="false"')}

                                  <div class="modal-body py-8 px-8">
                                      <div class="no-radius alt">
                                        
                                        <div>
                                            <input class="form-control"  type="text" name ="new_pv" id ="new_pv" placeholder="{lang('enter_pv')} " autocomplete="Off" value={set_value('new_pv')}>
                                         
                                        {form_error('new_pv')}
                                        </div>
                                    
                                    </div>
                                    
                                  </div>

                                   <div class="modal-body py-8 px-8">
                                    <div class="mb-3 text-center">
                                      
                                        <button  class="btn btn-primary d-block w-100 mt-3" name="add_pv" id="add_pv"  type="button" value="{lang('add_pv')}">{lang('add_pv')}</button>

                                        <button  class="btn btn-primary d-block w-100 mt-3" name="deduct_pv" id="deduct_pv" type="button" value="{lang('deduct_pv')}" > {lang('deduct_pv')}</button>

                                        <button class="btn button-no d-block w-100 mt-3 " id="cancel_update_pv" data-dismiss="modal">{lang('cancel')}</button>
                                      </div>
                                   </div>
                                   
                                   {form_close()}
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        {* end of change password model *}