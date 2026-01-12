
        <!-- PAGE -->
        <div class="page">
            <div class="">

                <!-- CONTAINER OPEN -->
                <div class="col col-login mx-auto">
                    <div class="text-center">
                        <img src="<?= file_url('assets/images/logo-lg.png'); ?>" class="header-brand-img" alt="">
                    </div>
                </div>

                <div class="row m-0 mt-7">
                    <div class="col-12 col-md-4 col-lg-5"></div>
                    <div class="col-12 col-md-4 col-lg-2">
                        <?= form_open('login/validatelogin/','class="login100-form validate-form"'); ?>
                            <span class="login100-form-title pb-0 text-white">
                                Login
                            </span>
                            <div class="panel panel-primary">
                                <div class="panel-body tabs-menu-body p-0 pt-2">
                                    <div class="tab-content">
                                        <div class="tab-pane active" id="tab5">
                                            <div class="wrap-input100 validate-input input-group" data-bs-validate="Valid email is required: ex@abc.xyz">
                                                <a href="javascript:void(0)" class="input-group-text bg-white text-muted">
                                                    <i class="zmdi zmdi-email text-muted" aria-hidden="true"></i>
                                                </a>
                                                <input type="text" name="username" placeholder="Username" class="input100 border-start-0 form-control ms-0" required />
                                            </div>
                                            <div class="wrap-input100 validate-input input-group" id="Password-toggle">
                                                <a href="javascript:void(0)" class="input-group-text bg-white text-muted">
                                                    <i class="zmdi zmdi-eye text-muted" aria-hidden="true"></i>
                                                </a>
                                                <input type="password" name="password" placeholder="Password" class="input100 border-start-0 form-control ms-0" required/>
                                            </div>
                                            <div class="text-end pt-4 d-none">
                                                <p class="mb-0"><a href="forgot-password.html" class="text-primary ms-1">Forgot Password?</a></p>
                                            </div>
                                            <div class="text-danger text-center" <?= ($this->session->flashdata('logerr')!==NULL)?'style="background-color: #f0ffff; padding: 5px;"':'' ?>><?= $this->session->flashdata('logerr'); ?></div>
                                            <div class="container-login100-form-btn">
                                                <button type="submit" name="login" class="login100-form-btn btn-primary">Login</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?= form_close(); ?>
                    </div>
                </div>
                <!-- CONTAINER CLOSED -->
            </div>
        </div>
        <!-- End PAGE -->
