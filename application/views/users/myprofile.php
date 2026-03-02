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
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php
                                $name = !empty($admin['name']) ? $admin['name'] : '';
                                $attributes = array("id" => "name", "Placeholder" => "Name", "autocomplete" => "off");
                                echo create_form_input("text", "name", "Name", true, $name, $attributes);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php
                                $email = !empty($admin['email']) ? $admin['email'] : '';
                                $attributes = array("id" => "email", "Placeholder" => "Email", "autocomplete" => "off");
                                echo create_form_input("email", "email", "Email", true, $email, $attributes);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php
                                $mobile = !empty($admin['mobile']) ? $admin['mobile'] : '';
                                $attributes = array("id" => "mobile", "Placeholder" => "Mobile", "autocomplete" => "off", "pattern" => "[0-9]{10}", "title" => "Enter Valid Mobile No.", "maxlength" => "10");
                                echo create_form_input("text", "mobile", "Mobile", true, $mobile, $attributes);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label"><strong>Username:</strong></label>
                            <p><?= !empty($admin['username']) ? $admin['username'] : '-'; ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label class="form-label"><strong>Role:</strong></label>
                            <p><?= !empty($admin['role_name']) ? $admin['role_name'] : ucfirst($admin['role']); ?></p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php
                                $gstin = !empty($admin['gstin']) ? $admin['gstin'] : '';
                                $attributes = array("id" => "gstin", "Placeholder" => "GSTIN", "autocomplete" => "off", "maxlength" => "15", "pattern" => "[0-9]{2}[A-Z]{5}[0-9]{4}[A-Z]{1}[1-9A-Z]{1}Z[0-9A-Z]{1}", "title" => "Enter valid GSTIN (15 characters)");
                                echo create_form_input("text", "gstin", "GSTIN", false, $gstin, $attributes);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <h5 class="mb-3">Address Details</h5>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php
                                $address_text = (!empty($address) && !empty($address['address'])) ? $address['address'] : '';
                                $attributes = array("id" => "address", "Placeholder" => "Address", "autocomplete" => "off", 'rows' => 3);
                                echo create_form_input("textarea", "address", "Address", true, $address_text, $attributes);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php
                                $parent_id = (!empty($address) && !empty($address['parent_id'])) ? $address['parent_id'] : '';
                                $state = (!empty($address) && !empty($address['state'])) ? $address['state'] : '';
                                echo create_form_input('select', 'parent_id', "State", true, $parent_id, ['id' => 'parent_id'], $states);
                                echo create_form_input('hidden', 'state', "", true, $state, ['id' => 'state']);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php
                                $area_id = (!empty($address) && !empty($address['area_id'])) ? $address['area_id'] : '';
                                $district = (!empty($address) && !empty($address['district'])) ? $address['district'] : '';
                                echo create_form_input('select', 'area_id', "District", true, $area_id, ['id' => 'area_id'], $districts);
                                echo create_form_input('hidden', 'district', "", true, $district, ['id' => 'district']);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <?php
                                $pincode = (!empty($address) && !empty($address['pincode'])) ? $address['pincode'] : '';
                                $attributes = array("id" => "pincode", "Placeholder" => "Pincode", "autocomplete" => "off");
                                echo create_form_input("text", "pincode", "Pincode", true, $pincode, $attributes);
                            ?>
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
                            <i class="fa fa-save"></i> Update Profile
                        </button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(e) {
        $('body').on('change', '#parent_id', function() {
            var parent_id = $(this).val();
            var area_id = $('#area_id').data('value');
            var state = $(this).find('option:selected').text();
            $('#state').val(state);
            $.ajax({
                type: "post",
                url: "<?= base_url('masterkey/getdistricts/'); ?>",
                data: {parent_id: parent_id, area_id: area_id},
                success: function(data) {
                    $('#area_id').replaceWith(data);
                    if ($('#area_id').val() == '')
                        $('#district').val('');
                }
            });
        });
        $('form').on('change', '#area_id', function() {
            var district = $(this).find('option:selected').text();
            $('#district').val(district);
        });
    });
    
    function getPhoto(input, previewId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(previewId).src = e.target.result;
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>

