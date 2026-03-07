<style>
    .cell-right {
        text-align: right;
    }

    .summary-card {
        border-left: 4px solid;
    }

    .summary-card.primary {
        border-left-color: #467fcf;
    }

    .summary-card.warning {
        border-left-color: #f59f00;
    }

    .summary-card.info {
        border-left-color: #45aaf2;
    }

    .summary-card.danger {
        border-left-color: #cd201f;
    }

    .badge.badge-renewal {
        background-color: #f59f00 !important;
        color: white !important;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge.badge-pending {
        background-color: #cd201f !important;
        color: white !important;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge.badge-info {
        background-color: #45aaf2 !important;
        color: white !important;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge.badge-secondary {
        background-color: #6c757d !important;
        color: white !important;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .service-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .service-list li {
        padding: 6px 10px;
        margin: 4px 0;
        background-color: #f8f9fa;
        border-left: 3px solid #467fcf;
        border-radius: 3px;
    }

    .expandable-row {
        cursor: pointer;
    }

    .expandable-row:hover {
        background-color: #f8f9fa;
    }

    .details-row {
        display: none;
    }

    .details-row.show {
        display: table-row;
    }

    .details-content {
        padding: 15px;
        background-color: #f8f9fa;
    }

    .details-section {
        margin-bottom: 15px;
    }

    .details-section h6 {
        color: #467fcf;
        font-weight: 600;
        margin-bottom: 10px;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 5px;
    }

    .no-data {
        text-align: center;
        padding: 40px;
        color: #6c757d;
    }

    .no-data i {
        font-size: 48px;
        margin-bottom: 15px;
        color: #dee2e6;
    }
</style>

<div class="card-body">
    <!-- Filter Form -->
    <form method="get" action="<?= base_url('reports/pendingpackagerenewals/'); ?>" class="mb-4">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Customer</label>
                    <?= form_dropdown('customer_id', $customers, $selected_customer, 'class="form-control" id="customer_id"'); ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Financial Year</label>
                    <?= form_dropdown('year', $years, $selected_year, 'class="form-control" id="year_select"'); ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-filter"></i> Filter
                    </button>
                    <a href="<?= base_url('reports/pendingpackagerenewals/'); ?>" class="btn btn-secondary">
                        <i class="fa fa-refresh"></i> Reset
                    </a>
                </div>
            </div>
        </div>
    </form>

    <?php
    $total_customers = count($pending_renewals);
    $total_renewal_services = 0;
    $total_pending_purchases = 0;

    if (!empty($pending_renewals)) {
        foreach ($pending_renewals as $renewal) {
            $total_renewal_services += $renewal['renewal_count'];
            $total_pending_purchases += $renewal['pending_count'];
        }
    }
    ?>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card summary-card primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h3 class="mb-0"><?= $total_customers; ?></h3>
                            <p class="text-muted mb-0">Customers with Pending Renewals</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fa fa-users fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h3 class="mb-0"><?= $total_renewal_services; ?></h3>
                            <p class="text-muted mb-0">Services Pending Renewal</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fa fa-refresh fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card danger">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h3 class="mb-0"><?= $total_pending_purchases; ?></h3>
                            <p class="text-muted mb-0">Pending Purchases</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fa fa-shopping-cart fa-2x text-danger"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card summary-card info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h3 class="mb-0"><?= $selected_year ? (strlen($selected_year) == 8 ? substr($selected_year, 0, 4) . '-' . substr($selected_year, 4, 4) : $selected_year) : 'Current'; ?></h3>
                            <p class="text-muted mb-0">Selected Year</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fa fa-calendar fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Renewals Table -->
    <?php if (!empty($pending_renewals)) { ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="renewals_table">
                <thead>
                    <tr>
                        <th width="30"></th>
                        <th>Customer Name</th>
                        <th>Firm Name</th>
                        <th>Financial Year</th>
                        <th class="text-center">Renewal Services</th>
                        <th class="text-center">Pending Purchases</th>
                        <th class="text-center">Expired Years</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    foreach ($pending_renewals as $renewal) {
                        $row_id = 'row_' . $renewal['user_id'] . '_' . $renewal['firm_id'];
                        $details_id = 'details_' . $renewal['user_id'] . '_' . $renewal['firm_id'];
                    ?>
                        <tr class="expandable-row" data-target="<?= $details_id; ?>">
                            <td class="text-center">
                                <i class="fa fa-chevron-down expand-icon" id="icon_<?= $details_id; ?>"></i>
                            </td>
                            <td>
                                <strong><?= htmlspecialchars($renewal['customer_name']); ?></strong>
                            </td>
                            <td><?= htmlspecialchars($renewal['firm_name']); ?></td>
                            <td>
                                <span class="badge badge-info" style="background-color: #45aaf2 !important; color: #fff !important;"><?= $renewal['year_display']; ?></span>
                            </td>
                            <td class="text-center">
                                <?php if ($renewal['renewal_count'] > 0) { ?>
                                    <span class="badge badge-renewal"><?= $renewal['renewal_count']; ?> Service(s)</span>
                                <?php } else { ?>
                                    <span class="text-muted">-</span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <?php if ($renewal['pending_count'] > 0) { ?>
                                    <span class="badge badge-pending"><?= $renewal['pending_count']; ?> Purchase(s)</span>
                                <?php } else { ?>
                                    <span class="text-muted">-</span>
                                <?php } ?>
                            </td>
                            <td class="text-center">
                                <?php
                                if (!empty($renewal['expired_years'])) {
                                    $expired_years_display = array();
                                    foreach ($renewal['expired_years'] as $ey) {
                                        if (strlen($ey) == 8 && is_numeric($ey)) {
                                            $ey1 = substr($ey, 0, 4);
                                            $ey2 = substr($ey, 4, 4);
                                            $expired_years_display[] = $ey1 . '-' . $ey2;
                                        } else {
                                            $expired_years_display[] = $ey;
                                        }
                                    }
                                    echo '<span class="badge badge-secondary" style="background-color: #6c757d !important; color: #fff !important;">' . implode(', ', $expired_years_display) . '</span>';
                                } else {
                                    echo '<span class="text-muted">-</span>';
                                }
                                ?>
                            </td>
                        </tr>
                        <tr class="details-row" id="<?= $details_id; ?>">
                            <td colspan="7">
                                <div class="details-content">
                                    <?php if (!empty($renewal['renewal_services'])) { ?>
                                        <div class="details-section">
                                            <h6>
                                                <i class="fa fa-refresh"></i> Services Pending Renewal (<?= count($renewal['renewal_services']); ?>)
                                            </h6>
                                            <ul class="service-list">
                                                <?php foreach ($renewal['renewal_services'] as $service) { ?>
                                                    <li>
                                                        <i class="fa fa-circle" style="font-size: 6px; color: #467fcf;"></i>
                                                        <strong><?= htmlspecialchars($service['service_name']); ?></strong>
                                                        <?php
                                                        if (!empty($service['expired_year'])) {
                                                            $exp_year = $service['expired_year'];
                                                            if (strlen($exp_year) == 8 && is_numeric($exp_year)) {
                                                                $ey1 = substr($exp_year, 0, 4);
                                                                $ey2 = substr($exp_year, 4, 4);
                                                                $exp_year_display = $ey1 . '-' . $ey2;
                                                            } else {
                                                                $exp_year_display = $exp_year;
                                                            }
                                                            echo ' <span class="text-muted">(Expired: ' . $exp_year_display . ')</span>';
                                                        }
                                                        ?>
                                                    </li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    <?php } ?>

                                    <?php if (!empty($renewal['pending_purchases'])) { ?>
                                        <div class="details-section">
                                            <h6>
                                                <i class="fa fa-shopping-cart"></i> Pending Purchases (<?= count($renewal['pending_purchases']); ?>)
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered">
                                                    <thead>
                                                        <tr>
                                                            <th>Service</th>
                                                            <th>Date</th>
                                                            <th class="cell-right">Amount</th>
                                                            <th>Status</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($renewal['pending_purchases'] as $purchase) { ?>
                                                            <tr>
                                                                <td><?= htmlspecialchars($purchase['service'] ?? 'N/A'); ?></td>
                                                                <td><?= !empty($purchase['date']) ? date('d-m-Y', strtotime($purchase['date'])) : 'N/A'; ?></td>
                                                                <td class="cell-right">₹ <?= number_format($purchase['amount'] ?? 0, 2); ?></td>
                                                                <td>
                                                                    <span class="badge badge-warning" style="background-color: #f59f00 !important; color: #fff !important;">Pending</span>
                                                                </td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    <?php } ?>

                                    <?php if (empty($renewal['renewal_services']) && empty($renewal['pending_purchases'])) { ?>
                                        <div class="alert alert-info mb-0">
                                            <i class="fa fa-info-circle"></i> No detailed information available.
                                        </div>
                                    <?php } ?>
                                </div>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <div class="no-data">
            <i class="fa fa-inbox"></i>
            <h4>No Pending Renewals Found</h4>
            <p>There are no packages pending renewal for the selected filters.</p>
        </div>
    <?php } ?>
</div>

<script>
    $(document).ready(function() {
        // Handle expandable rows
        $('.expandable-row').on('click', function() {
            var target = $(this).data('target');
            var detailsRow = $('#' + target);
            var icon = $('#icon_' + target);

            if (detailsRow.hasClass('show')) {
                detailsRow.removeClass('show');
                icon.removeClass('fa-chevron-up').addClass('fa-chevron-down');
            } else {
                detailsRow.addClass('show');
                icon.removeClass('fa-chevron-down').addClass('fa-chevron-up');
            }
        });

        <?php if ($datatable && !empty($pending_renewals)) { ?>
            $('#renewals_table').DataTable({
                "order": [
                    [1, "asc"]
                ],
                "pageLength": 25,
                "columnDefs": [{
                    "orderable": false,
                    "targets": [0]
                }],
                "language": {
                    "emptyTable": "No pending renewals found",
                    "zeroRecords": "No matching records found"
                }
            });
        <?php } ?>
    });
</script>