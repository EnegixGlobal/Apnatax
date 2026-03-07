<style>
    .cell-right {
        text-align: right;
    }

    .payment-summary {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
</style>
<div class="card-body">
    <?= form_open_multipart('reports/processpayment/'); ?>
    <div class="row mb-4">
        <div class="col-md-12">
            <h4>Accountancy Payment</h4>
            <p class="text-muted">Review and pay your outstanding accountancy fees</p>
        </div>
    </div>

    <?php if (!empty($unpaid_months)) { ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Month</th>
                                <th>Due Date</th>
                                <th class="cell-right">Outstanding</th>
                                <th class="cell-right">Accounts Fee</th>
                                <th class="cell-right">Late Fee</th>
                                <th class="cell-right">Balance</th>
                                <th>Delay in Days</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($unpaid_months as $month) { ?>
                                <tr>
                                    <td><?= $month['month']; ?></td>
                                    <td><?= $month['due_date']; ?></td>
                                    <td class="cell-right"><?= $this->amount->toDecimal($month['outstanding'], false); ?></td>
                                    <td class="cell-right"><?= $this->amount->toDecimal($month['acc_fees'], false); ?></td>
                                    <td class="cell-right"><?= $this->amount->toDecimal($month['penalty'], false); ?></td>
                                    <td class="cell-right"><strong><?= $this->amount->toDecimal($month['balance'], false); ?></strong></td>
                                    <td><?= $month['days']; ?></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="5" class="cell-right">Total Amount to Pay:</th>
                                <th class="cell-right"><?= $this->amount->toDecimal($last_month_balance, false); ?></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="payment-summary">
                    <h5>Payment Summary</h5>
                    <div class="row">
                        <div class="col-md-12">
                            <?php if (!empty($last_month_data)) { ?>
                                <p><strong>Payment Month:</strong> <?= !empty($payment_month_range) ? $payment_month_range : $last_month_data['month']; ?></p>
                                <p><strong>Due Date:</strong> <?= $last_month_data['due_date']; ?></p>
                                <p><strong>Amount to Pay:</strong> <span class="text-success" style="font-size: 1.2em;">₹<?= $this->amount->toDecimal($last_month_balance, false); ?></span></p>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <?= create_form_input('hidden', 'year', '', true, $year); ?>
                <?= create_form_input('hidden', 'firm_id', '', true, $firm_id); ?>
                <?= create_form_input('hidden', 'user_id', '', true, $user_id); ?>
                <?= create_form_input('hidden', 'amount', '', true, $last_month_balance); ?>
                <?php if (!empty($last_month_data)) { ?>
                    <?= create_form_input('hidden', 'last_month_id', '', true, $last_month_data['id']); ?>
                    <?= create_form_input('hidden', 'last_month_date', '', true, $last_month_data['date']); ?>
                <?php } ?>
                <button type="button" id="payButton" class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#paymentConfirmModal">
                    <i class="fa fa-money"></i> Pay ₹<?= $this->amount->toDecimal($last_month_balance, false); ?>
                </button>
                <a href="<?= base_url('reports/'); ?>" class="btn btn-secondary btn-lg">Cancel</a>
            </div>
        </div>

        <!-- Payment Confirmation Modal -->
        <div class="modal fade" id="paymentConfirmModal" tabindex="-1" aria-labelledby="paymentConfirmModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="paymentConfirmModalLabel">
                            <i class="fa fa-money me-2"></i>Confirm Payment
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="alert alert-info mb-3" role="alert">
                            <h6 class="alert-heading"><i class="fa fa-info-circle me-2"></i>Payment Details</h6>
                            <hr>
                            <div class="row mb-2">
                                <div class="col-5"><strong>Payment Period:</strong></div>
                                <div class="col-7"><?= !empty($payment_month_range) ? $payment_month_range : (!empty($last_month_data) ? $last_month_data['month'] : 'N/A'); ?></div>
                            </div>
                            <?php if (!empty($last_month_data)) { ?>
                            <div class="row mb-2">
                                <div class="col-5"><strong>Due Date:</strong></div>
                                <div class="col-7"><?= $last_month_data['due_date']; ?></div>
                            </div>
                            <?php } ?>
                            <div class="row mb-2">
                                <div class="col-5"><strong>Total Amount:</strong></div>
                                <div class="col-7"><span class="text-success fw-bold" style="font-size: 1.1em;">₹<?= $this->amount->toDecimal($last_month_balance, false); ?></span></div>
                            </div>
                            <div class="row">
                                <div class="col-5"><strong>Months to Pay:</strong></div>
                                <div class="col-7"><?= count($unpaid_months); ?> month(s)</div>
                            </div>
                        </div>
                        <div class="alert alert-warning mb-0" role="alert">
                            <i class="fa fa-exclamation-triangle me-2"></i>
                            <strong>Please confirm:</strong> This amount will be deducted from your wallet balance. Are you sure you want to proceed?
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="fa fa-times me-1"></i>Cancel
                        </button>
                        <button type="button" id="confirmPayButton" class="btn btn-success">
                            <i class="fa fa-check me-1"></i>Confirm & Pay
                        </button>
                    </div>
                </div>
            </div>
        </div>
    <?php } else { ?>
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-success">
                    <i class="fa fa-check-circle"></i> All payments are up to date! No outstanding balance.
                </div>
                <a href="<?= base_url('reports/'); ?>" class="btn btn-primary">Back to Reports</a>
            </div>
        </div>
    <?php } ?>

    <?= form_close(); ?>
</div>

<script>
$(document).ready(function() {
    // Handle confirm payment button click
    $('#confirmPayButton').on('click', function() {
        var $btn = $(this);
        // Disable button to prevent double submission
        $btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-1"></span>Processing...');
        
        // Close modal
        var modalElement = $('#paymentConfirmModal');
        modalElement.modal('hide');
        
        // Submit the form after a short delay to allow modal to close
        setTimeout(function() {
            $('form').submit();
        }, 300);
    });
    
    // Reset button state if modal is closed without confirming
    $('#paymentConfirmModal').on('hidden.bs.modal', function() {
        $('#confirmPayButton').prop('disabled', false).html('<i class="fa fa-check me-1"></i>Confirm & Pay');
    });
});
</script>