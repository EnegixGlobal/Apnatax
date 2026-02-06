<div class="card">
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <?= form_open('masterkey/addserviceoption/', 'onSubmit="return validate()"'); ?>
                <div class="form-group row my-2">
                    <label class="col-sm-12 col-form-label">Service</label>
                    <div class="col-sm-12">
                        <select name="service_id" id="service_id" class="form-control" required>
                            <option value="">Select Service</option>
                            <?php
                            if (!empty($services)) {
                                foreach ($services as $service) {
                                    echo '<option value="' . $service['id'] . '">' . $service['name'] . '</option>';
                                }
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label class="col-sm-12 col-form-label">Option Key</label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" name="option_key" id="option_key" placeholder="e.g., audit, non-audit" required>
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label class="col-sm-12 col-form-label">Display Name</label>
                    <div class="col-sm-12">
                        <input type="text" class="form-control" name="display_name" id="display_name" placeholder="e.g., Audit, Non Audit" required>
                        <small class="form-text text-muted">Name shown to users</small>
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label class="col-sm-12 col-form-label">Rate (Price)</label>
                    <div class="col-sm-12">
                        <input type="number" step="0.01" class="form-control" name="rate" id="rate" placeholder="0.00" required>
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label class="col-sm-12 col-form-label">Status</label>
                    <div class="col-sm-12">
                        <div class="form-group m-0">
                            <div class="custom-controls-stacked">
                                <label class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="status" value="1" checked>
                                    <span class="custom-control-label">Active</span>
                                </label>
                                <label class="custom-control custom-radio">
                                    <input type="radio" class="custom-control-input" name="status" value="0">
                                    <span class="custom-control-label">Inactive</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group row my-2">
                    <label class="col-sm-12 col-form-label"></label>
                    <div class="col-sm-12">
                        <input type="hidden" name="id" id="id">
                        <input type="submit" class="btn btn-success waves-effect waves-light" name="addserviceoption" value="Save Option">
                        <button type="button" class="btn btn-danger waves-effect waves-light cancel-btn hidden">Cancel</button>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
            <div class="col-md-8">
                <div class="table-responsive">
                    <table class="table table-condensed" id="table">
                        <thead>
                            <tr>
                                <th>Sl.No.</th>
                                <th>Service</th>
                                <th>Option Key</th>
                                <th>Display Name</th>
                                <th>Rate</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($service_options)) {
                                $i = 0;
                                foreach ($service_options as $option) {
                                    $i++;
                                    $service_name = isset($service_names[$option['service_id']]) ? $service_names[$option['service_id']] : 'N/A';
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= $service_name; ?></td>
                                        <td><?= $option['option_key']; ?></td>
                                        <td><?= $option['display_name']; ?></td>
                                        <td>₹<?= number_format($option['rate'], 2); ?></td>
                                        <td>
                                            <?php if ($option['status'] == 1) { ?>
                                                <span class="badge" style="background-color:#28a745;color:#fff;">Active</span>
                                            <?php } else { ?>
                                                <span class="badge" style="background-color:#dc3545;color:#fff;">Inactive</span>
                                            <?php } ?>

                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-primary edit-btn" value="<?= $option['id'] ?>"><i class="fa fa-edit"></i></button>
                                            <button type="button" class="btn btn-sm btn-danger delete-btn" value="<?= $option['id'] ?>"><i class="fa fa-trash"></i></button>
                                        </td>
                                    </tr>
                            <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        $(document).ready(function(e) {
            $('table').on('click', '.edit-btn', function() {
                $.ajax({
                    type: "post",
                    url: "<?= base_url('masterkey/getserviceoption/'); ?>",
                    data: {
                        id: $(this).val()
                    },
                    dataType: "json",
                    success: function(data) {
                        $('#service_id').val(data.service_id);
                        $('#option_key').val(data.option_key);
                        $('#display_name').val(data.display_name);
                        $('#rate').val(data.rate);
                        $('input[name="status"][value="' + data.status + '"]').prop('checked', true);
                        $('#id').val(data.id);
                        $('.cancel-btn').removeClass('hidden');
                        $('input[name="addserviceoption"]').attr('name', 'updateserviceoption').val('Update Option');
                    }
                });
            });

            $('table').on('click', '.delete-btn', function() {
                if (confirm('Are you sure you want to delete this option?')) {
                    var option_id = $(this).val();
                    $.ajax({
                        type: "post",
                        url: "<?= base_url('masterkey/deleteserviceoption/'); ?>",
                        data: {
                            id: option_id
                        },
                        dataType: "json",
                        success: function(response) {
                            if (response.status) {
                                alert(response.message);
                                location.reload();
                            } else {
                                alert(response.message);
                            }
                        }
                    });
                }
            });

            $('.cancel-btn').click(function() {
                $('#service_id, #option_key, #display_name, #rate, #id').val('');
                $('input[name="status"][value="1"]').prop('checked', true);
                $('.cancel-btn').addClass('hidden');
                $('input[name="updateserviceoption"]').attr('name', 'addserviceoption').val('Save Option');
            });

            $('#table').dataTable();
        });

        function validate() {
            return true;
        }
    </script>
</div>