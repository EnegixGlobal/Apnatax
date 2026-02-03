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

    .summary-card.success {
        border-left-color: #5eba00;
    }

    .summary-card.info {
        border-left-color: #45aaf2;
    }
</style>
<div class="card-body">
    <!-- Filter Form -->
    <form method="get" action="<?= base_url('reports/adminincome/'); ?>" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Period</label>
                    <select name="period" class="form-control" id="period">
                        <option value="" <?= empty($selected_period) || $selected_period == '' ? 'selected' : ''; ?>>All Time</option>
                        <option value="monthly" <?= $selected_period == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                        <option value="quarterly" <?= $selected_period == 'quarterly' ? 'selected' : ''; ?>>Quarterly</option>
                        <option value="yearly" <?= $selected_period == 'yearly' ? 'selected' : ''; ?>>Yearly</option>
                        <option value="custom" <?= $selected_period == 'custom' ? 'selected' : ''; ?>>Custom Date Range</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Service</label>
                    <?= form_dropdown('service_id', $services, $selected_service, 'class="form-control"'); ?>
                </div>
            </div>
            <div class="col-md-2" id="month_div" style="display:<?= $selected_period == 'monthly' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>Month</label>
                    <?= form_dropdown('month', $month_options, $selected_month, 'class="form-control" id="month_select"'); ?>
                </div>
            </div>
            <div class="col-md-2" id="quarter_div" style="display:<?= $selected_period == 'quarterly' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>Quarter</label>
                    <?= form_dropdown('quarter', $quarter_options, $selected_quarter, 'class="form-control" id="quarter_select"'); ?>
                </div>
            </div>
            <div class="col-md-2" id="year_div" style="display:<?= $selected_period == 'yearly' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>Year</label>
                    <?= form_dropdown('year', $year_options, $selected_year, 'class="form-control" id="year_select"'); ?>
                </div>
            </div>
            <div class="col-md-2" id="start_date_div" style="display:<?= $selected_period == 'custom' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date; ?>">
                </div>
            </div>
            <div class="col-md-2" id="end_date_div" style="display:<?= $selected_period == 'custom' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date; ?>">
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </div>
    </form>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card summary-card primary">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h3 class="mb-0"><?= number_format($total_amount, 2); ?></h3>
                            <p class="text-muted mb-0">Total Income</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fa fa-rupee fa-2x text-primary"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card summary-card success">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h3 class="mb-0"><?= $total_orders; ?></h3>
                            <p class="text-muted mb-0">Total Orders</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fa fa-shopping-cart fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card summary-card info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h3 class="mb-0"><?= $total_customers; ?></h3>
                            <p class="text-muted mb-0">Total Customers</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fa fa-users fa-2x text-info"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Income Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="income_table">
            <thead>
                <tr>
                    <th>Service Name</th>
                    <th class="cell-right">Total Amount</th>
                    <th class="cell-right">Total Orders</th>
                    <th class="cell-right">Total Customers</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($income_data)) {
                    foreach ($income_data as $row) {
                ?>
                        <tr>
                            <td><?= $row['service_name']; ?></td>
                            <td class="cell-right"><?= number_format($row['total_amount'], 2); ?></td>
                            <td class="cell-right"><?= $row['total_orders']; ?></td>
                            <td class="cell-right"><?= $row['total_customers']; ?></td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="4" class="text-center">No data found</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
            <tfoot>
                <tr class="font-weight-bold">
                    <td>Total</td>
                    <td class="cell-right"><?= number_format($total_amount, 2); ?></td>
                    <td class="cell-right"><?= $total_orders; ?></td>
                    <td class="cell-right"><?= $total_customers; ?></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#period').change(function() {
            var period = $(this).val();
            // Hide all period-specific fields first
            $('#month_div').hide();
            $('#quarter_div').hide();
            $('#year_div').hide();
            $('#start_date_div').hide();
            $('#end_date_div').hide();

            // Show relevant fields based on period
            if (period == 'custom') {
                $('#start_date_div').show();
                $('#end_date_div').show();
            } else if (period == 'monthly') {
                $('#month_div').show();
            } else if (period == 'quarterly') {
                $('#quarter_div').show();
            } else if (period == 'yearly') {
                $('#year_div').show();
            }
        });

        // Show/hide fields on page load
        var period = $('#period').val();
        // Hide all period-specific fields first
        $('#month_div').hide();
        $('#quarter_div').hide();
        $('#year_div').hide();
        $('#start_date_div').hide();
        $('#end_date_div').hide();

        // Show relevant fields based on period
        if (period == 'custom') {
            $('#start_date_div').show();
            $('#end_date_div').show();
        } else if (period == 'monthly') {
            $('#month_div').show();
        } else if (period == 'quarterly') {
            $('#quarter_div').show();
        } else if (period == 'yearly') {
            $('#year_div').show();
        }

        <?php if ($datatable) { ?>
            $('#income_table').DataTable({
                "order": [
                    [1, "desc"]
                ],
                "pageLength": 25
            });
        <?php } ?>
    });
</script>