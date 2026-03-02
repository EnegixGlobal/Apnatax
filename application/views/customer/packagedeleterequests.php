            <div class="card">

                <div class="card-body">
                    <?php if (!empty($migration_needed)) { ?>
                        <div class="alert alert-warning">
                            <strong>Migration Required!</strong> The 'request' column does not exist in the service_packages or customer_packages table.
                            Please run the migration file: <code>database_migration_package_delete_request.sql</code>
                        </div>
                    <?php } else { ?>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed" id="table">
                                        <thead>
                                            <tr>
                                                <th>Sl.No.</th>
                                                <th>Package Type</th>
                                                <th>Customer Name</th>
                                                <th>Firm Name</th>
                                                <th>Year</th>
                                                <th>Services/Package</th>
                                                <th>Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            $i = 0;
                                            // Display service packages
                                            if (!empty($packages)) {
                                                foreach ($packages as $single) {
                                                    $i++;
                                                    $service_names = array();
                                                    if (!empty($single['services'])) {
                                                        foreach ($single['services'] as $service) {
                                                            $service_names[] = $service['name'];
                                                        }
                                                    }
                                                    $services_display = !empty($service_names) ? implode(', ', $service_names) : 'N/A';

                                                    // Format year: 20252026 -> 2025-2026
                                                    $year_display = $single['year'];
                                                    if (strlen($year_display) == 8 && is_numeric($year_display)) {
                                                        $year1 = substr($year_display, 0, 4);
                                                        $year2 = substr($year_display, 4, 4);
                                                        $year_display = $year1 . '-' . $year2;
                                                    }
                                            ?>
                                                    <tr>
                                                        <td><?= $i; ?></td>
                                                        <td><strong style="color: #000;">Service Package</strong></td>
                                                        <td><?= $single['customer_name']; ?></td>
                                                        <td><?= !empty($single['firm_name']) ? $single['firm_name'] : 'N/A (ID: ' . $single['firm_id'] . ')'; ?></td>
                                                        <td><?= $year_display; ?></td>
                                                        <td><?= $services_display; ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-success approve" value="<?= md5('package-id-' . $single['id']) ?>" data-type="service">Approve</button>
                                                            <button type="button" class="btn btn-sm btn-danger reject" value="<?= md5('package-id-' . $single['id']) ?>" data-type="service">Reject</button>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                            }
                                            // Display Account Work packages
                                            if (!empty($accountancy_packages)) {
                                                foreach ($accountancy_packages as $single) {
                                                    $i++;
                                                    $package_display = !empty($single['package']) ? $single['package'] : 'N/A';

                                                    // Format year: 20252026 -> 2025-2026
                                                    $year_display = $single['year'];
                                                    if (strlen($year_display) == 8 && is_numeric($year_display)) {
                                                        $year1 = substr($year_display, 0, 4);
                                                        $year2 = substr($year_display, 4, 4);
                                                        $year_display = $year1 . '-' . $year2;
                                                    }
                                                ?>
                                                    <tr>
                                                        <td><?= $i; ?></td>
                                                        <td><strong style="color: #000;">Account Work</strong></td>
                                                        <td><?= !empty($single['name']) ? $single['name'] : 'N/A'; ?></td>
                                                        <td><?= !empty($single['firm_name']) ? $single['firm_name'] : 'N/A'; ?></td>
                                                        <td><?= $year_display; ?></td>
                                                        <td><?= $package_display; ?></td>
                                                        <td>
                                                            <button type="button" class="btn btn-sm btn-success approve" value="<?= md5('package-id-' . $single['id']) ?>" data-type="accountancy">Approve</button>
                                                            <button type="button" class="btn btn-sm btn-danger reject" value="<?= md5('package-id-' . $single['id']) ?>" data-type="accountancy">Reject</button>
                                                        </td>
                                                    </tr>
                                                <?php
                                                }
                                            }
                                            if ($i == 0) {
                                                ?>
                                                <tr>
                                                    <td colspan="7" class="text-center">No delete requests found.</td>
                                                </tr>
                                            <?php
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
                <script>
                    $(document).ready(function(e) {
                        $('body').on('click', '.approve', function() {
                            var id = $(this).val();
                            var packageType = $(this).data('type') || 'service';
                            var packageTypeName = packageType == 'accountancy' ? 'Account Work Package' : 'Package';
                            if (confirm("Confirm Approve " + packageTypeName + " Delete Request?")) {
                                updatepackagerequest(id, 1, packageType);
                            }
                        });
                        $('body').on('click', '.reject', function() {
                            var id = $(this).val();
                            var packageType = $(this).data('type') || 'service';
                            var packageTypeName = packageType == 'accountancy' ? 'Account Work Package' : 'Package';
                            if (confirm("Confirm Reject " + packageTypeName + " Delete Request?")) {
                                updatepackagerequest(id, 0, packageType);
                            }
                        });
                        $('#table').dataTable();
                    });

                    function updatepackagerequest(id, status, packageType) {
                        $.ajax({
                            type: 'post',
                            url: '<?= base_url('customers/updatepackagestatus'); ?>',
                            data: {
                                id: id,
                                status: status,
                                package_type: packageType
                            },
                            success: function(data) {
                                window.location.reload();
                            }
                        });
                    }
                </script>
            </div>