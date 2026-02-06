            <div class="card">

                <div class="card-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="table-responsive">
                                <table class="table table-condensed" id="table">
                                    <thead>
                                        <tr>
                                            <th>Sl.No.</th>
                                            <th>Services</th>
                                            <th>Amount</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if (!empty($services)) {
                                            $i = 0;
                                            foreach ($services as $single) {
                                                $i++;
                                                $single = checkservicepurchase($single, $user, $this->session->firm);
                                        ?>
                                                <tr>
                                                    <td><?= $i; ?></td>
                                                    <td><?= $single['name']; ?></td>
                                                    <td><?= $single['rate']; ?></td>
                                                    <td>
                                                        <?php
                                                        if ($single['buy_status'] === true) {
                                                        ?>
                                                            <button type="button" class="btn btn-sm btn-success buy-btn" value="<?= $single['id'] ?>" data-types="<?= htmlspecialchars($single['type'], ENT_QUOTES); ?>" data-service-name="<?= htmlspecialchars($single['name'], ENT_QUOTES); ?>"><i class="fa fa-shopping-cart"></i> Purchase</button>
                                                        <?php
                                                        } elseif ($single['buy_status'] === false) {
                                                            echo '<span class="text-danger">' . $single['message'] . '</span>';
                                                        }
                                                        ?>
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

                <div class="modal  fade" id="typemodal" tabindex="-1" role="dialog" data-bs-backdrop="static" data-bs-keyboard="false">
                    <div class="modal-dialog modal-sm" role="document">
                        <div class="modal-content" id="type-form">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <?= create_form_input('select', '', 'Type', false, '', ['id' => 'type'], ['' => 'Select Type']); ?>
                                    </div>
                                </div>
                                <div class="row mt-3" id="period-selection-row" style="display: none;">
                                    <div class="col-12">
                                        <div class="form-group">
                                            <label id="period-label">Select Period</label>
                                            <select name="period_value" id="period_value" class="form-control">
                                                <option value="">Select Period</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" id="purchase">Purchase</button>
                            </div>
                        </div>
                        <div class="modal-content d-none" id="package-form">
                            <div class="modal-header">
                                <h5 class="modal-title"></h5>
                                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <?php
                                        $packages = $this->master->getpackages();
                                        ?>
                                        <select name="package_id" id="package_id" class="form-control">
                                            <option value="">Select Package</option>
                                            <option value="<?= generate_slug('Accountancy Prime'); ?>">Accountancy Prime</option>
                                            <option value="<?= generate_slug('Accountancy Premium'); ?>">Accountancy Premium</option>
                                        </select>
                                        <div class="form-group">
                                            <?= create_form_input('number', 'amount', 'Monthly Amount', false, '', ['id' => 'amount']); ?>
                                        </div>
                                        <table class="table table-bordered d-none" id="package-table">
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
                            <div class="modal-footer">
                                <button class="btn btn-primary" id="purchase-package">Purchase</button>
                            </div>
                        </div>
                        <div class="modal-content d-none" id="service-options-form">
                            <div class="modal-header">
                                <h5 class="modal-title">Select Service Option</h5>
                                <button class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                                    <span aria-hidden="true">×</span>
                                </button>
                            </div>
                            <div class="modal-body">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form-group mb-3">
                                            <label>Select Option:</label>
                                            <select name="service_option" id="service_option" class="form-control">
                                                <option value="">Select Option</option>
                                            </select>
                                        </div>
                                        <div class="alert alert-info" id="service_option_amount_display" style="display: none;">
                                            <strong>Amount: ₹<span id="service_option_amount">0</span></strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button class="btn btn-primary" id="purchase-service-option">Purchase</button>
                            </div>
                        </div>
                    </div>
                </div>
                <input type="hidden" id="id">
                <script>
                    var myModal;
                    // Service options pricing - will be loaded dynamically for any service
                    var serviceOptionsPricing = {};
                    var serviceOptionsDisplayNames = {};

                    $(document).ready(function(e) {
                        myModal = new bootstrap.Modal(document.getElementById('typemodal'));
                        $('table').on('click', '.buy-btn', function() {
                            var serviceName = $(this).data('service-name') || '';
                            var serviceId = $(this).val();
                            var btnElement = $(this);

                            // Check if this service has dynamic options (for ANY service)
                            // First, check if service has options loaded in page data
                            var serviceHasOptions = false;
                            <?php if (!empty($services)) { ?>
                                var servicesData = <?= json_encode($services); ?>;
                                for (var i = 0; i < servicesData.length; i++) {
                                    if (servicesData[i].id == serviceId && servicesData[i].has_options) {
                                        serviceHasOptions = true;
                                        break;
                                    }
                                }
                            <?php } ?>

                            // Always check via AJAX to be sure (works for all services)
                            $.ajax({
                                type: "post",
                                url: "<?= base_url('services/getserviceoptions/'); ?>",
                                data: {
                                    service_id: serviceId
                                },
                                dataType: "json",
                                async: false, // Make synchronous to check before proceeding
                                success: function(response) {
                                    if (response.status && response.pricing && Object.keys(response.pricing).length > 0) {
                                        serviceHasOptions = true;
                                        serviceOptionsPricing = response.pricing;
                                        serviceOptionsDisplayNames = response.display_names || {};

                                        // Populate dropdown with options
                                        var optionsHtml = '<option value="">Select Option</option>';
                                        if (response.options && response.options.length > 0) {
                                            response.options.forEach(function(option) {
                                                optionsHtml += '<option value="' + option.option_key + '">' + option.display_name + '</option>';
                                            });
                                        }
                                        $('#service_option').html(optionsHtml);

                                        // Update modal title with service name
                                        $('#service-options-form .modal-title').text('Select ' + serviceName + ' Option');

                                        // Show modal
                                        $('#id').val(serviceId);
                                        $('#type-form').addClass('d-none');
                                        $('#package-form').addClass('d-none');
                                        $('#service-options-form').removeClass('d-none');
                                        $('#service_option').val('');
                                        $('#service_option_amount_display').hide();
                                        $('.modal-dialog').removeClass('modal-md').addClass('modal-sm');
                                        myModal.show();
                                    }
                                },
                                error: function() {
                                    // On error, continue with normal flow
                                    serviceHasOptions = false;
                                }
                            });

                            // If service has options, stop here (modal already shown)
                            if (serviceHasOptions) {
                                return false;
                            }

                            // Continue with normal flow for services without options

                            if ($(this).val() == 1) {
                                $('#purchase').text('Select Package');
                            } else {
                                $('#purchase').text('Purchase');
                            }
                            // $('#package_id').val(''); // Commented out: Package selection reset
                            $('.modal-dialog').removeClass('modal-md').addClass('modal-sm')
                            $('#id').val($(this).val());
                            $('#type-form').removeClass('d-none');
                            $('#package-form').addClass('d-none');
                            $('#service-options-form').addClass('d-none');
                            var type = $(this).data('types');
                            if (type == '') {

                            } else if (type.search(',') == -1) {
                                $('#type').html('<option value="">Select Type</option><option value="' + type + '">' + type + '</option>');
                                $('#type').val(type);
                                myModal.show();
                                $('#type').trigger('change');
                                return false;
                            } else {
                                // Multiple types - show dropdown
                                var types = type.split(',');
                                var options = '<option value="">Select Type</option>';
                                for (let i = 0; i < types.length; i++) {
                                    options += '<option value="' + types[i] + '">' + types[i] + '</option>';
                                }
                                $('#type').html(options);

                                myModal.show();
                                return false;
                            }

                            buypackage();
                        });

                        // Handle service option selection (generic for all services)
                        $('body').on('change', '#service_option', function() {
                            var selectedOption = $(this).val();
                            if (selectedOption && serviceOptionsPricing[selectedOption]) {
                                $('#service_option_amount').text(serviceOptionsPricing[selectedOption].toLocaleString('en-IN'));
                                $('#service_option_amount_display').show();
                            } else {
                                $('#service_option_amount_display').hide();
                            }
                        });

                        // Handle service option purchase button (generic for all services)
                        $('body').on('click', '#purchase-service-option', function() {
                            var selectedOption = $('#service_option').val();
                            if (selectedOption == '') {
                                alert('Please select an option!');
                                return false;
                            }

                            var id = $('#id').val();
                            var amount = serviceOptionsPricing[selectedOption];

                            var year = '<?= $this->session->year; ?>';
                            var periodValue = '';
                            // For Yearly type, get the year period value
                            $.ajax({
                                type: "post",
                                url: "<?= base_url('api/common/getyears'); ?>",
                                dataType: "json",
                                async: false,
                                success: function(response) {
                                    if (response.status && response.years && response.years.length > 0) {
                                        // Use the first year as default or match with current session year
                                        periodValue = response.years[0].id;
                                        // Try to match with current year
                                        var currentYearId = year + (parseInt(year) + 1);
                                        response.years.forEach(function(y) {
                                            if (y.id == currentYearId) {
                                                periodValue = y.id;
                                            }
                                        });
                                    }
                                }
                            });

                            $.ajax({
                                type: "post",
                                url: "<?= base_url('services/buyservice/'); ?>",
                                data: {
                                    id: id,
                                    type: 'Yearly', // Default type for services with options
                                    amount: amount,
                                    package_id: '',
                                    service_option: selectedOption,
                                    period_value: periodValue
                                },
                                success: function(data) {
                                    if (data != '') {
                                        window.location = data;
                                    } else {
                                        window.location.reload();
                                    }
                                },
                                error: function(xhr, status, error) {
                                    alert('Error purchasing service. Please try again.');
                                    console.error('Purchase error:', error);
                                }
                            });
                        });
                        // Handle type change to show/hide period dropdown
                        $('body').on('change', '#type', function() {
                            var selectedType = $(this).val();
                            var year = '<?= $this->session->year; ?>';

                            if (selectedType == 'Monthly' || selectedType == 'Quarterly' || selectedType == 'Yearly') {
                                $('#period-selection-row').show();
                                $('#period_value').html('<option value="">Loading...</option>');

                                if (selectedType == 'Monthly') {
                                    $('#period-label').text('Select Month');
                                    // Load months via AJAX
                                    $.ajax({
                                        type: "post",
                                        url: "<?= base_url('api/common/getmonths'); ?>",
                                        data: {
                                            year: year
                                        },
                                        dataType: "json",
                                        success: function(response) {
                                            if (response.status && response.months) {
                                                var options = '<option value="">Select Month</option>';
                                                response.months.forEach(function(month) {
                                                    options += '<option value="' + month.id + '">' + month.value + '</option>';
                                                });
                                                $('#period_value').html(options);
                                            }
                                        }
                                    });
                                } else if (selectedType == 'Quarterly') {
                                    $('#period-label').text('Select Quarter');
                                    // Load quarters via AJAX
                                    $.ajax({
                                        type: "post",
                                        url: "<?= base_url('api/common/getquarters'); ?>",
                                        data: {
                                            year: year
                                        },
                                        dataType: "json",
                                        success: function(response) {
                                            if (response.status && response.quarters) {
                                                var options = '<option value="">Select Quarter</option>';
                                                response.quarters.forEach(function(quarter) {
                                                    options += '<option value="' + quarter.id + '">' + quarter.value + '</option>';
                                                });
                                                $('#period_value').html(options);
                                            }
                                        }
                                    });
                                } else if (selectedType == 'Yearly') {
                                    $('#period-label').text('Select Year');
                                    // Load years via AJAX
                                    $.ajax({
                                        type: "post",
                                        url: "<?= base_url('api/common/getyears'); ?>",
                                        dataType: "json",
                                        success: function(response) {
                                            if (response.status && response.years) {
                                                var options = '<option value="">Select Year</option>';
                                                response.years.forEach(function(year) {
                                                    options += '<option value="' + year.id + '">' + year.value + '</option>';
                                                });
                                                $('#period_value').html(options);
                                            }
                                        }
                                    });
                                }
                            } else {
                                $('#period-selection-row').hide();
                                $('#period_value').val('');
                            }
                        });

                        $('body').on('click', '#purchase', function() {
                            if ($('#type').val() == '') {
                                alert('Select type!');
                                return false;
                            }

                            // Validate period selection for Monthly, Quarterly, Yearly
                            var selectedType = $('#type').val();
                            if ((selectedType == 'Monthly' || selectedType == 'Quarterly' || selectedType == 'Yearly') && $('#period_value').val() == '') {
                                alert('Please select ' + (selectedType == 'Monthly' ? 'Month' : (selectedType == 'Quarterly' ? 'Quarter' : 'Year')) + '!');
                                return false;
                            }

                            if ($('#id').val() == 1) {
                                $('#type-form').addClass('d-none');
                                $('#package-form').removeClass('d-none');
                                $('.modal-dialog').removeClass('modal-sm').addClass('modal-md');

                                if ($('#type').val() == 'Monthly') {
                                    // For Monthly: hide package dropdown, show only Monthly Amount
                                    $('#package_id').val('<?= generate_slug('Accountancy Prime'); ?>');
                                    $('#package_id').addClass('d-none');
                                    $('#package-table').addClass('d-none');
                                    $('#amount').parent().removeClass('d-none');
                                } else {
                                    // For Turnover (or other): show package dropdown & table (after selection), hide Monthly Amount
                                    $('#package_id').removeClass('d-none');
                                    $('#package-table').addClass('d-none');
                                    $('#amount').parent().addClass('d-none');
                                }

                                return false;
                            }
                            buypackage();
                        });
                        $('body').on('click', '#purchase-package', function() {
                            if ($('#package_id').val() == '') {
                                alert('Select Package!');
                                return false;
                            }
                            if ($('#type').val() == 'Monthly' && $('#amount').val() == '') {
                                alert('Enter Monthly Debit Amount!');
                                return false;
                            }

                            buypackage();
                        });
                        $('body').on('change', '#package_id', function() {
                            $('.package').hide();
                            $('.' + $(this).val()).show();
                            $('#package-table').removeClass('d-none');
                        });
                        $('#table').dataTable();
                    });

                    function buypackage() {
                        var id = $('#id').val();
                        var amount = $('#amount').val();
                        // package_id is commented out, sending empty string
                        var package_id = $('#package_id').length > 0 ? $('#package_id').val() : '';
                        var period_value = $('#period_value').val() || '';
                        $.ajax({
                            type: "post",
                            url: "<?= base_url('services/buyservice/'); ?>",
                            data: {
                                id: id,
                                type: $('#type').val(),
                                amount: amount,
                                package_id: package_id,
                                period_value: period_value
                            },
                            success: function(data) {
                                if (data != '') {
                                    window.location = data;
                                } else {
                                    window.location.reload();
                                }
                            }
                        });
                    }

                    function validate() {

                        return true; // Allow form submiss

                    }
                </script>
            </div>