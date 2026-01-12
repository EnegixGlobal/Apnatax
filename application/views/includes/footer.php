                                <?php if(empty($nocard)){ ?>

                                </div>
                                <?php } ?>

                            </div>
                        </div>
                    </div>
                    <!-- CONTAINER CLOSED -->
                </div>
            </div>
            <!--app-content closed-->
        </div>
        <input type="hidden" id="base_url" value="<?= base_url(); ?>">
        <?php $this->load->view('includes/right-sidebar'); ?>
        <?php $this->load->view('includes/country-modal'); ?>
        <!-- FOOTER -->
        <footer class="footer">
            <div class="container">
                <div class="row align-items-center flex-row-reverse">
                    <div class="col-md-12 col-sm-12 text-center">
                        Copyright © <span id="year"></span> <?php /*Designed & Developed by <a href="https://tripledotss.com/"  class="text-danger"> Tripledots Software Services Pvt. Ltd. </a> */ ?>  All rights reserved.
                    </div>
                </div>
            </div>
        </footer>
        <!-- FOOTER CLOSED -->
    </div>
