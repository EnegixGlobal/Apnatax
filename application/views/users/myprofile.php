<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">My Profile</h5>
            </div>
            <div class="card-body">
                <?= form_open_multipart('users/updateprofile/'); ?>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="form-group text-center">
                            <label class="form-label"><strong>Profile Photo</strong></label>
                            <div class="mb-3">
                                <?php
                                $photo_url = !empty($admin['photo']) ? file_url($admin['photo']) : base_url('profileimage/?letter=' . strtoupper(substr($admin['name'], 0, 1)));
                                ?>
                                <img src="<?= $photo_url; ?>" id="photo_preview" class="rounded-circle" style="width: 150px; height: 150px; object-fit: cover; border: 3px solid #ddd; cursor: pointer;" onclick="document.getElementById('photo').click();" alt="Profile Photo">
                                <input type="file" name="photo" id="photo" accept="image/*" style="display: none;" onchange="getPhoto(this, 'photo_preview')">
                                <div class="mt-2">
                                    <small class="text-muted">Click on image to change photo (Max size: 5MB, Formats: JPG, PNG, GIF)</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
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
                <div class="row mb-4">
                    <div class="col-md-12 text-center">
                        <button type="submit" name="updateprofile" class="btn btn-success">
                            <i class="fa fa-upload"></i> Update Profile Photo
                        </button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    function getPhoto(input, previewId){
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

