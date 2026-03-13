                            <div class="card">
                                <div class="card-body">
                                    <div class="row mb-4">
                                        <div class="col-md-12">
                                            <h4>Certificates</h4>
                                            <p class="text-muted">Certificates uploaded by admin will be displayed here.</p>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>TDS Certificate</strong></label>
                                                <?php if(!empty($kyc) && !empty($kyc['tds_certificate'])){ ?>
                                                    <div class="mt-2">
                                                        <a href="<?= $kyc['tds_certificate'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fa fa-eye"></i> View TDS Certificate
                                                        </a>
                                                        <a href="<?= base_url('profile/download_certificate/tds_certificate') ?>" class="btn btn-sm btn-success">
                                                            <i class="fa fa-download"></i> Download TDS Certificate
                                                        </a>
                                                    </div>
                                                <?php } else { ?>
                                                    <p class="text-muted">TDS Certificate not uploaded yet.</p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>GST Certificate</strong></label>
                                                <?php if(!empty($kyc) && !empty($kyc['gst_certificate'])){ ?>
                                                    <div class="mt-2">
                                                        <a href="<?= $kyc['gst_certificate'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fa fa-eye"></i> View GST Certificate
                                                        </a>
                                                        <a href="<?= base_url('profile/download_certificate/gst_certificate') ?>" class="btn btn-sm btn-success">
                                                            <i class="fa fa-download"></i> Download GST Certificate
                                                        </a>
                                                    </div>
                                                <?php } else { ?>
                                                    <p class="text-muted">GST Certificate not uploaded yet.</p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>Company Registration Certificate</strong></label>
                                                <?php if(!empty($kyc) && !empty($kyc['audit_report'])){ ?>
                                                    <div class="mt-2">
                                                        <a href="<?= $kyc['audit_report'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fa fa-eye"></i> View Company Registration Certificate
                                                        </a>
                                                        <a href="<?= base_url('profile/download_certificate/audit_report') ?>" class="btn btn-sm btn-success">
                                                            <i class="fa fa-download"></i> Download Company Registration Certificate
                                                        </a>
                                                    </div>
                                                <?php } else { ?>
                                                    <p class="text-muted">Company Registration Certificate not uploaded yet.</p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="form-group">
                                                <label><strong>DIN Certificate</strong></label>
                                                <?php if(!empty($kyc) && !empty($kyc['income_tax_certificate'])){ ?>
                                                    <div class="mt-2">
                                                        <a href="<?= $kyc['income_tax_certificate'] ?>" target="_blank" class="btn btn-sm btn-info">
                                                            <i class="fa fa-eye"></i> View DIN Certificate
                                                        </a>
                                                        <a href="<?= base_url('profile/download_certificate/income_tax_certificate') ?>" class="btn btn-sm btn-success">
                                                            <i class="fa fa-download"></i> Download DIN Certificate
                                                        </a>
                                                    </div>
                                                <?php } else { ?>
                                                    <p class="text-muted">DIN Certificate not uploaded yet.</p>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

