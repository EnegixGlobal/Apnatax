<div class="card">
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Wallet Recharge History</h5>
                    <a href="<?= base_url('customers/walletrecharge/'); ?>" class="btn btn-sm btn-success">
                        <i class="fa fa-plus"></i> Add New Recharge
                    </a>
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
                                <th>Date</th>
                                <th>Customer Name</th>
                                <th>Mobile</th>
                                <th>Email</th>
                                <th>Amount</th>
                                <th>Transaction ID</th>
                                <th>Payment Method/Remarks</th>
                                <th>Recharged On</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            if (!empty($recharges)) {
                                $i = 0;
                                foreach ($recharges as $recharge) {
                                    $i++;
                            ?>
                                    <tr>
                                        <td><?= $i; ?></td>
                                        <td><?= date('d-m-Y', strtotime($recharge['date'])); ?></td>
                                        <td><?= !empty($recharge['customer_name']) ? $recharge['customer_name'] : 'N/A'; ?></td>
                                        <td><?= !empty($recharge['customer_mobile']) ? $recharge['customer_mobile'] : '-'; ?></td>
                                        <td><?= !empty($recharge['customer_email']) ? $recharge['customer_email'] : '-'; ?></td>
                                        <td><strong>₹<?= number_format($recharge['amount'], 2); ?></strong></td>
                                        <td><?= $recharge['merchant_transaction_id']; ?></td>
                                        <td>
                                            <?php
                                            if (!empty($recharge['remarks'])) {
                                                echo '<span class="badge bg-info">' . htmlspecialchars($recharge['remarks']) . '</span>';
                                            } else {
                                                echo '<span class="text-muted">-</span>';
                                            }
                                            ?>
                                        </td>
                                        <td><?= date('d-m-Y H:i:s', strtotime($recharge['added_on'])); ?></td>
                                    </tr>
                            <?php
                                }
                            } else {
                            ?>
                                <tr>
                                    <td colspan="9" class="text-center">
                                        <div class="alert alert-info">
                                            <i class="fa fa-info-circle"></i> No wallet recharges found.
                                        </div>
                                    </td>
                                </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    $(document).ready(function(e) {
        $('#table').dataTable({
            "order": [[0, "desc"]],
            "pageLength": 25
        });
    });
</script>

