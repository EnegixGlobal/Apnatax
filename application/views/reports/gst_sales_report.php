<style>
    .cell-right {
        text-align: right;
    }
</style>

<div class="card-body">
    <form method="get" action="<?= base_url('reports/gstsalesreport/'); ?>" class="mb-4">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Period</label>
                    <select name="period" class="form-control" id="period">
                        <option value="all" <?= $selected_period == 'all' ? 'selected' : ''; ?>>All Time</option>
                        <option value="monthly" <?= $selected_period == 'monthly' ? 'selected' : ''; ?>>Monthly</option>
                        <option value="quarterly" <?= $selected_period == 'quarterly' ? 'selected' : ''; ?>>Quarterly</option>
                        <option value="yearly" <?= $selected_period == 'yearly' ? 'selected' : ''; ?>>Yearly</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3" id="month_div" style="display:<?= $selected_period == 'monthly' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>Month</label>
                    <?= form_dropdown('month', $month_options, $selected_month, 'class="form-control"'); ?>
                </div>
            </div>
            <div class="col-md-3" id="quarter_div" style="display:<?= $selected_period == 'quarterly' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>Quarter</label>
                    <?= form_dropdown('quarter', $quarter_options, $selected_quarter, 'class="form-control"'); ?>
                </div>
            </div>
            <div class="col-md-3" id="year_div" style="display:<?= $selected_period == 'yearly' ? 'block' : 'none'; ?>;">
                <div class="form-group">
                    <label>Year</label>
                    <?= form_dropdown('year', $year_options, $selected_year, 'class="form-control"'); ?>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>&nbsp;</label><br>
                    <button type="submit" class="btn btn-primary">Filter</button>
                </div>
            </div>
        </div>
    </form>

    <div class="mb-3">
        <?php
        $query = $_GET;
        $query['export'] = 'csv';
        ?>
        <a href="<?= base_url('reports/gstsalesreport/?' . http_build_query($query)); ?>" class="btn btn-sm btn-outline-primary">Export CSV</a>
        <?php
        $query['export'] = 'json';
        ?>
        <a href="<?= base_url('reports/gstsalesreport/?' . http_build_query($query)); ?>" class="btn btn-sm btn-outline-info">Export JSON</a>
        <?php
        $query['export'] = 'pdf';
        ?>
        <a href="<?= base_url('reports/gstsalesreport/?' . http_build_query($query)); ?>" class="btn btn-sm btn-outline-danger">Export PDF</a>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-hover" id="gst_sales_table">
            <thead>
                <tr>
                    <th>GSTIN/UIN of Recipient</th>
                    <th>Receiver Name</th>
                    <th>Invoice Number</th>
                    <th>Invoice date</th>
                    <th class="cell-right">Invoice Value</th>
                    <th>Place Of Supply</th>
                    <th>Reverse Charge</th>
                    <th>Applicable % of Tax Rate</th>
                    <th>Invoice Type</th>
                    <th>E-Commerce GSTIN</th>
                    <th class="cell-right">Rate</th>
                    <th class="cell-right">Taxable Value</th>
                    <th class="cell-right">Cess Amount</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($report_rows)) { ?>
                    <?php foreach ($report_rows as $row) { ?>
                        <tr>
                            <td><?= $row['recipient_gstin']; ?></td>
                            <td><?= $row['receiver_name']; ?></td>
                            <td><?= $row['invoice_number']; ?></td>
                            <td><?= $row['invoice_date']; ?></td>
                            <td class="cell-right"><?= number_format((float)$row['invoice_value'], 2); ?></td>
                            <td><?= $row['place_of_supply']; ?></td>
                            <td><?= $row['reverse_charge']; ?></td>
                            <td><?= $row['applicable_tax_rate']; ?></td>
                            <td><?= $row['invoice_type']; ?></td>
                            <td><?= $row['ecommerce_gstin']; ?></td>
                            <td class="cell-right"><?= $row['rate']; ?></td>
                            <td class="cell-right"><?= number_format((float)$row['taxable_value'], 2); ?></td>
                            <td class="cell-right"><?= $row['cess_amount']; ?></td>
                        </tr>
                    <?php } ?>
                <?php } else { ?>
                    <tr>
                        <td colspan="13" class="text-center">No data found</td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#period').change(function() {
            var period = $(this).val();
            $('#month_div').hide();
            $('#quarter_div').hide();
            $('#year_div').hide();
            if (period === 'monthly') {
                $('#month_div').show();
            } else if (period === 'quarterly') {
                $('#quarter_div').show();
            } else if (period === 'yearly') {
                $('#year_div').show();
            }
        });

        <?php if (!empty($datatable)) { ?>
            $('#gst_sales_table').DataTable({
                "order": [
                    [3, "desc"]
                ],
                "pageLength": 25
            });
        <?php } ?>
    });
</script>
