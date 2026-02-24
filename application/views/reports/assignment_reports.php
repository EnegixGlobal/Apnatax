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

    .summary-card.warning {
        border-left-color: #f59f00;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 3px;
        font-size: 12px;
        font-weight: bold;
    }

    .status-pending {
        background-color: #f59f00;
        color: white;
    }

    .status-done {
        background-color: #5eba00;
        color: white;
    }
</style>
<div class="card-body">
    <!-- Filter Form -->
    <form method="get" action="<?= base_url('reports/assignmentreports/'); ?>" class="mb-4">
        <div class="row">
            <div class="col-md-2">
                <div class="form-group">
                    <label>Period</label>
                    <select name="period" class="form-control" id="period">
                        <option value="" <?= empty($selected_period) || $selected_period == '' ? 'selected' : ''; ?>>All Time</option>
                        <option value="date" <?= $selected_period == 'date' ? 'selected' : ''; ?>>Date</option>
                        <option value="month" <?= $selected_period == 'month' ? 'selected' : ''; ?>>Month</option>
                        <option value="year" <?= $selected_period == 'year' ? 'selected' : ''; ?>>Year</option>
                        <option value="custom" <?= $selected_period == 'custom' ? 'selected' : ''; ?>>Custom Date Range</option>
                    </select>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                    <label>Service</label>
                    <?= form_dropdown('service_id', $services, $selected_service, 'class="form-control"'); ?>
                </div>
            </div>
            <div class="col-md-2" id="date_div" style="display:<?= $selected_period == 'date' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>Date</label>
                    <input type="date" name="date" class="form-control" value="<?= $selected_date; ?>">
                </div>
            </div>
            <div class="col-md-2" id="month_div" style="display:<?= $selected_period == 'month' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>Month</label>
                    <?= form_dropdown('month', $month_options, $selected_month, 'class="form-control" id="month_select"'); ?>
                </div>
            </div>
            <div class="col-md-2" id="year_div" style="display:<?= $selected_period == 'year' ? 'block' : 'none'; ?>;">
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
            <div class="card summary-card info">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h3 class="mb-0"><?= $total_records; ?></h3>
                            <p class="text-muted mb-0">Total Records</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fa fa-list fa-2x text-info"></i>
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
                            <h3 class="mb-0"><?= $total_done; ?></h3>
                            <p class="text-muted mb-0">Done</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fa fa-check-circle fa-2x text-success"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card summary-card warning">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div>
                            <h3 class="mb-0"><?= $total_pending; ?></h3>
                            <p class="text-muted mb-0">Pending</p>
                        </div>
                        <div class="ms-auto">
                            <i class="fa fa-clock-o fa-2x text-warning"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Reports Table -->
    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="assignment_table">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Service Name</th>
                    <th>Customer Name</th>
                    <th>Employee Name</th>
                    <th>Status</th>
                    <th>Done Date</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if (!empty($assignments)) {
                    foreach ($assignments as $row) {
                        $status = (isset($row['assignment_done']) && $row['assignment_done'] == 1) ? 'Done' : 'Pending';
                        $status_class = ($status == 'Done') ? 'status-done' : 'status-pending';
                        $done_date = (!empty($row['assignment_done_date'])) ? date('d-m-Y H:i', strtotime($row['assignment_done_date'])) : '-';
                        $assessment_date = !empty($row['assessment_date']) ? date('d-m-Y', strtotime($row['assessment_date'])) : '-';
                        $employee_name = !empty($row['employee_full_name']) ? $row['employee_full_name'] : (!empty($row['employee_name']) ? $row['employee_name'] : 'Not Assigned');
                ?>
                        <tr>
                            <td><?= $assessment_date; ?></td>
                            <td><?= !empty($row['service_name']) ? $row['service_name'] : 'N/A'; ?></td>
                            <td><?= !empty($row['customer_name']) ? $row['customer_name'] : 'N/A'; ?></td>
                            <td><?= $employee_name; ?></td>
                            <td><span class="status-badge <?= $status_class; ?>"><?= $status; ?></span></td>
                            <td><?= $done_date; ?></td>
                        </tr>
                    <?php
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6" class="text-center">No assignment records found</td>
                    </tr>
                <?php
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Initialize DataTable if available
        if ($.fn.DataTable) {
            // Check if table exists
            var table = $('#assignment_table');
            if (table.length > 0) {
                // Destroy existing DataTable instance if it exists
                if ($.fn.DataTable.isDataTable('#assignment_table')) {
                    $('#assignment_table').DataTable().destroy();
                }
                
                // Initialize DataTable
                try {
                    table.DataTable({
                        "order": [[0, "desc"]],
                        "pageLength": 25,
                        "lengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                        "responsive": true,
                        "autoWidth": false
                    });
                } catch (e) {
                    console.error('DataTable initialization error:', e);
                }
            }
        }

        // Show/hide period-specific fields
        $('#period').on('change', function() {
            var period = $(this).val();
            $('#date_div, #month_div, #year_div, #start_date_div, #end_date_div').hide();
            
            if (period == 'date') {
                $('#date_div').show();
            } else if (period == 'month') {
                $('#month_div').show();
            } else if (period == 'year') {
                $('#year_div').show();
            } else if (period == 'custom') {
                $('#start_date_div, #end_date_div').show();
            }
        });
    });
</script>

