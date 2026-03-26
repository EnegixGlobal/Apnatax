<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Add Wallet Recharge</h5>
            </div>
            <div class="card-body">
                <?= form_open('customers/savewalletrecharge/'); ?>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php
                            $user_id = '';
                            $attributes = array("id" => "user_id", "class" => "form-control");
                            echo create_form_input('select', 'user_id', "Customer", true, $user_id, $attributes, $customers);
                            ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php
                            $date = date('Y-m-d');
                            $attributes = array("id" => "date", "class" => "form-control", "max" => date('Y-m-d'));
                            echo create_form_input("date", "date", "Transaction Date", true, $date, $attributes);
                            ?>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php
                            $amount = '';
                            $attributes = array("id" => "amount", "Placeholder" => "Enter Amount", "autocomplete" => "off", "step" => "0.01", "min" => "0.01");
                            echo create_form_input("number", "amount", "Amount", true, $amount, $attributes);
                            ?>
                            <small class="text-muted">Enter the amount to be added to customer wallet</small>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <?php
                            $payment_method = '';
                            $payment_options = array(
                                '' => 'Select Payment Method',
                                                // Keep PayU only (other payment modes commented out)
                                'PayU' => 'PayU'
                                // 'RTGS' => 'RTGS',
                                // 'NEFT' => 'NEFT',
                                // 'IMPS' => 'IMPS',
                                // 'Bank Transfer' => 'Bank Transfer',
                                // 'Cash Deposit' => 'Cash Deposit',
                                // 'Other' => 'Other'
                            );
                            $attributes = array("id" => "payment_method", "class" => "form-control");
                            echo create_form_input('select', 'payment_method', "Payment Method", false, $payment_method, $attributes, $payment_options);
                            ?>
                            <small class="text-muted">Select the payment method used (PayU only)</small>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="form-group">
                            <?php
                            $remarks = '';
                            $attributes = array("id" => "remarks", "Placeholder" => "Additional Remarks (Optional)", "autocomplete" => "off", "rows" => 3);
                            echo create_form_input("textarea", "remarks", "Remarks", false, $remarks, $attributes);
                            ?>
                            <small class="text-muted">Add any additional notes or transaction reference number</small>
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="alert alert-info">
                            <i class="fa fa-info-circle"></i> <strong>Note:</strong> This will directly add the amount to the customer's wallet balance. The transaction will appear in the customer's transaction history.
                        </div>
                    </div>
                </div>
                <div class="row mb-4">
                    <div class="col-md-12">
                        <input type="submit" class="btn btn-sm btn-success" name="savewalletrecharge" value="Recharge Wallet">
                        <a href="<?= base_url('customers/'); ?>" class="btn btn-sm btn-danger">Cancel</a>
                    </div>
                </div>
                <?= form_close(); ?>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function(e) {
        // Set max date to today
        var today = new Date().toISOString().split('T')[0];
        $('#date').attr('max', today);
    });
</script>

