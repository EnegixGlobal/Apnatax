<?php
if (!empty($package)) {
    $name = $package['package_id'] == 1 ? 'Accountancy Prime' : 'Accountancy Premium';
    $packages = $this->master->getpackages(['name' => $name]);
}
?>
<div class="card">

    <div class="card-body">
        <?php
        if (!empty($package)) {
        ?>
            <div class="row">
                <div class="col-md-8">
                    <h3 class="lead"><?= $name ?></h3>
                    <div class="table-responsive">
                        <table class="table table-bordered" id="package-table">
                            <thead>
                                <tr>
                                    <th>Turnover</th>
                                    <th>Rate</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if (!empty($packages)) {
                                    foreach ($packages as $package) {
                                ?>
                                        <tr class="<?= generate_slug($package['name']); ?> package">
                                            <td><?= $package['remarks']; ?></td>
                                            <td><?= $package['rate']; ?></td>
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
        <?php
        }
        ?>
        <div class="row">
            <div class="col-12">
                <?= form_open('package/savepackage'); ?>
                <table class="table table-bordered" id="service-table">
                    <thead>
                        <tr>
                            <th>Service</th>
                            <?php /*?><th>Type</th><?php */ ?>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $required = true;
                        $is_readonly = !empty($has_purchases); // Make read-only if purchases exist

                        if (!empty($service_package)) {
                            $service_ids = explode(',', $service_package['service_ids']);
                            foreach ($service_ids as $service_id) {
                                // Get service name for display
                                $service_info = $this->master->getservices(['id' => $service_id], 'single');
                                $service_name = !empty($service_info) ? $service_info['name'] : 'Service #' . $service_id;
                        ?>
                                <tr>
                                    <td>
                                        <?php if ($is_readonly) { ?>
                                            <input type="text" class="form-control" value="<?= htmlspecialchars($service_name); ?>" readonly>
                                            <input type="hidden" name="service_id[]" value="<?= $service_id; ?>">
                                        <?php } else { ?>
                                            <?= create_form_input('select', 'service_id[]', '', $required, $service_id, ['class' => 'service_id'], service_dropdown()); ?>
                                        <?php } ?>
                                    </td>
                                    <?php /*?><td>
                                            <?= create_form_input('select','type[]','',true,'',['class'=>'type'],array(''=>'Select Type')); ?>
                                        </td><?php */ ?>
                                    <td>
                                        <?php if (!$is_readonly) { ?>
                                            <button type="button" class="btn btn-sm btn-danger del-btn"><i class="fa fa-trash"></i> Delete</button>
                                        <?php } else { ?>
                                            <span class="badge bg-success">Purchased</span>
                                        <?php } ?>
                                    </td>
                                </tr>
                            <?php
                            }
                            $required = false;

                            // Always show add row - allow adding new services even if purchases exist
                            ?>
                            <tr>
                                <td>
                                    <?= create_form_input('select', 'service_id[]', '', false, '', ['class' => 'service_id'], service_dropdown()); ?>
                                </td>
                                <?php /*?><td>
                                        <?= create_form_input('select','type[]','',true,'',['class'=>'type'],array(''=>'Select Type')); ?>
                                    </td><?php */ ?>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info add-btn"><i class="fa fa-plus"></i> Add</button>
                                </td>
                            </tr>
                        <?php
                        } else {
                        ?>
                            <tr>
                                <td>
                                    <?= create_form_input('select', 'service_id[]', '', $required, '', ['class' => 'service_id'], service_dropdown()); ?>
                                </td>
                                <?php /*?><td>
                                            <?= create_form_input('select','type[]','',true,'',['class'=>'type'],array(''=>'Select Type')); ?>
                                        </td><?php */ ?>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info add-btn"><i class="fa fa-plus"></i> Add</button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php if (!empty($has_purchases)) { ?>
                    <div class="alert alert-info mt-3">
                        <i class="fa fa-info-circle"></i> <strong>Note:</strong> Purchased services cannot be removed, but you can add new services to your package.
                    </div>
                <?php } ?>
                <?php
                if (empty($service_package)) {
                ?>
                    <button type="submit" name="savepackage" class="btn btn-sm btn-success">Save Package</button>
                <?php
                } else {
                ?>
                    <button type="submit" name="savepackage" class="btn btn-sm btn-primary">Update Package</button>
                <?php
                }
                ?>
                <?= form_close(); ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function(e) {
            $('body').on('click', '.add-btn', function() {
                var row = $(this).closest('tr').clone();
                $(row).find('.add-btn').html('<i class="fa fa-trash"></i> Remove');
                $(row).find('.add-btn').removeClass('add-btn btn-info').addClass('remove-btn btn-danger');
                $(row).find('.form-control').val('');
                $('#service-table tbody').append(row);
            });
            $('body').on('click', '.remove-btn', function() {
                // Don't allow removing if it's the only row with a service selected
                var totalRows = $('#service-table tbody tr').length;
                var rowsWithService = $('#service-table tbody tr').filter(function() {
                    return $(this).find('.service_id').val() != '';
                }).length;

                if (totalRows <= 1 || (rowsWithService <= 1 && $(this).closest('tr').find('.service_id').val() != '')) {
                    alert('You must have at least one service in your package!');
                    return false;
                }
                $(this).closest('tr').remove();
            });
            $('body').on('click', '.del-btn', function() {
                // Check if this is the last service
                var totalRows = $('#service-table tbody tr').length;
                var rowsWithService = $('#service-table tbody tr').filter(function() {
                    return $(this).find('.service_id').val() != '' && !$(this).find('.service_id').is(':disabled');
                }).length;

                if (rowsWithService <= 1) {
                    alert('You must have at least one service in your package!');
                    return false;
                }

                var service_id = $(this).closest('tr').find('.service_id').val();
                $(this).closest('tr').find('.form-control').attr('readonly', true);
                $(this).closest('tr').find('.service_id').attr('disabled', true);
                $(this).closest('tr').find('.service_id').removeAttr('name');
                $(this).closest('tr').append('<input type="hidden" name="service_id[]" class="temp_service_id" value="' + service_id + '">');
                $(this).html('<i class="fa fa-undo" ></i> Undo');
                $(this).removeClass('del-btn').addClass('undo-btn');
            });
            $('body').on('click', '.undo-btn', function() {
                $(this).closest('tr').find('.form-control').removeAttr('readonly');
                $(this).closest('tr').find('.service_id').removeAttr('disabled');
                $(this).closest('tr').find('.service_id').attr('name', 'service_id[]');
                $(this).closest('tr').find('.temp_service_id').remove();
                $(this).html('<i class="fa fa-trash" ></i> Delete');
                $(this).removeClass('undo-btn').addClass('del-btn');
            });

            // Filter out empty service selections before form submission
            $('form').on('submit', function(e) {
                var hasValidService = false;
                var hasReadOnlyService = false;

                $('#service-table tbody tr').each(function() {
                    // Check for read-only services (hidden inputs)
                    var hiddenServiceId = $(this).find('input[type="hidden"][name="service_id[]"]').val();
                    if (hiddenServiceId && hiddenServiceId != '') {
                        hasReadOnlyService = true;
                    }

                    // Check for select dropdown services
                    var serviceVal = $(this).find('.service_id').val();
                    if (serviceVal && serviceVal != '' && !$(this).find('.service_id').is(':disabled')) {
                        hasValidService = true;
                    }
                });

                // Must have at least one service (either read-only purchased or new)
                if (!hasValidService && !hasReadOnlyService) {
                    e.preventDefault();
                    alert('Please select at least one service!');
                    return false;
                }
            });
        });
    </script>
</div>