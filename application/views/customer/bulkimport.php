<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Bulk Customer Import</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <h6><i class="fa fa-info-circle"></i> Instructions:</h6>
                    <ul class="mb-0">
                        <li>Download the CSV template below</li>
                        <li>Fill in customer details (Name, Mobile, Email, Address, State, District, Pincode)</li>
                        <li>Mobile number will be used as username and password</li>
                        <li>Upload the CSV file (Max 200 customers per file)</li>
                        <li>After import, you can download the login credentials</li>
                    </ul>
                </div>

                <div class="row mb-4">
                    <div class="col-md-6">
                        <a href="<?= base_url('customers/downloadtemplate/'); ?>" class="btn btn-primary">
                            <i class="fa fa-download"></i> Download CSV Template
                        </a>
                    </div>
                </div>

                <?= form_open_multipart('customers/processbulkimport/'); ?>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Upload CSV File</strong> <span class="text-danger">*</span></label>
                            <input type="file" name="csv_file" id="csv_file" accept=".csv" class="form-control" required>
                            <small class="text-muted">Only CSV files are allowed. Maximum file size: 5MB</small>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label"><strong>Default Password</strong></label>
                            <input type="text" name="default_password" id="default_password" class="form-control"
                                placeholder="Leave empty to use mobile number as password" value="">
                            <small class="text-muted">If left empty, mobile number will be used as password for all customers</small>
                        </div>
                    </div>
                </div>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <button type="submit" name="bulkimport" class="btn btn-success">
                            <i class="fa fa-upload"></i> Import Customers
                        </button>
                        <a href="<?= base_url('customers/'); ?>" class="btn btn-secondary">
                            <i class="fa fa-times"></i> Cancel
                        </a>
                    </div>
                </div>
                <?= form_close(); ?>

                <?php if (!empty($import_results)) { ?>
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card border">
                                <div class="card-header bg-primary text-white">
                                    <h6 class="mb-0">Import Results</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-success"><?= $import_results['success_count']; ?></h4>
                                                <p class="mb-0">Successfully Imported</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-danger"><?= $import_results['error_count']; ?></h4>
                                                <p class="mb-0">Failed</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-info"><?= $import_results['duplicate_count']; ?></h4>
                                                <p class="mb-0">Duplicates Skipped</p>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="text-center">
                                                <h4 class="text-warning"><?= $import_results['total_count']; ?></h4>
                                                <p class="mb-0">Total Processed</p>
                                            </div>
                                        </div>
                                    </div>

                                    <?php if (!empty($import_results['errors'])) { ?>
                                        <div class="alert alert-danger">
                                            <h6>Errors:</h6>
                                            <ul class="mb-0">
                                                <?php foreach ($import_results['errors'] as $error) { ?>
                                                    <li><?= $error; ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    <?php } ?>

                                    <?php if (!empty($import_results['credentials']) && $import_results['success_count'] > 0) { ?>
                                        <div class="mt-3">
                                            <a href="<?= base_url('customers/downloadcredentials/'); ?>" class="btn btn-success">
                                                <i class="fa fa-download"></i> Download Login Credentials (CSV)
                                            </a>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('form').on('submit', function(e) {
            var fileInput = $('#csv_file');
            if (fileInput[0].files.length === 0) {
                alert('Please select a CSV file to upload!');
                e.preventDefault();
                return false;
            }

            var file = fileInput[0].files[0];
            if (file.size > 5 * 1024 * 1024) { // 5MB
                alert('File size exceeds 5MB limit!');
                e.preventDefault();
                return false;
            }
        });
    });
</script>