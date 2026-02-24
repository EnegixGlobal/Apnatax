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
                            <p><?= !empty($admin['name']) ? $admin['name'] : '-'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Username:</strong></label>
                            <p><?= !empty($admin['username']) ? $admin['username'] : '-'; ?></p>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Email:</strong></label>
                            <p><?= !empty($admin['email']) ? $admin['email'] : '-'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Mobile:</strong></label>
                            <p><?= !empty($admin['mobile']) ? $admin['mobile'] : '-'; ?></p>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Role:</strong></label>
                            <p><?= !empty($admin['role_name']) ? $admin['role_name'] : ucfirst($admin['role']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Status:</strong></label>
                            <p>
                                <?php
                                if ($admin['status'] == 1) {
                                    echo '<span class="badge bg-success">Active</span>';
                                } elseif ($admin['status'] == 0) {
                                    echo '<span class="badge bg-warning">Pending</span>';
                                } else {
                                    echo '<span class="badge bg-danger">Blocked</span>';
                                }
                                ?>
                            </p>
                        </div>
                    </div>
                </div>
                <?php if (!empty($admin['created_on'])) { ?>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Account Created On:</strong></label>
                            <p><?= date('d-m-Y H:i:s', strtotime($admin['created_on'])); ?></p>
                        </div>
                    </div>
                    <?php if (!empty($admin['updated_on'])) { ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label"><strong>Last Updated On:</strong></label>
                            <p><?= date('d-m-Y H:i:s', strtotime($admin['updated_on'])); ?></p>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>

