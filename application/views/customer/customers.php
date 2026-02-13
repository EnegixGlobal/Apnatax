            <div class="card">

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">Customers List</h5>
                                <?php
                                // Only show GST toggle for admin
                                $CI =& get_instance();
                                if ($CI->session->role == 'admin' || $CI->session->role == 'superadmin') {
                                ?>
                                <div>
                                    <label class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="bulk_gst_toggle"
                                            <?php
                                            // Check if all customers have GST enabled
                                            $all_gst_enabled = true;
                                            if (!empty($customers)) {
                                                foreach ($customers as $customer) {
                                                    if (empty($customer['gst_enabled']) || $customer['gst_enabled'] != 1) {
                                                        $all_gst_enabled = false;
                                                        break;
                                                    }
                                                }
                                            } else {
                                                $all_gst_enabled = false;
                                            }
                                            echo $all_gst_enabled ? 'checked' : '';
                                            ?>>
                                        <span class="form-check-label" for="bulk_gst_toggle">
                                            <strong>Enable GST (18%) for All Customers</strong>
                                        </span>
                                    </label>
                                </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-condensed" id="table">
                                    <thead>
                                        <tr>
                                            <th>Sl.No.</th>
                                            <th>Name</th>
                                            <th>Mobile</th>
                                            <th>E-Mail</th>
                                            <th>State</th>
                                            <th>District</th>
                                            <th>Added By</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($customers)) {
                                            $i = 0;
                                            foreach ($customers as $single) {
                                                $i++;
                                        ?>
                                                <tr>
                                                    <td><?= $i; ?></td>
                                                    <td><?= $single['name']; ?></td>
                                                    <td><?= $single['mobile']; ?></td>
                                                    <td><?= $single['email']; ?></td>
                                                    <td><?= $single['state']; ?></td>
                                                    <td><?= $single['district']; ?></td>
                                                    <td><?= empty($single['user_name']) ? '-' : $single['user_name']; ?></td>
                                                    <td>
                                                        <a href="<?= base_url('customers/editcustomer/' . md5($single['id'])); ?>" class="btn btn-sm btn-primary"><i class="fa fa-edit"></i></a>
                                                        <a href="<?= base_url('customers/kycdetails/' . md5($single['id'])); ?>" class="btn btn-sm btn-info"><i class="fa fa-eye"></i> View KYC</a>
                                                        <a href="<?= base_url('customers/uploadolddata/' . md5($single['id'])); ?>" class="btn btn-sm btn-success"><i class="fa fa-upload"></i> Upload Old Data</a>
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
                        $('#name').keyup(function() {
                            //if($('#id').val()==''){
                            var name = $(this).val();
                            $.ajax({
                                type: "POST",
                                url: "<?= base_url('masterkey/getslug/'); ?>",
                                data: {
                                    name: name
                                },
                                success: function(data) {
                                    $('#slug').val(data);
                                }
                            });
                            //}
                        });
                        $('#table').dataTable();

                        // Bulk GST Toggle Handler - Only for admin
                        <?php
                        // Only load GST toggle handler for admin
                        $CI =& get_instance();
                        if ($CI->session->role == 'admin' || $CI->session->role == 'superadmin') {
                        ?>
                        $('#bulk_gst_toggle').change(function() {
                            var isEnabled = $(this).is(':checked');
                            var action = isEnabled ? 'enable' : 'disable';

                            if (confirm('Are you sure you want to ' + action + ' GST (18%) for ALL customers? This will affect all customers in the system.')) {
                                $.ajax({
                                    type: "POST",
                                    url: "<?= base_url('customers/bulkgsttoggle/'); ?>",
                                    data: {
                                        enable: isEnabled ? 1 : 0
                                    },
                                    dataType: 'json',
                                    success: function(response) {
                                        if (response.status === true) {
                                            alertify.success(response.message);
                                            // Reload page after 1 second to reflect changes
                                            setTimeout(function() {
                                                location.reload();
                                            }, 1000);
                                        } else {
                                            alertify.error(response.message || 'An error occurred');
                                            // Revert checkbox state
                                            $('#bulk_gst_toggle').prop('checked', !isEnabled);
                                        }
                                    },
                                    error: function() {
                                        alertify.error('An error occurred while updating GST settings');
                                        // Revert checkbox state
                                        $('#bulk_gst_toggle').prop('checked', !isEnabled);
                                    }
                                });
                            } else {
                                // Revert checkbox state if user cancels
                                $(this).prop('checked', !isEnabled);
                            }
                        });
                        <?php
                        }
                        ?>
                    });

                    function getPhoto(input) {

                    }
                </script>
            </div>