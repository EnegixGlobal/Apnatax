<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">My Profile</h5>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Name:</strong></label>
                            <p><?= !empty($employee['name']) ? $employee['name'] : '-'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Mobile:</strong></label>
                            <p><?= !empty($employee['mobile']) ? $employee['mobile'] : '-'; ?></p>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Email:</strong></label>
                            <p><?= !empty($employee['email']) ? $employee['email'] : '-'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Date of Birth:</strong></label>
                            <p><?= !empty($employee['dob']) ? date('d-m-Y', strtotime($employee['dob'])) : '-'; ?></p>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>PAN Number:</strong></label>
                            <p><?= !empty($employee['pan']) ? $employee['pan'] : '-'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Aadhar Number:</strong></label>
                            <p><?= !empty($employee['aadhar']) ? $employee['aadhar'] : '-'; ?></p>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label"><strong>Address:</strong></label>
                            <p><?= !empty($employee['address']) ? $employee['address'] : '-'; ?></p>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>State:</strong></label>
                            <p><?= !empty($employee['state']) ? $employee['state'] : '-'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>District:</strong></label>
                            <p><?= !empty($employee['district']) ? $employee['district'] : '-'; ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">My Documents</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card border">
                            <div class="card-body text-center">
                                <h6 class="card-title">PAN Card</h6>
                                <?php
                                if (!empty($employee['pan_file'])) {
                                    echo '<p class="text-success"><i class="fa fa-check-circle"></i> Document Uploaded</p>';
                                    echo '<div class="mt-3">';
                                    echo '<a href="' . file_url($employee['pan_file']) . '" target="_blank" class="btn btn-sm btn-info me-2"><i class="fa fa-eye"></i> View</a>';
                                    echo '<a href="' . base_url('employees/downloaddocument/pan') . '" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download</a>';
                                    echo '</div>';
                                } else {
                                    echo '<p class="text-muted"><i class="fa fa-times-circle"></i> No Document Uploaded</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border">
                            <div class="card-body text-center">
                                <h6 class="card-title">Aadhar Card</h6>
                                <?php
                                if (!empty($employee['aadhar_file'])) {
                                    echo '<p class="text-success"><i class="fa fa-check-circle"></i> Document Uploaded</p>';
                                    echo '<div class="mt-3">';
                                    echo '<a href="' . file_url($employee['aadhar_file']) . '" target="_blank" class="btn btn-sm btn-info me-2"><i class="fa fa-eye"></i> View</a>';
                                    echo '<a href="' . base_url('employees/downloaddocument/aadhar') . '" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download</a>';
                                    echo '</div>';
                                } else {
                                    echo '<p class="text-muted"><i class="fa fa-times-circle"></i> No Document Uploaded</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border">
                            <div class="card-body text-center">
                                <h6 class="card-title">Terms & Conditions</h6>
                                <?php
                                if (!empty($employee['terms_file'])) {
                                    echo '<p class="text-success"><i class="fa fa-check-circle"></i> Document Uploaded</p>';
                                    echo '<div class="mt-3">';
                                    echo '<a href="' . file_url($employee['terms_file']) . '" target="_blank" class="btn btn-sm btn-info me-2"><i class="fa fa-eye"></i> View</a>';
                                    echo '<a href="' . base_url('employees/downloaddocument/terms') . '" class="btn btn-sm btn-success"><i class="fa fa-download"></i> Download</a>';
                                    echo '</div>';
                                } else {
                                    echo '<p class="text-muted"><i class="fa fa-times-circle"></i> No Document Uploaded</p>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

