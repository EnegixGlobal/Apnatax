<style>
    .summary-card {
        border-left: 4px solid;
        transition: transform 0.2s, box-shadow 0.2s;
    }

    .summary-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
        border-left-color: #f1c40f;
    }

    .cell-right {
        text-align: right;
    }
</style>
<div class="card">
    <div class="card-body">
        <!-- Summary Cards -->
        <?php if (isset($total_earnings) || isset($total_orders) || isset($total_payments) || isset($balance)) { ?>
            <div class="row mb-4">
                <div class="col-md-3">
                    <div class="card summary-card primary">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= isset($total_earnings) ? $this->amount->toDecimal($total_earnings, false) : '0.00'; ?></h3>
                                    <p class="text-muted mb-0">Total Earnings</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fa fa-rupee fa-2x text-primary"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card summary-card success">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div>
                                    <h3 class="mb-0"><?= isset($total_orders) ? $total_orders : 0; ?></h3>
                                    <p class="text-muted mb-0">Total Orders</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fa fa-shopping-cart fa-2x text-success"></i>
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
                                    <h3 class="mb-0"><?= isset($total_payments) ? $this->amount->toDecimal($total_payments, false) : '0.00'; ?></h3>
                                    <p class="text-muted mb-0">Total Paid</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fa fa-money fa-2x text-info"></i>
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
                                    <h3 class="mb-0"><?= isset($balance) ? $this->amount->toDecimal($balance, false) : '0.00'; ?></h3>
                                    <p class="text-muted mb-0">Pending Balance</p>
                                </div>
                                <div class="ms-auto">
                                    <i class="fa fa-clock-o fa-2x text-warning"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>

        <!-- Earnings Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="datatable">
                <thead>
                    <tr>
                        <th>Sl.No.</th>
                        <th>Date</th>
                        <th>Customer Name</th>
                        <th>Mobile</th>
                        <th>Email</th>
                        <th>Service Purchased</th>
                        <th class="cell-right">Order Amount</th>
                        <th class="cell-right">Commission</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if (!empty($earnings)) {
                        $i = 0;
                        foreach ($earnings as $single) {
                            $i++;
                    ?>
                            <tr>
                                <td><?= $i; ?></td>
                                <td><?= !empty($single['date']) ? date('d-m-Y', strtotime($single['date'])) : (!empty($single['added_on']) ? date('d-m-Y', strtotime($single['added_on'])) : '-'); ?></td>
                                <td><?= !empty($single['name']) ? $single['name'] : '-'; ?></td>
                                <td><?= !empty($single['mobile']) ? $single['mobile'] : '-'; ?></td>
                                <td><?= !empty($single['email']) ? $single['email'] : '-'; ?></td>
                                <td><?= !empty($single['service_name']) ? $single['service_name'] : '-'; ?></td>
                                <td class="cell-right"><?= !empty($single['order_amount']) ? $this->amount->toDecimal($single['order_amount'], false) : '0.00'; ?></td>
                                <td class="cell-right"><strong><?= !empty($single['amount']) ? $this->amount->toDecimal($single['amount'], false) : '0.00'; ?></strong></td>
                            </tr>
                        <?php
                        }
                    } else {
                        ?>
                        <tr>
                            <td colspan="8" class="text-center">No earnings found</td>
                        </tr>
                    <?php
                    }
                    ?>
                </tbody>
                <?php if (!empty($earnings) && isset($total_earnings)) { ?>
                    <tfoot>
                        <tr class="font-weight-bold">
                            <td colspan="6" class="text-right">Total:</td>
                            <td class="cell-right"><?= $this->amount->toDecimal($total_earnings, false); ?></td>
                            <td class="cell-right"><?= $this->amount->toDecimal($total_earnings, false); ?></td>
                        </tr>
                    </tfoot>
                <?php } ?>
            </table>
        </div>
        <?php if (isset($title) && $title == 'View Earnings') { ?>
            <div class="mt-3">
                <a href="<?= base_url('wallet/employeeearnings/'); ?>" class="btn btn-sm btn-danger">Close</a>
            </div>
        <?php } ?>
    </div>
</div>
<script>
    $(document).ready(function(e) {
        $('#datatable').DataTable({
            "order": [
                [0, "asc"]
            ],
            "pageLength": 25,
            "language": {
                "search": "Search:",
                "lengthMenu": "Show _MENU_ entries",
                "info": "Showing _START_ to _END_ of _TOTAL_ entries",
                "infoEmpty": "No entries found",
                "infoFiltered": "(filtered from _MAX_ total entries)"
            }
        });
    });
</script>