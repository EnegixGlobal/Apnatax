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
                            <th>Service Options</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $required = true;
                        $service_option_ids_data = array();
                        if (!empty($service_package) && !empty($service_package['service_option_ids'])) {
                            $service_option_ids_data = json_decode($service_package['service_option_ids'], true);
                            if (!is_array($service_option_ids_data)) {
                                $service_option_ids_data = array();
                            }
                        }
                        if (!empty($service_package)) {
                            $service_ids = explode(',', $service_package['service_ids']);
                            foreach ($service_ids as $index => $service_id) {
                                // Get single selected option (not array) - handle both old array format and new single value format
                                $selected_option_value = '';
                                if (!empty($service_option_ids_data[$service_id])) {
                                    if (is_array($service_option_ids_data[$service_id])) {
                                        // Old format: array - take first value
                                        $selected_option_value = !empty($service_option_ids_data[$service_id][0]) ? $service_option_ids_data[$service_id][0] : '';
                                    } else {
                                        // New format: single value
                                        $selected_option_value = $service_option_ids_data[$service_id];
                                    }
                                }
                        ?>
                                <tr>
                                    <td>
                                        <?= create_form_input('select', 'service_id[]', '', $required, $service_id, ['class' => 'service_id'], service_dropdown(['id>' => 1])); ?>
                                    </td>
                                    <td class="service-options-cell">
                                        <div class="service-options-wrapper" style="display: <?= !empty($services_with_options[$service_id]['options']) ? 'block' : 'none'; ?>;">
                                            <select name="service_option[<?= $index; ?>]" class="form-control service-option-select" style="min-width: 200px;">
                                                <option value="">Select Option</option>
                                                <?php
                                                if (!empty($services_with_options[$service_id]['options'])) {
                                                    foreach ($services_with_options[$service_id]['options'] as $option) {
                                                        $selected = ($selected_option_value == $option['id']) ? 'selected' : '';
                                                        echo '<option value="' . $option['id'] . '" ' . $selected . '>' . $option['display_name'] . '</option>';
                                                    }
                                                }
                                                ?>
                                            </select>
                                        </div>
                                        <div class="no-options-message text-muted" style="display: <?= empty($services_with_options[$service_id]['options']) ? 'block' : 'none'; ?>;">
                                            <i class="fa fa-info-circle"></i> No options available for this service
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-sm btn-danger del-btn d-none"><i class="fa fa-trash"></i> Delete</button>
                                    </td>
                                </tr>
                            <?php
                            }
                            $required = false;
                        } else {
                            ?>
                            <tr>
                                <td>
                                    <?= create_form_input('select', 'service_id[]', '', $required, '', ['class' => 'service_id'], service_dropdown(['id>' => 1])); ?>
                                </td>
                                <td class="service-options-cell">
                                    <div class="service-options-wrapper" style="display: none;">
                                        <select name="service_option[0]" class="form-control service-option-select" style="min-width: 200px;">
                                            <option value="">Select Option</option>
                                        </select>
                                    </div>
                                    <div class="no-options-message text-muted" style="display: none;">
                                        <i class="fa fa-info-circle"></i> No options available for this service
                                    </div>
                                    <div class="select-service-message text-muted" style="display: block;">
                                        <i class="fa fa-arrow-left"></i> Please select a service first
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info add-btn"><i class="fa fa-plus"></i> Add</button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                        <?php
                        // Always show Add button row, even after saving
                        if (!empty($service_package)) {
                            $next_index = count(explode(',', $service_package['service_ids']));
                        ?>
                            <tr>
                                <td>
                                    <?= create_form_input('select', 'service_id[]', '', false, '', ['class' => 'service_id'], service_dropdown(['id>' => 1])); ?>
                                </td>
                                <td class="service-options-cell">
                                    <div class="service-options-wrapper" style="display: none;">
                                        <select name="service_option[<?= $next_index; ?>]" class="form-control service-option-select" style="min-width: 200px;">
                                            <option value="">Select Option</option>
                                        </select>
                                    </div>
                                    <div class="no-options-message text-muted" style="display: none;">
                                        <i class="fa fa-info-circle"></i> No options available for this service
                                    </div>
                                    <div class="select-service-message text-muted" style="display: block;">
                                        <i class="fa fa-arrow-left"></i> Please select a service first
                                    </div>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-info add-btn"><i class="fa fa-plus"></i> Add</button>
                                </td>
                            </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
                <button type="submit" name="savepackage" class="btn btn-sm btn-success">Save Package</button>
                <?php
                // Show delete request button if package exists and no delete request is pending (request=0) or if rejected (request=2)
                if (!empty($service_package) && ($service_package['request'] == 0 || $service_package['request'] == 2)) {
                ?>
                    <button type="button" class="btn btn-sm btn-danger" id="request-delete-btn" style="margin-left: 10px;">
                        <i class="fa fa-trash"></i> Request Package Deletion
                    </button>
                    <?php
                    if ($service_package['request'] == 2) {
                    ?>
                        <span class="badge badge-danger" style="margin-left: 10px; padding: 8px 12px;">
                            <i class="fa fa-times"></i> Previous Request Was Rejected
                        </span>
                    <?php
                    }
                } elseif (!empty($service_package) && $service_package['request'] == 1) {
                    ?>
                    <span class="badge badge-warning" style="margin-left: 10px; padding: 8px 12px;">
                        <i class="fa fa-clock-o"></i> Delete Request Pending Admin Approval
                    </span>
                <?php
                }
                ?>
                <?= form_close(); ?>
            </div>
        </div>
    </div>

    <style>
        .service-options-cell {
            min-width: 250px;
        }

        .service-options-wrapper {
            animation: fadeIn 0.3s ease-in;
        }

        .no-options-message,
        .select-service-message {
            padding: 8px;
            font-style: italic;
            animation: fadeIn 0.3s ease-in;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        .service-option-select {
            border: 1px solid #ced4da;
            border-radius: 4px;
            padding: 6px 12px;
        }

        .service-option-select:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        #service-table tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
    <script>
        // Store service options data for JavaScript
        var servicesWithOptions = <?= json_encode($services_with_options); ?>;

        $(document).ready(function(e) {
            // Function to update service options dropdown when service is selected
            // preSelectedValue: optional - used to preserve the previously selected option
            function updateServiceOptions(selectElement, serviceId, preSelectedValue) {
                var optionsCell = $(selectElement).closest('tr').find('.service-options-cell');
                var optionsWrapper = optionsCell.find('.service-options-wrapper');
                var noOptionsMessage = optionsCell.find('.no-options-message');
                var selectServiceMessage = optionsCell.find('.select-service-message');
                var optionsSelect = optionsWrapper.find('.service-option-select');

                // Hide all messages first
                selectServiceMessage.hide();
                optionsWrapper.hide();
                noOptionsMessage.hide();

                if (!serviceId || serviceId === '') {
                    // No service selected
                    selectServiceMessage.show();
                    optionsSelect.html('<option value="">Select Options</option>');
                    return;
                }

                optionsSelect.html('<option value="">Select Option</option>');

                if (servicesWithOptions[serviceId] && servicesWithOptions[serviceId].options && servicesWithOptions[serviceId].options.length > 0) {
                    // Service has options - show the select
                    $.each(servicesWithOptions[serviceId].options, function(index, option) {
                        var isSelected = (preSelectedValue && preSelectedValue == option.id) ? ' selected' : '';
                        optionsSelect.append('<option value="' + option.id + '"' + isSelected + '>' + option.display_name + '</option>');
                    });
                    optionsWrapper.show();
                    noOptionsMessage.hide();
                    selectServiceMessage.hide();
                } else {
                    // Service has no options
                    optionsWrapper.hide();
                    noOptionsMessage.show();
                    selectServiceMessage.hide();
                }
            }

            // Handle service selection change (no preSelectedValue - user is choosing fresh)
            $('body').on('change', '.service_id', function() {
                var serviceId = $(this).val();
                updateServiceOptions(this, serviceId, null);
            });

            // Initialize options for existing rows - preserve the already-selected option from PHP
            $('.service_id').each(function() {
                var serviceId = $(this).val();
                // Read the currently selected value rendered by PHP before rebuilding
                var currentSelectedOption = $(this).closest('tr').find('.service-option-select').val();
                updateServiceOptions(this, serviceId, currentSelectedOption);
            });

            $('body').on('click', '.add-btn', function() {
                var row = $(this).closest('tr').clone();
                var rowIndex = $('#service-table tbody tr').length;
                $(row).find('.add-btn').html('<i class="fa fa-trash"></i> Remove');
                $(row).find('.add-btn').removeClass('add-btn btn-info').addClass('remove-btn btn-danger');
                $(row).find('.service_id').val('').attr('name', 'service_id[]');
                $(row).find('.service-option-select').attr('name', 'service_option[' + rowIndex + ']').html('<option value="">Select Option</option>');
                // Reset the service options display
                $(row).find('.service-options-wrapper').hide();
                $(row).find('.no-options-message').hide();
                $(row).find('.select-service-message').show();
                $('#service-table tbody').append(row);
            });
            $('body').on('click', '.remove-btn', function() {
                $(this).closest('tr').remove();
            });
            $('body').on('click', '.del-btn', function() {
                var service_id = $(this).closest('tr').find('.service_id').val();
                $(this).closest('tr').find('.form-control').attr('readonly', true);
                $(this).closest('tr').find('.service_id').attr('disabled', true);
                $(this).closest('tr').find('.service_id').removeAttr('name');
                $(this).closest('div').append('<input type="hidden" name="service_id[]" class="temp_service_id" value="' + service_id + '">');
                $(this).closest('div').find('.status').val(0);
                $(this).html('<i class="fa fa-undo" ></i> Undo');
                $(this).removeClass('del-btn').addClass('undo-btn');
            });
            $('body').on('click', '.undo-btn', function() {
                $(this).closest('tr').find('.form-control').removeAttr('readonly');
                $(this).closest('tr').find('.service_id').removeAttr('disabled');
                $(this).closest('tr').find('.service_id').attr('name', 'service_id[]');
                $(this).closest('div').find('.temp_service_id').remove();
                $(this).closest('div').find('.status').val(1);
                $(this).html('<i class="fa fa-trash" ></i> Delete');
                $(this).removeClass('undo-btn').addClass('del-btn');
            });
            $('body').on('click', '#request-delete-btn', function() {
                if (confirm("Are you sure you want to request package deletion? Admin will review your request.")) {
                    $.ajax({
                        type: 'post',
                        url: '<?= base_url('package/requestdelete'); ?>',
                        success: function(data) {
                            window.location.reload();
                        },
                        error: function() {
                            alert('Error occurred while submitting delete request!');
                        }
                    });
                }
            });
        });
    </script>
</div>